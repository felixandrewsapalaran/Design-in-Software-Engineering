<?php
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');
/*
 sql command: SELECT * FROM `question_bank` LIMIT 0, 2
 */

$POSToption = $_POST['question_option'];
$POSTget_range = $_POST['get_range'];
$POSTdebugger = $_POST['debug'];
/*
 * To call a range, call 'get-range' like this: a-b (if you wanted the first 5 questions, u say: '0-4'
 */

$data = [];

/* DEBUGGER START*/
if(!empty($POSTdebugger))
{
    $debugreturn = array(
        'return_code' => 0,
        'error_message' => 'DEBUGGER CALLED',
        'question_option' => $POSToption,
        'get_range' => $POSTget_range
    );
    echo '<br>';
    echo json_encode($debugreturn);
    echo '<br>';
}

if(empty($POSToption))
{
    $data = array(
        'return_code' => 0,
        'error_message' => '"question_option" is empty',
        'question_option' => $POSToption,
        'get_range' => $POSTget_range,
    );
    echo json_encode($data);
    exit();
}
else
{
    if($POSToption = 'first_run')
    {
        $sqlrunner = "SELECT * FROM `question_bank` LIMIT 0, 9";
        $results = $con->query($sqlrunner);
        if ($results->num_rows > 0)
        {
            while ( $row = $results->fetch_assoc())
            {
                $data[] = $row;
            }
        }
        else
        {
            $data = array(
                'return_code' => 0,
                'error_message' => 'No Questions in the Question Bank'
            );
        }
        echo(json_encode($data));
    }
    elseif($POSToption = 'get_range')
    {
        if(empty($POSTget_range))
        {
            $data = array(
                'return_code' => 0,
                'error_message' => '"get_range" is empty',
                'question_option' => $POSToption,
                'get_range' => $POSTget_range,
            );
            echo json_encode($data);
            exit();
        }
        else
        {
            $ranges = explode('-', $POSTget_range);
            $value1 = $ranges[0];
            $value2 = $ranges[1];
            $sqlrunner = "SELECT * FROM `question_bank` LIMIT " . $value1 . ", " . $value2;

            $results = $con->query($sqlrunner);
            if ($results->num_rows > 0)
            {
                while ( $row = $results->fetch_assoc())
                {
                    $data[] = $row;
                }
            }
            else
            {
                $data = array(
                    'return_code' => 0,
                    'error_message' => 'Exam List Out Of Range'
                );
            }
            echo(json_encode($data));
        }
    }
}
?>