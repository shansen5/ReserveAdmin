<?php

/**
 * Miscellaneous utility methods.
 */
final class Utils {

    const ADMIN = 1;
    const USER = 2;
    
    const NUMBER_OF_UNITS = 33;
    
    private function __construct() {
    }

    /**
     * Generate link.
     * @param string $page target page
     * @param array $params page parameters
     */
    public static function createLink($page, array $params = array()) {
        $params = array_merge(array('page' => $page), $params);
        // TODO add support for Apache's module rewrite
        return 'index.php?' .http_build_query($params);
    }

    /**
     * Format date.
     * @param DateTime $date date to be formatted
     * @return string formatted date
     */
    public static function formatDate(DateTime $date = null) {
        if ($date === null) {
            return '';
        }
        return $date->format('m-d-Y');
    }

    /**
     * Format date and time.
     * @param DateTime $date date to be formatted
     * @return string formatted date and time
     */
    public static function formatDateTime(DateTime $date = null) {
        if ($date === null) {
            return '';
        }
        return $date->format('m-d-Y H:i');
    }

    /**
     * Redirect to the given page.
     * @param type $page target page
     * @param array $params page parameters
     */
    public static function redirect($page, array $params = array()) {
        header('Location: ' . self::createLink($page, $params));
        die();
    }

    /**
     * Get value of the URL param.
     * @return string parameter value
     * @throws NotFoundException if the param is not found in the URL
     */
    public static function getUrlParam($name) {
        if (!array_key_exists($name, $_GET)) {
            throw new NotFoundException('URL parameter "' . $name . '" not found.');
        }
        return $_GET[$name];
    }

    /**
     * Get {@link Person} by the identifier 'id' found in the URL.
     * @return Person {@link Person} instance
     * @throws NotFoundException if the param or {@link Person} instance is not found
     */
    public static function getPersonByGetId( $id_name = 'id' ) {
        $id = null;
        try {
            $id = self::getUrlParam( $id_name );
        } catch (Exception $ex) {
            throw new NotFoundException('No Person identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Person identifier provided.');
        }
        $dao = new PersonDao();
        $person = $dao->findById($id);
        if ($person === null) {
            throw new NotFoundException('Unknown Person identifier provided.');
        }
        return $person;
    }

    /**
     * Get {@link PersonName} by the identifier 'id' found in the URL.
     * @return PersonName {@link PersonName} instance
     * @throws NotFoundException if the param or {@link PersonName} instance is not found
     */
    public static function getPersonNameByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam( 'id' );
        } catch (Exception $ex) {
            throw new NotFoundException('No PersonName identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid PersonName identifier provided.');
        }
        $dao = new PersonNameDao();
        $person_name = $dao->findById($id);
        if ($person_name === null) {
            throw new NotFoundException('Unknown PersonName identifier provided.');
        }
        return $person_name;
    }

    /**
     * Get {@link UnitPerson} by the identifier 'id' found in the URL.
     * @return UnitPerson {@link UnitPerson} instance
     * @throws NotFoundException if the param or {@link PersonName} instance is not found
     */
    public static function getUnitPersonByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam( 'id' );
        } catch (Exception $ex) {
            throw new NotFoundException('No UnitPerson identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid UnitPerson identifier provided.');
        }
        $dao = new UnitPersonDao();
        $unit_person = $dao->findById($id);
        if ($unit_person === null) {
            throw new NotFoundException('Unknown UnitPerson identifier provided.');
        }
        return $unit_person;
    }

    /**
     * Get {@link Unit} by the identifier 'id' found in the URL.
     * @return Unit {@link Unit} instance
     * @throws NotFoundException if the param or {@link Unit} instance is not found
     */
    public static function getUnitByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Unit identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Unit identifier provided.');
        }
        $dao = new UnitDao();
        $unit = $dao->findById($id);
        if ($unit === null) {
            throw new NotFoundException('Unknown Unit identifier provided.');
        }
        return $unit;
    }

    /**
     * Get {@link Unit} by the identifier 'id' found in the URL.
     * @return Unit {@link Unit} instance
     * @throws NotFoundException if the param or {@link Unit} instance is not found
     */
    public static function getUnitPersonsByUnitGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Unit identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Unit identifier provided.');
        }
        $dao = new UnitPersonDao();
        $search = new UnitPersonSearchCriteria();
        $search->setUnitId($id);
        $unit_persons = $dao->find($search);
        return $unit_persons;
    }

    public static function getUserRole() {
        if ( $_SESSION['oc_user_role'] == 'ADMIN' ) {
            return self::ADMIN;
        }
        return self::USER;
    }

    public static function getUserName() {
        return $_SESSION['oc_user'];        
    }
    
    public static function getUserIdByName() {
        $dao = new UserDao();
        $user = $dao->findByUsername($_SESSION['oc_user']);
        if ($user) {
            return $user->getId();
        }
        throw new NotFoundException('User not found: ' . $_SESSION['oc_user']);
    }
    
        /**
     * Get {@link User} by the identifier 'id' found in the URL.
     * @return User {@link User} instance
     * @throws NotFoundException if the param or {@link User} instance is not found
     */
    public static function getUserByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No User identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid User identifier provided.');
        }
        return Utils::getUserById( $id );
    }

    public static function getUserById( $id ) {
        $dao = new UserDao();
        $user = $dao->findById($id);
        if ($user === null) {
            throw new NotFoundException('Unknown User identifier provided.');
        }
        return $user;
    }
   
    /**
     * @return array of id, type, sub_type for all units
     */
    public static function getAllUnits() {
        $dao = new UnitDao();
        $result = $dao->find();
        return $result;
    }
    
    /**
     * @return integer
     */
    public static function getNumberOfUnits() {
        return self::NUMBER_OF_UNITS;
    }
    
    /**
     * @return array of strings
     */
    public static function getOccupantTypes() {
        $up_class = 'UnitPerson';
        return $up_class::getOccupantTypes();
    }
    
    /**
     * Capitalize the first letter of the given string
     * @param string $string string to be capitalized
     * @return string capitalized string
     */
    public static function capitalize($string) {
        return ucfirst(mb_strtolower($string));
    }

    /**
     * Escape the given string
     * @param string $string string to be escaped
     * @return string escaped string
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }

    public static function quoteString( $str ) {
        return '"' . $str . '"';
    }
}
