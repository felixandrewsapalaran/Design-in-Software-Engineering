<?php
require "db.php";
header('Content-Type: application/json;charset=utf-8');
global $con;

$exam_id = $_POST['exam_id'];
$visible = $_POST['visible'];
$grade = $_POST['grade'];
$comments = $_POST['prof_comment'];

$sql = "UPDATE student_ans_exam SET visible_status='".$visible."', grade= '".$grade."' , prof_comments='".$comments."' WHERE uniq_exam_solve_id = '".$exam_id."'";
if ($con->query($sql) === TRUE) 
  $rc = array('return_code'=>'exam updated successfully');
else 
  $rc = array('return_code'=>mysqli_error($con));
      //echo 'inside the script';
echo(json_encode($rc));
?>