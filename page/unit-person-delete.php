<?php

$unit_person = Utils::getUnitPersonByGetId();
$person_id = $unit_person->getPersonId();

$dao = new UnitPersonDao();
$sense = $dao->delete($unit_person->getId());
if ( $sense ) {
    Flash::addFlash('UnitPerson deleted successfully.');
} else {
    Flash::addFlash('There was a problem deleting the UnitPerson record.');    
}
Utils::redirect('person-detail', array('id' => $person_id));
