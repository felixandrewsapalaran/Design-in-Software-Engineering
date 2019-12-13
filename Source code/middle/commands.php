<?php
error_reporting(1);

$backend_url = 'https://web.njit.edu/~oa227/490/final/back/';
$gradeurl = './pywrite/';

//Done
function login($postreq)
{
    global $backend_url;
    $add_q_b_curl = curl_request_input($backend_url . "login.php", $postreq);
    return $add_q_b_curl;
}
//Done
function add_question_bank($question_input)
{
    global $backend_url;
    $add_q_b_curl = curl_request_input($backend_url . "add_question_bank.php", $question_input);
    return $add_q_b_curl;
}
//Done
function fetch_question_bank($info)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_question_bank.php", $info);
    return $fetch_q_b;
}
function fetch_question_query($query_info)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_question_by_specific.php", $query_info);
    return $fetch_q_b;
}
//Done
function create_exam($exam_data)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "create_exam.php", $exam_data);
    return $fetch_q_b;
}
//Done
function fetch_all_exams()
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_all_exams.php");
    return $fetch_q_b;
}
//(Maybe) Done but not sure if needed
function fetch_exam_id($exam_id_info)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_exam_id.php", $exam_id_info);
    return $fetch_q_b;
}
function fetch_unique_student_exam($exam_id_info)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_unique_student_exam.php", $exam_id_info);
    return $fetch_q_b;
}
//Done
function fetch_all_untaken_exams()
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_all_untaken_exams.php");
    return $fetch_q_b;
}

function fetch_all_graded_exams()
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_all_graded_exams.php");
    return $fetch_q_b;
}

function fetch_exam_name_by_id($exam_id_number)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "fetch_exam_name_by_id.php", $exam_id_number);
    return $fetch_q_b;
}

function submit_exam($student_info)
{
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "submit_exam.php", $student_info);
    return $fetch_q_b;
}

function update_exam($update_info){
    global $backend_url;
    $fetch_q_b = curl_request_input($backend_url . "update_exam.php", $update_info);
    return $fetch_q_b;
}

function send_to_auto_grader($data)
{
    global $gradeurl;
    $fetch_q_b = curl_request_input("auto_grader.php", $data);
    return $fetch_q_b;
}

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

/*-------- Student Show -------------- */
/*
function data($)
{
    global $gradeurl;
    $fetch_q_b = curl_request_input("auto_grader.php", $data);
    return $fetch_q_b;
}
*/


/*   ---- AUTO GRADER ---- */
function clean($code, $function)
{
  if(strpos($code, $function)!=false)
    return $code;
  else 
    return substr_replace($code, 'def '.$function, 0, strpos($code,'('));
}
function python($code, $function, $inp, $outp)
{
    $result = php_python_execute($code, $function, $inp);
    if($result == $outp)
         $r = array('return_code' => 1, 'output' => $result );
    else
    {
        if(strpos($result,'Error')!=false)
        {
            $r = array(
                'return_code' => 0,
                'output' => 'compile_error');
        }
        else
        {
            $r = array(
                'return_code' => 0,
                'output' => $result
            );
        }
    }
    return $r;
}

function php_python_execute($code, $function, $inp)
{    
    ini_set('track_errors', 1);
    $file = fopen("/afs/cad.njit.edu/u/d/f/oa227/public_html/490/final/back/student_submission.py", 'w');
    if ( !$file ) {
      echo 'fopen failed. reason: ', $php_errormsg;
    }
    
    //Store the input
    fwrite($file, "#!/usr/bin/env python");
    fwrite($file, "\r\n");
    fwrite($file, $code);
    fwrite($file, "\r\n");
    fwrite($file, "\r\n");
    fwrite($file, 'print(' . $function . '(' . $inp . '))');
    fclose($file);
    
    //Run Python Exec &&Store the results
    $cmd = 'python student_submission.py 2>&1';
    $python_output = exec($cmd);
    //echo var_dump($python_output);
    //Delete the file
    unlink('/afs/cad.njit.edu/u/d/f/oa227/public_html/490/final/back/student_submission.py');
    return $python_output;
}

?>