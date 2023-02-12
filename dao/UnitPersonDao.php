<?php

/**
 * DAO for {@link Unit}.
 * <p>
 * 
 */
final class UnitPersonDao extends AbstractDao {

    /**
     * Find all {@link Unit}s by search criteria.
     * @return array array of {@link Unit}s
     */
    public function find( AbstractSearchCriteria $search = null ) {
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
                'SELECT id, unit_id, sub_unit, person_id, start_date, end_date, type as occupant_type FROM person_units WHERE id = ' . (int) $id)->fetch();
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
    public function save( AbstractModel $unit_person ) {
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
    public function saveAll( array $unit_persons ) {
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

    private function handleWhere( $sql, $where_started ) {
        if ( $where_started ) {
            $sql .= ' AND ';
        } else {
            $sql .= ' WHERE ';
        } 
        return $sql;
    }

    protected function getFindSql( AbstractSearchCriteria $search = null ) {
        $sql = 'SELECT pu.id as id, pu.unit_id as unit_id, pu.sub_unit as sub_unit, pu.person_id as person_id, '
                . 'pu.start_date as start_date, pu.end_date as end_date,'
                . 'pu.type as occupant_type, pn.first_name as first_name, pn.last_name as last_name '
                . ' FROM person_units pu JOIN person_names pn WHERE pn.person_id = pu.person_id ';
        if ( $search && $search->hasFilter() ) {
            $where_started = true;
            if ( $search->getSearchDate() > $GLOBALS['BEGIN_DATE'] ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $search_date = "'" . $search->getSearchDate()->format('Y-m-d') . "'";
                $sql .= 'pu.start_date <= ' . $search_date;
                $sql .= ' AND ( pu.end_date is null OR pu.end_date >= ' . $search_date . ')';
                $sql .= ' AND pn.start_date <= ' . $search_date;
                $sql .= ' AND ( pn.end_date is null OR pn.end_date >= ' . $search_date . ')';
            } else {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pn.end_date is null ';
            }
            if ( $search->getUnitId() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.unit_id = ' . $search->getUnitId();
            }
            if ( $search->getSubUnit() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.sub_unit = ' . $search->getSubUnit();
            }
            if ( $search->getPersonId() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.person_id = ' . $search->getPersonId();
            }
            if ( $search->getOccupantType() ) {
                $sql = $this->handleWhere( $sql, $where_started );
                $sql .= 'pu.type = ' . "'" . $search->getOccupantType() . "'";
            }
            if ( $search->getOrderByName() ) {
                $sql .= ' ORDER BY pn.first_name';
            } else {
                $sql .= ' ORDER BY pu.start_date';
            }
        } else {
            $sql .= ' ORDER BY pu.start_date';
        }
        return $sql;
    }

    /**
     * @return Todo
     * @throws Exception
     */
    protected function insert( AbstractModel $up ) {
        $up->setId( null );
        $sql = 'INSERT INTO person_units (unit_id, sub_unit, person_id, start_date, end_date, type)
                VALUES (:unit_id, :sub_unit, :person_id, :start_date, :end_date, :type)';
        $unit_person = $this->execute($sql, $up);
        $unit_person->setId( $this->getDb()->lastInsertId() ); 
        return $unit_person;
    }

    /**
     * @return UnitPerson
     * @throws Exception
     */
    protected function update( AbstractModel $unit_person ) {
        $sql = '
            UPDATE person_units SET
                unit_id = :unit_id,
                sub_unit = :sub_unit,
                person_id = :person_id,
                start_date = :start_date,
                end_date = :end_date,
                type = :type
            WHERE
                id = :id';
        return $this->execute($sql, $unit_person, true);
    }

    protected function getParams( AbstractModel $unit_person, $update = false ) {
        $params = array(
            ':unit_id' => $unit_person->getUnitId(),
            ':sub_unit' => $unit_person->getSubUnit(),
            ':person_id' => $unit_person->getPersonId(),
            ':start_date' => $unit_person->getStartDate()->format( 'Y-m-d' ),
            ':end_date' => $unit_person->getEndDate() ? $unit_person->getEndDate()->format( 'Y-m-d' ) : null,
            ':type' => $unit_person->getOccupantType()
        );
        if ( $update ) {
            $params[':id'] = $unit_person->getId();
        }
        return $params;
    }

}
