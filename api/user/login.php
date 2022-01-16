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
$random = generateRandomString();

if (empty($username) || empty($password) ) :

    echo json_encode([
        'status' => 0,
        'message' => 'Please fill all the fields',
    ]);
    exit; 
endif;

try {
    $query2 = "UPDATE tbl_user SET token = :token WHERE username = :username AND password = :password";
        $stmt2 =  $conn->prepare($query2);
        $stmt2->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt2->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt2->bindValue(':token', $random, PDO::PARAM_STR);
        $stmt2->execute();
    $query1 = "SELECT * FROM tbl_user WHERE username = :username AND password = :password";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt1->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt1->execute();
    if ($stmt1->rowCount() > 0) { 
        $data = $stmt1->fetch(PDO::FETCH_OBJ);
        $data1 = array(
            'id' => $data->id,
            'username' => $data->username,
            'email' => $data->email,
            'create' => $data->dateCreate,
            'img' => "localhost/gadget/imgaes/".$data->img,
            'token ' => $data->token,
        );
       
        http_response_code(200);
        echo json_encode([
            'status' => 1,
            'message' => "Fetch Data Sukses",
            'data' => $data1
        ]);
        exit;
    }
    echo json_encode([
                'status' => 0,
                'message' => 'Username or Password not match'
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


function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrs092u3tuvwxyzaskdhfhf9882323ABCDEFGHIJKLMNksadf9044OPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>