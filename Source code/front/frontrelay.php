<?php 

    $pageDataSend = array(
        'start_index' => $_POST['start'],
        'end_index' => $_POST['end']
    );

    $qDataSend = array(
        'question' => $_POST["description"],
        'question_type' => $_POST["type"],
        'question_difficulty' => $_POST["difficulty"],
        'question_cases' => $_POST['cases'],
        'question_function' => $_POST['function'],
        'keyword' => $_POST['keywords']
    );

    $examDataSend = array(
        'exam_name' => $_POST["exam_name"],
        'max_points' => $_POST["max_points"],
        'question_list' => $_POST["question_list"],
        'question_points' => $_POST["question_points"]
    );

    $userDataSend = array(
        'userID' => $_POST['userID'],
        'userPass' => $_POST['userPass']
    );

    $studentExamDataSend = array(
        'ucid' => $_POST["ucid"],
        'exam_id' => $_POST["exam_id"],
        'student_ans' => $_POST["student_ans"]
    );

    $queryDataSend = array(
        'question_type'  => $_POST['question_type'],
        'question_difficulty'  => $_POST['question_difficulty'],
        'question_keyword'  => $_POST['question_keyword']
    );

    $examUpdateDataSend = array(
        'visible' => $_POST['visible'],
        'prof_comment' => $_POST['comments'],
        'grade' => $_POST['updated_scores'],
        'exam_id' => $_POST['unique_solver_id']
    );

    $qInfo = array(
        'option_no' => "relay",
        'option_req' => $_POST["option_req"],
        'option_data' => $qDataSend,
    );

    if($_POST['data'] === "exam"){
        $qInfo['option_data'] = $examDataSend;
    }else if($_POST['data'] === "login"){
        $qInfo['option_data'] = $userDataSend;
    }else if($_POST['data'] === "submission"){
        $qInfo['option_data'] = $studentExamDataSend;
    }else if($_POST['data'] === "query"){
        $qInfo['option_data'] = $queryDataSend;
    }else if($_POST['data'] === "page"){
        $qInfo['option_data'] = $pageDataSend;
    }else if($_POST['data'] === "gradedExam"){
        $qInfo['option_data'] = array(
            'unique_solve_id' => $_POST['uniqueId']
        );
    }else if($_POST['data'] === "first_run"){
        $qInfo['option_data'] = array(
            'question_option' => 'first_run'
        );
    }else if($_POST['data'] === "get_range"){
        $qInfo['option_data'] = array(
            'question_option' => 'get_range',
            'get_range' => $_POST['start'] . "-" . $_POST['end']
        );
         
    }else if($_POST['data'] === "update"){
        $qInfo['option_data'] = $examUpdateDataSend;
    }

    $url = 'https://web.njit.edu/~mn398/cs490/final/middle/middle-re.php';

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($qInfo),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);

    if($response === FALSE) {
        echo "Curl error: " . curl_error($ch);
    }

    curl_close($ch);

    echo $response;
?>
