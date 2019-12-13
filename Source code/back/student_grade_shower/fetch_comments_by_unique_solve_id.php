<?php
require "../db.php";
$POSTunique_id = $_POST['unique_id'];

global $con;
$sql = "SELECT `prof_comments` FROM `student_ans_exam` WHERE `uniq_exam_solve_id` = $POSTunique_id";
$data = [];

$results = $con->query($sql);

if(empty($POSTunique_id))
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'You sent an empty unique_solve_id',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
elseif(is_numeric($POSTunique_id) == false)
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'The unique ID you sent is not numeric',
        'unique_id' => $POSTunique_id
    );
    echo json_encode($errk);
    exit();
}
else
{
    if ($results->num_rows > 0)
    {
        while ($row = $results->fetch_assoc())
        {
            $data[] = $row;
        }
    }
    echo $data[0]['prof_comments'];
}

?>