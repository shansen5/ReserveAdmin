<?php

/**
 * Validator for {@link UnitPerson}.
 * @see UnitPersonMapper
 */
final class UnitPersonValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link UnitPerson} instance.
     * @param UnitPerson $unit_person {@link UnitPerson} instance to be validated
     * @return array array of {@link RError} s
     */
    public static function validate(UnitPerson $unit_person) {
        $errors = array();
        if (!$unit_person->getStartDate()) {
            $errors[] = new RError('start_date', 'Start date cannot be empty.');
        }
        $up_class = 'UnitPerson';
        if (!$unit_person->getOccupantType()) {
            $errors[] = new RError('occupant_type', 'Occupant type cannot be empty.');
        } else {
            if( !in_array( $unit_person->getOccupantType(), $up_class::getOccupantTypes() )) {
                $errors[] = new RError('occupant_type', 'Occupant type not a valid value.');
            }
        }
        return $errors;
    }

}
