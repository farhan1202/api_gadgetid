<?php

header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

// include_once('../../configs/database.php');
 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') :
    http_response_code(405);
    echo json_encode([
        'status' => 0,
        'message' => 'Invalid Request Method. HTTP method should be POST',
    ]);
    exit;
endif;

require '../../configs/database.php';
$database = new Database();
$conn = $database->dbConnection();

$username = $_POST['username'];
$password = md5($_POST['password']);
$name = $_POST['name'];
$email = $_POST['email'];
$date = new DateTime();
$datenow = $date->format("Y-m-d H:i:s");

if (empty($username) || empty($password) || empty($name) || empty($email)) :

    echo json_encode([
        'status' => 0,
        'message' => 'Please fill all the fields',
    ]);
    exit; 
endif;

try {
    $query1 = "SELECT * FROM tbl_user WHERE username = :username";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt1->execute();
    if ($stmt1->rowCount() > 0) {
        echo json_encode([
            'status' => 0,
            'message' => 'Username has been used'
        ]);
        exit;
    }

    $query = "INSERT INTO tbl_user VALUES('',:username,:password,:name,:email,'default.png',:dateCreate,'')";

    $stmt = $conn->prepare($query);

    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':dateCreate', $datenow, PDO::PARAM_STR);

    if ($stmt->execute()) { 
        http_response_code(201);
        echo json_encode([
            'status' => 1,
            'message' => 'Data Inserted Successfully.'
        ]);
        exit;
    }
    
    echo json_encode([
        'status' => 0,
        'message' => 'Data not Inserted.'
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage()
    ]);
    exit;
}



?>