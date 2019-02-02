<?php

/**
 * Main application class.
 */
final class Index {

    const DEFAULT_PAGE = 'home';
    const PAGE_DIR = '../page/';
    const LAYOUT_DIR = '../layout/';

    /**
     * System config.
     */
    public function init() {
        // error reporting - all errors for development (ensure you have display_errors = On in your php.ini file)
        error_reporting(E_ALL | E_STRICT);
        mb_internal_encoding('UTF-8');
        set_exception_handler(array($this, 'handleException'));
        spl_autoload_register(array($this, 'loadClass'));
        // session
        if ( session_status() == PHP_SESSION_NONE ) {
            session_start();
        }
    }

    /**
     * Run the application!
     */
    public function run() {
        $this->runPage($this->getPage());
    }

    /**
     * Exception handler.
     */
    public function handleException(Exception $ex) {
        $extra = array('message' => $ex->getMessage());
        if ($ex instanceof NotFoundException) {
            header('HTTP/1.0 404 Not Found');
            $this->runPage('404', $extra);
        } else {
            // TODO log exception
            header('HTTP/1.1 500 Internal Server Error');
            $this->runPage('500', $extra);
        }
    }

    /**
     * Class loader.
     */
    public function loadClass($name) {
        $classes = array(
            'Config' => '../config/Config.php',
            'RError' => '../validation/RError.php',
            'Flash' => '../flash/Flash.php',
            'NotFoundException' => '../exception/NotFoundException.php',
            'AddressValidator' => '../validation/AddressValidator.php',
            'PersonDao' => '../dao/PersonDao.php',
            'PersonMapper' => '../mapping/PersonMapper.php',
            'Person' => '../model/Person.php',
            'PersonSearchCriteria' => '../dao/PersonSearchCriteria.php',
            'PersonValidator' => '../validation/PersonValidator.php',
            'PersonNameDao' => '../dao/PersonNameDao.php',
            'PersonNameMapper' => '../mapping/PersonNameMapper.php',
            'PersonName' => '../model/PersonName.php',
            'PersonNameSearchCriteria' => '../dao/PersonNameSearchCriteria.php',
            'PersonNameValidator' => '../validation/PersonNameValidator.php',
            'UnitDao' => '../dao/UnitDao.php',
            'UnitMapper' => '../mapping/UnitMapper.php',
            'Unit' => '../model/Unit.php',
            'UnitSearchCriteria' => '../dao/UnitSearchCriteria.php',
            'UnitValidator' => '../validation/UnitValidator.php',
            'UnitPersonDao' => '../dao/UnitPersonDao.php',
            'UnitPersonMapper' => '../mapping/UnitPersonMapper.php',
            'UnitPerson' => '../model/UnitPerson.php',
            'UnitPersonSearchCriteria' => '../dao/UnitPersonSearchCriteria.php',
            'UnitPersonValidator' => '../validation/UnitPersonValidator.php',
            'DBConnection' => '../dao/DBConnection.php',
            'UserDao' => '../dao/UserDao.php',
            'UserMapper' => '../mapping/UserMapper.php',
            'User' => '../model/User.php',
            'UserSearchCriteria' => '../dao/UserSearchCriteria.php',
            'UserValidator' => '../validation/UserValidator.php',
            'Utils' => '../util/Utils.php',
        );
        if (!array_key_exists($name, $classes)) {
            die('Class "' . $name . '" not found.');
        }
        require_once $classes[$name];
    }

    private function getPage() {
        $page = self::DEFAULT_PAGE;
        if (array_key_exists('page', $_GET)) {
            $page = $_GET['page'];
        }
        return $this->checkPage($page);
    }

    private function checkPage($page) {
        if (!preg_match('/^[a-z0-9-]+$/i', $page)) {
            // TODO log attempt, redirect attacker, ...
            throw new NotFoundException('Unsafe page "' . $page . '" requested');
        }
        if (!$this->hasScript($page) && !$this->hasTemplate($page)) {
            // TODO log attempt, redirect attacker, ...
            throw new NotFoundException('Page "' . $page . '" not found');
        }
        return $page;
    }

    private function runPage($page, array $extra = array()) {
        $run = false;
        if ($this->hasScript($page)) {
            $run = true;
            require $this->getScript($page);
        }
        if ($this->hasTemplate($page)) {
            $run = true;
            // data for main template
            $template = $this->getTemplate($page);
            $flashes = null;
            if (Flash::hasFlashes()) {
                $flashes = Flash::getFlashes();
            }

            // main template (layout)
            require self::LAYOUT_DIR . 'index.phtml';
        }
        if (!$run) {
            die('Page "' . $page . '" has neither script nor template!');
        }
    }

    private function getScript($page) {
        return self::PAGE_DIR . $page . '.php';
    }

    private function getTemplate($page) {
        return self::PAGE_DIR . $page . '.phtml';
    }

    private function hasScript($page) {
        return file_exists($this->getScript($page));
    }

    private function hasTemplate($page) {
        return file_exists($this->getTemplate($page));
    }

}

require_once '../util/Auth2.php';

ini_set( 'display_errors', 'off');

if ( isset( $_POST[ 'username' ]) && isset( $_POST[ 'password' ])) {
    if ( authenticate( $_POST[ 'username' ], $_POST[ 'password' ] )) {
        $index = new Index();
        $index->init();
        // run application!
        $index->run();  
    }
} else 
    if ( ! $_SESSION['oc_user'] ) : ?>
<html>
<head>
  <title>User Login</title>
  <link rel="stylesheet" type="text/css" href="css/login.css" />
</head>
<body>
  <form name="frmUser" method="post" action="index.php">
    <div class="message"><?php if($message!="") { echo $message; } ?></div>
    <table border="0" cellpadding="10" cellspacing="1" width="500" align="center">
      <tr class="tableheader">
        <td align="center" colspan="2">Enter Login Details</td>
      </tr>
      <tr class="tablerow">
        <td align="right">Username</td>
        <td><input type="text" name="username"></td>
      </tr>
      <tr class="tablerow">
        <td align="right">Password</td>
        <td><input type="password" name="password"></td>
      </tr>
      <tr class="tableheader">
        <td align="center" colspan="2"><input type="submit" name="submit" value="Submit"></td>
      </tr>
    </table>
  </form>
</body></html>
<?php else :
    $index = new Index();
    $index->init();
    // run application!
    $index->run();  

endif;