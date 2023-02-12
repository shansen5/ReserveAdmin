<?php

/**
 * Miscellaneous utility methods.
 */
final class Utils {

    const USER = 1;
    const MEALS_ADMIN = 2;
    const ADMIN = 3;
    
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
     * Create DateTime from string of one of two formats 
     * 'm-d-Y' or 'Y-m-d'
     * @param string in one of the two formats
     * @return DateTime
     */
    public static function createDateTimeFromString( $str ) {
        $dt = DateTime::createFromFormat( 'm-d-Y', $str );
        if ( ! $dt ) {
            $dt = DateTime::createFromFormat( 'Y-m-d', $str );
        }
        return $dt;
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
     * Get {@link unit_id} of the Person idenfied
     * @return unit_id {@link unit_id}
     * @throws NotFoundException if the param or {@link unit_id} instance is not found
     */
    public static function getUnitIdByPersonId( $person_id ) {
        $dao = new PersonDao();
        $person = $dao->findById( $person_id );
        if ( $person === null ) {
            throw new NotFoundException( 'Unknown person id' );
        }
        $result = array( 'unit_id' => $person->getUnit(),
                         'sub_unit' => $person->getSubUnit() );
        return $result;
    }

    /**
     * @return array of Persons with the same current unit_id
     * @throws NotFoundException if the param or {@link Unit} instance is not found
     */
    public static function getUnitMembersByUnitId( $id = null ) {
        $dao = new UnitPersonDao();
        $search = new UnitPersonSearchCriteria();
        $search->setSearchDate( new DateTime( 'now', 
                                new DateTimeZone( 'America/Los_Angeles' )) );
        $search->setOrderByName();
        if ( $id ) {
            $search->setUnitId( $id['unit_id'] );
            $search->setSubUnit( $id['sub_unit'] );
        }
        $persons = $dao->find( $search );
        return $persons;
    }

    /**
     * @return array of MemberAttendees
     * @throws NotFoundException if the param or {@link Unit} instance is not found
     */
    public static function getMemberAttendeesByMealId( $id ) {
        $dao = new MemberAttendeeDao();
        $search = new MemberAttendeeSearchCriteria();
        $search->setMealId( $id );
        $attendees = $dao->find( $search );
        return $attendees;
    }

    /**
     * @return array of Guests
     * @throws NotFoundException if the param or {@link Unit} instance is not found
     */
    public static function getGuestsByMealId( $id ) {
        $dao = new GuestDao();
        $search = new GuestSearchCriteria();
        $search->setMealId( $id );
        $guests = $dao->find( $search );
        return $guests;
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
     * Get {@link Meal} by the identifier 'id' found in the URL.
     * @return Meal {@link Meal} instance
     * @throws NotFoundException if the param or {@link Meal} instance is not found
     */
    public static function getMealByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('meal_id');
        } catch (Exception $ex) {
            throw new NotFoundException('No Meal identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid Meal identifier provided.');
        }
        $dao = new MealDao();
        $meal = $dao->findById($id);
        return $meal;
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
        if ( $_SESSION['oc_user_role'] == 'MEALS_ADMIN' ) {
            return self::MEALS_ADMIN;
        }
        return self::USER;
    }

    public static function getUserName() {
        return $_SESSION['oc_user'];        
    }
    
    public static function getCurrentAdultsAndIds() {
        $search = new PersonSearchCriteria();
        $now = new DateTime( 'now', new DateTimeZone( 'America/Los_Angeles' ));
        $search->setSearchDate( $now );
        $search->setExcludeOccupantType( 'child' );
        $personDao = new PersonDao();
        return $personDao->find( $search );
    }

    /**
     * @return string of team lead names
     */
    public static function getMealTeamLeads( $id ) {
        $dao = new MealTeamDao();
        $team = $dao->findById( $id );
        if ( $team ) {
            $leads = $team->getLead1Name();
            $lead2 = $team->getLead2Name();
            if ( $lead2 ) {
                $leads .= ', ' . $lead2;
            }
            return $leads;
        }
        return "";
    }

    /**
     * @return array of id, lead1, lead2 for all meal teams
     */
    public static function getMealTeams() {
        $dao = new MealTeamDao();
        $result = $dao->find();
        return $result;
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
        if (! $string) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES);
    }

    public static function quoteString( $str ) {
        return '"' . $str . '"';
    }
}
