<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/Public/Scripts/PHP/PDO.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/Modules/PHPMailer/src/PHPMailer.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/Modules/PHPMailer/src/Exception.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/Modules/PHPMailer/src/SMTP.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/Public/Scripts/PHP/Environment.php";
class User
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $mailAddress;
    private string $password;
    private int $key;
    private string $encryptedPassword;
    private Environment $Environment;
    protected PHPDataObject $PDO;
    protected $Mail;
    public $domain = "http://pvm.local";
    // Constructor method
    public function __construct()
    {
        $this->PDO = new PHPDataObject();
        $this->Mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $this->Environment = new Environment();
    }
    public function getID()
    {
        return $this->id;
    }
    public function setID(int $id)
    {
        $this->id = $id;
    }
    public function getMailAddress()
    {
        return $this->mailAddress;
    }
    public function setMailAddress(string $mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    public function getKey()
    {
        return $this->key;
    }
    public function setKey(int $key)
    {
        $this->key = $key;
    }
    public function getEncryptedPassword()
    {
        return $this->encryptedPassword;
    }
    public function setEncryptedPassword(string $encryptedPassword)
    {
        $this->encryptedPassword = $encryptedPassword;
    }
    public function getFirstName()
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }
    public function getLastName()
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }
    public function keyFinder(string $text)
    {
        $this->setKey((int)round(pow(strlen($text), 0.5)));
    }
    public function encrypt(string $plain)
    {
        $matrix = array();
        $cipher = "";
        for ($firstIndex = 0; $firstIndex < (int)(round(strlen($plain) / $this->getKey())); $firstIndex++) {
            $matrixRow = array();
            for ($secondIndex = 0; $secondIndex < $this->getKey(); $secondIndex++) {
                if ($firstIndex * $this->getKey() + $secondIndex < strlen($plain)) {
                    $matrixRow[] = $plain[$firstIndex * $this->getKey() + $secondIndex];
                } else {
                    $matrixRow[] = ".";
                }
            }
            $matrix[] = $matrixRow;
        }
        $width = count($matrix[0]);
        $height = count($matrix);
        if ($width / $height) {
            $depth = pow($width, 0.5);
        } else {
            $depth = pow($height, 0.5);
        }
        for ($firstIndex = 0; $firstIndex < $depth; $firstIndex++) {
            for ($secondIndex = 0; $secondIndex < $height - $firstIndex - 1; $secondIndex++) {
                $cipher += $matrix[$secondIndex][$width - $firstIndex - 1];
            }
            for ($secondIndex = $width - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $cipher += $matrix[$height - $firstIndex - 1][$secondIndex];
            }
            for ($secondIndex = $height - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $cipher += $matrix[$secondIndex][$firstIndex];
            }
            for ($secondIndex = 0; $secondIndex < $width - $firstIndex - 1; $secondIndex++) {
                $cipher += $matrix[$firstIndex][$secondIndex];
            }
        }
        $this->setEncryptedPassword($cipher);
    }
    public function decrypt($cipher)
    {
        $count = 0;
        $plain = "";
        $width = $this->getKey();
        $height = (int)(round(strlen($cipher) / $this->getKey()));
        if ($width < $height) {
            $depth = pow($width, 0.5);
        } else {
            $depth = pow($height, 0.5);
        }
        $matrix = array(array());
        for ($firstIndex = 0; $firstIndex < $depth; $firstIndex++) {
            for ($secondIndex = 0; $secondIndex < $height - $firstIndex - 1; $secondIndex++) {
                $matrix[$secondIndex][$width - $firstIndex - 1] = $this->getEncryptedPassword()[$count];
                $count++;
            }
            for ($secondIndex = $width - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $matrix[$height - $firstIndex - 1][$secondIndex] = $this->getEncryptedPassword()[$count];
                $count++;
            }
            for ($secondIndex = $height - $firstIndex - 1; $secondIndex > $firstIndex; $secondIndex--) {
                $matrix[$secondIndex][$firstIndex] = $this->getEncryptedPassword()[$count];
                $count++;
            }
            for ($secondIndex = 0; $secondIndex < $width - $firstIndex - 1; $secondIndex++) {
                $matrix[$firstIndex][$secondIndex] = $this->getEncryptedPassword()[$count];
                $count++;
            }
        }
        for ($firstIndex = 0; $firstIndex < $height; $firstIndex++) {
            for ($secondIndex = 0; $secondIndex < $width; $secondIndex++) {
                $plain += $matrix[$firstIndex][$secondIndex];
            }
        }
        $this->setPassword($plain);
    }
    public function register()
    {
        $request = json_decode(file_get_contents('php://input'));
        $this->PDO->query("SELECT * FROM PasswordManager.Users WHERE mailAddress = :mailAddress");
        $this->PDO->bind(":mailAddress", $request->mailAddress);
        $this->PDO->execute();
        if (empty($this->PDO->resultSet())) {
            $this->setMailAddress($request->mailAddress);
            $this->setPassword($this->generatePassword());
            $this->Mail->IsSMTP();
            $this->Mail->CharSet = "UTF-8";
            $this->Mail->Host = "smtp-mail.outlook.com";
            $this->Mail->SMTPDebug = 0;
            $this->Mail->Port = 587;
            $this->Mail->SMTPSecure = 'tls';
            $this->Mail->SMTPAuth = true;
            $this->Mail->IsHTML(true);
            $this->Mail->Username = $this->Environment->mailUsername;
            $this->Mail->Password = $this->Environment->mailPassword;
            $this->Mail->setFrom($this->Mail->Username);
            $this->Mail->addAddress($this->getMailAddress());
            $this->Mail->Subject = "Password: Registration Complete!";
            $this->Mail->Body = "Your password is " . $this->getPassword() . ".  Please consider to change your password after logging in!";
            $this->Mail->send();
            $this->keyFinder($this->getPassword());
            $this->encrypt($this->getPassword());
            $this->PDO->query("INSERT INTO PasswordManager.Users(mailAddress, encrytedPassword) VALUES (:mailAddress, :encryptedPassword)");
            $this->PDO->bind(":mailAddress", $this->getMailAddress());
            $this->PDO->bind(":encryptedPassword", $this->getEncryptedPassword());
            $this->PDO->execute();
            $response = array(
                "success" => "success",
                "url" => $this->domain . "/Login",
                "message" => "You have been successfully registered for this service!"
            );
            header("Content-Type: application/json");
            echo json_encode($response);
        } else {
            $response = array(
                "success" => "failure",
                "url" => $this->domain . "/Login",
                "message" => "You already have accessed to this service!"
            );
            header("Content-Type: application/json");
            echo json_encode($response);
        }
    }
    public function generatePassword()
    {
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length = rand(25, 128);
        $plain = "";
        for ($index = 0; $index < $length; $index++) {
            $plain += $characters[rand(0, strlen($characters) - 1)];
        }
        return $plain;
    }
}
