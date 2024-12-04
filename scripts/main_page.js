const button = document.getElementById('tryButton');
const modal = document.getElementById("qrCodeModal");
const questionCode = document.getElementById('questionCode');
const questionAdress = document.getElementById('questionAdress');
const questionQrCode = document.getElementById('questionQrCode');

var select1 = document.querySelector("select[name='select1']");
var select2 = document.querySelector("select[name='select2']");

document.addEventListener("DOMContentLoaded", function () {
    function updateTable() {
        var selectedSubject = select1.value;
        var selectedDate = select2.value;

        var rows = document.querySelectorAll("#questionsTable tbody tr");

        rows.forEach(function (row) {
            var cells = row.cells;
            var rowSubject = cells[2].textContent;
            var rowDate = cells[5].textContent;

            if ((selectedSubject === rowSubject || selectedSubject === "") && (selectedDate === rowDate || selectedDate === "")) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);

            var tableBody = document.querySelector("#questionsTable tbody");
            response.forEach(function (question) {
                var row = tableBody.insertRow();
                var cell1 = row.insertCell(0);
                cell1.classList.add('align-to-left');
                var cell2 = row.insertCell(1);
                var cell7 = row.insertCell(2);
                var cell3 = row.insertCell(3);
                var cell4 = row.insertCell(4);
                var cell5 = row.insertCell(5);
                var cell6 = row.insertCell(6);
                var cell8 = row.insertCell(7);

                cell1.textContent = question.question;
                cell2.textContent = question.id;
                cell2.classList.add('question-id-cell');
                cell7.textContent = question.subject;
                cell8.textContent = question.user_email;
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                if(question.is_active === 'true') checkbox.checked = true;

                checkbox.addEventListener('change', function () {
                    Swal.fire({
                        title: 'Naozaj chcete zmeniť stav tejto otázky?',
                        text: "Táto akcia môže ovplyvniť údaje v systéme!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Áno, zmeniť!',
                        cancelButtonText: 'Zrušiť'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var questionId = question.id;
                            var questionState = checkbox.checked;

                            var xhr = new XMLHttpRequest();

                            xhr.open("PUT", "edit_question_active_state.php", true);
                            xhr.setRequestHeader("Content-Type", "application/json");

                            xhr.onreadystatechange = function () {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    console.log(xhr.responseText);
                                }
                            };

                            var data = {
                                question_id: questionId,
                                question_state: questionState
                            };

                            var jsonData = JSON.stringify(data);
                            xhr.send(jsonData);
                        } else {
                            checkbox.checked = !checkbox.checked;
                        }
                    });
                });


                cell3.appendChild(checkbox);
                cell3.classList.add('center-align');

                cell4.textContent = question.question_type;
                cell5.textContent = question.creation_date;

                var removeImage = document.createElement('img');
                removeImage.src = 'images/remove.png';
                removeImage.classList.add('remove-image');
                removeImage.classList.add('question-id-cell');
                cell6.appendChild(removeImage);

                var editImage = document.createElement('img');
                editImage.src = 'images/edit.png';
                editImage.classList.add('edit-image');
                editImage.classList.add('question-id-cell');
                cell6.appendChild(editImage);

                var copyImage = document.createElement('img');
                copyImage.src = 'images/copy.png';
                copyImage.classList.add('copy-image');
                copyImage.classList.add('question-id-cell');
                cell6.appendChild(copyImage);

                removeImage.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Naozaj chcete vymazať túto otázku?',
                        text: "Tento úkon je nevratný!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Áno, vymazať!',
                        cancelButtonText: 'Zrušiť'
                    }).then((result) => {
                        if (result.isConfirmed) {

                            var questionId = question.id;
                            var user_email = question.user_email;

                            var xhr = new XMLHttpRequest();

                            xhr.open("DELETE", "database_services/delete_question.php?id=" + questionId + "&email=" + encodeURIComponent(user_email), true);

                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    console.log(xhr.responseText);
                                    location.reload();

                                }
                            };

                            xhr.send();
                        }
                    });
                });

                editImage.addEventListener('click', function() {
                    var questionId = question.id;
                    window.location.href = "edit_question.php?id=" + questionId;
                });

                copyImage.addEventListener('click', function() {
                    var questionId = question.id;
                    window.location.href = "copy_question.php?id=" + questionId;
                });

                var cell9 = row.insertCell(8);
                var closeButton = document.createElement('button');
                closeButton.textContent = 'close';
                closeButton.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Naozaj chcete uzatvoriť hlasovanie na túto otázku?',
                        html: '<input type="text" id="note" class="swal2-input" placeholder="Poznámka">',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Áno, uzatvoriť!',
                        cancelButtonText: 'Zrušiť',
                        preConfirm: () => {
                            var note = document.getElementById('note').value;
                            if (!note) {
                                Swal.showValidationMessage('Poznámka je povinná!');
                                return false;
                            }
                            return { note: note };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var questionId = question.id;
                            var note = result.value.note;
                            console.log("Question ID:", questionId);
                            console.log("Note:", note);

                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "../WEBTE2_Zaverecne_zadanie/close_voting.php", true);
                            xhr.setRequestHeader("Content-Type", "application/json");
                            xhr.onreadystatechange = function () {
                                if (xhr.readyState === 4) {
                                    console.log("Response Status:", xhr.status);
                                    if (xhr.status === 200) {
                                        console.log("Response:", xhr.responseText);
                                    } else {
                                        console.error("Error:", xhr.responseText);
                                    }
                                }
                            };
                            var data = JSON.stringify({ question_id: questionId, note: note });
                            console.log("Sending Data:", data);
                            xhr.send(data);
                        }
                    });
                });
                cell9.appendChild(closeButton);


                cell2.addEventListener('click', function () {
                    questionCode.textContent = question.id;
                    questionAdress.textContent = "https://node97.webte.fei.stuba.sk/" + question.id;
                    questionQrCode.innerHTML = '';
                    var qrcode = new QRCode(questionQrCode, {
                        text: "https://node97.webte.fei.stuba.sk/" + question.id,
                        width: 280,
                        height: 280
                    });
                    modal.style.display = "flex";
                });
            });
            $('#questionsTable').DataTable();
        }
    };
    xhr.open("POST", "get_users_questions.php", true);
    xhr.send();

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    select1.addEventListener("change", updateTable);
    select2.addEventListener("change", updateTable);

    updateTable();
});