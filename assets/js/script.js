document.addEventListener('DOMContentLoaded', function () {
    // Auto-scroll message thread to the latest message
    var thread = document.querySelector('.thread');
    if (thread) {
        thread.scrollTop = thread.scrollHeight;
    }

    // Highlight selected vehicle card on the booking form
    var vehicleInputs = document.querySelectorAll('.vehicle-choice input');
    vehicleInputs.forEach(function (input) {
        input.addEventListener('change', function () {
            vehicleInputs.forEach(function (i) {
                i.closest('.vehicle-choice-item') && i.closest('.vehicle-choice-item').classList.remove('is-selected');
            });
        });
    });
});
