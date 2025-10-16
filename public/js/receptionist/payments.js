// Payments Page JavaScript
let currentAppointment = null;
let invoiceData = null;

// Open payment form modal
function openPaymentForm(appointment) {
  currentAppointment = appointment;
  
  // Fill in appointment summary
  document.getElementById('summary-client').textContent = appointment.client;
  document.getElementById('summary-pet').textContent = `${appointment.pet} (${appointment.animal})`;
  document.getElementById('summary-vet').textContent = appointment.vet;
  document.getElementById('summary-type').textContent = appointment.type;
  document.getElementById('appointment-id').value = appointment.id;
  
  // Reset form
  document.getElementById('paymentForm').reset();
  document.getElementById('consultation-fee').value = '2500';
  
  // Calculate initial total
  calculateTotal();
  
  // Show modal
  document.getElementById('paymentModal').style.display = 'flex';
}

// Close payment form modal
function closePaymentModal() {
  document.getElementById('paymentModal').style.display = 'none';
  currentAppointment = null;
}

// Calculate total amount
function calculateTotal() {
  const consultationFee = parseFloat(document.getElementById('consultation-fee').value) || 0;
  const treatmentCharges = parseFloat(document.getElementById('treatment-charges').value) || 0;
  const medicationCharges = parseFloat(document.getElementById('medication-charges').value) || 0;
  const labCharges = parseFloat(document.getElementById('lab-charges').value) || 0;
  const otherCharges = parseFloat(document.getElementById('other-charges').value) || 0;
  
  const subtotal = consultationFee + treatmentCharges + medicationCharges + labCharges + otherCharges;
  const tax = 0; // 0% tax for now
  const total = subtotal + tax;
  
  document.getElementById('subtotal').textContent = `LKR ${subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
  document.getElementById('tax').textContent = `LKR ${tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
  document.getElementById('total').textContent = `LKR ${total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
}

// Add event listeners to all charge inputs
document.addEventListener('DOMContentLoaded', function() {
  const chargeInputs = [
    'consultation-fee',
    'treatment-charges',
    'medication-charges',
    'lab-charges',
    'other-charges'
  ];
  
  chargeInputs.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener('input', calculateTotal);
    }
  });
});

// Generate invoice
function generateInvoice() {
  // Collect all data
  const consultationFee = parseFloat(document.getElementById('consultation-fee').value) || 0;
  const treatmentCharges = parseFloat(document.getElementById('treatment-charges').value) || 0;
  const medicationCharges = parseFloat(document.getElementById('medication-charges').value) || 0;
  const labCharges = parseFloat(document.getElementById('lab-charges').value) || 0;
  const otherCharges = parseFloat(document.getElementById('other-charges').value) || 0;
  const servicesProvided = document.getElementById('services-provided').value;
  const medications = document.getElementById('medications').value;
  const notes = document.getElementById('notes').value;
  
  const subtotal = consultationFee + treatmentCharges + medicationCharges + labCharges + otherCharges;
  const tax = 0;
  const total = subtotal + tax;
  
  // Store invoice data
  invoiceData = {
    appointment: currentAppointment,
    charges: {
      consultation: consultationFee,
      treatment: treatmentCharges,
      medication: medicationCharges,
      lab: labCharges,
      other: otherCharges
    },
    subtotal: subtotal,
    tax: tax,
    total: total,
    servicesProvided: servicesProvided,
    medications: medications,
    notes: notes,
    invoiceNumber: 'INV-' + Date.now(),
    date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  };
  
  // Generate invoice HTML
  const invoiceHTML = generateInvoiceHTML(invoiceData);
  document.getElementById('invoice-content').innerHTML = invoiceHTML;
  
  // Close payment form and open invoice preview
  closePaymentModal();
  document.getElementById('invoiceModal').style.display = 'flex';
}

// Generate invoice HTML
function generateInvoiceHTML(data) {
  const charges = [];
  
  if (data.charges.consultation > 0) {
    charges.push({ description: 'Consultation Fee', amount: data.charges.consultation });
  }
  if (data.charges.treatment > 0) {
    charges.push({ description: 'Treatment Charges', amount: data.charges.treatment });
  }
  if (data.charges.medication > 0) {
    charges.push({ description: 'Medication Charges', amount: data.charges.medication });
  }
  if (data.charges.lab > 0) {
    charges.push({ description: 'Lab/Test Charges', amount: data.charges.lab });
  }
  if (data.charges.other > 0) {
    charges.push({ description: 'Other Charges', amount: data.charges.other });
  }
  
  let chargesRows = '';
  charges.forEach(charge => {
    chargesRows += `
      <tr>
        <td>${charge.description}</td>
        <td class="text-right">LKR ${charge.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}</td>
      </tr>
    `;
  });
  
  return `
    <div class="invoice-header">
      <div class="clinic-info">
        <h1>PETVET Clinic</h1>
        <p>123 Pet Care Street</p>
        <p>Colombo, Sri Lanka</p>
        <p>Phone: +94 11 234 5678</p>
        <p>Email: info@petvet.lk</p>
      </div>
      <div class="invoice-meta">
        <h2>INVOICE</h2>
        <p><strong>Invoice #:</strong> ${data.invoiceNumber}</p>
        <p><strong>Date:</strong> ${data.date}</p>
        <p><strong>Status:</strong> <span style="color: #f59e0b;">Pending Payment</span></p>
      </div>
    </div>
    
    <div class="invoice-details">
      <div class="detail-box">
        <h3>Bill To</h3>
        <p><strong>${data.appointment.client}</strong></p>
        <p>Pet: ${data.appointment.pet}</p>
        <p>Type: ${data.appointment.animal}</p>
      </div>
      <div class="detail-box">
        <h3>Appointment Details</h3>
        <p><strong>Veterinarian:</strong> ${data.appointment.vet}</p>
        <p><strong>Type:</strong> ${data.appointment.type}</p>
        <p><strong>Date:</strong> ${data.appointment.date}</p>
        <p><strong>Time:</strong> ${data.appointment.time}</p>
      </div>
    </div>
    
    ${data.servicesProvided ? `
    <div style="margin-bottom: 20px;">
      <h4 style="font-size: 14px; font-weight: 700; color: #475569; margin-bottom: 8px;">Services Provided:</h4>
      <p style="color: #64748b; font-size: 14px; line-height: 1.6;">${data.servicesProvided}</p>
    </div>
    ` : ''}
    
    ${data.medications ? `
    <div style="margin-bottom: 20px;">
      <h4 style="font-size: 14px; font-weight: 700; color: #475569; margin-bottom: 8px;">Medications Prescribed:</h4>
      <p style="color: #64748b; font-size: 14px; line-height: 1.6;">${data.medications}</p>
    </div>
    ` : ''}
    
    <table class="invoice-table">
      <thead>
        <tr>
          <th>Description</th>
          <th class="text-right">Amount</th>
        </tr>
      </thead>
      <tbody>
        ${chargesRows}
      </tbody>
    </table>
    
    <div class="invoice-totals">
      <div class="totals-box">
        <div class="total-line">
          <span>Subtotal:</span>
          <span>LKR ${data.subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}</span>
        </div>
        <div class="total-line">
          <span>Tax (0%):</span>
          <span>LKR ${data.tax.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}</span>
        </div>
        <div class="total-line final">
          <span>Total Amount:</span>
          <span>LKR ${data.total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}</span>
        </div>
      </div>
    </div>
    
    ${data.notes ? `
    <div class="invoice-notes">
      <h4>Notes:</h4>
      <p>${data.notes}</p>
    </div>
    ` : ''}
    
    <div class="invoice-notes">
      <p style="text-align: center; color: #64748b; font-size: 13px; margin-top: 40px;">
        Thank you for choosing PETVET Clinic! We care for your pets like family.
      </p>
    </div>
  `;
}

// Close invoice modal
function closeInvoiceModal() {
  document.getElementById('invoiceModal').style.display = 'none';
}

// Print invoice
function printInvoice() {
  window.print();
}

// Show confirm payment dialog
function showConfirmPaymentDialog() {
  document.getElementById('confirm-client').textContent = invoiceData.appointment.client;
  document.getElementById('confirm-invoice').textContent = invoiceData.invoiceNumber;
  document.getElementById('confirm-amount').textContent = `LKR ${invoiceData.total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}`;
  
  document.getElementById('confirmPaymentModal').style.display = 'flex';
}

// Close confirm payment dialog
function closeConfirmPaymentModal() {
  document.getElementById('confirmPaymentModal').style.display = 'none';
}

// Confirm payment (final)
function confirmPaymentFinal() {
  // In production, send data to server
  console.log('Payment confirmed:', invoiceData);
  
  /*
  fetch('/PETVET/receptionist/confirm-payment', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(invoiceData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showSuccessMessage();
    }
  });
  */
  
  // Close modals
  closeConfirmPaymentModal();
  closeInvoiceModal();
  
  // Show success message
  showSuccessMessage();
}

// Show success modal
function showSuccessMessage() {
  document.getElementById('success-invoice').textContent = invoiceData.invoiceNumber;
  document.getElementById('successModal').style.display = 'flex';
}

// Close success modal
function closeSuccessModal() {
  document.getElementById('successModal').style.display = 'none';
  // In production, reload to update the list
  // window.location.reload();
}

// Close modal when clicking outside
window.onclick = function(event) {
  const paymentModal = document.getElementById('paymentModal');
  const invoiceModal = document.getElementById('invoiceModal');
  const confirmModal = document.getElementById('confirmPaymentModal');
  const successModal = document.getElementById('successModal');
  
  if (event.target === paymentModal) {
    closePaymentModal();
  }
  if (event.target === invoiceModal) {
    closeInvoiceModal();
  }
  if (event.target === confirmModal) {
    closeConfirmPaymentModal();
  }
  if (event.target === successModal) {
    closeSuccessModal();
  }
}
