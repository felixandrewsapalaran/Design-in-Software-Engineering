<?php
require "db.php";
header('Content-Type: application/json;charset=utf-8');

$POSTunique_id = $_POST['unique_id'];

global $con;
$sql = "SELECT `grade` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $POSTunique_id";
$data = [];

if(empty($POSTunique_id))
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'The Unique ID you sent is empty',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
elseif (is_numeric($POSTunique_id) == false)
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'The Unique ID you sent is not numeric',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
else
{
    $results = $con->query($sql);
    if ($results->num_rows > 0)
    {
        while ($row = $results->fetch_assoc())
        {
            $data[] = $row;
        }
    }

    $question_num_list = $data[0]['grade'];
    echo $question_num_list;
}
?>
