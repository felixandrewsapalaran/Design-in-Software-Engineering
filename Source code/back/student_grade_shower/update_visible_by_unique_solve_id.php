<?php
require "../db.php";
$POSTunique_id = $_POST['unique_id'];
$POSTupdate_status = $_POST['update_status'];

global $con;
$sql = "UPDATE `student_ans_exam` SET `visible_status` = '$POSTupdate_status' WHERE `student_ans_exam`.`uniq_exam_solve_id` = $POSTunique_id; ";

if(empty($POSTunique_id))
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'You sent an empty unique_solve_id',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
elseif(!is_numeric($POSTunique_id))
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'The unique ID you sent is not numeric',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
elseif ($POSTupdate_status != 1 && $POSTupdate_status != 0)
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'You must send either a 0 or 1 to the update status',
        'unique_id' => $POSTunique_id,
        'update_status' => $POSTupdate_status
    );
    echo json_encode($errk);
    exit();
}

else
{
    $results = $con->query($sql);
    if ($results)
    {
        $stat = array(
            'return_code' => 1,
            'message' => 'Success! Unique ID ' . $POSTunique_id . ' is now set to: ' . $POSTupdate_status
        );
        echo json_encode($stat);
        exit();
    }
    else
    {
        $stat = array(
            'return_code' => 0,
            'message' => 'There was an error',
            'mysql_error' => mysqli_error($con)
        );
        echo json_encode($stat);
        exit();
    }

}

?>