<?php 
    $POSTunique = $_POST['unique_solve_id'];

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

    $examId = curl_request_input($url . 'fetch_exam_id_by_unique_solve_id.php', array('unique_id' => $POSTunique));

    //get all the answers in base64 (comma seperated), decode them, and place them in a new array ansArr
    $ansArr = [];

    $answers = curl_request_input($url . 'fetch_student_ans_by_unique_id.php', array('unique_id' => $POSTunique));

    foreach (explode(',', $answers) as $i) {
        array_push($ansArr, base64_decode($i));
    }


    //get the question IDs, fetch their corresponding descriptions, and place them in a new array qArr
    $qArr = [];

    $questions = curl_request_input($url . 'fetch_question_list_by_exam_id.php', array('exam_id' => $examId));

    foreach (json_decode($questions,true) as $i) {
        array_push($qArr,curl_request_input($url . 'fetch_question_name_by_question_id.php', array('qid' => $i['qid'])));
    }

    //get the autograder comments by unique solver id
    $auData =  curl_request_input('https://web.njit.edu/~oa227/490/final/back/fetch_auto_grader_comment_by_uniq_exam_id.php', array('unique_id' => $POSTunique));

    //get professor comments by unique_solver_id
    $comments = curl_request_input($url . 'fetch_comments_by_unique_solve_id.php', array('unique_id' => $POSTunique));

    //create a main array and echo that back as one big JSON
    $examDisplayInfo = array(
        'questions' => $qArr,
        'answers' => $ansArr,
        'auData' => json_decode(json_decode($auData,true),true),
        'comments' => json_decode($comments,true)
    );

    echo json_encode($examDisplayInfo);
    
?>
