//test
var qBank;

const pageLen = 10;
var pageNum = [1,1];
var qIndexStart = [0,0];
var qIndexEnd = [pageLen,pageLen];

const qBtns = {
    next: document.getElementsByClassName('nextBtn'),
    prev: document.getElementsByClassName('prevBtn'),
    pNum: document.getElementsByClassName('pageNumDisplay')
};

for (var i = 0; i < qBtns.pNum.length; i++) {
    qBtns.pNum[i].innerHTML = `${pageNum[i]}`;
}

var total = Number(0);

const maxPointInput = document.getElementById('maxPoints');
const pointTotal = document.getElementById('pointTotal');
const pointMessage = document.getElementById('pointMessage');

var maxPoints = maxPointInput.value;
maxPointInput.addEventListener('input',updateMaxPoints);
pointTotal.innerHTML = `${total}/${maxPoints}`;


function openTab(evt, tabName) {

    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tabcontent");

    for(i=0; i < tabcontent.length; i++){
        tabcontent[i].style.display = "none";
    }

    tablinks = document.getElementsByClassName("tablinks_t");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

document.getElementsByClassName('tablinks_t')[2].click();

//Question data
var qData = {
    type: document.getElementById('qType'),
    diff: document.getElementById('qDiff'),  
    desc: document.getElementById('qDesc'),  
    inputs: document.getElementsByClassName('qInput'),  
    outputs: document.getElementsByClassName('qOutput'),  
    func: document.getElementById('qFunction'),  
};

function addTestCase(){
    const qInput = document.createElement('input');
    const qOutput = document.createElement('input');
    const qTestCases = document.getElementById('qTestCases');

    if(document.getElementsByClassName('qInput').length == 6){
        //show error message
        return; 
    }


    qInput.setAttribute('type','text');
    qInput.setAttribute('class','qInput');
    qInput.setAttribute('placeholder','Test Input. Ex: \'\'string\'\',num');
    qInput.setAttribute('style','margin: 0 3px');

    qOutput.setAttribute('type','text');
    qOutput.setAttribute('class','qOutput');
    qOutput.setAttribute('placeholder','Expected output.');
    qOutput.setAttribute('style','margin: 0 3px');

    qTestCases.appendChild(qInput);
    qTestCases.appendChild(qOutput);
    qTestCases.appendChild(document.createElement('br'));

}

function removeTestCase(){
    const qTestCases = document.getElementById('qTestCases');
    const qInputs = document.getElementsByClassName('qInput');
    const qOutputs = document.getElementsByClassName('qOutput');
    const breaks = qTestCases.getElementsByTagName('br');

    if(qInputs.length == 2){
        //show error message
        return; 
    }

    qTestCases.removeChild(qInputs[qInputs.length-1]);
    qTestCases.removeChild(qOutputs[qOutputs.length-1]);
    qTestCases.removeChild(breaks[breaks.length-1]);

}

function addToBank() {
    const xhr = new XMLHttpRequest();

    const addStatus = document.getElementById('bankAddStatus');
    const keywords = document.getElementsByClassName('keywords');
    var statusColor;
    var statusText;

    if(qData.desc.value == ""){
        statusColor = 'red';
        statusText = 'Error: Question field is blank!';

        addStatus.style.color = statusColor;
        addStatus.innerHTML = statusText;
        return; 
    }

    xhr.onload = function(){
        if(this.status == 200){
            refreshDisplay();
            const jsonData = JSON.parse(this.responseText);
            //TODO: Also print reason why
            if(jsonData['return_code'] == 0){
                statusColor = 'red';
                statusText = 'Error: Question could not be added!';
            }else{
                statusColor = 'green';
                statusText = 'Question successfully added!';
            }

            addStatus.style.color = statusColor;
            addStatus.innerHTML = statusText;
            
            qData.desc.value = "";
            qData.func.value = "";

            for(i = 0; i < qData.inputs.length; i++){
                qData.inputs[i].value = "";
                qData.outputs[i].value = "";
            }


            populateQuestionTable('qBank');
        }
    };

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    //cases must be: input1,output1;input2,output2 (no semicolon at the last element)
    // const cases = `${btoa(qData.input.value)},${btoa(qData.output.value)}`;

    var cases = '';

    // for base 64 encoding
     for(i = 0; i < qData.inputs.length; i++){
         var testCase = `${btoa(qData.inputs[i].value)},${btoa(qData.outputs[i].value)}`;
         if(i != qData.inputs.length - 1){
             testCase = testCase + ';';
         }
         cases = cases + testCase;
     }

    console.log(cases);

    //for constraints
    var keywordData = [];
    var keywordString = "";

    console.log(keywords[1].checked);

    for(i = 0; i < keywords.length; i++){
        if(keywords[i].checked) {
            keywordData.push(keywords[i].value) 
        }
    }

    for(i = 0; i < keywordData.length; i++){
        if(i == keywordData.length) {
            keywordString += keywordData[i].value;
        }
        keywordString += keywordData[i].value + ',';
    }

    console.log(keywordString);

    const xhrData = `description=${qData.desc.value}&type=${qData.type.value}&difficulty=${qData.diff.value}&function=${qData.func.value}&cases=${cases}&keywords=${keywordData}&option_req=${'add_question'}`;

    console.log(xhrData);

    xhr.send(xhrData);

}

//an array containing the list of questions retrieved from the listQuestions function
function populateQuestionTable(t){

    const xhr = new XMLHttpRequest();
    const qTable = document.getElementById(t).getElementsByTagName('tbody')[0];

    xhr.onload = function(){
        if(this.status == 200){

            qBank = JSON.parse(this.responseText);

            qTable.innerHTML = "";
            var row;

            //populate table with questions from question bank
            //each table row will contain the following:
            //qid, description, difficulty, type
            //in addition to an empty box to take a number for point value
            
            if(t == 'qTable'){
                for(i = 0; i < qBank.length; i++){
                    row = qTable.insertRow(i);
                    row.insertCell(0).innerHTML = `<input type="checkbox" class="qCheckbox" onclick="updateTotal();" value="${qBank[i]['qid']}">`;
                    row.insertCell(1).innerHTML = `<p>${qBank[i]['question']}</p>`;
                    row.insertCell(2).innerHTML = `<p>${qBank[i]['q_type']}</p>`;
                    row.insertCell(3).innerHTML = `<p>${qBank[i]['q_difficulty']}</p>`;
                    row.insertCell(4).innerHTML = `<input type="text" value="0" class="pValue">`;
                }

                //add an event listener to each pValue input

                const pValues = document.getElementsByClassName('pValue');

                for(i = 0; i < pValues.length; i++){
                    pValues[i].addEventListener('input',updateTotal);
                }
            }else if(t == 'qBank'){
                for(i = 0; i < qBank.length; i++){
                    row = qTable.insertRow(i);
                    row.insertCell(0).innerHTML = `<p>${qBank[i]['question']}</p>`;
                    row.insertCell(1).innerHTML = `<p>${qBank[i]['q_type']}</p>`;
                    row.insertCell(2).innerHTML = `<p>${qBank[i]['q_difficulty']}</p>`;
                }
            
            }


        }
    };

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const xhrData = 'option_req=list_question_bank';

    console.log(xhrData);
    xhr.send(xhrData);
}

function updateTotal(){

    const pValues = document.getElementsByClassName('pValue');
    const qCheckboxes = document.getElementsByClassName('qCheckbox');
    total = Number(0);

    //first check if there are any negative pValues for selected questions

    for(i = 0; i < pValues.length; i++){
        if(pValues[i].value < 0 && qCheckboxes[i].checked){
            pointMessage.style.css = 'red';
            pointMessage.innerHTML = 'Error: Questions cannot have negative values!';
            return;
        }else{
            pointMessage.innerHTML = '';
        
        }
    }

    for(i = 0; i < qCheckboxes.length; i++){
        if(qCheckboxes[i].checked) {
            total += Number(pValues[i].value);
        }
    }
    
    pointTotal.innerHTML = `${total}/${maxPoints}`;

}

function updateMaxPoints(){
    maxPoints = maxPointInput.value;
    pointTotal.innerHTML = `${total}/${maxPoints}`;
}

function createExam(){
    const xhr = new XMLHttpRequest();
    const qCheckboxes = document.getElementsByClassName('qCheckbox');
    const pValueList = document.getElementsByClassName('pValue');
    const examName = document.getElementById('examName').value;

    var checkedQuestions = [];
    var qIDs = [];
    var pVals = [];



    //error checking

    if(total != maxPoints){
        pointMessage.style.color = 'red';
        pointMessage.innerHTML = "Error: total points must be equal to your maximum!";
        return;
    }else if(examName == ''){
        document.getElementById('examAddStatus').style.color = 'red';
        document.getElementById('examAddStatus').innerHTML = 'Error: Exam must have a name!';
        return;
    }
    
    //add the pValues from the input tag as a key-value pair within qBank
    
    for(i = 0; i < qBank.length; i++){
        qBank[i]['point_value'] = `${pValueList[i].value}`;
        if(qCheckboxes[i].checked){
            checkedQuestions.push(qBank[i]);
        }
    }

    //create an array where each index is a key,value pair: qID => pValue

    for(i = 0; i < checkedQuestions.length; i++){
        qIDs.push({qid:checkedQuestions[i]['qid']});
        pVals.push({point_value:checkedQuestions[i]['point_value']});
    }


    xhr.onload = function(){
        if(this.status == 200) {
            console.log(this.responseText);
            document.getElementById('examAddStatus').style.color = 'green';
            document.getElementById('examAddStatus').innerHTML = "Successfully created exam!";
            refreshDisplay();
        }
    }

    xhr.open('POST', 'frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const xhrData=`data=exam&option_no=relay&option_req=create_exam&exam_name=${examName}&max_points=${maxPoints}&question_list=${JSON.stringify(qIDs)}&question_points=${JSON.stringify(pVals)}`;

    console.log(xhrData);

    xhr.send(xhrData)
}

function getGradedExams(){
    
    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){
            document.getElementById('gradedExams').style.display = 'block';
            const examList = JSON.parse(this.responseText);
            console.log(examList);
            const gradedTable = document.getElementById('gradedExamTable').getElementsByTagName('tbody')[0];
            var row;

            if (examList['return_code'] == 0) {
                document.getElementById('gradedExamAvailabilityStatus').innerHTML = 'No Exams have been taken or graded at this time.';
            }else{
                document.getElementById('gradedExamAvailabilityStatus').innerHTML = '';
            
            }

            gradedTable.innerHTML = "";

            var uniqueSolverId;
            var totalgrade = 0;
            
            for(i = 0; i < examList.length; i++){
                uniqueSolverId = examList[i]['unique_exam_solver_id'];
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
                row.insertCell(0).innerHTML = `${examList[i]['exam_name']}`;
                row.insertCell(1).innerHTML = `${examList[i]['ucid']}`;
                row.insertCell(2).innerHTML = `${totalgrade}/${examList[i]['get_exam_max_points']}`;
                row.insertCell(3).innerHTML = `<button onclick="displayExam(${uniqueSolverId})">View Exam</button>`;
            }

            document.getElementById('saveExamBtn').setAttribute('onclick',`saveExamChanges(${uniqueSolverId})`);
            
        }
    }

    xhr.open('POST', 'frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const xhrData=`option_req=fetch_all_graded_exams`;

    xhr.send(xhrData)

}

function displayExam(e){
    //e is the exam id
    //hide the gradedExamTable and reveal the displayedExamTable
    document.getElementById('gradedExams').style.display = 'none';
    const currentExam = document.getElementById('displayedExamTable').getElementsByTagName('tbody')[0];

    currentExam.innerHTML = '';
    document.getElementById('displayedExamTable').style.display = 'table';
    document.getElementById('reviewBackBtn').style.display = 'inline-block';
    document.getElementById('saveExamBtn').style.display = 'inline-block';
    document.getElementById('releaseCheckbox').style.display = 'inline-block';

    const xhr = new XMLHttpRequest();
    
    xhr.onload = function(){
        if(this.status == 200){
            const examData = JSON.parse(this.responseText);
            const saveBtn = document.getElementById('saveExamBtn');

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
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="${auData['score'][0]}" style="width:50px"> points.`;
                }else{
                    feedbackRow.insertCell(0).innerHTML = `Function declared incorrectly. +${auData['score'][0]} points`;
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="${auData['score'][0]}" style="width:50px"> points.`;
                }
                
                k = 1;
                var feedbackRow = feedbackTable.insertRow(k);
                //check if the program compiles
                if(auData['compile'] == true){
                    feedbackRow.insertCell(0).innerHTML = `Program compiles successfully. +${auData['score'][1]} points.` 
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="${auData['score'][1]}" style="width:50px"> points.`
                }else{
                    feedbackRow.insertCell(0).innerHTML = `Program does not compile. +${auData['score'][1]} points.` 
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="0" style="width:50px"> points.`
                }

                k = 2;
                var feedbackRow = feedbackTable.insertRow(k);
                //check if there is a print statement
                if(auData['print'] == true){
                    feedbackRow.insertCell(0).innerHTML = `Values are printed, not returned. +${auData['score'][2]} points.` 
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="${auData['score'][2]}" style="width:50px"> points.`
                }else{
                    feedbackRow.insertCell(0).innerHTML = `Values are returned properly. +${auData['score'][2]} points.` 
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="${auData['score'][2]}" style="width:50px"> points.`
                }

                k = 3;
                var feedbackRow = feedbackTable.insertRow(k);
                //check constraints
                if(auData['keywords expected'][0] == ""){
                    feedbackRow.insertCell(0).innerHTML = `No constraints specified.`;
                    feedbackRow.insertCell(1).innerHTML = `<input type="text" class="editableScores" value="0" style="display:none">`;
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
                    feedbackRow.insertCell(1).innerHTML = `<br/>+<input type="text" class="editableScores" value="${auData['score'][3]}" style="width:50px"> points`;
                }

                k = 4;
                //check test cases
                for(j = 0; j < testCases.length; j++,k++){
                    var feedbackRow = feedbackTable.insertRow(k);
                    feedbackRow.insertCell(0).innerHTML = `<b>Test Case ${j+1}</b><br/>Expected: ${testCases[j]['expected']}<br/>Received: ${testCases[j]['result']}<br/>+${testCases[j]['points']} points`
                    feedbackRow.insertCell(1).innerHTML = `+<input type="text" class="editableScores" value="${testCases[j]['points']}" style="width:50px"> points`
                }

                //enable comments
                if(examData['comments'] != null){
                    feedbackTable.insertRow(k).innerHTML = `<textarea class="profComments" style="width:100%" rows="6">${examData['comments'][i]}</textarea>`;
                }else{
                    feedbackTable.insertRow(k).innerHTML = `<textarea class="profComments" style="width:100%" rows="6"></textarea>`;
                
                }

            }

            //done populating table
            saveBtn.setAttribute('onclick',`saveExamChanges(${e},${this.responseText})`);

        }
    }

    xhr.open('POST', 'viewExam.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const xhrData=`unique_solve_id=${e}&option_req=fetch_unique_student_exam`;

    console.log(xhrData);

    xhr.send(xhrData)
    
}

