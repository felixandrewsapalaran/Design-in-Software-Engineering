<?php
error_reporting(1);
//echo $_SERVER['DOCUMENT_ROOT'];

function clean($code, $function)
{
  if(strpos($code, $function)!=false)
    return $code;
  else 
    return substr_replace($code, 'def '.$function, 0, strpos($code,'('));
}
function python($code, $function, $inp, $outp)
{
    $result = php_python_execute($code, $function, $inp);
    if($result == $outp)
         $r = array('return_code' => 1, 'output' => $result );
    else
    {
        if(strpos($result,'Error')!=false)
        {
            $r = array(
                'return_code' => 0,
                'output' => 'compile_error');
        }
        else
        {
            $r = array(
                'return_code' => 0,
                'output' => $result
            );
        }
    }
    return $r;
}

function php_python_execute($code, $function, $inp)
{    
    ini_set('track_errors', 1);
    $file = fopen("/afs/cad.njit.edu/u/d/f/oa227/public_html/490/final/back/student_submission.py", 'w');
    if ( !$file ) {
      echo 'fopen failed. reason: ', $php_errormsg;
    }
    
    //Store the input
    fwrite($file, "#!/usr/bin/env python");
    fwrite($file, "\r\n");
    fwrite($file, $code);
    fwrite($file, "\r\n");
    fwrite($file, "\r\n");
    fwrite($file, 'print(' . $function . '(' . $inp . '))');
    fclose($file);
    
    //Run Python Exec &&Store the results
    $cmd = 'python student_submission.py 2>&1';
    $python_output = exec($cmd);
    //echo var_dump($python_output);
    //Delete the file
    unlink('/afs/cad.njit.edu/u/d/f/oa227/public_html/490/final/back/student_submission.py');
    return $python_output;
}

?>