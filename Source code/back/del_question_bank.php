<?php
/*
 * Adds a question to the question bank
 */
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$question_id = $_POST['question_id'];

$remove_question = $con->query("
DELETE FROM `question_bank` WHERE `qid` = $question_id 
");

if(!empty($question_id))
{
    if(is_numeric($remove_question)
    {
    	if($remove_question)
    	{
	        $return = array(
	            'return_code' => 1,
	            'message' => 'Your question was added successfully!',
	        );
    	}
    	else
    	{
	    	$return = array(
	          'return_code' => -1,
	          'error_message' => $con->errorInfo()
	        );
    	}
    }
    else
    {
        $return = array(
          'return_code' => -1,
          'error_message' => 'Question ID Passed Is Not A Numerical Value'
        );
    }
}
else
{
        $return = array(
          'return_code' => 0,
          'error_message'  => 'One of the POST fields were empty',
          'question_id' => $question_id,
        );
}



?>
