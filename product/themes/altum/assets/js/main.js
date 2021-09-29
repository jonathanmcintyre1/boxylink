'use strict';

/* Temporarily disable the form submission button to avoid multiple submissions */
document.querySelectorAll('[type="submit"][name="submit"]').forEach(element => {
    element.addEventListener('click', event => {
        let button = event.currentTarget;

        /* Disable the button */
        button.classList.add('disabled');

        /* Save the current button text */
        let current_button_text = button.innerText;

        /* Show a loading spinner instead of the text */
        button.innerHTML = '<div class="spinner-grow spinner-grow-sm" role="status"></div>';

        /* Go back to the button default state */
        setTimeout(() => {
            button.classList.remove('disabled');
            button.innerText = current_button_text;
        }, 2500);

    });
});

/* Enable tooltips everywhere */
$('[data-toggle="tooltip"]').tooltip();
