<?php

/**
 * Abstract model class 
 */
abstract class AbstractModel {
   
    /** @var int */
    private $id;

    /**
     * Create new {@link Item} with default properties set.
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

}
