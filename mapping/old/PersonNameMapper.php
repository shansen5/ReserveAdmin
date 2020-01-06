<?php

/**
 * Mapper for {@link PersonName} from array.
 * @see PersonNameValidator
 */
final class PersonNameMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link PersonName}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>person_id</li>
     *   <li>first_name</li>
     *   <li>last_name</li>
     *   <li>start_date</li>
     *   <li>end_date</li>
     * </ul>
     * @param PersonName $person_name
     * @param array $properties
     */
    public static function map(PersonName $person_name, array $properties) {
        if (array_key_exists('id', $properties)) {
            $person_name->setId($properties['id']);
        }
        if (array_key_exists('person_id', $properties)) {
            $person_name->setPersonId($properties['person_id']);
        }
        if (array_key_exists('first_name', $properties)) {
            $person_name->setFirstName($properties['first_name']);
        }
        if (array_key_exists('last_name', $properties)) {
            $person_name->setLastName($properties['last_name']);
        }
        if (array_key_exists('start_date', $properties)) {
            $startDate = self::createDateTime($properties['start_date']);
            if ($startDate) {
                $person_name->setStartDate($startDate);
            }
        }
        if (array_key_exists('end_date', $properties)) {
            $endDate = self::createDateTime($properties['end_date']);
            if ($endDate) {
                $person_name->setEndDate($endDate);
            }
        }
    }

    private static function createDateTime($input) {
        $d = DateTime::createFromFormat('Y-m-d', $input);
        return $d;
    }

}
