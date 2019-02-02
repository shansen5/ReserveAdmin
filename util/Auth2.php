<?php

	//
	// This file implements the authentication using
	// HTTP digest algorithm.
	// just include it on you php file and call authenticate();
	// written by Jader Feijo (jader@movinpixel.com)
	//

        session_start();

        require_once( '../model/User.php' );
        require_once( '../dao/UserDao.php' );
        require_once( '../mapping/UserMapper.php' );
        require_once( '../config/Config.php' );

	function get_user_password($username) {
		// return the password for the given username
                $dao = new UserDao();
                $user = $dao->findByUsername($username);
                if ( $user ) {
                    $_SESSION['oc_user_role'] = $user->getRole();
                    return $user->getPassword();
                }
		return null;
	}
	
	function authenticate( $uname, $pwd ) {
		$password = get_user_password($uname);
		if (!$password) {
			return FALSE;
		}
                $len = strlen( $password );
                $password = substr( $password, 0, $len/2 );
		
                $pwd_md5 = md5( $pwd );
		if ($password != $pwd_md5) {
			return FALSE;
		}
		
                $_SESSION['oc_user'] = $uname;
                    
		return TRUE;
	}

?>
