<?php

/**
 * Model class representing one person.
 */
final class Person {
   
    /** @var int */
    private $id;
    /** @var string */
    private $names = [];
    /** @var string */
    private $units = [];
    /** @var string */
    private $password;
    /** @var string */
    private $email;
    /** @var string */
    private $phone1;
    private $phone2;
    private $phone3;
    /** Store the current name and unit*/
    private $first_name;
    private $last_name;
    private $unit;
    private $birthdate;


    /**
     * Create new {@link Item} with default properties set.
     */
    public function __construct() {
    }

    //~ Getters & setters

    /**
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = (int) $id;
    }

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
     * @return DateTime <i>null</i> if not persistent
     */
    public function getBirthdate() {
        return $this->birthdate;
    }

    public function setBirthdate($birthdate) {
        $this->birthdate = $birthdate;
    }
    
    public function setCurrentNameAndUnit() {
        foreach( $this->names as $person_name ) {
            if ( $person_name->getEndDate() === null ) {
                $this->setFirstName( $person_name->getFirstName() );
                $this->setLastName( $person_name->getLastName() );
            }
        }
        foreach( $this->units as $unit_person ) {
            if ( $unit_person->getEndDate() === null ) {
                $this->setUnit( $unit_person->getUnitId() );
            }
        }
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
    public function getCurrentUnits() {
        $units = array();
        foreach( $this->units as $unit_person ) {
            if ( $unit_person->getEndDate() === null ) {
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
}
