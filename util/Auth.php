<?php

	//
	// This file implements the authentication using
	// HTTP digest algorithm.
	// just include it on you php file and call authenticate();
	// written by Jader Feijo (jader@movinpixel.com)
	//

        require_once( '../model/User.php' );
        require_once( '../dao/UserDao.php' );
        require_once( '../mapping/UserMapper.php' );
        require_once( '../config/Config.php' );

	// function to parse the http auth header
	function http_digest_parse($txt)
	{
		$keys_arr = array();
		$values_arr = array();
		$cindex = 0;
		$parts = explode(',', $txt);
		
		foreach($parts as $p) {
			$p = trim($p);
			$kvpair = explode('=', $p);
			$kvpair[1] = str_replace("\"", "", $kvpair[1]);
			$keys_arr[$cindex] = $kvpair[0];
			$values_arr[$cindex] = $kvpair[1];
			$cindex++;
		}
	
		$ret_arr = array_combine($keys_arr, $values_arr);
		$ret_arr['uri'] = $_SERVER['REQUEST_URI'];
		return $ret_arr;
	}
	
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
	
	function authenticate() {
		$realm = "Restricted area";
		$header1 = 'WWW-Authenticate: Digest realm="' . $realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($realm). '"';
		$header2 = 'HTTP/1.0 401 Unauthorized';
		
		if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
		   header($header1);
		   header($header2);
		   die('<h1>Access denied</h1>');
		}
		
		// analyze the PHP_AUTH_DIGEST variable
		$data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
		$password = get_user_password($data['username']);
		if (!$password) {
			header($header1);
			header($header2);
			return FALSE;
		}
		
		if (!$data || $password == -1) {
			header($header1);
			header($header2);
			return FALSE;
		}
	
		// generate the valid response
		$A1 = md5($data['username'] . ':' . $realm . ':' . $password);
		$A2 = md5($_SERVER['REQUEST_METHOD'] . ':' . $data['uri']);
		$valid_response = md5($A1 . ':' . $data['nonce'] . ':' . $data['nc'] . ':' . $data['cnonce'] . ':' . $data['qop'] . ':' . $A2);
		
		if ($data['response'] != $valid_response) {
			header($header1);
			header($header2);
			return FALSE;
		}
		
                $_SESSION['oc_user'] = $data['username'];
                    
		return TRUE;
	}

?>
