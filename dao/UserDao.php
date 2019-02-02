<?php

/**
 * DAO for {@link User}.
 * <p>
 * It is also a service, ideally, this class should be divided into DAO and Service.
 */
final class UserDao {

    const USER_INSERT = 1;
    
    const ADMIN_ROLE = 'ADMIN';
    const USER_ROLE = 'USER';
    
    /** @var PDO */
    private $db = null;


    public function __destruct() {
        // close db connection
        $this->db = null;
    }

    /**
     * Find all {@link User}s by search criteria.
     * @return array array of {@link User}s
     */
    public function find(UserSearchCriteria $search = null) {
        $result = array();
        foreach ($this->query($this->getFindSql($search)) as $row) {
            $user = new User();
            UserMapper::map($user, $row);
            $result[$user->getId()] = $user;
        }
        return $result;
    }

    /**
     * Find {@link User} by identifier.
     * @return User or <i>null</i> if not found
     */
    public function findById($id) {
        $row = $this->query('SELECT id, username, password, role
                        FROM users WHERE id = '
                 . (int) $id)->fetch();
        if (!$row) {
            return null;
        }
        $user = new User();
        UserMapper::map($user, $row);
        return $user;
    }

    /**
     * Find {@link User} by identifier.
     * @return User or <i>null</i> if not found
     */
    public function findByUsername($name) {
        $row = $this->query('SELECT  id, username, password, role FROM users
                        WHERE username = "' . $name . '"' )->fetch();
        if (!$row) {
            return null;
        }
        $user = new User();
        UserMapper::map($user, $row);
        return $user;
    }

    public function getAllUserRoles() {
        return array( self::ADMIN_ROLE, self::USER_ROLE );
    }
    /**
     * Save {@link User}.
     * @param User $user {@link User} to be saved
     * @return User saved {@link User} instance
     */
    public function save(User $user) {
        if ($user->getId() === null) {
            return $this->insert($user);
        }
        return $this->update($user);
    }

    /**
     * Delete {@link User} by identifier.
     * @param int $id {@link User} identifier
     * @return bool <i>true</i> on success, <i>false</i> otherwise
     */
    public function delete($id) {
        $sql = '
            DELETE FROM users 
            WHERE
                id = :id';
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, array(
            ':id' => $id,
        ));
        return $statement->rowCount() == 1;
    }

    /**
     * @return PDO
     */
    private function getDb() {
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

    private function getFindSql(UserSearchCriteria $search = null) {
        $sql = 'SELECT id, username, password, role FROM users';
        $orderBy = ' username';
        $sql .= ' ORDER BY ' . $orderBy;
        return $sql;
    }

    /**
     * @return User
     * @throws Exception
     */
    private function insert(User $user) {
        $user->setId( null );

        $sql = 'INSERT INTO users (id, username, password, role)
                VALUES (:id, :username, :password, :role )';
        $result = $this->execute($sql, $user, self::USER_INSERT);
       
        return $result;
    }

    /**
     * @return User
     * @throws Exception
     */
    private function update(User $user) {
        $sql = '
            UPDATE users SET
                username = :username,
                password = :password,
                role = :role
            WHERE
                id = :id';
        return $this->execute($sql, $user, self::USER_INSERT);
    }

    /**
     * @return User
     * @throws Exception
     */
    private function execute($sql, User $user, $query_type) {
        $statement = $this->getDb()->prepare($sql);
        $this->executeStatement($statement, $this->getParams($user, $query_type));
        if (!$user->getId()) {
            return $this->findById($this->getDb()->lastInsertId());
        }
        
        if (!$statement->rowCount()) {
            throw new NotFoundException('User with ID "' . $user->getId() . '" does not exist.');
        }
        
        return $user;
    }

    private function getParams(User $user, $query_type ) {
        $params = array();
        switch( $query_type ) {
            case self::USER_INSERT:
                $pwd_md5 = md5( $user->getPassword() );
                $pwd_md5 = $pwd_md5 . $pwd_md5;
                $params = array(
                    ':id' => $user->getId(),
                    ':username' => $user->getUsername(),
                    ':password' => $pwd_md5,
                    ':role' => $user->getRole()
                    );
                break;
            default:
                break;
        }
        return $params;
    }

    private function executeStatement(PDOStatement $statement, array $params) {
        if (!$statement->execute($params)) {
            $errorInfo = $this->getDb()->errorInfo();
            self::throwDbError( $errorInfo );
        }
    }

    /**
     * @return PDOStatement
     */
    private function query($sql) {
        $statement = $this->getDb()->query($sql, PDO::FETCH_ASSOC);
        if ($statement === false) {
            self::throwDbError($this->getDb()->errorInfo());
        }
        return $statement;
    }

    private static function throwDbError(array $errorInfo) {
        // TODO log error, send email, etc.
        throw new Exception('DB error [' . $errorInfo[0] . ', ' . $errorInfo[1] . ']: ' . $errorInfo[2]);
    }

    private static function formatDateTime(DateTime $date) {
        return $date->format(DateTime::ISO8601);
    }

}
