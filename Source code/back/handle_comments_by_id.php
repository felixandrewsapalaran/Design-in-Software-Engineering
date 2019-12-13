<?php
require "db.php";
//header('Content-Type: application/json;charset=utf-8');
global $con;
$POSTuniqexam_id = $_POST['unique_exam_id'];
$POSTstatus = $_POST['handlestatus'];
$POSTcomments = $_POST['professor_comments'];

if(empty($POSTuniqexam_id))
{
    $returner = array(
        'return_code' => 0,
        'error_message' => 'Exam ID field is empty',
        'unique_exam_id' => $POSTuniqexam_id,
        'status' => $POSTstatus
    );
}
elseif(empty($POSTstatus))
{
    $returner = array(
        'return_code' => 0,
        'error_message' => 'Status is empty',
        'unique_exam_id' => $POSTuniqexam_id,
        'status' => $POSTstatus
    );
}
elseif (is_numeric($POSTuniqexam_id) == false)
{
    $returner = array(
        'return_code' => 0,
        'error_message' => 'Unique Exam ID needs to be submitted as a numerical value, not a string',
        'unique_exam_id' => $POSTuniqexam_id,
        'status' => $POSTstatus
    );
}
else
{
    //If we are asked to display the comments
    if($POSTstatus = 'display')
    {
        if ($result = $con->query("SELECT * FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $POSTuniqexam_id"))
        {
            while ($row = $result->fetch_row())
            {
                $returner = $row[0];
            }
            $result->close();
        }
    }
    elseif ($POSTstatus = 'update')
    {
        if(empty($POSTcomments))
        {
            $returner = array(
                'return_code' => 0,
                'error_message' => 'Unique Exam ID needs to be submitted as a numerical value, not a string',
                'unique_exam_id' => $POSTuniqexam_id,
                'status' => $POSTstatus,
                'professor_comments' => $POSTcomments,
            );
        }
        elseif ($result = $con->query("UPDATE `student_ans_exam` SET `prof_comments` = '$POSTcomments' WHERE `student_ans_exam`.`uniq_exam_solve_id` = $POSTuniqexam_id "))
        {
            while ($row = $result->fetch_row())
            {
                $returner = $row[0];
            }
            $result->close();
        }
    }
}

?>