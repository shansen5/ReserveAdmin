<?php

$user = Utils::getUserByGetId();

$dao = new UserDao();
$dao->delete($user->getId());
Flash::addFlash('User deleted successfully.');

Utils::redirect('user-list', array());
