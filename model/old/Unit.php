<?php

/**
 * Model class representing one unit.
 */
final class Unit {

    private $unit_types = array( 1 => 'A - 2 bedroom upper unit',
        2 => 'A - 2 bedroom lower unit',
        3 => 'C - 2 bedroom, 2 floor unit',
        4 => 'D - 3 bedroom unit, living right',
        5 => 'D - 3 bedroom unit, living left',
        6 => 'E - 4 bedroom unit, living right',
        7 => 'E - 4 bedroom unit, living left',
        8 => 'Other' );
    
    /** @var int */
    private $id;
    /** @var string */
    private $address;
    /** @var string */
    private $building_id;
    /** @var number */
    private $type_id;
    /** @var number */
    private $guest_limit;


    /**
     * Create new {@link Unit} with default properties set.
     */
    public function __construct() {
    }

    public static function allUnitTypes() {
        $unit = new Unit();
        return $unit->unit_types;
    }

    //~ Getters & setters

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = (int) $id;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @return int
     */
    public function getBuildingId() {
        return $this->building_id;
    }

    public function setBuildingId($building_id) {
        $this->building_id = $building_id;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->unit_types[ $this->type_id ];
    }
    
    public function setType( $type ) {
        foreach( $this->unit_types as $t => $t_value ) {
            if ( $type === $t_value ) {
                $this->type_id = $t;
                break;
            }
        }
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->type_id;
    }

    public function setTypeId($type_id) {
        $this->type_id = $type_id;
    }

    /**
     * @return int
     */
    public function getGuestLimit() {
        return $this->guest_limit;
    }

    public function setGuestLimit($guest_limit) {
        $this->guest_limit = $guest_limit;
    }

}
