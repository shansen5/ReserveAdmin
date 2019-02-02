<?php

/**
 * Mapper for {@link UnitPerson} from array.
 * @see UnitPersonValidator
 */
final class UnitPersonMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link UnitPerson}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>unit_id</li>
     *   <li>person_id</li>
     *   <li>start_date</li>
     *   <li>end_date</li>
     *   <li>occupant_type</li>
     *   <li>first_name</li>
     *   <li>last_name</li>
     * </ul>
     * @param UnitPerson $unit_person
     * @param array $properties
     */
    public static function map(UnitPerson $unit_person, array $properties) {
        if (array_key_exists('id', $properties)) {
            $unit_person->setId($properties['id']);
        }
        if (array_key_exists('unit_id', $properties)) {
            $unit_person->setUnitId($properties['unit_id']);
        }
        if (array_key_exists('person_id', $properties)) {
            $unit_person->setPersonId($properties['person_id']);
        }
        if (array_key_exists('occupant_type', $properties)) {
            $unit_person->setOccupantType($properties['occupant_type']);
        }
        if (array_key_exists('start_date', $properties)) {
            $startDate = self::createDateTime($properties['start_date']);
            if ($startDate) {
                $unit_person->setStartDate($startDate);
            }
        }
        if (array_key_exists('end_date', $properties)) {
            $endDate = self::createDateTime($properties['end_date']);
            if ($endDate) {
                $unit_person->setEndDate($endDate);
            }
        }
        if (array_key_exists('last_name', $properties)) {
            $unit_person->setLastName($properties['last_name']);
        }
        if (array_key_exists('first_name', $properties)) {
            $unit_person->setFirstName($properties['first_name']);
        }
    }

    private static function createDateTime($input) {
        $d = DateTime::createFromFormat('Y-m-d', $input);
        return $d;
    }

}
