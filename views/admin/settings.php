<link rel="stylesheet" href="/PETVET/public/css/admin/settings.css">

<div class="main-content settings-page">
  <div class="settings-header">
    <h1>Admin Settings</h1>
    <div class="settings-actions">
      <button id="saveAllBtn" class="btn primary">Save All</button>
      <button id="resetBtn" class="btn">Reset</button>
    </div>
  </div>

  <!-- Tabs -->
  <div class="settings-tabs" role="tablist">
    <button class="tab active" data-tab="general" role="tab">General</button>
    <button class="tab" data-tab="branding" role="tab">Branding</button>
    <button class="tab" data-tab="notifications" role="tab">Notifications</button>
    <button class="tab" data-tab="security" role="tab">Security</button>
    <button class="tab" data-tab="integrations" role="tab">Integrations</button>
    <button class="tab" data-tab="billing" role="tab">Billing</button>
  </div>

  <div class="tab-panels">
    <!-- General -->
    <section id="tab-general" class="tab-panel active">
      <h2>General</h2>
      <div class="grid">
        <div class="field">
          <label for="siteName">Site Name</label>
          <input type="text" id="siteName" placeholder="PETVET" value="PETVET" />
        </div>
        <div class="field">
          <label for="defaultCurrency">Default Currency</label>
          <select id="defaultCurrency">
            <option value="LKR" selected>LKR - Sri Lankan Rupee</option>
            <option value="USD">USD - US Dollar</option>
            <option value="EUR">EUR - Euro</option>
          </select>
        </div>
        <div class="field">
          <label for="timezone">Timezone</label>
          <select id="timezone">
            <option value="Asia/Colombo" selected>Asia/Colombo (GMT+05:30)</option>
            <option value="UTC">UTC</option>
            <option value="Asia/Kolkata">Asia/Kolkata (GMT+05:30)</option>
          </select>
        </div>
        <div class="field switch">
          <label>Maintenance Mode</label>
          <label class="toggle">
            <input type="checkbox" id="maintenanceMode" />
            <span class="slider"></span>
          </label>
        </div>
      </div>
    </section>

    <!-- Branding -->
    <section id="tab-branding" class="tab-panel">
      <h2>Branding</h2>
      <div class="grid">
        <div class="field col-2">
          <label>Logo</label>
          <div class="logo-uploader">
            <img id="logoPreview" src="/PETVET/views/shared/images/sidebar/petvet-logo-web.png" alt="Logo Preview" />
            <div>
              <input type="file" id="logoInput" accept="image/*" />
              <small>PNG or SVG, max 1MB</small>
            </div>
          </div>
        </div>
        <div class="field">
          <label for="primaryColor">Primary Color</label>
          <input type="color" id="primaryColor" value="#3b82f6" />
        </div>
        <div class="field">
          <label for="accentColor">Accent Color</label>
          <input type="color" id="accentColor" value="#10b981" />
        </div>
      </div>
    </section>

    <!-- Notifications -->
    <section id="tab-notifications" class="tab-panel">
      <h2>Notifications</h2>
      <div class="grid">
        <div class="field switch">
          <label>Email Alerts</label>
          <label class="toggle">
            <input type="checkbox" id="emailAlerts" checked />
            <span class="slider"></span>
          </label>
        </div>
        <div class="field switch">
          <label>SMS Alerts</label>
          <label class="toggle">
            <input type="checkbox" id="smsAlerts" />
            <span class="slider"></span>
          </label>
        </div>
        <div class="field">
          <label for="senderEmail">Sender Email</label>
          <input type="email" id="senderEmail" placeholder="no-reply@petvet.lk" value="no-reply@petvet.lk" />
        </div>
        <div class="field">
          <label for="dailySummaryTime">Daily Summary Time</label>
          <input type="time" id="dailySummaryTime" value="09:00" />
        </div>
      </div>
    </section>

    <!-- Security -->
    <section id="tab-security" class="tab-panel">
      <h2>Security</h2>
      <div class="grid">
        <div class="field switch">
          <label>Two-Factor Authentication (2FA)</label>
          <label class="toggle">
            <input type="checkbox" id="twoFA" checked />
            <span class="slider"></span>
          </label>
        </div>
        <div class="field">
          <label for="sessionTimeout">Session Timeout (minutes)</label>
          <input type="number" id="sessionTimeout" min="5" max="240" value="30" />
        </div>
        <div class="field">
          <label for="allowedIPs">Allowed IPs (comma separated)</label>
          <textarea id="allowedIPs" rows="3" placeholder="123.123.123.123, 10.0.0.1"></textarea>
        </div>
      </div>
    </section>

    <!-- Integrations -->
    <section id="tab-integrations" class="tab-panel">
      <h2>Integrations</h2>
      <div class="grid">
        <div class="field col-2">
          <label>Payment Gateway</label>
          <div class="integration-card">
            <div>
              <strong>Stripe</strong>
              <p>Enable card payments and subscriptions</p>
            </div>
            <label class="toggle small">
              <input type="checkbox" id="stripeEnabled" />
              <span class="slider"></span>
            </label>
          </div>
        </div>
        <div class="field col-2">
          <label>Cloud Storage</label>
          <div class="integration-card">
            <div>
              <strong>AWS S3</strong>
              <p>Store media and backups securely</p>
            </div>
            <label class="toggle small">
              <input type="checkbox" id="s3Enabled" />
              <span class="slider"></span>
            </label>
          </div>
        </div>
      </div>
    </section>

    <!-- Billing -->
    <section id="tab-billing" class="tab-panel">
      <h2>Billing</h2>
      <div class="grid">
        <div class="field">
          <label for="billingEmail">Billing Email</label>
          <input type="email" id="billingEmail" placeholder="accounts@petvet.lk" />
        </div>
        <div class="field">
          <label for="invoicePrefix">Invoice Prefix</label>
          <input type="text" id="invoicePrefix" placeholder="PV-" value="PV-" />
        </div>
        <div class="field">
          <label for="taxRate">Tax Rate (%)</label>
          <input type="number" id="taxRate" min="0" max="100" step="0.1" value="8.0" />
        </div>
      </div>
    </section>
  </div>

  <!-- Sticky Footer -->
  <div class="settings-footer">
    <div class="status" id="saveStatus">Unsaved changes</div>
    <div class="actions">
      <button class="btn" id="discardBtn">Discard</button>
      <button class="btn primary" id="saveBtn">Save Changes</button>
    </div>
  </div>
</div>

<script src="/PETVET/public/js/admin/settings.js"></script>
