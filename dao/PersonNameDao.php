<?php

/**
 * DAO for {@link PersonName}.
 * <p>
 * 
 */
final class PersonNameDao {

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
    public function find(PersonNameSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $person_name = new PersonName();
            PersonNameMapper::map($person_name, $row);
            $result[] = $person_name;
        }
        return $result;
    }

    /**
     * Find {@link PersonName} by identifier.
     * @return PersonName or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query(
                'SELECT id, person_id, first_name, last_name, start_date, end_date FROM person_names WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $person_name = new PersonName();
        PersonNameMapper::map($person_name, $row);
        return $person_name;
    }

    /**
     * Save {@link PersonName}.
     * @param PersonName $person_name {@link PersonName} to be saved
     * @return PersonName saved {@link PersonName} instance
     */
    public function save( $person_name ) {
        if ( $person_name->getId() === null ) {
            return $this->insert($person_name);
        }
        return $this->update($person_name);
    }
    
    /**
     * Save {@link PersonName}.
     * @param PersonName $person_names {@link PersonName} to be saved
     * @return PersonName saved {@link PersonName} instance
     */
    public function saveAll( $person_names ) {
        foreach( $person_names as $person_name ) {
            $this->save( $person_name );
        }
    }

    /**
     * Delete {@link Unit} by identifier.
     * @param int $id {@link Unit} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM person_names 
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

    private function getFindSql(PersonNameSearchCriteria $search = null) {
        $sql = 'SELECT id, person_id, first_name, last_name, start_date, end_date FROM person_names ';
        if ( $search && $search->hasFilter() ) {
            $where_started = false;
            if ( $search->getSearchDate() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $where_started = true;
                $search_date = "'" . $search->getSearchDate()->format('Y-m-d') . "'";
                $sql .= ' start_date <= ' . $search_date;
                $sql .= ' AND ( end_date is null OR end_date >= ' . $search_date . ')';
            }
            if ( $search->getFirstName() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $where_started = true;
                $sql .= ' first_name = ' . $search->getFirstName();
            }
            if ( $search->getLastName() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $where_started = true;
                $sql .= ' last_name = ' . $search->getLastName();
            }
            if ( $search->getPersonId() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $where_started = true;
                $sql .= ' person_id = ' . $search->getPersonId();
            }
        }
        $sql .= ' ORDER BY start_date';
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    public function insert(PersonName $person_name) {
        $person_name->setId( null );
        $sql = 'INSERT INTO person_names (person_id, first_name, last_name, start_date, end_date)
                VALUES (:person_id, :first_name, :last_name, :start_date, :end_date)';
        $person_name = $this->execute($sql, $person_name);
        $person_name->setId( $this->getDb()->lastInsertId() ); 
        return $person_name;
    }

    /**
     * @return PersonName
     * @throws Exception
     */
    private function update(PersonName $person_name) {
        $sql = '
            UPDATE person_names SET
                person_id = :person_id,
                first_name = :first_name,
                last_name = :last_name,
                start_date = :start_date,
                end_date = :end_date
            WHERE
                id = :id';
        return $this->execute($sql, $person_name, true);
    }

    /**
     * @return PersonName
     * @throws Exception
     */
    private function execute($sql, PersonName $person_name, $update = false ) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($person_name, $update));
        return $person_name;
    }

    private function getParams(PersonName $person_name, $update ) {
        $params = array(
            ':person_id' => $person_name->getPersonId(),
            ':first_name' => $person_name->getFirstName(),
            ':last_name' => $person_name->getLastName(),
            ':start_date' => $person_name->getStartDate()->format(DateTime::ISO8601),
            ':end_date' => $person_name->getEndDate() === null ? null : $person_name->getEndDate()->format(DateTime::ISO8601)
        );
        if ( $update ) {
            $params[ ':id'] = $person_name->getId();
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
