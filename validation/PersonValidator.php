<?php

/**
 * Validator for {@link Person}.
 * @see PersonMapper
 */
final class PersonValidator {

    private function __construct() {
    }

    /**
     * Validate the given {@link Todo} instance.
     * @param Todo $todo {@link Todo} instance to be validated
     * @return array array of {@link RError} s
     */
    public static function validate(Person $person) {
        $errors = array();
        if (!trim($person->getPassword())) {
            $errors[] = new RError('password', 'Password cannot be empty.');
        }
        return $errors;
    }

}
