<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Payments | Receptionist</title>
  <link rel="stylesheet" href="/PETVET/public/css/receptionist/payments.css">
</head>
<body>
  <main class="main-content">
    <div class="page-container">
      <header class="page-header">
        <h1>Pending Payments</h1>
        <p>Appointments completed but payment pending</p>
      </header>

      <?php if (empty($pendingPayments)): ?>
        <div class="empty-state">
          <p>🎉 No pending payments at the moment!</p>
        </div>
      <?php else: ?>
        <section class="payments-grid">
          <?php foreach ($pendingPayments as $payment): ?>
            <article class="payment-card">
              <div class="payment-header">
                <div class="payment-info">
                  <h3><?php echo htmlspecialchars($payment['client']); ?></h3>
                  <p class="pet-name"><?php echo htmlspecialchars($payment['pet']); ?> (<?php echo htmlspecialchars($payment['animal']); ?>)</p>
                </div>
                <span class="badge badge-pending">Pending Payment</span>
              </div>
              
              <div class="payment-details">
                <div class="detail-row">
                  <span class="label">Vet:</span>
                  <span class="value"><?php echo htmlspecialchars($payment['vet']); ?></span>
                </div>
                <div class="detail-row">
                  <span class="label">Type:</span>
                  <span class="value"><?php echo htmlspecialchars($payment['type']); ?></span>
                </div>
                <div class="detail-row">
                  <span class="label">Date:</span>
                  <span class="value"><?php echo htmlspecialchars($payment['date']); ?></span>
                </div>
                <div class="detail-row">
                  <span class="label">Time:</span>
                  <span class="value"><?php echo htmlspecialchars($payment['time']); ?></span>
                </div>
              </div>

              <button class="btn-process" onclick="openPaymentForm(<?php echo htmlspecialchars(json_encode($payment)); ?>)">
                Process Payment
              </button>
            </article>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>
    </div>
  </main>

  <!-- Payment Form Modal -->
  <div id="paymentModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Payment Details</h2>
        <button class="modal-close" onclick="closePaymentModal()">&times;</button>
      </div>
      
      <div class="modal-body">
        <div class="appointment-summary">
          <h3>Appointment Summary</h3>
          <div class="summary-grid">
            <div><strong>Client:</strong> <span id="summary-client"></span></div>
            <div><strong>Pet:</strong> <span id="summary-pet"></span></div>
            <div><strong>Vet:</strong> <span id="summary-vet"></span></div>
            <div><strong>Type:</strong> <span id="summary-type"></span></div>
          </div>
        </div>

        <form id="paymentForm">
          <input type="hidden" id="appointment-id" name="appointment_id">
          
          <div class="form-section">
            <h3>Charges</h3>
            
            <div class="form-group">
              <label for="consultation-fee">Consultation Fee (LKR)</label>
              <input type="number" id="consultation-fee" name="consultation_fee" value="2500" step="0.01" required>
            </div>

            <div class="form-group">
              <label for="treatment-charges">Treatment Charges (LKR)</label>
              <input type="number" id="treatment-charges" name="treatment_charges" value="0" step="0.01">
            </div>

            <div class="form-group">
              <label for="medication-charges">Medication Charges (LKR)</label>
              <input type="number" id="medication-charges" name="medication_charges" value="0" step="0.01">
            </div>

            <div class="form-group">
              <label for="lab-charges">Lab/Test Charges (LKR)</label>
              <input type="number" id="lab-charges" name="lab_charges" value="0" step="0.01">
            </div>

            <div class="form-group">
              <label for="other-charges">Other Charges (LKR)</label>
              <input type="number" id="other-charges" name="other_charges" value="0" step="0.01">
            </div>
          </div>

          <div class="form-section">
            <h3>Additional Information</h3>
            
            <div class="form-group">
              <label for="services-provided">Services Provided</label>
              <textarea id="services-provided" name="services_provided" rows="3" placeholder="e.g., General checkup, vaccination, dental cleaning"></textarea>
            </div>

            <div class="form-group">
              <label for="medications">Medications Prescribed</label>
              <textarea id="medications" name="medications" rows="3" placeholder="e.g., Amoxicillin 500mg, Pain relief tablets"></textarea>
            </div>

            <div class="form-group">
              <label for="notes">Additional Notes</label>
              <textarea id="notes" name="notes" rows="2" placeholder="Any special notes or comments"></textarea>
            </div>
          </div>

          <div class="total-section">
            <div class="total-row">
              <span class="total-label">Subtotal:</span>
              <span class="total-value" id="subtotal">LKR 2,500.00</span>
            </div>
            <div class="total-row">
              <span class="total-label">Tax (0%):</span>
              <span class="total-value" id="tax">LKR 0.00</span>
            </div>
            <div class="total-row total-final">
              <span class="total-label">Total Amount:</span>
              <span class="total-value" id="total">LKR 2,500.00</span>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" onclick="closePaymentModal()">Cancel</button>
        <button class="btn-primary" onclick="generateInvoice()">Generate Invoice</button>
      </div>
    </div>
  </div>

  <!-- Invoice Preview Modal -->
  <div id="invoiceModal" class="modal-overlay" style="display: none;">
    <div class="modal-content modal-large">
      <div class="modal-header">
        <h2>Invoice Preview</h2>
        <button class="modal-close" onclick="closeInvoiceModal()">&times;</button>
      </div>
      
      <div class="modal-body">
        <div id="invoice-content" class="invoice-preview">
          <!-- Invoice will be generated here -->
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" onclick="closeInvoiceModal()">Cancel</button>
        <button class="btn-outline" onclick="printInvoice()">🖨️ Print Invoice</button>
        <button class="btn-success" onclick="showConfirmPaymentDialog()">✓ Mark as Paid</button>
      </div>
    </div>
  </div>

  <!-- Confirm Payment Modal -->
  <div id="confirmPaymentModal" class="modal-overlay" style="display: none;">
    <div class="modal-content modal-small">
      <div class="modal-header">
        <h2>⚠️ Confirm Payment</h2>
        <button class="modal-close" onclick="closeConfirmPaymentModal()">&times;</button>
      </div>
      
      <div class="modal-body">
        <div class="confirm-content">
          <p class="confirm-message">Are you sure the customer has completed the payment?</p>
          <div class="confirm-details">
            <div class="confirm-row">
              <span class="confirm-label">Client:</span>
              <span class="confirm-value" id="confirm-client"></span>
            </div>
            <div class="confirm-row">
              <span class="confirm-label">Invoice:</span>
              <span class="confirm-value" id="confirm-invoice"></span>
            </div>
            <div class="confirm-row total-row">
              <span class="confirm-label">Amount:</span>
              <span class="confirm-value" id="confirm-amount"></span>
            </div>
          </div>
          <p class="confirm-warning">⚠️ This action cannot be undone</p>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" onclick="closeConfirmPaymentModal()">Cancel</button>
        <button class="btn-success" onclick="confirmPaymentFinal()">✓ Confirm Payment</button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div id="successModal" class="modal-overlay" style="display: none;">
    <div class="modal-content modal-small">
      <div class="modal-body">
        <div class="success-content">
          <div class="success-icon">✅</div>
          <h2>Payment Recorded Successfully!</h2>
          <p>Invoice <strong id="success-invoice"></strong> has been marked as paid.</p>
          <button class="btn-primary btn-full" onclick="closeSuccessModal()">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script src="/PETVET/public/js/receptionist/payments.js"></script>
</body>
</html>
