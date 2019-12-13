<?php
header('Content-Type: application/json;charset=utf-8');
require 'commands.php';
//get post fields and unserialize data
$POSTdata_toget = $_POST['data'];
$data = unserialize($POSTdata_toget);
/*ARRAY RECIEVED
FIRST INDEX	question index in exam
SECOND INDEX element of arraygrader
		1 - question id
		2 - pt value
		3 - students answer
		4 - array of test cases 	THIRD INDEX
		5 - num test cases
		6 - function name
		7 - array of keywords

checks for empty post field*/
//echo 'hi';
if(empty($POSTdata_toget))
{
    $ert = array(
        'return_code' => 0,
        'error_message' => 'You didnt send any data'
    );
    echo json_encode($ert);
    exit();
}
$exam_points=0;
//calls php exec using student input and test cases(both encoded); grades based on guidlines set in class
function grade_question($points, $ans, $argv, $argc, $function, $kwds)
{
	global $exam_points;
	$answer = base64_decode($ans);
	$questionP = 0;
	$s = [];
	if($argc>0&&$kwds[0]!='')
    $pp = $points/5;
  else if($argc>0&&$kwds[0]=='')
    $pp = $points/4;  
  else
    $pp = $points/3;
	//checking for function name return or print statement and compiler check before testing.
  $compile_check = false;
	if(strpos($answer, $function)!=false)
		$function_check = true;
	else
		$function_check =false;
	if(strpos($answer, 'print')!=false)
		$print_check = true;
	else
		$print_check = false;
	if(strpos($answer, 'return')!=false)
		$return_check = true;
	else
		$return_check = false;
	
	//checks for keywords of concepts
  $keys=-1;
  $keys_hit=[];
  for($i=0;$i<count($kwds);$i++)
  {
      if(strpos($answer,$kwds[$i]))
      {
        $keys++;
        array_push($keys_hit,$kwds[$i]);      
      }	
	}
	//checks for correct output on test cases.
	$cases=0;
	$py_output=[];
 if(!$function_check)
   $answer = clean($answer, $function);
   //echo $answer;
	for($x=0;$x<$argc;$x++)
    {
		$in = base64_decode($argv[$x][0]);
		$out = base64_decode($argv[$x][1]);
		$py = python($answer, $function,$in,$out);
		if ($py['return_code'] == 1)
		{
			$py_output[$x] = array('function'=>$function, 'expected'=> $out, 'result'=>$py['output'], 'points'=>$pp/$argc);
			$cases++;
      $compile_check = true;
		}		
		else
		{
       if($py['output']!='compile_error')
         $compile_check = true;
        $py_output[$x] = array('function'=>$function, 'expected'=> $out, 'result'=>$py['output'], 'points'=>0);
    }
	}
	//adds points to s for each check up to $points
	
	if($function_check)
	{
		$questionP+=$pp;
		array_push($s, $pp);
	}
 else
   {
     array_push($s, 0);
   }

	if($compile_check)
	{
		$questionP+=$pp;
		array_push($s, $pp);
	}
 else
   {
     array_push($s, 0);
   }

	if($return_check&&!$print_check)
	{
		$questionP+=$pp;
		array_push($s, $pp);
	}
 else
   {
     array_push($s, 0);
   }

  if($keys>=0)
	{
		$questionP+=$keys*($pp/count($kwds));
		array_push($s, ((++$keys)*($pp/count($kwds))));
	}
 else
   {
     array_push($s, 0);
   }

	if($argc>0)
	{
		$questionP+=$cases*($pp/$argc);
		array_push($s, ($cases*($pp/$argc)));
  }
  else
   {
     array_push($s, 0);
   }

	$exam_points+=$questionP;
	//sends summary back
	$r = array(
        'function' => $function_check,
        'compile' => $compile_check,
        'print' => $print_check,
		'return' => $return_check,
		'keywords expected' => $kwds,
    'keywords hit' => $keys_hit,
		'test cases passed' => $cases,
		'score' => $s,
		'python' => $py_output
		);
	return $r;
}

/* ---- FUNCTIONS END HERE --- */
$scores=[];
for($i=0;$i<count($data);$i++)
{
	$result = grade_question($data[$i][1], $data[$i][2], $data[$i][3], $data[$i][4], $data[$i][5],$data[$i][6]);
    $scores[$i] = array($result);
}

$returner = array($scores,$exam_points);
echo json_encode($returner);
?>