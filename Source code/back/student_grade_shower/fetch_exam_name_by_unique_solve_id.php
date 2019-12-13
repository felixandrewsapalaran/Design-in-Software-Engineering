<?php
require "db.php";
header('Content-Type: application/json;charset=utf-8');

$POSTunique_id = $_POST['unique_id'];

global $con;
$sql = "SELECT `exam_id` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $POSTunique_id";
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
    /* Get Exam ID via Unique Solve ID*/
    $results = $con->query($sql);
    if ($results->num_rows > 0)
    {
        while ($row = $results->fetch_assoc())
        {
            $data[] = $row;
        }
    }

    $exam_id_ret = $question_num_list = $data[0]['exam_id'];
    /* Get Exam Name via Exam ID*/

    $sql2 = "SELECT `exam_name` FROM `exam_list` WHERE `exam_id` = $exam_id_ret";
    $results2 = $con->query($sql2);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }

    $exam_id_name = $data2[0]['exam_name'];
    echo $exam_id_name;

}
?>
