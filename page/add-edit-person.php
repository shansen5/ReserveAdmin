<?php

$errors = array();
$person = null;
$person_name = null;
$unit_person = null;
$start_date_string = '';
$unit_persons = array();
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $person = Utils::getPersonByGetId();
    $count = count( $person->getNames() );
    if ( $person ) {
        $person_name = $person->getCurrentPersonName();
        $start_date_string = $person_name->getStartDate()->format('Y-m-d');
        $unit_persons = $person->getUnits();
    } 
} else {
    // set defaults
    $person = new Person();
    $person_name = new PersonName();
    $person->addPersonName( $person_name );
    $unit_person = new UnitPerson();
    $person->addUnitPerson( $unit_person );
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $person->getId() ) {
        Utils::redirect('person-detail', array('id' => $person->getId()));
    } else {
        Utils::redirect('person-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    $person_data = array(
        'first_name' => $_POST['person']['first_name'],
        'last_name' => $_POST['person']['last_name'],
        'password' => $_POST['person']['password'],
        'email' => $_POST['person']['email'],
        'phone1' => $_POST['person']['phone1'],
        'phone2' => $_POST['person']['phone2'],
        'phone3' => $_POST['person']['phone3']
        );
    $name_data = array(
        'first_name' => $_POST['person']['first_name'],
        'last_name' => $_POST['person']['last_name'],
        'start_date' => $_POST['person']['name_start_date']
    );
    $unit_data = array(
        'unit_id' => $_POST['person']['unit_id'],
        'start_date' => $_POST['person']['unit_start_date'],
        'occupant_type' => $_POST['person']['occupant_type']
    );
    // map
    PersonMapper::map($person, $person_data);
    PersonNameMapper::map($person_name, $name_data);
    // validate
    $errors1 = PersonValidator::validate($person);
    $errors2 = PersonNameValidator::validate($person_name);
    $errors = array_merge( $errors1, $errors2 );
    if ( $unit_person ) {
        UnitPersonMapper::map( $unit_person, $unit_data );
        $errors3 = UnitPersonValidator::validate( $unit_person );
        $errors = array_merge( $errors, $errors3 );
    }
    if (empty($errors)) {
        // save
        DBConnection::beginTransaction();
        try {
            $dao = new PersonDao( DBConnection::getDb() );
            $person = $dao->save($person);
            $person_name->setPersonId( $person->getId() );
            $pn_dao = new PersonNameDao( DBConnection::getDb() );
            $pn_dao->save( $person_name );
            if ( $unit_person ) {
                $unit_person->setPersonId( $person->getId() );
                $up_dao = new UnitPersonDao( DBConnection::getDb() );
                $up_dao->save( $unit_person );
            }
            DBConnection::commit();
            DBConnection::close();
            Flash::addFlash('Person saved successfully.');
            // redirect
            Utils::redirect('person-detail', array('id' => $person->getId()));
        } catch( Exception $e ) {
            $error = new RError( 'transaction', $e->getMessage() );
            $errors[] = $error;
            DBConnection::rollback();
            Flash::addFlash('Failed to save person.');
            // redirect
            Utils::redirect('person-list', array('id' => $person->getId()));
            DBConnection::close();
        }
        
    }
}
