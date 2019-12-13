<?php
require 'commands.php';
header('Content-Type: application/json;charset=utf-8');

$POSToption_no = $_POST['option_no'];
$POSToption_req = $_POST['option_req'];
$POSToption_data = $_POST['option_data'];

if($POSToption_no == 'relay')
{
    //relay stuff
    if($POSToption_req == 'add_question')
    {
        		if(!empty($POSToption_data))
        		{
				
        			echo add_question_bank($POSToption_data);
        		}
    		  else
            {
                $errk = array(
                    'return_code' => 0,
                    'error_message' => 'POSTOption data field is empty!',
                    'option_no' => $POSToption_no,
                    'option_req' => $POSToption_req,
                    'option_data' => $POSToption_data
                );
                echo json_encode($errk);
                exit();
            }
    }
    elseif ($POSToption_req == 'login')
    {
            if(!empty($POSToption_data))
        		{
        			echo login($POSToption_data);
        		}
    		    else
            {
                $errk = array(
                    'return_code' => 0,
                    'error_message' => 'POSTOption data field is empty!',
                    'option_no' => $POSToption_no,
                    'option_req' => $POSToption_req,
                    'option_data' => $POSToption_data
                );
                echo json_encode($errk);
                exit();
            }
    }
    elseif ($POSToption_req == 'list_question_bank')
    {
        echo fetch_question_bank($POSToption_data);
    }
    elseif ($POSToption_req == 'fetch_question_query')
    {
        echo fetch_question_query($POSToption_data);
    }
    /*---- Exam Commands Part---- */
    elseif ($POSToption_req == 'create_exam')
    {
        echo create_exam($POSToption_data);
    }
    elseif ($POSToption_req == 'fetch_all_exams')
    {
        echo fetch_all_exams();
    }
	elseif ($POSToption_req == 'fetch_exam_name_by_id')
    {
        echo fetch_exam_name_by_id($POSToption_data);
    }
    elseif ($POSToption_req == 'fetch_exam_by_id')
    {
        echo fetch_exam_id($POSToption_data);
    }
    elseif ($POSToption_req == 'fetch_all_untaken_exams')
    {
        echo fetch_all_untaken_exams();
    }
    elseif ($POSToption_req == 'fetch_all_graded_exams')
    {
        echo fetch_all_graded_exams();
    }
    /*----Exam Commands End -----*/
    elseif($POSToption_req == 'create_account')
    {
        echo create_account($POSToption_data);
    }
    elseif($POSToption_req == 'submit_exam')
    {
       echo submit_exam($POSToption_data);
    }
	elseif($POSToption_req == 'fetch_unique_student_exam')
    {
       echo fetch_unique_student_exam($POSToption_data);
    }
    elseif($POSToption_req == 'update_exam')
    {
        echo update_exam($POSToption_data);
    }
    elseif (empty($POSToption_req))
    {
        echo 'You didnt set anything for POSTOption req. ';
        debugoutput();
    }
    else
    {
        $errk = array(
            'return_code' => 0,
            'error_message' => 'You Didnt Set Anything for POSTOption Req or Its Invalid',
            'option_no' => $POSToption_no,
            'option_req' => $POSToption_req,
            'option_data' => $POSToption_data
        );
        echo json_encode($errk);
        exit();
    }
}
else
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'You Didnt Set Anything for POSTOption Req or Its Invalid',
        'option_no' => $POSToption_no,
        'option_req' => $POSToption_req,
        'option_data' => $POSToption_data
    );
    echo json_encode($errk);
    exit();
}

?>
