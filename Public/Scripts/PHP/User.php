<?php
// Importing PDO
require_once $_SERVER["DOCUMENT_ROOT"] . "/Public/Scripts/PHP/PDO.php";
// Importing PHP Mailer
require_once $_SERVER["DOCUMENT_ROOT"] . "/Modules/PHPMailer/src/PHPMailer.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/Modules/PHPMailer/src/Exception.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/Modules/PHPMailer/src/SMTP.php";
// User class
class User
{
    // Class variables
    private int $id;
    private string $mailAddress;
    private string $password;
    private int $key;
    private string $encryptedPassword;
    protected PHPDataObject $PDO;
    protected $Mail;
    // Constructor method
    public function __construct()
    {
        // Instantiating PDO
        $this->PDO = new PHPDataObject();
        // Instantiating PHP Mailer
        $this->Mail = new PHPMailer\PHPMailer\PHPMailer(true);
    }
    // ID accessor method
    public function getID()
    {
        return $this->id;
    }
    // ID mutator method
    public function setID(int $id)
    {
        $this->id = $id;
    }
    // Mail Address accessor method
    public function getMailAddress()
    {
        return $this->mailAddress;
    }
    // Mail Address mutator method
    public function setMailAddress(string $mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }
    // Password accessor method
    public function getPassword()
    {
        return $this->password;
    }
    // Password mutator method
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    // Key accessor method
    public function getKey()
    {
        return $this->key;
    }
    // Key mutator method
    public function setKey(int $key)
    {
        $this->key = $key;
    }
    // Encrypted Password accessor method
    public function getEncryptedPassword()
    {
        return $this->encryptedPassword;
    }
    // Encrypted Password mutator method
    public function setEncryptedPassword(string $encryptedPassword)
    {
        $this->encryptedPassword = $encryptedPassword;
    }
    // Key Finder method
    public function keyFinder(string $text)
    {
        // Calculating the key which is the square root of the length of the password given that a password should never be less than 25 characters of length
        $this->setKey((int)round(pow(strlen($this->getPassword()), 0.5)));
    }
    // Encrypt method
    public function encrypt()
    {
        // Local variables
        $matrix = array();
        $cipher = "";
        // For-loop to fill the matrix
        for ($firstIndex = 0; $firstIndex < (int)(round(strlen($this->getPassword()) / $this->getKey())); $firstIndex++) {
            // Local variables
            $matrixRow = array();
            // For-loop to fill the row of the matrix
            for ($secondIndex = 0; $secondIndex < $this->getKey(); $secondIndex++) {
                // If-statement to determine whether the indices have not reached the length of the password
                if ($firstIndex * $this->getKey() + $secondIndex < strlen($this->getPassword())) {
                    // Adding the charcter of the password into the array
                    $matrixRow[] = $this->getPassword()[$firstIndex * $this->getKey() + $secondIndex];
                } else {
                    $matrixRow[] = ".";
                }
            }
            $matrix[] = $matrixRow;
        }
        // Calculating the dimensions of the matrix
        $width = count($matrix[0]);
        $height = count($matrix);
        // If-statement to calculate the depth of the encryption
        if ($width / $height) {
            $depth = pow($width, 0.5);
        } else {
            $depth = pow($height, 0.5);
        }
        // For-loop to encrypt the data
        for ($firstIndex = 0; $firstIndex < $depth; $firstIndex++) {
            // For-loop to go down
            for ($secondIndex = 0; $secondIndex < $height - $firstIndex - 1; $secondIndex++) {
                $cipher += $matrix[$secondIndex][$width - $firstIndex - 1];
            }
            // For-loop to go left
            for ($secondIndex = $width - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $cipher += $matrix[$height - $firstIndex - 1][$secondIndex];
            }
            // For-loop to go up
            for ($secondIndex = $height - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $cipher += $matrix[$secondIndex][$firstIndex];
            }
            // For-loop to go right
            for ($secondIndex = 0; $secondIndex < $width - $firstIndex - 1; $secondIndex++) {
                $cipher += $matrix[$firstIndex][$secondIndex];
            }
        }
        // Setting the data for the encrypted password
        $this->setEncryptedPassword($cipher);
    }
}
