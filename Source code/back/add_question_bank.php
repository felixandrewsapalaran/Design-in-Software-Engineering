<?php
/*
 * Adds a question to the question bank
 */
require "db.php";
header('Content-Type: application/json;charset=utf-8');
global $con;


$question = $_POST['question'];
$q_type = $_POST['question_type'];
$q_difficulty = $_POST['question_difficulty'];
$q_cases = $_POST['question_cases'];
$function_name = $_POST['question_function'];
$keyword = $_POST['keyword'];

$add_question = $con->query("
INSERT INTO `question_bank` ( `question`, `q_type`, `function_name`, `q_difficulty`, `q_cases`, `keyword`) VALUES ('$question', '$q_type', '$function_name', '$q_difficulty', '$q_cases', '$keyword')
");

if(!empty($question) || !empty($q_type) || !empty($q_difficulty) || !empty($q_cases) || !empty($function_name) || !empty($keyword))
{
  /*BONUS: Check if Question exist */
    if($add_question)
    {
        $return = array(
            'return_code' => 1,
            'message' => 'Your question was added successfully!',
            'question_added' => $question,
            'question_type_added' => $q_type,
            'q_difficulty_added' => $q_difficulty,
            'q_cases_added' => $q_cases,
            'function_name' => $function_name,
            'keyword' => $keyword
        );
    }
    else
    {
        $return = array(
          'return_code' => -1,
          'error_message' => mysqli_error($con)
		  //'error_message' => "test"
        );
    }
}
else
{
        $return = array(
          'return_code' => 0,
          'error_message'  => 'One of the POST fields were empty',
          'question_added' => $question,
          'question_type_added' => $q_type,
          'q_difficulty_added' => $q_difficulty,
          'q_cases_added' => $q_cases,
          'function_name' => $function_name,
           'keyword' => $keyword
        );
}

echo(json_encode($return));

?>
