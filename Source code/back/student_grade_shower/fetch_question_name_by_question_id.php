<?php
require "../db.php";

$POSTqid = $_POST['qid'];

global $con;
$sql = "SELECT `question` FROM `question_bank` WHERE `qid` = $POSTqid";
$data = [];

$results = $con->query($sql);
if ($results->num_rows > 0)
{
    while ($row = $results->fetch_assoc())
    {
        $data[] = $row;
    }
}
echo $data[0]['question'];


?>