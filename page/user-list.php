<?php

$dao = new UserDao();

// data for template
$title = 'Users';
$users = $dao->find();
