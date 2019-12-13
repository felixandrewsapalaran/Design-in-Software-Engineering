<?php
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$data = [];
$select_completed_exam_query = "SELECT * FROM `exam_list` WHERE `exam_id` IN (SELECT `exam_id` FROM `student_ans_exam`)";
$results = $con->query($select_completed_exam_query);

/* */
function get_exam_grade($exam_name)
{
    global $con;
    $data2 = [];
    $select_completed_exam_query2 =
        "SELECT `grade` FROM `student_ans_exam` WHERE `exam_id` IN (SELECT `exam_id` FROM `exam_list` WHERE `exam_name` = '$exam_name')";
    $results2 = $con->query($select_completed_exam_query2);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    return $data2[0]['grade'];
}

function get_max_points($exam_name)
{
    global $con;
    $data2 = [];
    $select_completed_exam_query2 =
        "SELECT `max_points` FROM `exam_list` WHERE `exam_name` = '$exam_name'";
    $results2 = $con->query($select_completed_exam_query2);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    return $data2[0]['max_points'];
}

function get_question_points($exam_name)
{
    global $con;
    $data2 = [];
    $select_completed_exam_query2 =
        "SELECT `question_points` FROM `exam_list` WHERE `exam_name` = '$exam_name'";
    $results2 = $con->query($select_completed_exam_query2);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    return $data2[0]['question_points'];
}

function get_unique_exam_id($exam_name)
{
    global $con;
    $data2 = [];
    $select_completed_exam_query2 =
        "
SELECT `uniq_exam_solve_id` FROM `student_ans_exam` WHERE `exam_id` = (SELECT `exam_id` FROM `exam_list` WHERE `exam_name` = '$exam_name') 
";
    $results2 = $con->query($select_completed_exam_query2);
    if ($results2->num_rows > 0)
    {
        while ($row = $results2->fetch_assoc())
        {
            $data2[] = $row;
        }
    }
    return $data2[0]['uniq_exam_solve_id'];
}


if ($results->num_rows > 0)
{
    while ( $row = $results->fetch_assoc())
    {
        $data[] = $row;
    }
}
else
{
    $errk = array(
        'return_code' => 0,
        'error_message' => 'There are no graded exams in the database'
    );
    echo json_encode($errk);
    exit();
}

$ia = 0;
while($ia < count($data))
{
    $arrayqret[$ia] = array(
        'unique_exam_solver_id' => get_unique_exam_id($data[$ia]['exam_name']),
        'exam_name' => $data[$ia]['exam_name'],
        'ucid' => 'stud',
        'grade' => get_exam_grade($data[$ia]['exam_name']),
        'get_exam_max_points' => get_max_points($data[$ia]['exam_name'])
    );
    $ia++;
}
echo json_encode($arrayqret);
//echo json_encode($data);

?>