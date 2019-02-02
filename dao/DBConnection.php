<?php


/**
 * Description of DBConnection
 *
 * @author steve
 */
final class DBConnection {
    /** @var PDO */
    private static $db = null;

    protected function __construct() {}
    
    private function __clone() {}
    
    private function __wakeup() {}
    
    /**
     * @return PDO
     */
    public static function getDb() {
        if (self::$db !== null) {
            return self::$db;
        }
        $config = Config::getConfig("db");
        try {
            self::$db = new PDO($config['dsn'], $config['username'], $config['password'], 
                    array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return self::$db;
    }
    
    public static function close() {
        self::$db = null;
    }

    public static function beginTransaction() {
        $pdo = null;
        if ( !self::$db ) {
            $pdo = self::getDb();
        }
        $pdo->beginTransaction();
    }
    
    public static function commit() {
        self::$db->commit();
    }
    
    public static function rollback() {
        self::$db->rollback();
    }
}
