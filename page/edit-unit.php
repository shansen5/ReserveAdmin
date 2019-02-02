<?php

$errors = array();
$unit = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $unit = Utils::getUnitByGetId();
    $unit_persons = Utils::getUnitPersonsByUnitGetId();
    $count = count( $unit_persons );
} 

if (array_key_exists('cancel', $_POST)) {
    // redirect
    Utils::redirect('unit-list', array());
} elseif (array_key_exists('save', $_POST)) {
    // for security reasons, do not map the whole $_POST['unit']
    $data = array(
        'id' => $_POST['unit']['id'],
        'address' => $_POST['unit']['address'],
        'building_id' => $_POST['unit']['building_id'],
        'type' => $_POST['unit']['type'],
        'guest_limit' => $_POST['unit']['guest_limit'],
    );
    // map
    UnitMapper::map($unit, $data);
    // validate
    $errors = UnitValidator::validate($unit);
    // validate
    if (empty($errors)) {
        // save
        $dao = new UnitDao();
        $unit = $dao->save($unit);
        Flash::addFlash('Unit saved successfully.');
        // redirect
        Utils::redirect('unit-detail', array('id' => $unit->getId()));
    }
}
