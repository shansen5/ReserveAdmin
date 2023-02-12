<?php

/**
 * DAO for {@link Person}.
 * <p>
 * 
 */
final class PersonDao extends AbstractDao {

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
     * Find all {@link Person}s by search criteria.
     * @return array array of {@link Person}s
     */
    public function find( AbstractSearchCriteria $search = null ) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $person = new Person();
            PersonMapper::map($person, $row);
            $person_names = $this->getNames( $person->getId(), $search );
            $unit_persons = $this->getUnits( $person->getId(), $search );
            $person->setNames( $person_names );
            $person->setUnits( $unit_persons );
            $person->setIsMealAdmin( $person->getId() );
            $person->setCurrentNameAndUnit( $search->getSearchDate() );
            $result[$person->getId()] = $person;
        }
        return $result;
    }

    /**
     * Find {@link Person} by identifier.
     * @return Person or <i>null</i> if not found
     */
    public function findById($id) {
        $sql = 'SELECT id, password, email, phone_land as phone1, '
                    . 'phone_mobile as phone2, phone_work as phone3, '
                    . ' birthdate as birthdate'
                    . ' FROM people WHERE id = ' 
                    . (int) $id;
        $row = $this->query( $sql )->fetch();
        if (!$row) {
            return null;
        }
        $person = new Person();
        PersonMapper::map($person, $row);
        $person_names = $this->getNames( $id );
        $unit_persons = $this->getUnits( $id );
        $person->setNames( $person_names );
        $person->setUnits( $unit_persons );
        $person->setCurrentNameAndUnit(new DateTime);
        $person->setIsMealAdmin( $this->isMealAdmin( $id ));

        return $person;
    }

    private function isMealAdmin( $person_id ) {
        $row = $this->query(
            'SELECT person_id from meal_admin WHERE person_id = ' . (int) $person_id 
        ) -> fetch();
        if ( $row ) {
            return( true );
        }
        return false;
    }

    /**
     * Save {@link Person}.
     * @param Person $person {@link Person} to be saved
     * @return Person saved {@link Person} instance
     */
    public function save(AbstractModel $person) {
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

    protected function getFindSql(AbstractSearchCriteria $search = null) {
        $sql = 'SELECT p.id as id, p.password as password, p.email as email, '
                . 'p.phone_land as phone1, p.phone_mobile as phone2, '
                . 'p.phone_work as phone3, pn.first_name as first_name, '
                . 'pn.last_name as last_name, pu.unit_id as unit_id, '
                . 'pu.type as occupant_type, p.birthdate as birthdate '
                . 'FROM people p JOIN person_names pn JOIN person_units pu '
                . 'WHERE p.id = pn.person_id AND p.id = pu.person_id ';
        if ( $search && $search->hasFilter() ) {
            if ( $search->getSearchDate() > $GLOBALS['BEGIN_DATE'] ) {
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
            if ( $search->getPersonIdArray() ) {
                $sql .= ' AND p.id in ( ';
                $i = 1;
                $num = count( $search->getPersonIdArray() );
                foreach ( $search->getPersonIdArray() as $person ) {
                    $sql .= $person;
                    if ( $i < $num ) {
                        $sql .= ', ';
                        $i += 1;
                    }
                }
                $sql .= ') ';
            }
            if ( $search->getOccupantType() ) {
                $sql .= ' AND pu.type = ' . "'" . $search->getOccupantType() . "'";
            }
            if ( $search->getExcludeOccupantType() ) {
                $sql .= ' AND pu.type <> ' . "'" . $search->getExcludeOccupantType() . "'";
            }
            if ( $search->getOrderByUnit() ) {
                $sql .= ' ORDER BY unit_id, first_name';
            } else {
                $sql .= ' ORDER BY first_name';
            }
        }
        return $sql;
    }

    /**
     * @return Person
     * @throws Exception
     */
    protected function insert( AbstractModel $person ) {
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
    protected function update(AbstractModel $person) {
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

    protected function getParams( AbstractModel $person, $update = false ) {
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

}
