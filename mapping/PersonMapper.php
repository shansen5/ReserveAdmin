<?php

/**
 * Mapper for {@link Person} from array.
 * @see PersonValidator
 */
final class PersonMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link Person}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>password</li>
     *   <li>email</li>
     *   <li>phone_land</li>
     *   <li>phone_mobile</li>
     *   <li>phone_work</li>
     * </ul>
     * @param Person $person
     * @param array $properties
     */
    public static function map(Person $person, array $properties) {
        if (array_key_exists('id', $properties)) {
            $person->setId($properties['id']);
        }
        if (array_key_exists('password', $properties)) {
            $person->setPassword($properties['password']);
        }
        if (array_key_exists('email', $properties)) {
            $person->setEmail($properties['email']);
        }
        if (array_key_exists('phone1', $properties)) {
            $person->setPhone1($properties['phone1']);
        }
        if (array_key_exists('phone2', $properties)) {
            $person->setPhone2($properties['phone2']);
        }
        if (array_key_exists('phone3', $properties)) {
            $person->setPhone3($properties['phone3']);
        }
    }

}
