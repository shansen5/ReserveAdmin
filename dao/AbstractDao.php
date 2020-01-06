<?php

/**
 * DAO base class
 * <p>
 * 
 */
abstract class AbstractDao {

    /** @var PDO */
    private $db = null;

    /**
     * 
     * @param type $db PDO
     */
    public function __construct( $db = null ) {
        $this->db = $db;
    }

    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * @return PDO
     */
    protected function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $config = Config::getConfig("db");
        try {
            $this->db = new PDO($config['dsn'], $config['username'], $config['password'], 
                    array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        } catch (Exception $ex) {
            throw new Exception('DB connection error: ' . $ex->getMessage());
        }
        return $this->db;
    }

    /**
     * @return AbstractModel 
     * @throws Exception
     */
    protected function execute($sql, 
                               AbstractModel $model = null, 
                               $update = false ) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams( $model, $update ));
        if ( ! $model || ! $model->getId() ) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        return $model;
    }

    protected function executeStatement( PDOStatement $statement, 
                                         array $params ) {
        if ( ! $statement->execute( $params )) {
            $errorInfo = $this->getDb()->errorInfo();
            self::throwDbError( $errorInfo );
        }
    }

    /**
     * @return PDOStatement
     */
    protected function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    protected static function throwDbError(array $errorInfo) {
        // TODO log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }

    /**
     * Find all by search criteria.
     * @return array 
     */
    abstract public function find( AbstractSearchCriteria $search = null );

    /**
     * Find by identifier.
     * @return 
     */
    abstract public function findById($id);

    /**
     * Save 
     * @param AbstractModel-derived object
     * @return Model object saved {@link AbstractModel} instance
     */
    abstract public function save( AbstractModel $model );

    /**
     * Delete {@link AbstractModel} by identifier.
     * @param int $id {@link AbstractModel} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    abstract public function delete($id);

    /**
     * @return AbsractModel
     * @throws Exception
     */
    abstract protected function insert( AbstractModel $model );

    /**
     * @return AbstractModel
     * @throws Exception
     */
    abstract protected function update( AbstractModel $model );

    abstract protected function getFindSql( AbstractSearchCriteria $search = null );

    abstract protected function getParams( AbstractModel $model, $update = false );

}
