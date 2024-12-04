const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const repeatPasswordInput = document.getElementById('confirm_password');

function validateEmail(email) {
    // Regulárny výraz pre overenie správneho formátu emailu
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password, confirmPassword) {
    return password === confirmPassword;
}

function markInputAsInvalid(inputElement) {
    inputElement.style.borderColor = 'red';
}

function markInputAsValid(inputElement) {
    inputElement.style.borderColor = '';
}

function validateForm() {
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const repeatPassword = repeatPasswordInput.value;
    let isValid = true;

    // Overenie správnosti emailu
    if (!validateEmail(email)) {
        markInputAsInvalid(emailInput);
        isValid = false;
    } else {
        markInputAsValid(emailInput);
    }

    // Overenie zhodnosti hesiel
    if (!validatePassword(password, repeatPassword)) {
        markInputAsInvalid(passwordInput);
        markInputAsInvalid(repeatPasswordInput);
        isValid = false;
    } else {
        markInputAsValid(passwordInput);
        markInputAsValid(repeatPasswordInput);
    }

    return isValid;
}

document.getElementById('registration_form').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault();
    }
});
