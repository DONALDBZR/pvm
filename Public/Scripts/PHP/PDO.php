<?php
// PHP Data Object class
class PHPDataObject
{
    // Class variables
    private string $dataSourceName = "mysql:dbname=pvm;host=localhost:3306";
    private string $username = "root";
    private string $password = "Aegis4869";
    private PDO $databaseHandler;
    private $statement;
    // Constructor method
    public function __construct()
    {
        $options = array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        try {
            $this->databaseHandler = new PDO($this->dataSourceName, $this->username, $this->password, $options);
        } catch (PDOException $error) {
            echo "Connection Failed: " . $error->getMessage();
        }
    }
    // Bind method
    public function bind($parameter, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->statement->bindValue($parameter, $value, $type);
    }
    // Query method
    public function query($query)
    {
        $this->statement = $this->databaseHandler->prepare($query);
    }
    // Execute method
    public function execute()
    {
        return $this->statement->execute();
    }
    // Result Set method
    public function resultSet()
    {
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