function saveExamChanges(uid,examData){
    console.log(uid);

    const releaseBox = document.getElementById('releaseCheckbox').getElementsByTagName('input')[0];
    const editableScores = document.getElementsByClassName('editableScores');
    const profComments = document.getElementsByClassName('profComments');
    const auData = examData['auData'];
    const xhr = new XMLHttpRequest();

    var isReleased;

    if(releaseBox.checked){
        isReleased = 1; 
    }else{
        isReleased = 0; 
    }

    for(i = 0; i < editableScores.length;i++){
        console.log(editableScores[i].value);
    }

    var revisedScoreIndex = 0;
    var revisedScores = [];
    //revise scores in examData to input values in editableScores
    for(i = 0; i < auData.length; i++){
        var numScores = auData[i][0]['score'].length;
        for(j = 0; j < numScores; j++,revisedScoreIndex++){
            if(j == 4){
                auData[i][0]['score'][j] = 0;
                console.log('processing test cases');

                for(k = 0; k < auData[i][0]['python'].length; k++) {
                    console.log(k);
                    console.log(Number(editableScores[revisedScoreIndex].value));
                     auData[i][0]['score'][j] += Number(editableScores[revisedScoreIndex].value);
                     auData[i][0]['python'][k]['points'] = Number(editableScores[revisedScoreIndex].value);
                    revisedScoreIndex++;
                }

                revisedScoreIndex--;
            }else{
                auData[i][0]['score'][j] = Number(editableScores[revisedScoreIndex].value);
            }
        }
        revisedScores.push(auData[i][0]['score']);
    }

    var comments = [];
    //set comments in examData to input values in profComments
    for(i = 0; i < profComments.length; i++){
        comments.push(profComments[i].value);
    }

    //send the data to the middle
    xhr.onload = function(){
        if(this.status == 200){
            console.log(this.responseText);

            const examRevisionStatus = document.getElementById('examRevisionStatus');

            if(releaseBox.checked){
                examRevisionStatus.innerHTML = 'Exam released and updated successfully!';
            }else{
                examRevisionStatus.innerHTML = 'Exam updated successfully!';
            }

            examRevisionStatus.style.color = 'green';
        }
    }

    xhr.open('POST', 'updateExam.php',true);
    
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    console.log(isReleased);
    
    const xhrData=`data=update&visible=${isReleased}&comments=${JSON.stringify(comments)}&updated_scores=${JSON.stringify(revisedScores)}&auData=${JSON.stringify(examData['auData'])}&unique_solve_id=${uid}`;

    console.log(xhrData);

    xhr.send(xhrData)

}

