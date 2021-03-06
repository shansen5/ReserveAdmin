<?php

require_once( 'AbstractModel.php' );
/**
 * Model class representing one user.
 */
final class User extends AbstractModel {

    /** @var string */
    private $username;
    /** @var string */
    private $password;
    /** @var string */
    private $role;


    /**
     * Create new {@link User} with default properties set.
     */
    public function __construct() {
    }

    //~ Getters & setters

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return string <i>null</i> if not persistent
     */
    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

}
