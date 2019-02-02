<?php

/**
 * DAO for {@link Unit}.
 * <p>
 * 
 */
final class PersonDao {

    /** @var PDO */
    private $db = null;

    /**
     * 
     * @param type $db PDO
     */
    public function __construct( $db = null ) {
        $this->db = $db;
    }

    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    private function getNames( $id, $search = null ) {
        $pn_dao = new PersonNameDao();
        $pn_search = new PersonNameSearchCriteria();
        $pn_search->setPersonSearch( $search );
        $pn_search->setPersonId( $id );
        $person_names = $pn_dao->find( $pn_search );   
        return $person_names;
    }
    
    private function getUnits( $id, $search = null ) {
        $up_dao = new UnitPersonDao();
        $up_search = new UnitPersonSearchCriteria();
        $up_search->setPersonSearch( $search );
        $up_search->setPersonId( $id );
        $units = $up_dao->find( $up_search );  
        return $units;
    }
    
    private function saveNames( $person ) {
        $pn_dao = new PersonNameDao();
        foreach( $person->getNames() as $name ) {
            $pn_dao->save( $name );
        }
    }
    
    private function saveUnits( $person ) {
        $up_dao = new UnitPersonDao();
        foreach( $person->getUnits() as $unit ) {
            $up_dao->save( $unit );
        }
    }
    
    /**
     * Find all {@link Unit}s by search criteria.
     * @return array array of {@link Unit}s
     */
    public function find(PersonSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $person = new Person();
            PersonMapper::map($person, $row);
            $person_names = $this->getNames( $person->getId(), $search );
            $unit_persons = $this->getUnits( $person->getId(), $search );
            $person->setNames( $person_names );
            $person->setUnits( $unit_persons );
            $person->setCurrentNameAndUnit();
            $result[$person->getId()] = $person;
        }
        return $result;
    }

    /**
     * Find {@link Person} by identifier.
     * @return Person or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query(
                'SELECT id, password, email, phone_land as phone1, phone_mobile as phone2, phone_work as phone3 '
                    . ' FROM people u WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $person = new Person();
        echo 'DB queery returned row: ' . $row->email;
        PersonMapper::map($person, $row);
        $person_names = $this->getNames( $person->getId() );
        $unit_persons = $this->getUnits( $person->getId() );
        $person->setNames( $person_names );
        $person->setUnits( $unit_persons );
        $person->setCurrentNameAndUnit();
        return $person;
    }

    /**
     * Save {@link Person}.
     * @param Unit $person {@link Unit} to be saved
     * @return Unit saved {@link Unit} instance
     */
    public function save(Person $person) {
        /*
        $pn_dao = new PersonNameDao();
        foreach( $person->getNames() as $person_name ) {
            $pn_dao->save( $person_name );
        }
        $pu_dao = new UnitPersonDao();
        foreach( $person->getUnits() as $unit ) {
            $pu_dao->save( $unit );
        }
         * 
         */
        if ($person->getId() === null) {
            return $this->insert($person);
        }
        return $this->update($person);
    }

    /**
     * Delete {@link Person} by identifier.
     * @param int $id {@link Person} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM people 
            WHERE
                id = :id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':id' => $id,
        ));
        return $statement->rowCount() == 1;
    }

    /**
     * @return PDO
     */
    private function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $config = Config::getConfig("db");
        try {
            $this->db = new PDO($config['dsn'], $config['username'], $config['password'], 
                    array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $this->db;
    }

    private function getFindSql(PersonSearchCriteria $search = null) {
        $sql = 'SELECT p.id as id, p.password as password, p.email as email, '
                . 'p.phone_land as phone1, p.phone_mobile as phone2, p.phone_work as phone3,'
                . ' pn.first_name as first_name, pn.last_name as last_name, pu.unit_id as unit_id,'
                . ' pu.type as occupant_type'
                . ' FROM people p JOIN person_names pn JOIN person_units pu '
                . 'WHERE p.id = pn.person_id AND p.id = pu.person_id ';
        if ( $search && $search->hasFilter() ) {
            if ( $search->getSearchDate() ) {
                $search_date = "'" . $search->getSearchDate()->format('Y-m-d') . "'";
                $sql .= ' AND pu.start_date <= ' . $search_date;
                $sql .= ' AND ( pu.end_date is null OR pu.end_date >= ' . $search_date;
                $sql .= ')';
                $sql .= ' AND pn.start_date <= ' . $search_date;
                $sql .= ' AND ( pn.end_date is null OR pn.end_date >= ' . $search_date;
                $sql .= ')';
            } else {
                $sql .= ' AND pn.end_date is null';
            }
            if ( $search->getUnitId() ) {
                $sql .= 'AND pu.unit_id = ' . $search->getUnitId();
            }
            if ( $search->getPersonId() ) {
                $sql .= ' AND p.id = ' . $search->getPersonId();
            }
            if ( $search->getOccupantType() ) {
                $sql .= ' AND pu.type = ' . "'" . $search->getOccupantType() . "'";
            }
        }
        $sql .= ' ORDER BY first_name';
        return $sql;
    }

    /**
     * @return Person
     * @throws Exception
     */
    private function insert(Person $person) {
        $person->setId( null );
        $sql = '
            INSERT INTO people (id, password, email, phone_land, phone_mobile, phone_work)
                VALUES (:id, :password, :email, :phone1, :phone2, :phone3)';
        return $this->execute($sql, $person);
    }

    /**
     * @return Person
     * @throws Exception
     */
    private function update(Person $person) {
        $sql = '
            UPDATE people SET
                password = :password,
                email = :email,
                phone_land = :phone1,
                phone_mobile = :phone2,
                phone_work = :phone3
            WHERE
                id = :id';
        return $this->execute($sql, $person);
    }

    /**
     * @return Person 
     * @throws Exception
     */
    private function execute($sql, Person $person) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($person));
        if (!$person->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        return $person;
    }

    private function getParams(Person $person) {
        $params = array(
            ':id' => $person->getId(),
            ':password' => $person->getPassword(),
            ':email' => $person->getEmail(),
            ':phone1' => $person->getPhone1(),
            ':phone2' => $person->getPhone2(),
            ':phone3' => $person->getPhone3()
        );
        return $params;
    }

    private function executeStatement(PDOStatement $statement, array $params) {
        if (!$statement->execute($params)) {
            $errorInfo = $this->getDb()->errorInfo();
            self::throwDbError( $errorInfo );
        }
    }

    /**
     * @return PDOStatement
     */
    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    private static function throwDbError(array $errorInfo) {
        // TODO log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }

}
