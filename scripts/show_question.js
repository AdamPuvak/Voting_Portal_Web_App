document.getElementById('codeForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const code = document.getElementById('codeInput').value;
    const baseURL = "";
    window.location.href = baseURL + code;
});