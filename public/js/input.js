const inputsToConfirm = document.querySelectorAll(".confirm-input");
const formToSubmit = document.querySelector(".confirm-form");

const askForConfirmation = (event) => {
    event.preventDefault();
    if (confirm("Do you really want to delete this photo?")) {
        formToSubmit.submit(); //... fix this
    }
}

if (inputsToConfirm.length > 0) {
    for (const key in inputsToConfirm) {
        if ('object' == typeof(inputsToConfirm[key]))
            inputsToConfirm[key].addEventListener('click', function(e) {askForConfirmation(e);});
    }
}