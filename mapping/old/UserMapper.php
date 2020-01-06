<?php

/**
 * Mapper for {@link User} from array.
 * @see UserValidator
 */
final class UserMapper {

    private function __construct() {
    }

    /**
     * Maps array to the given {@link User}.
     * <p>
     * Expected properties are:
     * <ul>
     *   <li>id</li>
     *   <li>username</li>
     *   <li>password</li>
     *   <li>role</li>
     * </ul>
     * @param User $user
     * @param array $properties
     */
    public static function map(User $user, array $properties) {
        if (array_key_exists('id', $properties)) {
            $user->setId($properties['id']);
        }
        if (array_key_exists('username', $properties)) {
            $user->setUsername($properties['username']);
        }
        if (array_key_exists('password', $properties)) {
            $user->setPassword( $properties['password'] );
        }
        if (array_key_exists('role', $properties)) {
            $user->setRole($properties['role']);
        }
    }

}