function redisplayGradedTable(){
    //when user presses 'back' after viewing exam details
    document.getElementById('displayedExamTable').style.display = 'none';
    document.getElementById('reviewBackBtn').style.display = 'none';
    document.getElementById('saveExamBtn').style.display = 'none';
    document.getElementById('releaseCheckbox').style.display = 'none';
    document.getElementById('examRevisionStatus').style.display = 'none';
    getGradedExams();
}

function logout(){
    location.href = "index.html";
}

//question bank search bar implementation
var typingTimer;
var typingInterval = 500; //in milliseconds

function startTypingCountdown(){
    clearTimeout(typingTimer);
    typingTimer = setTimeout(filterList,typingInterval);
}

function clearTypingCountdown(){
    clearTimeout(typingTimer);
}

var recentSearch = [''];

// filters through already listed questions by keyword. Specifically only the question itself
function filterList(){
    const query = document.getElementById('qSearchbox').value.toUpperCase().split(' ');
    const qTable = document.getElementById('searchableBank');
    const tr = qTable.getElementsByTagName('tr'); 
    var found;

    if(query.length > recentSearch.length){
        recentSearch = query; 
    }else{
        //reset table visibility  
        for(i = 0; i < tr.length; i++){
            tr[i].style.display = '';
        }
    }
    

    //search by each word given
    //search each row
    //begin searching from index 1 and onwards. tr[0] is the header
    for(var k = 0; k < query.length; k++){
        for(i = 1; i < tr.length; i++){
            var td = tr[i].getElementsByTagName('td');
            //search only the question element of each row
            if(td[0].innerHTML.toUpperCase().indexOf(query[k]) != -1){
                found = true; 
                break;
            }
            if(found && tr[i].style.display == ''){
                tr[i].style.display = '';
                found = false;
            }else{
                tr[i].style.display = 'none';
            }
        
        }
    
    }
}

