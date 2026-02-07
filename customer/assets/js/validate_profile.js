document.getElementById('profileForm').addEventListener('submit', function(e) {
    let hasErrors = false;
    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    const showError = (input, msg) => {
        hasErrors = true;
        input.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.innerText = msg;
        input.parentNode.appendChild(feedback);
    };

    // PAN Check
    const pan = document.getElementById('pan_number');
    if(pan && !pan.readOnly && pan.value && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan.value.toUpperCase())) showError(pan, "Invalid Format (ABCDE1234F)");

    // Pincode
    const pin = document.getElementById('pin_code');
    if(pin && pin.value && !/^\d{6}$/.test(pin.value)) showError(pin, "Must be 6 digits");

    // Password
    const np = document.getElementById('new_password');
    const cp = document.getElementById('confirm_password');
    if(np.value && np.value.length < 8) showError(np, "Minimum 8 characters required");
    if(np.value && np.value !== cp.value) showError(cp, "Passwords do not match");

    if (hasErrors) {
        e.preventDefault();
        const firstErr = document.querySelector('.is-invalid');
        bootstrap.Tab.getOrCreateInstance(document.querySelector(`[href="#${firstErr.closest('.tab-pane').id}"]`)).show();
    }
});
