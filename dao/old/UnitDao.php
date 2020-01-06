<?php

/**
 * DAO for {@link Unit}.
 * <p>
 * 
 */
final class UnitDao {

    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link Unit}s by search criteria.
     * @return array array of {@link Unit}s
     */
    public function find(UnitSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $unit = new Unit();
            UnitMapper::map($unit, $row);
            $result[$unit->getId()] = $unit;
        }
        return $result;
    }

    /**
     * Find {@link Unit} by identifier.
     * @return Unit or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query(
                'SELECT id, address, building_id, type_id, guest_limit '
                    . ' FROM units u WHERE id = ' . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $unit = new Unit();
        UnitMapper::map($unit, $row);
        return $unit;
    }

    /**
     * @return array of unit id, type, name
     */
    public function getAllUnitIdAndType() {
        $statement = $this->getDb()->prepare('SELECT id, address, type_id ' 
                        . ' FROM units ORDER BY address');
        $statement->execute();
        $result = $statement->fetchAll();
        return $result;
    }

    /**
     * Save {@link Unit}.
     * @param Unit $unit {@link Unit} to be saved
     * @return Unit saved {@link Unit} instance
     */
    public function save(Unit $unit) {
        if ($unit->getId() === null) {
            return $this->insert($unit);
        }
        return $this->update($unit);
    }

    /**
     * Delete {@link Unit} by identifier.
     * @param int $id {@link Unit} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM units 
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

    private function getFindSql(UnitSearchCriteria $search = null) {
        $sql = 'SELECT * FROM units ';
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function insert(Unit $unit) {
        $unit->setId( null );
        $sql = '
            INSERT INTO units (id, address, building_id, type_id, guest_limit)
                VALUES (:id, :address, :building_id, :type_id, :guest_limit)';
        return $this->execute($sql, $unit);
    }

    /**
     * @return Todo
     * @throws Exception
     */
    private function update(Unit $unit) {
        $sql = '
            UPDATE units SET
                address = :address,
                building_id = :building_id,
                type_id = :type_id,
                guest_limit = :guest_limit
            WHERE
                id = :id';
        return $this->execute($sql, $unit);
    }

    /**
     * @return Unit
     * @throws Exception
     */
    private function execute($sql, Unit $unit) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($unit));
        if (!$unit->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        if (!$statement->rowCount()) {
            throw new NotFoundException('Unit with ID "' . $unit->getId() . '" does not exist.');
        }
        return $unit;
    }

    private function getParams(Unit $unit) {
        $params = array(
            ':id' => $unit->getId(),
            ':address' => $unit->getAddress(),
            ':building_id' => $unit->getBuildingId(),
            ':type_id' => $unit->getTypeId(),
            ':guest_limit' => $unit->getGuestLimit()
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
