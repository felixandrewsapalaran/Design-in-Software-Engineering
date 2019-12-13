<?php 

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

    $url = 'https://web.njit.edu/~oa227/490/final/back/';;
    
    $exams = curl_request_input($url . 'fetch_all_visible_exams.php');

    $names = [];

    $maxPoints = [];

    $it = json_decode($exams,true);
    for($i=0;$i<count($it);$i++){
        $unique_id = $it[$i]['uniq_exam_solve_id'];
        $exam_id = $it[$i]['exam_id'];
        array_push($names,curl_request_input($url . 'student_grade_shower/fetch_exam_name_by_unique_solve_id.php', array('unique_id' => $unique_id)));
        array_push($maxPoints, json_decode(curl_request_input($url . 'student_grade_shower/fetch_max_points_by_exam_id.php', array('exam_id' => $exam_id)),true));
    }

    $visibleExamData = array(
        'exams' => json_decode($exams,true),
        'names' => $names,
        'maxPoints' => $maxPoints
    );

    echo json_encode($visibleExamData);

?>
