var selectBox = document.getElementById('questionType');
selectBox.value = '1';
const messageField = document.getElementById('message');
const messageDiv = document.getElementById('message-div');
var answerQuestionContainer = document.getElementById('answerQuestionContainer');
var openQuestionContainer = document.getElementById('openQuestionContainer');

function showSelectedOption() {
    if (selectBox.value === '1') {
        answerQuestionContainer.style.display = 'block';
        openQuestionContainer.style.display = 'none';
    } else {
        answerQuestionContainer.style.display = 'none';
        openQuestionContainer.style.display = 'block';
    }
}

selectBox.addEventListener('change', showSelectedOption);
showSelectedOption();

document.getElementById("addOptionButton").addEventListener("click", function () {
    var answerOptions = document.getElementById("answerOptions");
    var optionCount = answerOptions.getElementsByClassName("answer-option").length;
    var numOfAnswersField = document.getElementById("numOfAnswers1");
    var numOfAnswers = parseInt(numOfAnswersField.value);

    numOfAnswers++;

    numOfAnswersField.value = numOfAnswers;

    var newOptionDiv = document.createElement("div");
    newOptionDiv.classList.add("answer-option");

    var newLabel = document.createElement("label");
    newLabel.htmlFor = "answer" + (optionCount + 1);
    newLabel.textContent = "Možnosť " + (optionCount + 1) + ":";

    var newInput = document.createElement("input");
    newInput.type = "text";
    newInput.id = "answer" + (optionCount + 1);
    newInput.name = "answer" + (optionCount + 1);
    newInput.required = true;

    var newCheckbox = document.createElement("input");
    newCheckbox.type = "checkbox";
    newCheckbox.id = "correctAnswer" + (optionCount + 1);
    newCheckbox.name = "correctAnswer" + (optionCount + 1);

    var newCheckboxLabel = document.createElement("label");
    newCheckboxLabel.htmlFor = "correctAnswer" + (optionCount + 1);
    newCheckboxLabel.textContent = "Správna možnosť";

    newOptionDiv.appendChild(newLabel);
    newOptionDiv.appendChild(newInput);
    newOptionDiv.appendChild(newCheckbox);
    newOptionDiv.appendChild(newCheckboxLabel);

    answerOptions.appendChild(newOptionDiv);
});


document.getElementById("closedQuestionForm").addEventListener("submit", function (event) {
    event.preventDefault(); // Zabrániť štandardnému odosielaniu formulára

    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
    document.getElementById('creationDate1').value = formattedDate;

    var formData = new FormData(this);
    formData.append('userEmail', document.getElementById('userEmail').value);


    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_closed_question.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            // Spracovať odpoveď zo servera, ak je potrebné
            messageDiv.style.display = 'block';
            messageField.innerHTML = 'Úloha bola úspešne vytvorená.';
            console.log(xhr.responseText);

            var answerOptions = document.getElementById("answerOptions");
            while (answerOptions.firstChild) {
                answerOptions.removeChild(answerOptions.firstChild);
            }

            // Zostaviť iba dva vstupné polia pre odpovede
            for (var i = 0; i < 2; i++) {
                var newOptionDiv = document.createElement("div");
                newOptionDiv.classList.add("answer-option");
            
                var newLabel = document.createElement("label");
                newLabel.htmlFor = "answer" + (i + 1);
                newLabel.textContent = "Možnosť " + (i + 1) + ":";
            
                var newInput = document.createElement("input");
                newInput.type = "text";
                newInput.id = "answer" + (i + 1);
                newInput.name = "answer" + (i + 1);
                newInput.required = true;
            
                var newCheckbox = document.createElement("input");
                newCheckbox.type = "checkbox";
                newCheckbox.id = "correctAnswer" + (i + 1);
                newCheckbox.name = "correctAnswer" + (i + 1);
            
                var newCheckboxLabel = document.createElement("label");
                newCheckboxLabel.htmlFor = "correctAnswer" + (i + 1);
                newCheckboxLabel.textContent = "Správna možnosť";
            
                var space = document.createTextNode("\u00A0"); // \u00A0 je nezlomitelná mezera
                newOptionDiv.appendChild(newLabel);
                newOptionDiv.appendChild(newInput);
                newOptionDiv.appendChild(newCheckbox);
                newOptionDiv.appendChild(space);
                newOptionDiv.appendChild(newCheckboxLabel);
            
                answerOptions.appendChild(newOptionDiv);
            }
            
            document.getElementById("numOfAnswers1").value = 2;

            document.getElementById("question1").value = "";
            document.getElementById("subject1").value = "";
        }
    };

    xhr.send(formData);
});

document.getElementById("openQuestionForm").addEventListener("submit", function (event) {
    event.preventDefault();

    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + "-" + (currentDate.getMonth() + 1) + "-" + currentDate.getDate();
    document.getElementById('creationDate').value = formattedDate;
    var formData = new FormData(this);
    formData.append('userEmail', document.getElementById('userEmail').value);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "add_open_question.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            messageDiv.style.display = 'block';
            messageField.innerHTML = 'Úloha bola úspešne vytvorená.';
            console.log(xhr.responseText);
            document.getElementById("question").value = "";
            document.getElementById("subject").value = "";
            document.querySelector('input[name="answerDisplay"]:checked').checked = false;
        }
    };

    xhr.send(formData);
});
