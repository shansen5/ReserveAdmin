<?php

/**
 * Validator for {@link Unit}.
 * @see UnitMapper
 */
final class UnitValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Unit} instance.
     * @param Unit $unit {@link Unit} instance to be validated
     * @return array array of {@link RError} s
     */
    public static function validate(Unit $unit) {
        $errors = array();
        if (!$unit->getAddress()) {
            $errors[] = new RError('address', 'Address cannot be empty.' );
        } else {
            $address = $unit->getAddress();
            if ( !is_numeric( $address ) || $address < 2600 || $address > 2700 ) {
                $errors[] = new RError( 'address', 'out of range' );
            }
        }
        if (!$unit->getBuildingId()) {
            $errors[] = new RError('building', 'Building cannot be empty.' );
        } else {
            $building = $unit->getBuildingId();
            if ( !is_numeric( $building ) || $building < 1 || $building > 10 ) {
                $errors[] = new RError( 'building', 'out of range' );
            }
        }
        if (!$unit->getTypeId()) {
            $errors[] = new RError('type', 'Type cannot be empty.' );
        } else {
            $type_id = $unit->getTypeId();
            if ( !is_numeric( $type_id ) || $type_id < 1 || $type_id > 10 ) {
                $errors[] = new RError( 'type', 'not recognized' );
            }
        }
        if (!$unit->getGuestLimit()) {
            $errors[] = new RError('guest_limit', 'Guest limit cannot be empty.' );
        } else {
            $guest_limit = $unit->getGuestLimit();
            if ( !is_numeric( $guest_limit ) || $guest_limit < 1 || $guest_limit > 100 ) {
                $errors[] = new RError( 'guest_limit', 'out of range' );
            }
        }
        return $errors;
    }

}
