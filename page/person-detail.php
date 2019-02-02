<?php

// data for template
$person = Utils::getPersonByGetId();
$unit_persons = $person->getUnits();
$count = count( $unit_persons );

