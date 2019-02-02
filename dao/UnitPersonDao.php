<?php

/**
 * DAO for {@link Unit}.
 * <p>
 * 
 */
final class UnitPersonDao {

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

    /**
     * Find all {@link Unit}s by search criteria.
     * @return array array of {@link Unit}s
     */
    public function find(UnitPersonSearchCriteria $search = null) {
        $result = array();
        $sql = $this->getFindSql($search);
        foreach ($this->query($sql) as $row) {
            $unit_person = new UnitPerson();
            UnitPersonMapper::map($unit_person, $row);
            $result[] = $unit_person;
        }
        return $result;
    }

    /**
     * Find {@link PersonName} by identifier.
     * @return PersonName or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query(
                'SELECT id, unit_id, person_id, start_date, end_date, type as occupant_type FROM person_units WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $unit_person = new UnitPerson();
        UnitPersonMapper::map($unit_person, $row);
        return $unit_person;
    }

    /**
     * Save {@link UnitPerson}.
     * @param UnitPerson $unit_person {@link UnitPerson} to be saved
     * @return UnitPerson saved {@link UnitPerson} instance
     */
    public function save( $unit_person ) {
        if ( $unit_person->getId() === null ) {
            return $this->insert($unit_person);
        }
        return $this->update($unit_person);
    }
    
    /**
     * Save {@link UnitPerson}.
     * @param UnitPerson $unit_persons {@link UnitPerson} to be saved
     * @return UnitPerson saved {@link UnitPerson} instance
     */
    public function saveAll( $unit_persons ) {
        foreach( $unit_persons as $unit_person ) {
            $this->save( $unit_person );
        }
    }

    /**
     * Delete {@link Unit} by identifier.
     * @param int $id {@link Unit} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM person_units 
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

    private function handleWhere( $sql, $where_started ) {
        if ( $where_started ) {
            $sql .= ' AND ';
        } else {
            $sql .= ' WHERE ';
        } 
        return $sql;
    }
    private function getFindSql(UnitPersonSearchCriteria $search = null) {
        $sql = 'SELECT pu.id as id, pu.unit_id as unit_id, pu.person_id as person_id, '
                . 'pu.start_date as start_date, pu.end_date as end_date,'
                . 'pu.type as occupant_type, pn.first_name as first_name, pn.last_name as last_name '
                . ' FROM person_units pu JOIN person_names pn WHERE pn.person_id = pu.person_id ';
        if ( $search && $search->hasFilter() ) {
            $where_started = true;
            if ( $search->getSearchDate() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $search_date = "'" . $search->getSearchDate()->format('Y-m-d') . "'";
                $sql .= 'pu.start_date <= ' . $search_date;
                $sql .= ' AND ( pu.end_date is null OR pu.end_date >= ' . $search_date . ')';
                $sql .= ' AND pn.start_date <= ' . $search_date;
                $sql .= ' AND ( pn.end_date is null OR pn.end_date >= ' . $search_date . ')';
            } else {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pn.end_date is null';
            }
            if ( $search->getUnitId() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.unit_id = ' . $search->getUnitId();
            }
            if ( $search->getPersonId() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.person_id = ' . $search->getPersonId();
            }
            if ( $search->getOccupantType() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.type = ' . "'" . $search->getOccupantType() . "'";
            }
        }
        $sql .= ' ORDER BY pu.start_date';
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    public function insert(UnitPerson $up) {
        $up->setId( null );
        $sql = 'INSERT INTO person_units (unit_id, person_id, start_date, end_date, type)
                VALUES (:unit_id, :person_id, :start_date, :end_date, :type)';
        $unit_person = $this->execute($sql, $up);
        $unit_person->setId( $this->getDb()->lastInsertId() ); 
        return $unit_person;
    }

    /**
     * @return UnitPerson
     * @throws Exception
     */
    private function update(UnitPerson $unit_person) {
        $sql = '
            UPDATE person_units SET
                unit_id = :unit_id,
                person_id = :person_id,
                start_date = :start_date,
                end_date = :end_date,
                type = :type
            WHERE
                id = :id';
        return $this->execute($sql, $unit_person, true);
    }

    /**
     * @return Unit
     * @throws Exception
     */
    private function execute($sql, UnitPerson $unit_person, $update = false) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($unit_person, $update));
        if (!$statement->rowCount()) {
            throw new NotFoundException('UnitPerson with ID "' . $unit_person->getId() . '" does not exist.');
        }
        return $unit_person;
    }

    private function getParams(UnitPerson $unit_person, $update) {
        $params = array(
            ':unit_id' => $unit_person->getUnitId(),
            ':person_id' => $unit_person->getPersonId(),
            ':start_date' => $unit_person->getStartDate()->format(DateTime::ISO8601),
            ':end_date' => $unit_person->getEndDate() ? $unit_person->getEndDate()->format(DateTime::ISO8601) : null,
            ':type' => $unit_person->getOccupantType()
        );
        if ( $update ) {
            $params[':id'] = $unit_person->getId();
        }
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
