<?php
/*
 * Fetches all the questions in the question bank
 */
require "db.php";
global $con;
header('Content-Type: application/json;charset=utf-8');

$sql = "select * from question_bank";
$result = $con->query($sql);
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'qid' => $row["qid"],
            'question' => $row["question"],
            'q_type' => $row["q_type"],
            'q_difficulty' => $row["q_difficulty"],
            'q_cases' => $row["q_cases"]
        );
    }
} else {
    $data = array('error_message' => 'There are no questions in the question bank. Please add a question.');
}
echo(json_encode($data));

?>