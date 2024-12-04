const oldPasswordInput = document.getElementById('oldPassword');
const newPasswordInput = document.getElementById('newPassword');
const confirmNewPasswordInput = document.getElementById('confirmNewPassword');

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
    const password = newPasswordInput.value;
    const repeatPassword = confirmNewPasswordInput.value;
    let isValid = true;

    // Overenie zhodnosti hesiel
    if (!validatePassword(password, repeatPassword)) {
        markInputAsInvalid(newPasswordInput);
        markInputAsInvalid(confirmNewPasswordInput);
        isValid = false;
    } else {
        markInputAsValid(newPasswordInput);
        markInputAsValid(confirmNewPasswordInput);
    }

    return isValid;
}

document.getElementById('change-password-form').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault();
    }
});