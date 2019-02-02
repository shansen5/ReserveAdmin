<?php

/**
 * Mapper for {@link Unit} from array.
 * @see UnitValidator
 */
final class UnitMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Unit}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>address</li>
     *   <li>building_id</li>
     *   <li>type_id</li>
     *   <li>guest_limit</li>
     * </ul>
     * @param Unit $unit
     * @param array $properties
     */
    public static function map(Unit $unit, array $properties) {
        if (array_key_exists('id', $properties)) {
            $unit->setId($properties['id']);
        }
        if (array_key_exists('address', $properties)) {
            $unit->setAddress($properties['address']);
        }
        if (array_key_exists('building_id', $properties)) {
            $unit->setBuildingId($properties['building_id']);
        }
        if (array_key_exists('type_id', $properties)) {
            $unit->setTypeId($properties['type_id']);
        }
        if (array_key_exists('type', $properties)) {
            $unit->setType($properties['type']);
        }
        if (array_key_exists('guest_limit', $properties)) {
            $unit->setGuestLimit($properties['guest_limit']);
        }
    }

}
