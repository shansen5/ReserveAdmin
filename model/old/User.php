<?php

/**
 * Model class representing one user.
 */
final class User {

    /** @var int */
    private $id;
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
     * @return int <i>null</i> if not persistent
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = (int) $id;
    }

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
