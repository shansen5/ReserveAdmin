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
    }
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
// data for template

