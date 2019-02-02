<?php


/**
 * Search criteria for {@link PersonDao}.
 * <p>
 * Can be easily extended without changing the {@link PersonDao} API.
 */
final class PersonSearchCriteria {
    private $search_date = null;
    private $person_id = null;
    private $first_name = null;
    private $last_name = null;
    private $occupant_type = null;
    private $unit_id = null;
    private $show_all = true;

    public function hasFilter() {
        if ( $this->search_date || $this->unit_id || $this->person_id
                || $this->occupant_type ) {
            return true;
        }
        return false;
    }

    public function setShowAll( $sense ) {
        $this->show_all = $sense;
    }
    
    public function getShowAll() {
        return $this->show_all;
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

    public function setUnitId( $id ) {
        $this->unit_id = $id;
    }
    
    public function getUnitId() {
        return $this->unit_id;
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

    public function setOccupantType( $type ) {
        $this->occupant_type = $type;
    }
    
    public function getOccupantType() {
        return $this->occupant_type;
    }
    

}
