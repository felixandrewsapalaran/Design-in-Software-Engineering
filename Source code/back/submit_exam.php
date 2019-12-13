<?php
/*
Submits Students Exam
-Add UCID Check Later
*/
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$ucid = $_POST['ucid'];
$exam_id = $_POST['exam_id'];
$student_ans = $_POST['student_ans'];

$submit_student_exam = $con->query("
INSERT INTO student_ans_exam( `ucid`, `exam_id`, `visible_status`, `student_ans`) 
VALUES ('$ucid', '$exam_id', '0', '$student_ans')
");

function curl_request_input($url, $input)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $page_result = curl_exec($ch);
    curl_close($ch);
    return $page_result;
}


if(!empty($ucid) || !empty($exam_id) || !empty($student_ans))
{
    if(is_numeric($exam_id))
    {
        if($submit_student_exam == TRUE)
        {
            $last_id = $con->insert_id;
            $return = array(
                'return_code' => 1,
                'message' => 'Your exam was submitted successfully!',
                'ucid' => $ucid,
                'exam_id' => $exam_id,
                'student_ans' => $student_ans,
                'unique_exam_id_from_db' => $last_id
            );

            $kickstartGrader = array('ucid' => $ucid, 'exam_id' => $exam_id);
            $comments = json_encode(curl_request_input('https://web.njit.edu/~oa227/490/final/back/submit_to_auto_grader.php', $kickstartGrader));
        }
        else
        {

            $return = array(
                'return_code' => 0,
                'error_message' => $con->errorInfo()
            );
        }
    }
    else
    {
        $return = array(
            'return_code' => 0,
            'error_message' => 'Exam ID is not numeric',
            'exam_id' => $exam_id
        );
    }
}
else
{
    $return = array(
        'return_code' => 0,
        'error_message'  => 'One of the POST fields were empty',
        'ucid' => $ucid,
        'exam_id' => $exam_id,
        'student_ans' => $student_ans
    );
}


echo(json_encode($return));

?>