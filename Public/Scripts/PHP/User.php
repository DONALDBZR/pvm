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
    private string $firstName;
    private string $lastName;
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
    // First Name accessor method
    public function getFirstName()
    {
        return $this->firstName;
    }
    // First Name mutator method
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }
    // Last Name accessor method
    public function getLastName()
    {
        return $this->lastName;
    }
    // Last Name mutator method
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }
    // Key Finder method
    public function keyFinder(string $text)
    {
        // Calculating the key which is the square root of the length of the password given that a password should never be less than 25 characters of length
        $this->setKey((int)round(pow(strlen($text), 0.5)));
    }
    // Encrypt method
    public function encrypt(string $plain)
    {
        // Local variables
        $matrix = array();
        $cipher = "";
        // For-loop to fill the matrix
        for ($firstIndex = 0; $firstIndex < (int)(round(strlen($plain) / $this->getKey())); $firstIndex++) {
            // Local variables
            $matrixRow = array();
            // For-loop to fill the row of the matrix
            for ($secondIndex = 0; $secondIndex < $this->getKey(); $secondIndex++) {
                // If-statement to determine whether the indices have not reached the length of the password
                if ($firstIndex * $this->getKey() + $secondIndex < strlen($plain)) {
                    // Adding the charcter of the password into the array
                    $matrixRow[] = $plain[$firstIndex * $this->getKey() + $secondIndex];
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
    // Decrypt method
    public function decrypt($cipher)
    {
        // Local variables
        $count = 0;
        $plain = "";
        $width = $this->getKey();
        $height = (int)(round(strlen($cipher) / $this->getKey()));
        // If-statement to calculate the depth
        if ($width < $height) {
            $depth = pow($width, 0.5);
        } else {
            $depth = pow($height, 0.5);
        }
        // Creating a two-dimensional array
        $matrix = array(array());
        // For-loop to decrypt the cipher
        for ($firstIndex = 0; $firstIndex < $depth; $firstIndex++) {
            // For-loop to go down
            for ($secondIndex = 0; $secondIndex < $height - $firstIndex - 1; $secondIndex++) {
                $matrix[$secondIndex][$width - $firstIndex - 1] = $this->getEncryptedPassword()[$count];
                $count++;
            }
            // For-loop to go left
            for ($secondIndex = $width - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $matrix[$height - $firstIndex - 1][$secondIndex] = $this->getEncryptedPassword()[$count];
                $count++;
            }
            // For-loop to go up
            for ($secondIndex = $height - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $matrix[$secondIndex][$firstIndex] = $this->getEncryptedPassword()[$count];
                $count++;
            }
            // For-loop to go right
            for ($secondIndex = 0; $secondIndex < $width - $firstIndex - 1; $secondIndex++) {
                $matrix[$firstIndex][$secondIndex] = $this->getEncryptedPassword()[$count];
                $count++;
            }
        }
        // For-loop to re-construct the password
        for ($firstIndex = 0; $firstIndex < $height; $firstIndex++) {
            // For-loop to read row by row
            for ($secondIndex = 0; $secondIndex < $width; $secondIndex++) {
                $plain += $matrix[$firstIndex][$secondIndex];
            }
        }
        // Setting the data for the decrypted password
        $this->setPassword($plain);
    }
    // Register method
    public function register()
    {
        // JSON to be decoded from the front-end
        $JSON = json_decode(file_get_contents('php://input'));
        // Query to select all the users from the database 
        $this->PDO->query("SELECT * FROM PasswordManager.Users WHERE mailAddress = :mailAddress");
        $this->PDO->bind(":mailAddress", $JSON->mailAddress);
        $this->PDO->execute();
        // If-statement to verify that the result returned is null
        if (empty($this->PDO->resultSet())) {
            // Encrypting the password that is generated
            $this->set;
            // Setting all the data needed
            $this->setMailAddress($JSON->mailAddress);

            // Sending the required details
            $this->PHPMailer->IsSMTP();
            $this->PHPMailer->CharSet = "UTF-8";
            $this->PHPMailer->Host = "smtp-mail.outlook.com";
            $this->PHPMailer->SMTPDebug = 0;
            $this->PHPMailer->Port = 587;
            $this->PHPMailer->SMTPSecure = 'tls';
            $this->PHPMailer->SMTPAuth = true;
            $this->PHPMailer->IsHTML(true);
            $this->PHPMailer->Username = "system.password@outlook.com";
            $this->PHPMailer->Password = "Aegis4869";
            $this->PHPMailer->setFrom($this->PHPMailer->Username);
            $this->PHPMailer->addAddress($this->getMailAddress());
            $this->PHPMailer->Subject = "Password: Registration Complete!";
            $this->PHPMailer->Body = "Your password is " . $this->getPassword() . ".  Please consider to change your password after logging in!";
            $this->PHPMailer->send();
        } else {
            # code...
        }
    }
    // Generate Password method
    public function generatePassword()
    {
        // Local variables
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length = rand(25, 128);
        $plain = "";
        // For-loop to generate the password needed
        for ($index = 0; $index < $length; $index++) {
            $plain += $characters[rand(0, strlen($characters) - 1)];
        }
        return $plain;
    }
}
