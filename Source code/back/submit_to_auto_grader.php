<?php
require "db.php";
require "commands.php";
global $con;
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;charset=utf-8');


$POSTucid = $_POST['ucid'];
$POSTexam_id = $_POST['exam_id'];
//$POSTexam_id = 41;
//$POSTucid = 'stud';



/* ----- FUNCTION START HERE -----*/
function get_exam_max_points($exam_id)
{
	global $con;
	if ($result = $con->query("SELECT `max_points` FROM `exam_list` WHERE `exam_id` = $exam_id"))
	{
		while ($row = $result->fetch_row()) 
		{
			$answ = $row[0];
		}
		$result->close();
	}
	return $answ;
}
//Fixed
function get_function_name($question_id)
{
	global $con;
	if ($result = $con->query("SELECT `function_name` FROM `question_bank` WHERE `qid` = $question_id"))
	{
		while ($row = $result->fetch_row()) 
		{
			$answ = $row[0];
		}
		$result->close();
	}
	return $answ;
}
function get_keywords($question_id)
{
global $con;
 	if ($result = $con->query("SELECT `keyword` FROM `question_bank` WHERE `qid` = $question_id"))
	{
 $answ=[];
		while ($row = $result->fetch_row()) 
		{
			array_push($answ,$row[0]);
		}
		$result->close();
	}
	return $answ;
}

//Fixed
function get_q_cases($question_id)
{
	global $con;
	if ($result = $con->query("SELECT `q_cases` FROM `question_bank` WHERE `qid` = $question_id "))
	{
		while ($row = $result->fetch_row()) 
		{
			$answ = $row[0];
		}
		$result->close();
	}
	return $answ;
}

function get_question_points($exam_id)
{
	global $con;
	if ($result = $con->query("SELECT `question_points` FROM `exam_list` WHERE `exam_id` = $exam_id"))
	{
		while ($row = $result->fetch_row()) 
		{
			$answ = $row[0];
		}
		$result->close();
	}
	return $answ;
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

function get_student_answer($ucid, $exam_id)
{
	global $con;
	if ($result = $con->query("SELECT `student_ans` FROM `student_ans_exam` WHERE `ucid` = '$ucid' AND `exam_id` = $exam_id "))
	{
		while ($row = $result->fetch_row()) 
		{
			$answ = $row[0];
		}
		$result->close();
	}
	return $answ;
}

function arrayify_numerical($input)
{
    //To get the exam questions into an array. Putting TRUE removes anything thats not a numerical value
    $inputpos = $input;
    $inputpos  = preg_replace("/[^0-9,-]/", "", $inputpos);
    $explodeintoarray = explode(",", $inputpos);
    return $explodeintoarray;
}

function arrayify_num_let($input)
{
    $inputpos = $input;
    $explodeintoarray = explode(",", $inputpos);
    return $explodeintoarray;
}


function arrayify_input_output($input)
{
    /*
     * Note to front end: Make detection where one pair of input/output is just "Input,Output"
     * but if there are two pairs, its "Input1,Output1;Input2,Output2"
     */
    $inputpos = $input;
    $explodeintoarray1 = explode(";", $inputpos);
    for($i=0;$i<count($explodeintoarray1);$i++)
    {
        $explodeintoarray2[$i] = explode(",", $explodeintoarray1[$i]);
    }
    $explcount = count($explodeintoarray2);
    if(empty($explodeintoarray2[$explcount-1]))
    {
        //unset($explodeintoarray2[$explcount-1]);
        array_pop($explodeintoarray2);
    }
    return $explodeintoarray2;
}
function updateStatus($exam_id_num, $ucid_usr, $graderStatus)
{
    $sql = "UPDATE `student_ans_exam` SET `auto_grader_status` = '$graderStatus' WHERE `student_ans_exam`.`ucid` = '$ucid_usr' AND `student_ans_exam`.`exam_id` = $exam_id_num";
}
/* ----- FUNCTION END HERE -----*/
/* --------- SUBMISSION HERE ------------ */
//Start preparing it

$question_list_send = arrayify_numerical(get_question_list($POSTexam_id));
$question_points_send = arrayify_numerical(get_question_points($POSTexam_id));
$student_ans_send = arrayify_num_let(get_student_answer($POSTucid, $POSTexam_id));


if(empty($POSTexam_id))
{
    $failrequest = array(
        'return_code' => 0,
        'error_message' => 'Exam ID field is empty',
        'exam_id' => $POSTexam_id,
        'ucid' => $POSTucid
    );
    echo json_encode($failrequest);
    exit();
}
elseif(empty($POSTucid))
{
    $failrequest = array(
        'return_code' => 0,
        'error_message' => 'UCID field is empty',
        'exam_id' => $POSTexam_id,
        'ucid' => $POSTucid
    );
    echo json_encode($failrequest);
    exit();
}
else
{
    // Pack up all the answers into an array to send to the auto grader
    for($i=0;$i<count($question_list_send);$i++)
    {
		// Send Question Number,
        // Send Question Point,
        // Send Student Answer (B64 encoded),
        // Send Input,
        // Send Expected Output
        $question_case_send = arrayify_input_output(get_q_cases($question_list_send[$i]));
        $question_function_name = (get_function_name($question_list_send[$i]));
        $arraygrader[$i] = array
        (
            $question_list_send[$i],
            $question_points_send[$i],
            $student_ans_send[$i],
            $question_case_send,
            count($question_case_send),
            $question_function_name,
            get_keywords($question_list_send[$i])
        );
        //echo var_dump(get_keywords($question_list_send[$i]));
    }

    $curl = array('data' => serialize($arraygrader));
    $graderURL = 'https://web.njit.edu/~oa227/490/final/back/auto_grader.php';
    
    //Send to the Auto Grader
    try
    {
      $ch = curl_init($graderURL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLINFO_HEADER_OUT, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $curl);
      $comments = (curl_exec($ch));
      //echo $comments;
     if ($comments === false) 
     {
       throw new Exception(curl_error($ch), curl_errno($ch));
     }
      $temp = json_decode($comments, true);
      $grades=[];
      //echo json_encode($temp[0]);
      foreach($temp[0] as $x)
      {
        //echo var_dump($x[0]);
        array_push($grades,$x[0]['score']);
      }
      
      $sql = "UPDATE student_ans_exam SET auto_grader_comments='".json_encode($temp[0])."',auto_grader_status=1, grade='".json_encode($grades)."' WHERE ucid= '".$POSTucid."' AND exam_id='".$POSTexam_id."'";
      if ($con->query($sql) === TRUE)
      {
          $rc = array('return_code'=>'comments stored successfully');
      }
      else
      {
          $rc = array('return_code'=>mysqli_error($con));
      }
      echo(json_encode($rc));
      
      // Close curl handle
      curl_close($ch);
    } 
    catch(Exception $e) 
    {
      trigger_error(sprintf('Curl failed with error #%d: %s',$e->getCode(), $e->getMessage()),E_USER_ERROR);
      echo('Did not reach auto grader');
    }
}
?>