<?php

/**
 * DAO for {@link Unit}.
 * <p>
 * 
 */
final class UnitDao extends AbstractDao {

    /**
     * Find all {@link Unit}s by search criteria.
     * @return array array of {@link Unit}s
     */
    public function find( AbstractSearchCriteria $search = null ) {
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
    public function save( AbstractModel $unit) {
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

    protected function getFindSql( AbstractSearchCriteria $search = null ) {
        $sql = 'SELECT * FROM units ';
        return $sql;
    }

    /**
     * @return Unit
     * @throws Exception
     */
    protected function insert( AbstractModel $unit ) {
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
    protected function update( AbstractModel $unit ) {
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

    protected function getParams( AbstractModel $unit, $update = false ) {
        $params = array(
            ':id' => $unit->getId(),
            ':address' => $unit->getAddress(),
            ':building_id' => $unit->getBuildingId(),
            ':type_id' => $unit->getTypeId(),
            ':guest_limit' => $unit->getGuestLimit()
        );
        return $params;
    }

}
