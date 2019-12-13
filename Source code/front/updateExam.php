<?php 
    $POSTunique = $_POST['unique_solve_id'];
    $POSTcomments = $_POST['comments'];
    $POSTgrades = $_POST['updated_scores'];
    $POSTvisibility = $_POST['visible'];
    $POSTautograder = $_POST['auData'];

    function curl_request_input($url, $input){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $page_result = curl_exec($ch);
        curl_close($ch);
        return $page_result;
    }

    $url = 'https://web.njit.edu/~oa227/490/final/back/student_grade_shower/';

    //send updated visibility
    curl_request_input($url . 'update_visible_by_unique_solve_id.php', array('unique_id' => $POSTunique, 'update_status' => $POSTvisibility));

    //send updated comments
    curl_request_input($url . 'update_comments_by_unique_solve_id.php', array('unique_id' => $POSTunique, 'professor_comments' => $POSTcomments));

    //send updated grades
    curl_request_input($url . 'update_grade_by_unique_solve_id.php', array('unique_id' => $POSTunique, 'update_grade' => $POSTgrades));

    curl_request_input($url . 'update_auto_grader_by_unique_solve_id.php', array('unique_id' => $POSTunique, 'update_grade' => $POSTautograder));
?>
