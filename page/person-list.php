<?php

$dao = new PersonDao();

$title = 'Persons';
$persons = array();

$occupant_type = null;
$search_date = null;
if ( isset( $_POST['show_date'] )) {
    $show_date = $_POST['show_date'];
    if ( $show_date === 'Current' ) {
        $search_date = new DateTime();
        $_POST['selected_date'] = $search_date->format('Y-m-d');
    } else if ( $show_date === 'Selected' ) {
        $search_date = new DateTime( $_POST['selected_date'] );
    } else {
        $search_date = $GLOBALS['BEGIN_DATE'];
    }
    $_POST['download_date'] = $search_date->format('Y-m-d');
} else {
        $search_date = new DateTime();
        $_POST['selected_date'] = $search_date->format('Y-m-d');
}
if ( isset( $_POST['occupant_type'] )) {
    $occupant_type = $_POST['occupant_type'];
} else {
    $_POST['occupant_type'] = 'All';
}

$search = new PersonSearchCriteria();
if( $search_date ) {
    $search->setSearchDate( $search_date );
}

if ( $occupant_type ) {
    if ( $occupant_type != 'All' ) {
        $search->setOccupantType( $occupant_type );    
    }
}

$persons = $dao->find( $search );

if (array_key_exists('download_all', $_POST)) {
    download_all();
}

function download_all() {
    $search = new PersonSearchCriteria();
    if ($_POST['download_date'] === '') {
        $_POST['download_date'] = (new DateTime())->format('Y-m-d');
    }
    $search->setSearchDate(DateTime::createFromFormat('Y-m-d', 
            $_POST['download_date'])->sub(new DateInterval('P1D')));
    $dao = new PersonDao();
    $persons = $dao->find($search);
    $dir = getcwd();
    $filename = '/tmp/residents' . date('Y-m-d-H-mi') . '.csv';
    $handle = fopen($filename, 'w');
    if ($handle) {
        fwrite($handle, "ID, Name,,,,,,,Unit" . PHP_EOL);
        fwrite($handle, ",First,Last,Email,DOB,,#,Unit,Type,Start,End" . PHP_EOL);
        $unit_dao = new UnitDao();
        foreach ($persons as $person) {
            $units = $person->getUnits();
            $names = $person->getNames();
            $unit_person = end($units);
            $unit = $unit_dao->findById($unit_person->getUnitId());
            $name = end($names);
            $name_end = $name->getEndDate() ? Utils::formatDate($name->getEndDate()) : "";
            $unit_end = $unit_person->getEndDate() ? Utils::formatDate($unit_person->getEndDate()) : "";
            fwrite($handle,$person->getID() . ',' . $name->getFirstName() . ',' 
                    . $name->getLastName() . ',' 
                    . $person->getEmail() . ',' 
                    . $person->getBirthdate() . ',,' 
                    . $unit_person->getUnitId() . ','
                    . '"' . $unit->getType() . '",'
                    . $unit_person->getOccupantType() . ','
                    . Utils::formatDate($unit_person->getStartDate()) . ','
                    . $unit_end . PHP_EOL);
            while (($unit_person = prev($units)) != null) {
                $unit = $unit_dao->findById($unit_person->getUnitId());
                $unit_end = $unit_person->getEndDate() ? Utils::formatDate($unit_person->getEndDate()) : "";
                fwrite($handle, ',,,,,,'
                    . $unit_person->getUnitId() . ','
                    . '"' . $unit->getType() . '",'
                    . $unit_person->getOccupantType() . ','
                    . Utils::formatDate($unit_person->getStartDate()) . ','
                    . $unit_end . PHP_EOL);
            }
        }
    }
    fclose( $handle );
    make_header( $filename );
}

function make_header( $filename ) {
if (file_exists($filename)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/text');
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
    // exit;
}
unlink( $filename );
}

// data for template

