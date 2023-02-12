<?php

/**
 * Model class representing one person.
 */

final class Person extends AbstractModel {
 

    /** @var string */
    private $names = [];
    /** @var string */
    private $units = [];
    /** @var string */
    private $password;
    /** @var string */
    private $email;
    /** @var Date */
    private $birthdate;
    /** @var string */
    private $phone1;
    private $phone2;
    private $phone3;
    private $isMealAdmin;
    /** Store the current name and unit*/
    private $first_name;
    private $last_name;
    private $unit;
    private $sub_unit;


    //~ Getters & setters

    /**
     * @return array
     */
    public function getNames() {
        return $this->names;
    }

    public function setNames($names) {
        $this->names = $names;
    }

    /**
     * @return array
     */
    public function getUnits() {
        return $this->units;
    }

    public function setUnits($units) {
        $this->units = $units;
    }

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return Date
     */
    public function getBirthdate() {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate) {
        $this->birthdate = $birthdate;
    }

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }
    /**
     * @return string <i>null</i> if not persistent
     */
    public function getPhone1() {
        return $this->phone1;
    }

    public function setPhone1($phone1) {
        $this->phone1 = $phone1;
    }
    /**
     * @return string <i>null</i> if not persistent
     */
    public function getPhone2() {
        return $this->phone2;
    }

    public function setPhone2($phone2) {
        $this->phone2 = $phone2;
    }
    /**
     * @return string <i>null</i> if not persistent
     */
    public function getPhone3() {
        return $this->phone3;
    }

    public function setPhone3($phone3) {
        $this->phone3 = $phone3;
    }
    
    /**
     * @return boolean
     */
    public function getIsMealAdmin() {
        return $this->isMealAdmin;
    }

    public function setIsMealAdmin( $sense ) {
        $this->isMealAdmin = $sense;
    }
    
    public function setCurrentNameAndUnit( DateTime $search_date ) {
        foreach( $this->names as $person_name ) {
            if ( $person_name->getEndDate() === null || $person_name->getEndDate() >= $search_date ) {
                if ( $search_date <= $GLOBALS['BEGIN_DATE'] ||
                        $person_name->getStartDate() <= $search_date ) {
                    $this->setFirstName( $person_name->getFirstName() );
                    $this->setLastName( $person_name->getLastName() );
                }
            }
        }
        $current_units = $this->getCurrentUnits( $search_date );
        if ( count( $current_units ) === 1 ) {
            $this->setUnit( $current_units[0]->getUnitId() );
            $this->setSubUnit( $current_units[0]->getSubUnit() );
            return;
        }
        foreach( $current_units as $unit_person ) {
            if ( $unit_person->getOccupantType() === 'owner' ) {
                $this->setUnit( $unit_person->getUnitId() );
                $this->setSubUnit( $unit_person->getSubUnit() );
                return;
            }
        }
        foreach( $current_units as $unit_person ) {
            if ( $unit_person->getOccupantType() === 'renter' ) {
                $this->setUnit( $unit_person->getUnitId() );
                $this->setSubUnit( $unit_person->getSubUnit() );
                return;
            }
        }
        foreach( $current_units as $unit_person ) {
            if ( $unit_person->getOccupantType() === 'owner-non-occupant' ) {
                $this->setUnit( $unit_person->getUnitId() );
                $this->setSubUnit( $unit_person->getSubUnit() );
                return;
            }
        }
        return null;
    }
    
    public function addPersonName( $pn ) {
        $this->names[] = $pn;
    }
    
    public function addUnitPerson( $up ) {
        $this->units[] = $up;
    }
    
    public function getCurrentPersonName() {
        foreach( $this->names as $person_name ) {
            if ( $person_name->getEndDate() === null ) {
                return $person_name;
            }
        }
        return null;
    }
    
    /**
     * @return array UnitPerson
     * The person may be own multiple units, or may own one and rent another, etc.
     */
    public function getCurrentUnits( DateTime $search_date ) {
        $units = array();
        foreach( $this->units as $unit_person ) {
            if ($search_date == $GLOBALS['BEGIN_DATE']) {
                $units[] = $unit_person;
                return $units; 
            }
            if (( $unit_person->getEndDate() === null || $unit_person->getEndDate() >= $search_date ) &&
                    $unit_person->getStartDate() <= $search_date ) {
                $units[] = $unit_person;
            }
        }
        return $units;
    }
    
    /**
     * @return string <i>null</i> if not persistent
     */
    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($name) {
        $this->first_name = $name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($name) {
        $this->last_name = $name;
    }
    /**
     * @return string <i>null</i> if not persistent
     */
    public function getUnit() {
        return $this->unit;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }
    public function getSubUnit() {
        return $this->sub_unit;
    }

    public function setSubUnit($sub_unit) {
        $this->sub_unit = $sub_unit;
    }
}
