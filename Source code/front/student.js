var qList;
var examList;
var examQuestions;

function openTab(evt, tabName) {

    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tabcontent");

    for(i=0; i < tabcontent.length; i++){
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";

}

document.getElementsByClassName('tablinks')[0].click();

function getQList(){
    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){
            qList = JSON.parse(this.responseText);
        }
    }

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.send('data=first_run&option_req=list_question_bank');

}

function getUntakenExams(){
    const xhr = new XMLHttpRequest();
    document.getElementById('viewExams').style.display = 'block';
    const examTable = document.getElementById('untakenExams').getElementsByTagName('tbody')[0];

    xhr.onload = function(){
        if(this.status == 200){
            examList = JSON.parse(this.responseText);
            examTable.innerHTML = "";
            var row;

            if(examList['return_code'] == 0){
                document.getElementById('examAvailabilityStatus').innerHTML = 'No exams are avaiable to take at this time';
            }


            getQList();


            for(var i = 0; i < examList.length; i++){
                row = examTable.insertRow(i);
                row.insertCell(0).innerHTML = `<button class="examButton" onClick="takeExam(${i})">${examList[i]['exam_name']}</button>`;
            }
             
        }
    }

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.send('option_req=fetch_all_untaken_exams');

}

function takeExam(examIndex){
    //clear screen and have student take exam
    tabcontents = document.getElementsByClassName('tabcontent');
    for(i = 0; i < tabcontents.length; i++){
        tabcontents[i].style.display = 'none';
    }
    
    //populate examQuestions table with questions from the test      

    document.getElementById('examPage').style.display = 'block';

    const currentExam = document.getElementById('currentExam').getElementsByTagName('tbody')[0];
    currentExam.innerHTML = '';
    var row;

    const examTitle = document.getElementById('currentExam').getElementsByTagName('caption')[0].innerHTML = `Exam Name: ${examList[examIndex]['exam_name']}`; 

    const examQids = JSON.parse(examList[examIndex]['question_list']);
    const examPvals = JSON.parse(examList[examIndex]['question_points']);

    examQuestions = [];

    for(i = 0,j = 0; i < qList.length && j < examQids.length; i++){
        if(qList[i]['qid'] == examQids[j]['qid']) {
            examQuestions.push(qList[i]);
            j++;
        }
    }
    
    for(i = 0; i < examQuestions.length; i++) {
        row = currentExam.insertRow(i);
        row.insertCell(0).innerHTML = `<b>${examPvals[i]['point_value']} points</b> <br> <p class="examQuestions" style="width:400px">${examQuestions[i]['question']}</p>`;
        row.insertCell(1).innerHTML = `<textarea class="answers" style="width:600px" rows="8"></textarea>`;
    }

    //create the submit and back buttons
        
    row = currentExam.insertRow(examQuestions.length);
    row.insertCell(0).innerHTML = `<button id="examBackBtn" onclick="getUntakenExams();refreshDisplay()">Back</button>`;
    row.insertCell(1).innerHTML = `<button id="examSubmitBtn" onclick="submitExam(${examIndex})">Submit</button>`;
    
    //make it such that tabs work as intended in a text area
    var textareas = document.getElementsByTagName('textarea');
    var count = textareas.length;
    for(var i=0;i<count;i++){
        textareas[i].onkeydown = function(e){
                if(e.keyCode==9 || e.which==9){
                            e.preventDefault();
                            var s = this.selectionStart;
                            this.value = this.value.substring(0,this.selectionStart) + "\t" + this.value.substring(this.selectionEnd);
                            this.selectionEnd = s+1; 
                        }
            }
    }

}

function submitExam(examIndex){
    const answerElems = document.getElementsByClassName('answers');
    var answers = [];
    
    for(i = 0; i < answerElems.length; i++){
        answers.push(btoa(answerElems[i].value));
    }

    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){
        
            console.log(this.responseText);
            
            location.href = "student.html";
        }
    };

    console.log(answers);

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhrData = `data=submission&ucid=stud&exam_id=${examList[examIndex]['exam_id']}&student_ans=${answers}&option_req=submit_exam`;

    console.log(xhrData);

    xhr.send(xhrData);
}