const searchData = {
    type: document.getElementById('searchType'),
    difficulty: document.getElementById('searchDiff'),
    keyword: document.getElementById('qSearchbox')
};

function searchBank(){
    const qTable = document.getElementById('searchableBank').getElementsByTagName('tbody')[0];

    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){

           const searchContents = JSON.parse(this.responseText);

           qTable.innerHTML = "";

           //display search contents into table
           for(i = 0; i < searchContents.length; i++){
                var row = qTable.insertRow(i);
                row.insertCell(0).innerHTML = `${searchContents[i]['question']}`;
                row.insertCell(1).innerHTML = `${searchContents[i]['q_type']}`;
                row.insertCell(2).innerHTML = `${searchContents[i]['q_difficulty']}`;
           }
            

        }
    }

    xhr.open('POST', 'frontrelay.php',true);
    
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    const xhrData=`data=query&option_req=fetch_question_query&question_type=${searchData.type.value}&question_difficulty=${searchData.difficulty.value}&question_keyword=${searchData.keyword.value}`;

    console.log(xhrData);

    xhr.send(xhrData)
        
}


function getNextQuestions(i){
    const qTable = document.getElementById('searchableBank').getElementsByTagName('tbody')[0];

    if(++pageNum[i] >=2){
        qBtns.prev[i].style.display = 'inline-block';
    }

    qBtns.pNum[i].innerHTML = `${pageNum[i]}`;

    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){

            console.log(this.responseText);
           const searchContents = JSON.parse(this.responseText);
           console.log(searchContents);

           qTable.innerHTML = "";

           //display search contents into table
           for(i = 0; i < searchContents.length; i++){
                var row = qTable.insertRow(i);
                row.insertCell(0).innerHTML = `${searchContents[i]['question']}`;
                row.insertCell(1).innerHTML = `${searchContents[i]['q_type']}`;
                row.insertCell(2).innerHTML = `${searchContents[i]['q_difficulty']}`;
           }
            

        }
    }

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    qIndexStart[i] += pageLen;
    qIndexEnd[i] += pageLen;

    const xhrData = `data=get_range&option_req=list_question_bank&start=${qIndexStart[i]}&end=${qIndexEnd[i]}`;

    console.log(xhrData);

    xhr.send(xhrData);
    
}

