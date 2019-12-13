<?php
/* For After Beta */
require "db.php";
global $con;

$userID = clean_input($_POST['userID']);
$userPass = hash('sha256', clean_input($_POST['userPass']));
$userPosition = clean_input($_POST['userPosition']);

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$sql = "INSERT INTO login(ucid,password,id) VALUES ('$userID',$userPass','$userPosition')";

if(empty($username) || empty($password))
{
    $rc = array(
        'return_code' => 0,
        'error_message' => 'Username And/Or Password Fields Empty',
        'username' => $userID,
        'password' => $userPass,
        'position' => $userPosition
    );
}
elseif(is_numeric($userPosition) === FALSE)
{
      $rc = array(
        'return_code' => 0,
        'error_message' => 'userPosition is NOT numeric!',
        'username' => $userID,
        'password' => $userPass,
        'position' => $userPosition
    );
}
else
{
  if ($con->query($sql) === TRUE)
    {
        //Exam created successfully
        $rc = array(
        'return_code' => 1,
        'message' => 'Account Successfully Created';
        );
    }
    else
    {
        $rc = array(
            'return_code' => 'Insert Failed',
            'error_message' => $mysqli->error,
         );
    }
}

echo(json_encode($rc));

?>