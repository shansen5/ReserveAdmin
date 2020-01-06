<?php


/**
 * Search criteria for {@link PersonNameDao}.
 * <p>
 * Can be easily extended without changing the {@link PersonNameDao} API.
 */
final class PersonNameSearchCriteria {
    private $search_date = null;
    private $person_id = null;
    private $first_name = null;
    private $last_name = null;

    public function hasFilter() {
        if ( $this->search_date || $this->person_id || $this->first_name ||
                $this->last_name ) {
            return true;
        }
        return false;
    }

    public function setPersonSearch( $search ) {
        if ( $search === NULL ) {
            return;
        }
        $this->setSearchDate( $search->getSearchDate() );
        $this->setPersonId( $search->getPersonId() );
        $this->setFirstName( $search->getFirstName() );
        $this->setLastName( $search->getLastName() );
    }
    
    public function setSearchDate( $s_date ) {
        $this->search_date = $s_date;
    }
    
    public function getSearchDate() {
        return $this->search_date;
    }
    
    public function setPersonId( $id ) {
        $this->person_id = $id;
    }
    
    public function getPersonId() {
        return $this->person_id;
    }

    public function setFirstName( $name ) {
        $this->first_name = $name;
    }
    
    public function getFirstName() {
        return $this->first_name;
    }

    public function setLastName( $name ) {
        $this->last_name = $name;
    }
    
    public function getLastName() {
        return $this->last_name;
    }
}
