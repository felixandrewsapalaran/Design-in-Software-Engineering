<?php
require "../db.php";
$POSTunique_id = $_POST['unique_id'];
$POSTupdate_grade = $_POST['update_grade'];

global $con;
$sql = "UPDATE `student_ans_exam` SET `grade` = '$POSTupdate_grade' WHERE `student_ans_exam`.`uniq_exam_solve_id` = $POSTunique_id;";

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
elseif(is_numeric($POSTunique_id) == false)
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'The unique ID you sent is not numeric',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
elseif(empty($POSTupdate_grade))
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'You sent an empty "update grade"',
        'update_grade' => $POSTupdate_grade
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
            'message' => 'Success! Grades have bee updated!',
            'update_grade' => $POSTupdate_grade
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