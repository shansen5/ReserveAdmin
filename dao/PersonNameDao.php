<?php

/**
 * DAO for {@link PersonName}.
 * <p>
 * 
 */
final class PersonNameDao extends AbstractDao {

    /**
     * Find all {@link Unit}s by search criteria.
     * @return array array of {@link Unit}s
     */
    public function find( AbstractSearchCriteria $search = null ) {
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
    public function save( AbstractModel $person_name ) {
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

    private function handleWhere( $sql, $where_started ) {
        if ( $where_started ) {
            $sql .= ' AND ';
        } else {
            $sql .= ' WHERE ';
        } 
        return $sql;
    }

    protected function getFindSql( AbstractSearchCriteria $search = null ) {
        $sql = 'SELECT id, person_id, first_name, last_name, start_date, end_date 
                FROM person_names ';
        if ( $search && $search->hasFilter() ) {
            $where_started = false;
            if ( $search->getSearchDate() ) {
                $search_date = "'" . $search->getSearchDate()->format('Y-m-d') . "'";
                if ($search_date > $GLOBALS['BEGIN_DATE']) {
                    $sql = $this->handleWhere( $sql, $where_started );
                    $where_started = true;
                    $sql .= ' start_date <= ' . $search_date;
                    $sql .= ' AND ( end_date is null OR end_date >= ' . $search_date . ')';
                }
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
    protected function insert( AbstractModel $person_name ) {
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
    protected function update( AbstractModel $person_name ) {
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

    protected function getParams( AbstractModel $person_name , $update = false ) {
        $params = array(
            ':person_id' => $person_name->getPersonId(),
            ':first_name' => $person_name->getFirstName(),
            ':last_name' => $person_name->getLastName(),
            ':start_date' => $person_name->getStartDate()->format( 'Y-m-d' ),
            ':end_date' => $person_name->getEndDate() === null ? null : $person_name->getEndDate()->format( 'Y-m-d' )
        );
        if ( $update ) {
            $params[ ':id'] = $person_name->getId();
        }
        return $params;
    }

}
