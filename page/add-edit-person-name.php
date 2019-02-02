<?php

$errors = array();
$person = null;
$person_name = null;
$unit_person = null;
$unit_persons = array();
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $person = Utils::getPersonByGetId( 'person_id' );
    $person_name = Utils::getPersonNameByGetId();
    if ( ! $person_name ) {
        $errors = new RError( 'add-edit-person-name', 'PersonName not found by id.');
        throw new Exception( 'PersonName not found by id.' );
    }
} else {
    // set defaults
    $person = Utils::getPersonByGetId( 'person_id' );
    if ( ! $person ) {
        $errors = new RError( 'add-edit-person-name', 'PersonName not found by id.');
        throw new Exception( 'PersonName not found by id.' );        
    }
    $person_name = new PersonName();
    $person_name->setPersonId( $person->getId() );
    $person->addPersonName( $person_name );
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $person->getId() ) {
        Utils::redirect('add-edit-person', array('id' => $person->getId()));
    } else {
        Utils::redirect('person-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    $name_data = array(
        'first_name' => $_POST['person_name']['first_name'],
        'last_name' => $_POST['person_name']['last_name'],
        'start_date' => $_POST['person_name']['start_date'],
        'end_date' => $_POST['person_name']['end_date']
    );
    // map
    PersonNameMapper::map($person_name, $name_data);
    // validate
    $errors = PersonNameValidator::validate($person_name);
    if (empty($errors)) {
        // save
        try {
            $pn_dao = new PersonNameDao( );
            $pn_dao->save( $person_name );
            Flash::addFlash('Person name saved successfully.');
            // redirect
            if ( $person ) {
                Utils::redirect('person-detail', array('id' => $person->getId()));                
            } else {
                Utils::redirect('person-list', array()); 
            }
        } catch( Exception $e ) {
            $error = new RError( 'transaction', $e->getMessage() );
            $errors[] = $error;
            Flash::addFlash('Failed to save person name.');
            // redirect
            Utils::redirect('person-list', array('id' => $person->getId()));
        }
        
    }
}
