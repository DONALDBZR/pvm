<?php
// Importing User
require $_SERVER['DOCUMENT_ROOT'] . "/Public/Scripts/PHP/User.php";
// Instantiating User
$User = new User();
// If-statement to verify that the request is not null
if (json_decode(file_get_contents("php://input")) != null) {
    if (!empty(json_decode(file_get_contents("php://input"))->mailAddress)) {
        // Registering the user
        $User->register();
    } else {
        $response = array(
            "success" => "failure",
            "url" => $User->domain . "/Register",
            "message" => "The form has been wrongly filled!  Please try again!"
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} else {
    $response = array(
        "success" => "failure",
        "url" => $User->domain . "/Register",
        "message" => "The form has not been filled!"
    );
    header('Content-Type: application/json');
    echo json_encode($response);
}
