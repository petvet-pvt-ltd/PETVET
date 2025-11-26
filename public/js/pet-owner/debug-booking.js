// Debug script to check appointment booking
console.log('=== APPOINTMENT BOOKING DEBUG ===');

// Check if form elements exist
const appointmentForm = document.getElementById('bookAppointmentForm');
const finalConfirmBtn = document.getElementById('appointmentFinalConfirmBtn');

console.log('Form exists:', !!appointmentForm);
console.log('Confirm button exists:', !!finalConfirmBtn);

// Intercept fetch calls to log them
const originalFetch = window.fetch;
window.fetch = function(...args) {
    console.log('FETCH CALL:', args[0]);
    if (args[1] && args[1].body) {
        console.log('FETCH BODY:', args[1].body);
        try {
            const parsed = JSON.parse(args[1].body);
            console.log('FETCH DATA:', parsed);
        } catch(e) {}
    }
    return originalFetch.apply(this, args).then(response => {
        console.log('FETCH RESPONSE STATUS:', response.status);
        return response.clone().json().then(data => {
            console.log('FETCH RESPONSE DATA:', data);
            return response;
        }).catch(() => response);
    });
};

console.log('Debug script loaded. Try booking an appointment now.');
