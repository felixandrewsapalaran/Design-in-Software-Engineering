<?php
/*
 * Verifies whether user is valid & if student or professor
 */
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$username = clean_input($_POST['userID']);
$password = clean_input($_POST['userPass']);

//Clean the input so no SQL injections happen
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

//query for ucid pswd match
$sql = "SELECT * FROM login WHERE ucid = '" . $username . "' AND password = '" . hash('sha256', $password) . "'";
$result = mysqli_query($con, $sql);

if(empty($username) || empty($password))
{
    $rc = array(
        'return_code' => -1,
        'error_message' => 'Username And/Or Password Fields Empty',
        'username' => $username,
        'password' => $password
    );
}
else
{
    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if($row['id'] == 1)
        {
            $rc = array('return_code' => 1,'professor'=> 1);
        }
        elseif($row['id'] == 0)
        {
            $rc = array('return_code' => 1,'professor' => 0);
        }
        else
        {
            $rc = array(
                'return_code' => 0,
                'error_message' => 'Unknown Position', 'output' => $row['id']);
        }
    }
    else
    {
        $rc = array('return_code'=> 0,
            'error_message'=> 'Username/Password wrong or User Doesnt Exist',
            'username' => $username);
    }
}

echo(json_encode($rc));

?>
