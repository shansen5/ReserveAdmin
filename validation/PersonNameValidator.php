<?php

/**
 * Validator for {@link PersonName}.
 * @see PersonNameMapper
 */
final class PersonNameValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link PersonName} instance.
     * @param PersonName $person_name {@link PersonName} instance to be validated
     * @return array array of {@link RError} s
     */
    public static function validate(PersonName $person_name) {
        $errors = array();
        if (!$person_name->getStartDate()) {
            $errors[] = new RError('start_date', 'Start date cannot be empty.');
        }
        return $errors;
    }

}
