<?php
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$POSTquestion_difficulty = $_POST['question_difficulty'];
$POSTquestion_type = $_POST['question_type'];
$POSTquestion_keyword = $_POST['question_keyword'];
$POSTdebug = $_POST['debug'];

if(!empty($POSTdebug))
{
    $debugk = array(
        'return_code' => -1,
        'error_message' => 'DEBUG TRIGGERED',
        'question_difficulty' => $POSTquestion_difficulty,
        'question_type' => $POSTquestion_type,
        'question_keyword' => $POSTquestion_keyword,
        'debug' => $POSTdebug
    );
    echo json_encode($debugk);
}

if(empty($POSTquestion_difficulty))
{
    $data = array(
        'return_code' => 0,
        'error_message' => 'Question Difficulty Field Is Empty',
        'question_difficulty' => $POSTquestion_difficulty,
        'question_type' => $POSTquestion_type,
        'question_keyword' => $POSTquestion_keyword,
    );
}
elseif(empty($POSTquestion_type))
{
    $data = array(
        'return_code' => 0,
        'error_message' => 'Question Type Field Is Empty',
        'question_difficulty' => $POSTquestion_difficulty,
        'question_type' => $POSTquestion_type,
        'question_keyword' => $POSTquestion_keyword,
    );
}
else
{
    if(empty($POSTquestion_keyword))
    {
        if($POSTquestion_type == 'All' && $POSTquestion_difficulty == 'All')
        {
            $sql = "SELECT * FROM `question_bank`";
        }
        elseif ($POSTquestion_type == 'All')
        {
            $sql = "SELECT * FROM `question_bank` WHERE `q_difficulty` = '$POSTquestion_difficulty'";
        }
        elseif ($POSTquestion_difficulty == 'All')
        {
            $sql = "SELECT * FROM `question_bank` WHERE `q_type` = '$POSTquestion_type'";
        }
        else
        {
            $sql = "SELECT * FROM `question_bank` WHERE `q_type` = '$POSTquestion_type' AND `q_difficulty` = '$POSTquestion_difficulty'";
        }

    }
    else
    {
        if($POSTquestion_type =='All' && $POSTquestion_difficulty == 'All')
        {
            $sql = "SELECT * FROM `question_bank` WHERE `question` LIKE '%$POSTquestion_keyword%'";
        }
        elseif ($POSTquestion_type == 'All')
        {
            $sql = "SELECT * FROM `question_bank` WHERE `q_difficulty` = '$POSTquestion_difficulty' AND `question` LIKE '%$POSTquestion_keyword%'";
        }
        elseif ($POSTquestion_difficulty == 'All')
        {
            $sql = "SELECT * FROM `question_bank` WHERE `q_type` = '$POSTquestion_type' AND `question` LIKE '%$POSTquestion_keyword%'";
        }
        else
        {
            $sql = "SELECT * FROM `question_bank` WHERE `q_type` = '$POSTquestion_type' AND `q_difficulty` = '$POSTquestion_difficulty' AND `question` LIKE '%$POSTquestion_keyword%'";
        }
    }

    $results = $con->query($sql);
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
            'error_message' => 'Question Keyword Not Found',
            'query' => $sql,
            'question_difficulty' => $POSTquestion_difficulty,
            'question_type' => $POSTquestion_type,
            'question_keyword' => $POSTquestion_keyword,
        );
    }
}
echo(json_encode($data));
?>