function getPrevQuestions(i){

    //i = index of the specific button
    if(--pageNum[i] == 1){
        qBtns.prev[i].style.display = 'none';
    }
    
    qBtns.pNum[i].innerHTML = `${pageNum[i]}`;

    const xhr = new XMLHttpRequest();

    xhr.onload = function(){
        if(this.status == 200){
            console.log(this.responseText);
        }
    }

    qIndexStart[i] -= pageLen;
    qIndexEnd[i] -= pageLen;

    xhr.open('POST','frontrelay.php',true);

    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    const xhrData = `data=get_range&option_req=list_question_bank&start=${qIndexStart[i]}&end=${qIndexEnd[i]}`
    xhr.send(xhrData);

}

function refreshDisplay(){
    //hide anything that isnt the main contents
    //reviewExams
    document.getElementById('displayedExamTable').style.display = 'none';
    document.getElementById('releaseCheckbox').style.display = 'none';
    document.getElementById('saveExamBtn').style.display = 'none';
    document.getElementById('reviewBackBtn').style.display = 'none';

    //modQbank
    //clear text fields
    document.getElementById('qDesc').value = '';
    const inputs = document.getElementsByClassName('qInput');
    const outputs = document.getElementsByClassName('qOutput');
    for(i = 0; i < inputs.length; i++){
        inputs[i].value = '';
        outputs[i].value = '';
    }
    keywords = document.getElementsByClassName('keywords');
    for(i = 0; i < keywords.length; i++){
        keywords[i].checked = false;
    }

    document.getElementById('bankAddStatus').innerHTML = '';
    document.getElementById('qSearchbox').value = '';

    //createExam
    document.getElementById('examName').value = '';
    const selections = document.getElementsByClassName('qCheckboxes');
    const pVals = document.getElementsByClassName('pVals');
    for(i = 0; i < selections.length; i++){
        selections[i].checked = false;
        pVals[i].value = 0;
    }
}

