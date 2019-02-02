<?php

$errors = array();
$user = null;
$edit = array_key_exists('id', $_GET);
if ($edit) {
    $user = Utils::getUserByGetId();
} else {
    // set defaults
    $user = new User();
}

if (array_key_exists('cancel', $_POST)) {
    // redirect
    if ( $user->getId() ) {
        Utils::redirect('user-detail', array('id' => $user->getId()));
    } else {
        Utils::redirect('user-list', array());
    }
} elseif (array_key_exists('save', $_POST)) {
    // for security reasons, do not map the whole $_POST['user']
    $data = array(
        'username' => $_POST['user']['username'],
        'password' => $_POST['user']['password'],
        'role' => $_POST['user']['role'],
    );
        ;
    // map
    UserMapper::map($user, $data);
    // validate
    $errors = UserValidator::validate($user);
    // validate
    if (empty($errors)) {
        // save
        $dao = new UserDao();
        $user = $dao->save($user);
        Flash::addFlash('User saved successfully.');
        // redirect
        Utils::redirect('user-detail', array('id' => $user->getId()));
    }
}
