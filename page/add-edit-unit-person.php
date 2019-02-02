<?php

$errors = array();
$person = null;
$unit_person = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $person = Utils::getPersonByGetId( 'person_id' );
    $unit_person = Utils::getUnitPersonByGetId();
    if ( ! $unit_person ) {
        $errors = new RError( 'add-edit-unit-person', 'UnitPerson not found by id.');
        throw new Exception( 'UnitPerson not found by id.' );
    }
} else {
    // set defaults
    $person = Utils::getPersonByGetId( 'person_id' );
    if ( ! $person ) {
        $errors = new RError( 'add-edit-unit-person', 'UnitPerson not found by id.');
        throw new Exception( 'UnitPerson not found by id.' );        
    }
    $unit_person = new UnitPerson();
    $unit_person->setPersonId( $person->getId() );
    $person->addUnitPerson( $unit_person );
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $person->getId() ) {
        Utils::redirect('add-edit-person', array('id' => $person->getId()));
    } else {
        Utils::redirect('person-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    $unit_data = array(
        'unit_id' => $_POST['unit_person']['unit'],
        'start_date' => $_POST['unit_person']['start_date'],
        'end_date' => $_POST['unit_person']['end_date'],
        'occupant_type' => $_POST['unit_person']['occupant_type']
    );
    // map
    UnitPersonMapper::map( $unit_person, $unit_data );
    // validate
    $errors = UnitPersonValidator::validate( $unit_person );
    if (empty($errors)) {
        // save
        try {
            $up_dao = new UnitPersonDao( );
            $up_dao->save( $unit_person );
            Flash::addFlash('Unit person saved successfully.');
            // redirect
            if ( $person ) {
                Utils::redirect('person-detail', array('id' => $person->getId()));                
            } else {
                Utils::redirect('person-list', array()); 
            }
        } catch( Exception $e ) {
            $error = new RError( 'transaction', $e->getMessage() );
            $errors[] = $error;
            Flash::addFlash('Failed to save unit person.');
            // redirect
            Utils::redirect('person-list', array('id' => $person->getId()));
        }
        
    }
}