function getGradedExams(){
    const xhr = new XMLHttpRequest();
    document.getElementById('viewGrades').style.display = 'block';

    xhr.onload = function(){
        if(this.status == 200){
            console.log(JSON.parse(this.responseText));
            const examDisplayData = JSON.parse(this.responseText);
            examList = JSON.parse(this.responseText)['exams'];
            gradedTable = document.getElementById('gradedExamTable').getElementsByTagName('tbody')[0];

            gradedTable.innerHTML = "";

            if(examList['return_code'] == 0){
                document.getElementById('gradedExamAvailabilityStatus').innerHTML = 'No Exams have been released by your professor at this time.';
            }else{
                document.getElementById('gradedExamAvailabilityStatus').innerHTML = '';
            }

            console.log(examList);

            for(i = 0; i < examList.length; i++){
                var totalgrade = 0;
                if(examList[i]['grade'] == ""){
                    continue; 
                }
                var examPointArr = JSON.parse(examList[i]['grade']);
                for(j = 0; j < examPointArr.length; j++){
                    var qPointArr = examPointArr[j];
                    for(k = 0; k < qPointArr.length; k++){
                        //each element k refers to the point addition itself
                        totalgrade +=  qPointArr[k];
                    }
                }
                row = gradedTable.insertRow(i);
                row.insertCell(0).innerHTML = `${examDisplayData['names'][i]}`
                row.insertCell(1).innerHTML = `${examList[i]['ucid']}`
                row.insertCell(2).innerHTML = `${totalgrade}/${examDisplayData['maxPoints'][i]}`
                row.insertCell(3).innerHTML = `<button onclick="displayExam(examList[${i}])">View Exam</button>`
            }
        }

    }

    xhr.open('POST','getVisibleExams.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhr.send();
}

function displayExam(e){
    document.getElementById('viewGrades').style.display = 'none';
    document.getElementById('reviewBackBtn').style.display = 'inline-block';
    document.getElementById('reviewPage').style.display = 'block';


    console.log(e);
    console.log(e['uniq_exam_solve_id']);

    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){
            const currentExam = document.getElementById('currentGradedExam').getElementsByTagName('tbody')[0];
            const examData = JSON.parse(this.responseText);

            console.log(examData);

            //popuate currentExam table

            for(i = 0; i < examData['questions'].length; i++){
                var row = currentExam.insertRow(i);
                var question = row.insertCell(0);
                var answer = row.insertCell(1);
                var feedback = row.insertCell(2);
                // var comments = row.insertCell(3);

                question.innerHTML = `<p>${examData['questions'][i]}</p>`;
                answer.innerHTML = `<textarea readonly placeholder="Enter a comment here." cols="30" rows="16">${examData['answers'][i]}</textarea>`;
                feedback.innerHTML = 
                `<table class="feedbackTable">
                     <tbody>
                     </tbody>
                </table>`;


                //feedback will be a table that is added to

                // comments.innerHTML = `<textarea class="profComments" cols="30" rows="6"></textarea>`;

                question.style.width = '25%';
                answer.style.width = '25%';
                feedback.style.width = '50%';


                const auData = examData['auData'][i][0];
                const testCases = auData['python'];

                //display autograde details and allow editing of each addition of points
                
                k = 0;
                const feedbackTable = document.getElementsByClassName('feedbackTable')[i];
                var feedbackRow = feedbackTable.insertRow(k);

                //check function declaration
                if(auData['function'] == true){
                    feedbackRow.insertCell(0).innerHTML = `Function declared correctly. +${auData['score'][0]} points`;
                }else{
                    feedbackRow.insertCell(0).innerHTML = `Function declared incorrectly. +${auData['score'][0]} points`;
                }
                
                k = 1;
                var feedbackRow = feedbackTable.insertRow(k);
                //check if the program compiles
                if(auData['compile'] == true){
                    feedbackRow.insertCell(0).innerHTML = `Program compiles successfully. +${auData['score'][1]} points.` 
                }else{
                    feedbackRow.insertCell(0).innerHTML = `Program does not compile. +${auData['score'][1]} points.` 
                }

                k = 2;
                var feedbackRow = feedbackTable.insertRow(k);
                //check if there is a print statement
                if(auData['print'] == true){
                    feedbackRow.insertCell(0).innerHTML = `Values are printed, not returned. +${auData['score'][2]} points.` 
                }else{
                    feedbackRow.insertCell(0).innerHTML = `Values are returned properly. +${auData['score'][2]} points.` 
                }

                k = 3;
                var feedbackRow = feedbackTable.insertRow(k);
                //check constraints
                if(auData['keywords expected'][0] == ""){
                    feedbackRow.insertCell(0).innerHTML = `No constraints specified.`;
                }else{
                    var constraints = feedbackRow.insertCell(0);
                    constraints.innerHTML = `Constraints specified: `;
                    for(l = 0; l < auData['keywords expected'].length; l++){
                        constraints.innerHTML +=  `<b>${auData['keywords expected'][l]}</b>` + " ";
                    }
                    constraints.innerHTML += `<br/>`;
                    constraints.innerHTML += `Contraints met: `
                    for(l = 0; l < auData['keywords hit'].length; l++){
                        constraints.innerHTML +=  `<b>${auData['keywords hit'][l]}</b>` + " ";
                    }
                }

                k = 4;
                //check test cases
                for(j = 0; j < testCases.length; j++,k++){
                    var feedbackRow = feedbackTable.insertRow(k);
                    feedbackRow.insertCell(0).innerHTML = `<b>Test Case ${j+1}</b><br/><b>Expected:</b> ${testCases[j]['expected']}&emsp;&ensp;<b>Received:</b> ${testCases[j]['result']}<br/>+${testCases[j]['points']} points`
                }

                //professor comments
                if(examData['comments'][i] == ""){
                    feedbackTable.insertRow(k).innerHTML = `<p class="profComments">Your professor left no comments.</p>`
                }else{
                    feedbackTable.insertRow(k).innerHTML = `<p class="profComments">${examData['comments'][i]}</p>`
                
                }

            }
        }
    };

    xhr.open('POST','viewExam.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const xhrData=`unique_solve_id=${e['uniq_exam_solve_id']}&option_req=fetch_unique_student_exam`;

    xhr.send(xhrData);

}

function redisplayGrades(){
   document.getElementById('reviewPage').style.display = 'none';
   document.getElementById('reviewBackBtn').style.display = 'none';
   document.getElementById('viewGrades').style.display = 'block';

   getGradedExams();

}

function logout(){
    location.href = "index.html";
}

function refreshDisplay(){
    document.getElementById('examPage').style.display = '';
    document.getElementById('reviewPage').style.display = '';
}

