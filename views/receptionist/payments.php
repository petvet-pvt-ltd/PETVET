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

        <form id="paymentForm" onsubmit="return false;">
          <input type="hidden" id="appointment-id" name="appointment_id">

          <div class="form-section">
            <div class="editor-head">
              <h3>Invoice Items</h3>
            </div>

            <div id="detected-items" class="detected-items" aria-live="polite"></div>

            <div class="items-table-wrap" role="region" aria-label="Invoice Items">
              <table class="items-table" id="itemsTable">
                <thead>
                  <tr>
                    <th style="width: 46%">Description</th>
                    <th style="width: 12%" class="num">Qty</th>
                    <th style="width: 18%" class="num">Unit Price</th>
                    <th style="width: 18%" class="num">Total</th>
                    <th style="width: 6%"></th>
                  </tr>
                </thead>
                <tbody id="itemsTbody"></tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" class="items-footer">
                      <button type="button" class="btn-outline btn-sm" onclick="addInvoiceRow(true)">+ Add Row</button>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <div class="form-section">
            <h3>Invoice Details</h3>

            <div class="meta-grid">
              <div class="form-group">
                <label for="invoice-date">Date</label>
                <input type="date" id="invoice-date" name="invoice_date" required>
              </div>

              <div class="form-group">
                <label for="invoice-no">Invoice No</label>
                    <input type="text" id="invoice-no" name="invoice_no" placeholder="Auto" autocomplete="off" readonly>
              </div>

              <div class="form-group">
                <label for="payment-method">Payment Method</label>
                <select id="payment-method" name="payment_method">
                  <option value="CASH">CASH</option>
                  <option value="CARD">CARD</option>
                </select>
              </div>

              <div class="form-group">
                <label for="client-phone">Client Phone</label>
                <input type="text" id="client-phone" name="client_phone" placeholder="e.g., 0773983002" autocomplete="off">
              </div>

              <div class="form-group" style="grid-column: 1 / -1;">
                <label for="invoice-note">Note</label>
                <input type="text" id="invoice-note" name="invoice_note" placeholder="-" autocomplete="off">
              </div>
            </div>
          </div>

          <div class="total-section">
            <div class="total-row">
              <span class="total-label">Gross Amount:</span>
              <span class="total-value" id="gross">LKR 0.00</span>
            </div>

            <div class="total-row total-input">
              <span class="total-label">Item Discount:</span>
              <span class="total-value">
                <input type="number" id="invoice-discount" value="0" step="0.01" min="0" class="money-input" aria-label="Item Discount">
              </span>
            </div>

            <div class="total-row total-input">
              <span class="total-label">Card Fee:</span>
              <span class="total-value">
                <input type="number" id="invoice-cardfee" value="0" step="0.01" min="0" class="money-input" aria-label="Card Fee">
              </span>
            </div>

            <div class="total-row total-final">
              <span class="total-label">Total Amount:</span>
              <span class="total-value" id="total">LKR 0.00</span>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button class="btn-secondary" onclick="closePaymentModal()">Cancel</button>
        <button class="btn-primary" onclick="generateInvoice()">Preview Invoice</button>
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
        <button class="btn-secondary" onclick="backToPaymentDetails()">Back</button>
        <button class="btn-secondary" onclick="closeInvoiceModal()">Close</button>
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
