<?php

$dao = new UnitDao();

// data for template
$title = 'Units';
$units = $dao->find();
