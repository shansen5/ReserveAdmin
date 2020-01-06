<?php


/**
 * Search criteria for {@link UnitPersonDao}.
 * <p>
 * Can be easily extended without changing the {@link UnitPersonSearchCriteria} API.
 */
final class UnitPersonSearchCriteria {

    private $search_date = null;
    private $unit_id = null;
    private $person_id = null;
    private $occupant_type = null;
    
    public function hasFilter() {
        if ( $this->search_date || $this->unit_id || $this->person_id
                || $this->type ) {
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
        $this->setOccupantType( $search->getOccupantType() );
    }    

    public function setSearchDate( $s_date ) {
        $this->search_date = $s_date;
    }
    
    public function getSearchDate() {
        return $this->search_date;
    }
    
    public function setUnitId( $id ) {
        $this->unit_id = $id;
    }
    
    public function getUnitId() {
        return $this->unit_id;
    }
    
    public function setPersonId( $id ) {
        $this->person_id = $id;
    }
    
    public function getPersonId() {
        return $this->person_id;
    }

    public function setOccupantType( $type ) {
        $this->occupant_type = $type;
    }
    
    public function getOccupantType() {
        return $this->occupant_type;
    }

}
