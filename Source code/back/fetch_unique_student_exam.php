<?php
require "db.php";
global $con;
//header('Content-Type: application/json;charset=utf-8');

$POSTunique_solve_id = $_POST['unique_solve_id'];

function get_question_text($qid)
{
    global $con;
    $data2 = [];
    $select_completed_exam_query2 =
        "SELECT `question` FROM `question_bank` WHERE `qid` = $qid";
    $results2 = $con->query($select_completed_exam_query2);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    return $data2[0]['question'];
}

function arrayify_numerical($input)
{
    //To get the exam questions into an array. Putting TRUE removes anything thats not a numerical value
    $inputpos = $input;
    $inputpos  = preg_replace("/[^0-9,-]/", "", $inputpos);
    $explodeintoarray = explode(",", $inputpos);
    return $explodeintoarray;
}

function get_question_list($exam_id)
{
    global $con;
    if ($result = $con->query("SELECT `question_list` FROM `exam_list` WHERE `exam_id` = $exam_id"))
    {
        while ($row = $result->fetch_row())
        {
            $answ = $row[0];
        }
        $result->close();
    }
    return $answ;
}

function get_exam_id_from_unique($number)
{
    $sqlrunner = "SELECT `exam_id` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $number";
    global $con;
    $data2 = [];
    $results2 = $con->query($sqlrunner);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    return $data2[0]['exam_id'];
}

$question_list_send = arrayify_numerical(get_question_list(get_exam_id_from_unique($POSTunique_solve_id)));

function get_student_ans($uniq)
{
    $sqlrunner = "SELECT `student_ans` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $uniq";
    global $con;
    $data2 = [];
    $results2 = $con->query($sqlrunner);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    $student_ans_unarrayed = $data2[0]['student_ans'];
    $student_ans_arrayed = explode(',', $student_ans_unarrayed);
    return $student_ans_arrayed;
}

function get_auto_grader_comment($unique_solve_id)
{
    global $con;
    $data2 = [];
    $select_completed_exam_query2 =
        "SELECT `auto_grader_comments` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $unique_solve_id";
    $results2 = $con->query($select_completed_exam_query2);

    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
   return unserialize($data2[0]['auto_grader_comments']);
}

/* Functions End Here*/

if(empty($POSTunique_solve_id))
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'The Unique Solver ID is empty',
        'unique_solve_id' => $POSTunique_solve_id
    );
    echo json_encode($errk);
    exit();
}
elseif(is_numeric($POSTunique_solve_id) == false)
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'You are passing a non-numeric ID.',
        'unique_solve_id' => $POSTunique_solve_id
    );
    echo json_encode($errk);
    exit();
}
else
{
    $iae = 0;
    $returner = [];
    while($iae < count($question_list_send))
    {
        $returner[$iae] = array(
            get_question_text($question_list_send[$iae]),
            base64_decode(get_student_ans($POSTunique_solve_id)[$iae]),
        );
        $iae++;
    }

    $finalshot = array(
        $returner,
        get_auto_grader_comment($POSTunique_solve_id)
    );
    echo json_encode($finalshot);
}




?>
