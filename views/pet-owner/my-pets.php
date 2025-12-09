<?php
$currentPage = basename($_SERVER['PHP_SELF']);
// Helper function to calculate age from date of birth
function calculateAge($dob) {
  $birthDate = new DateTime($dob);
  $today = new DateTime();
  $age = $today->diff($birthDate)->y;
  return $age;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Pets</title>
  <link rel="stylesheet" href="/PETVET/public/css/pet-owner/my-pets.css">
  <link rel="stylesheet" href="/PETVET/public/css/pet-owner/booking-calendar.css">
  <link rel="stylesheet" href="/PETVET/public/css/pet-owner/clinic-selector.css">
  <style>
    /* Pet Delete Button - Top Right Corner */
    .pet-hero {
      position: relative;
    }
    
    .pet-delete-btn {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      border: none;
      background: rgba(239, 68, 68, 0.9);
      color: white;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
      opacity: 0;
      transform: scale(0.8);
    }
    
    .pet-card:hover .pet-delete-btn {
      opacity: 1;
      transform: scale(1);
    }
    
    .pet-delete-btn:hover {
      background: rgba(220, 38, 38, 1);
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
    }
    
    .pet-delete-btn:active {
      transform: scale(0.95);
    }
    
    /* Mobile - always show delete button */
    @media (max-width: 768px) {
      .pet-delete-btn {
        opacity: 1;
        transform: scale(1);
      }
    }
    
    /* ========================================
       DELETE CONFIRMATION DIALOG STYLES
       ======================================== */
    
    #deletePetDialog {
      background: transparent;
      border: none;
      padding: 0;
    }
    
    #deletePetDialog::backdrop {
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
    }
    
    .delete-confirm-card {
      max-width: 480px;
      border-radius: 16px !important;
      overflow: hidden;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      animation: slideInScale 0.3s ease-out;
      border: none;
      background: white;
    }
    
    @keyframes slideInScale {
      from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
      }
      to {
        opacity: 1;
        transform: scale(1) translateY(0);
      }
    }
    
    .delete-confirm-card .dialog-header {
      text-align: center;
      padding: 32px 24px 20px;
      background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
      border-bottom: none;
    }
    
    .delete-icon-wrapper {
      width: 80px;
      height: 80px;
      margin: 0 auto 16px;
      background: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px rgba(239, 68, 68, 0.2);
    }
    
    .delete-icon {
      color: #ef4444;
      animation: wiggle 0.5s ease-in-out;
    }
    
    @keyframes wiggle {
      0%, 100% { transform: rotate(0deg); }
      25% { transform: rotate(-5deg); }
      75% { transform: rotate(5deg); }
    }
    
    .delete-confirm-card .dialog-header h3 {
      margin: 0 0 8px;
      font-size: 24px;
      font-weight: 700;
      color: #991b1b;
    }
    
    .delete-confirm-card .dialog-header .dialog-subtitle {
      margin: 0;
      font-size: 15px;
      color: #7f1d1d;
      line-height: 1.5;
    }
    
    .delete-confirm-card .dialog-header .dialog-subtitle strong {
      color: #991b1b;
      font-weight: 600;
    }
    
    .delete-confirm-card .dialog-body {
      padding: 24px;
    }
    
    .warning-box {
      background: transparent;
      border: none;
      border-radius: 0;
      padding: 0;
      display: flex;
      gap: 12px;
      align-items: flex-start;
    }
    
    .warning-box svg {
      flex-shrink: 0;
      color: #ef4444;
      margin-top: 2px;
    }
    
    .warning-title {
      margin: 0 0 4px;
      font-weight: 600;
      font-size: 14px;
      color: #991b1b;
    }
    
    .warning-text {
      margin: 0;
      font-size: 13px;
      color: #6b7280;
      line-height: 1.5;
    }
    
    .delete-confirm-card .dialog-actions {
      padding: 20px 24px 24px;
      gap: 12px;
      background: #fafafa;
    }
    
    .btn.danger {
      background: #ef4444;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
      transition: all 0.2s ease;
      min-width: 140px;
    }
    
    .btn.danger:hover {
      background: #dc2626;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .btn.danger:active {
      transform: translateY(0);
      box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
    }
    
    /* Mobile Responsive for Delete Dialog */
    @media (max-width: 768px) {
      .delete-confirm-card {
        width: 95vw !important;
        max-width: 95vw !important;
        margin: 10px;
        border-radius: 12px !important;
      }
      
      .delete-icon-wrapper {
        width: 70px;
        height: 70px;
      }
      
      .delete-icon {
        width: 40px;
        height: 40px;
      }
      
      .delete-confirm-card .dialog-header {
        padding: 24px 16px 16px;
      }
      
      .delete-confirm-card .dialog-header h3 {
        font-size: 20px;
      }
      
      .delete-confirm-card .dialog-body {
        padding: 16px;
      }
      
      .warning-box {
        padding: 0;
        gap: 10px;
      }
      
      .delete-confirm-card .dialog-actions {
        flex-direction: column-reverse;
        padding: 16px;
      }
      
      .delete-confirm-card .dialog-actions .btn {
        width: 100%;
        min-width: 100%;
      }
    }
    
    @media (max-width: 480px) {
      .delete-confirm-card {
        width: 100vw !important;
        max-width: 100vw !important;
        height: auto !important;
        max-height: 90vh !important;
        margin: 0 !important;
        border-radius: 16px 16px 0 0 !important;
        position: fixed !important;
        bottom: 0 !important;
        top: auto !important;
        left: 0 !important;
        right: 0 !important;
        animation: slideUpMobile 0.3s ease-out;
      }
      
      @keyframes slideUpMobile {
        from {
          transform: translateY(100%);
        }
        to {
          transform: translateY(0);
        }
      }
      
      .delete-confirm-card .dialog-header h3 {
        font-size: 18px;
      }
      
      .delete-confirm-card .dialog-header .dialog-subtitle {
        font-size: 14px;
      }
      
      .warning-title {
        font-size: 13px;
      }
      
      .warning-text {
        font-size: 12px;
      }
    }
    
    /* Prevent background scroll when dialog is open */
    body.dialog-open {
      overflow: hidden !important;
      position: fixed;
      width: 100%;
      height: 100%;
    }

    /* Make dialog footer buttons consistent across all popups on this page */
    dialog .dialog-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      align-items: stretch;
    }
    dialog .dialog-actions .btn {
      min-width: 140px; /* same minimum width for all buttons */
      padding: 10px 18px;
      box-sizing: border-box;
      text-align: center;
      height: 44px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 1.2;
      vertical-align: middle;
    }
    /* Ensure primary and ghost/outline buttons share height/line-height */
    dialog .dialog-actions .btn.primary,
    dialog .dialog-actions .btn.ghost,
    dialog .dialog-actions .btn.outline {
      height: 44px;
      line-height: 1.2;
    }

    /* Mobile responsive fixes for dialogs */
    @media (max-width: 768px) {
      dialog.dialog {
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        max-height: 100vh !important;
        margin: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        border-radius: 0 !important;
      }

      .dialog-card {
        max-height: 100vh;
        height: 100vh;
        border-radius: 0 !important;
        display: flex;
        flex-direction: column;
      }

      .dialog-header {
        flex-shrink: 0;
        padding: 20px 16px 12px !important;
      }

      .dialog-body {
        flex: 1;
        overflow-y: auto !important;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 20px 16px !important;
        max-height: none !important;
      }

      .dialog-actions {
        flex-shrink: 0;
        padding: 16px !important;
      }

      .grid-2 {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
      }

      .field-col {
        grid-column: span 1 !important;
      }
    }

    @media (max-width: 480px) {
      .page-header {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
      }
      
      .page-header .btn {
        width: 100%;
      }

      dialog .dialog-actions {
        flex-direction: column;
        gap: 10px;
      }

      dialog .dialog-actions .btn {
        width: 100%;
        min-width: 100%;
      }
    }

    /* ========================================
       NOTIFICATION PANEL STYLES (YouTube-style)
       ======================================== */
    
    .header-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .notification-container {
      position: relative;
    }

    /* Header Actions in Dark Header */
    .header-actions-content {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .header-actions-content .btn.primary {
      background: rgba(255, 255, 255, 0.15);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: 10px 20px;
      font-size: 14px;
      border-radius: 10px;
      box-shadow: none;
    }

    .header-actions-content .btn.primary:hover {
      background: rgba(255, 255, 255, 0.25);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Bell Button */
    .notification-bell {
      position: relative;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(255, 255, 255, 0.1);
      color: #ffffff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }

    .notification-bell:hover {
      background: rgba(255, 255, 255, 0.2);
      border-color: rgba(255, 255, 255, 0.3);
    }

    .notification-bell:active {
      transform: scale(0.95);
    }

    /* Red Badge for Unread */
    .notification-badge {
      position: absolute;
      top: 4px;
      right: 4px;
      background: #ef4444;
      color: white;
      font-size: 11px;
      font-weight: 600;
      min-width: 18px;
      height: 18px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 5px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .notification-badge.hidden {
      display: none;
    }

    /* Notification Panel */
    .notification-panel {
      position: absolute;
      top: calc(100% + 8px);
      right: 0;
      width: 420px;
      max-height: 600px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.15);
      border: 1px solid #e5e7eb;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.2s ease;
      z-index: 1000;
      display: flex;
      flex-direction: column;
    }

    .notification-panel.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    /* Header */
    .notification-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 20px;
      border-bottom: 1px solid #e5e7eb;
    }

    .notification-header h3 {
      margin: 0;
      font-size: 16px;
      font-weight: 600;
      color: #0f172a;
    }

    .mark-all-read {
      background: none;
      border: none;
      color: #2563eb;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      padding: 4px 8px;
      border-radius: 4px;
      transition: background 0.2s ease;
    }

    .mark-all-read:hover {
      background: #eff6ff;
    }

    /* Notification List */
    .notification-list {
      overflow-y: auto;
      max-height: 480px;
      flex: 1;
    }

    .notification-list::-webkit-scrollbar {
      width: 8px;
    }

    .notification-list::-webkit-scrollbar-track {
      background: #f9fafb;
    }

    .notification-list::-webkit-scrollbar-thumb {
      background: #d1d5db;
      border-radius: 4px;
    }

    .notification-list::-webkit-scrollbar-thumb:hover {
      background: #9ca3af;
    }

    /* Notification Item */
    .notification-item {
      display: flex;
      gap: 12px;
      padding: 14px 20px;
      border-bottom: 1px solid #f3f4f6;
      cursor: pointer;
      transition: background 0.15s ease;
      position: relative;
    }

    .notification-item:hover {
      background: #f9fafb;
    }

    .notification-item.unread {
      background: #eff6ff;
    }

    .notification-item.unread::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: #2563eb;
    }

    /* Notification Icons */
    .notification-icon {
      flex-shrink: 0;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .appointment-icon {
      background: #dbeafe;
      color: #2563eb;
    }

    .sitter-icon {
      background: #fef3c7;
      color: #f59e0b;
    }

    .trainer-icon {
      background: #e0e7ff;
      color: #6366f1;
    }

    .breeder-icon {
      background: #fce7f3;
      color: #ec4899;
    }

    /* Notification Content */
    .notification-content {
      flex: 1;
      min-width: 0;
    }

    .notification-text {
      margin: 0 0 4px 0;
      font-size: 14px;
      line-height: 1.5;
      color: #374151;
    }

    .notification-text strong {
      font-weight: 600;
      color: #111827;
    }

    .notification-text em {
      font-style: italic;
      color: #6b7280;
    }

    .notification-time {
      font-size: 12px;
      color: #9ca3af;
    }

    /* Footer */
    .notification-footer {
      padding: 16px 20px;
      text-align: center;
      border-top: 1px solid #e5e7eb;
      background: #fafbfc;
    }

    .notification-footer p {
      margin: 0;
      font-size: 13px;
      color: #6b7280;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .notification-panel {
        position: fixed;
        top: 60px !important;
        right: 8px;
        left: 8px;
        width: auto;
        max-height: calc(100vh - 80px);
      }
    }

    @media (max-width: 480px) {
      .header-actions {
        width: 100%;
        justify-content: space-between;
      }

      .notification-panel {
        right: 8px;
        left: 8px;
        width: auto;
      }

      .notification-list {
        max-height: calc(100vh - 200px);
      }
    }
  </style>
</head>
<body>
  <?php //require_once '../sidebar.php'; ?>

  <main class="main-content">
    <?php 
    // Include user welcome header
    include __DIR__ . '/../shared/components/user-welcome-header.php'; 
    ?>
    
    <script>
    // Insert header actions immediately
    (function() {
      const headerActionsHTML = `
        <div class="notification-container">
          <button type="button" class="notification-bell" id="notificationBell" aria-label="Notifications">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge" id="notificationBadge">3</span>
          </button>

          <!-- Notification Panel (YouTube-style) -->
          <div class="notification-panel" id="notificationPanel">
            <div class="notification-header">
              <h3>Notifications</h3>
              <button class="mark-all-read" id="markAllRead">Mark all as read</button>
            </div>
            <div class="notification-list" id="notificationList">
              <!-- TODO: Backend will populate notifications dynamically -->
              <!-- Sample notifications for UI demonstration -->
              
              <!-- Appointment Notifications -->
              <div class="notification-item unread" data-type="appointment">
                <div class="notification-icon appointment-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Appointment for <strong>Duke</strong> with <strong>Dr. Peter</strong> has been confirmed for <strong>October 20, 2023, at 10:00 AM</strong></p>
                  <span class="notification-time">2 hours ago</span>
                </div>
              </div>

              <div class="notification-item unread" data-type="appointment">
                <div class="notification-icon appointment-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Appointment declined for <strong>Duke</strong>. Reason: <em>Fully booked on that day</em></p>
                  <span class="notification-time">5 hours ago</span>
                </div>
              </div>

              <!-- Sitter Notifications -->
              <div class="notification-item unread" data-type="sitter">
                <div class="notification-icon sitter-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Sitter <strong>John</strong> has accepted your request for <strong>Max</strong></p>
                  <span class="notification-time">1 day ago</span>
                </div>
              </div>

              <!-- Trainer Notifications -->
              <div class="notification-item" data-type="trainer">
                <div class="notification-icon trainer-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Trainer <strong>Alex</strong> has accepted your request for <strong>Max</strong></p>
                  <span class="notification-time">2 days ago</span>
                </div>
              </div>

              <div class="notification-item" data-type="trainer">
                <div class="notification-icon trainer-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Training Session <strong>5</strong> for <strong>Max</strong> has been completed. Next session is scheduled for <strong>October 20, 2025</strong>.</p>
                  <span class="notification-time">3 days ago</span>
                </div>
              </div>

              <!-- Breeder Notifications -->
              <div class="notification-item" data-type="breeder">
                <div class="notification-icon breeder-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Breeder <strong>Peter</strong> accepted your request for <strong>Duke</strong>. Breeder's Pet: <strong>Molly</strong></p>
                  <span class="notification-time">1 week ago</span>
                </div>
              </div>

              <div class="notification-item" data-type="breeder">
                <div class="notification-icon breeder-icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                  </svg>
                </div>
                <div class="notification-content">
                  <p class="notification-text">Breeder <strong>Peter</strong> declined your request. Reason: <em>Not compatible breeds</em></p>
                  <span class="notification-time">1 week ago</span>
                </div>
              </div>

            </div>
            <div class="notification-footer">
              <p>You're all caught up!</p>
            </div>
          </div>
        </div>

          <button type="button" class="btn primary" id="addPetBtn">+ Add Pet</button>
        </div>
      `;
      
      const slot = document.getElementById('headerActionsSlot');
      if (slot) {
        slot.innerHTML = headerActionsHTML;
      }
    })();
    </script>

    <section class="pets-grid" id="petsGrid">
      <?php foreach ($pets as $pet): ?>
      <article class="pet-card" data-pet-id="<?php echo $pet['id']; ?>">
        <div class="pet-hero">
          <img src="<?php echo $pet['photo']; ?>" alt="<?php echo $pet['name']; ?>">
          <button class="pet-delete-btn" data-pet-id="<?php echo $pet['id']; ?>" data-pet-name="<?php echo htmlspecialchars($pet['name']); ?>" title="Delete Pet">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              <line x1="10" y1="11" x2="10" y2="17"></line>
              <line x1="14" y1="11" x2="14" y2="17"></line>
            </svg>
          </button>
        </div>
        <div class="pet-body">
          <div class="pet-title">
            <h3 class="pet-name"><?php echo $pet['name']; ?></h3>
          </div>
          <p class="pet-meta">
            <?php echo $pet['species']; ?> • 
            <?php echo $pet['breed']; ?> • 
            <?php echo calculateAge($pet['date_of_birth']); ?>y
          </p>
          <p class="pet-notes"><?php echo $pet['notes']; ?></p>
        </div>
        <div class="pet-actions">
          <a href="pet-profile.php?id=<?php echo $pet['id']; ?>" class="btn outline">View Profile</a>

          <!-- ✅ Route through the controller (no direct /views/ link) -->
          <a href="/PETVET/?module=pet-owner&page=medical-records&pet=1" class="btn outline">Medical Records</a>

          <?php if ($pet['has_upcoming_appointment']): ?>
            <button class="btn primary" disabled style="opacity: 0.5; cursor: not-allowed;" title="This pet already has an upcoming appointment">Has Upcoming Appointment</button>
          <?php else: ?>
            <a href="#book-appointment?pet=<?php echo $pet['id']; ?>" class="btn primary">Book Appointment</a>
          <?php endif; ?>
          <button class="btn danger markMissingBtn" data-pet="<?php echo $pet['id']; ?>">Mark as Missing</button>
        </div>
      </article>
      <?php endforeach; ?>
    </section>
  </main>

  <!-- Add Pet Dialog -->
  <dialog id="addPetDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="addPetForm" enctype="multipart/form-data">
      <header class="dialog-header">
        <h3>Add New Pet</h3>
        <p class="dialog-subtitle">Fill in your pet's details</p>
      </header>
      <div class="dialog-body">
        <div class="form-section">
          <h4 class="section-title">Basic Information</h4>
          <div class="grid-2">
            <label class="field">
              <span>Name *</span>
              <input type="text" class="input" name="name" required placeholder="e.g. Buddy">
            </label>
            <label class="field">
              <span>Species *</span>
              <select class="select" name="species" required>
                <option value="">Select species</option>
                <option>Dog</option>
                <option>Cat</option>
                <option>Bird</option>
                <option>Rabbit</option>
                <option>Hamster</option>
                <option>Guinea Pig</option>
                <option>Fish</option>
                <option>Turtle</option>
                <option>Other</option>
              </select>
            </label>
            <label class="field">
              <span>Breed</span>
              <input type="text" class="input" name="breed" placeholder="e.g. Labrador">
            </label>
            <label class="field">
              <span>Sex</span>
              <select class="select" name="sex">
                <option value="">Select sex</option>
                <option>Male</option>
                <option>Female</option>
                <option>Unknown</option>
              </select>
            </label>
            <label class="field">
              <span>Date of Birth</span>
              <input type="date" class="input" name="date_of_birth" max="<?php echo date('Y-m-d'); ?>">
            </label>
            <label class="field">
              <span>Weight (kg)</span>
              <input type="number" step="0.01" class="input" name="weight" placeholder="e.g. 12.5">
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="section-title">Appearance</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Color/Markings</span>
              <textarea class="input" name="color" rows="2" placeholder="e.g. Brown with white paws"></textarea>
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="section-title">Health & Notes</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Allergies</span>
              <textarea class="input" name="allergies" rows="2" placeholder="List any known allergies"></textarea>
            </label>
            <label class="field field-col">
              <span>Additional Notes</span>
              <textarea class="input" name="notes" rows="2" placeholder="Temperament, special needs, etc."></textarea>
            </label>
          </div>
        </div>

        <div class="form-section">
          <h4 class="section-title">Pet Photo</h4>
          <label class="field field-col">
            <span>Upload Photo</span>
            <input type="file" accept="image/*" class="input" name="pet_photo" id="addPetPhoto">
          </label>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn ghost" type="button" value="cancel">Cancel</button>
        <button class="btn primary" type="submit" value="save">Save Pet</button>
      </footer>
    </form>
  </dialog>

  <!-- Pet Profile Dialog -->
  <dialog id="petProfileDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="petProfileForm" enctype="multipart/form-data">
      <input type="hidden" name="id" id="editPetId">
      <header class="dialog-header">
        <h3>Edit Pet Profile</h3>
        <p class="dialog-subtitle">View and edit your pet's details</p>
      </header>
      <div class="dialog-body">
        <div class="form-section" style="align-items:center;display:flex;flex-direction:column;">
          <div id="petProfileImgWrap" style="margin-bottom:18px;">
            <img id="petProfileImg" src="/PETVET/public/images/emptyProfPic.png" alt="Pet Photo" style="width:110px;height:110px;object-fit:cover;border-radius:50%;border:2px solid #e5e7eb;box-shadow:0 2px 8px rgba(37,99,235,.10);background:#f5f7fb;">
          </div>
          <label class="btn ghost" style="margin-bottom:10px;cursor:pointer;">
            <input type="file" name="pet_photo" id="petProfileImgInput" accept="image/*" style="display:none;">
            <span>Change Photo</span>
          </label>
        </div>
        <div class="form-section">
          <h4 class="section-title">Basic Information</h4>
          <div class="grid-2">
            <label class="field">
              <span>Name *</span>
              <input type="text" class="input" name="name" required>
            </label>
            <label class="field">
              <span>Species *</span>
              <select class="select" name="species" required>
                <option value="">Select species</option>
                <option>Dog</option>
                <option>Cat</option>
                <option>Bird</option>
                <option>Rabbit</option>
                <option>Hamster</option>
                <option>Guinea Pig</option>
                <option>Fish</option>
                <option>Turtle</option>
                <option>Other</option>
              </select>
            </label>
            <label class="field">
              <span>Breed</span>
              <input type="text" class="input" name="breed">
            </label>
            <label class="field">
              <span>Sex</span>
              <select class="select" name="sex">
                <option value="">Select sex</option>
                <option>Male</option>
                <option>Female</option>
                <option>Unknown</option>
              </select>
            </label>
            <label class="field">
              <span>Date of Birth</span>
              <input type="date" class="input" name="date_of_birth" max="<?php echo date('Y-m-d'); ?>">
            </label>
            <label class="field">
              <span>Weight (kg)</span>
              <input type="number" step="0.01" class="input" name="weight">
            </label>
          </div>
        </div>
        <div class="form-section">
          <h4 class="section-title">Appearance</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Color/Markings</span>
              <textarea class="input" name="color" rows="2"></textarea>
            </label>
          </div>
        </div>
        <div class="form-section">
          <h4 class="section-title">Health & Notes</h4>
          <div class="grid-2">
            <label class="field field-col">
              <span>Allergies</span>
              <textarea class="input" name="allergies" rows="2"></textarea>
            </label>
            <label class="field field-col">
              <span>Additional Notes</span>
              <textarea class="input" name="notes" rows="2"></textarea>
            </label>
          </div>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn ghost" type="button" value="cancel">Cancel</button>
        <button class="btn primary" type="submit" value="save">Save Changes</button>
      </footer>
    </form>
  </dialog>

  <!-- Mark as Missing Dialog -->
  <dialog id="markMissingDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="markMissingForm">
      <header class="dialog-header">
        <h3>Report Missing Pet</h3>
        <p class="dialog-subtitle">Please provide details to help find your pet</p>
      </header>
      <div class="dialog-body">
        <div class="form-section">
          <label class="field field-col">
            <span>Last Seen Location *</span>
            <input type="text" class="input" name="location" required placeholder="Enter address or location">
          </label><br>
          <label class="field">
            <span>Last Seen Date & Time *</span>
            <input type="datetime-local" class="input" name="datetime" required>
          </label><br>
          <label class="field field-col">
            <span>Circumstances</span>
            <textarea class="input" name="circumstances" rows="2" maxlength="250" placeholder="How did your pet go missing?"></textarea>
          </label><br>
          <label class="field">
            <span>Distinguishing Features</span>
            <input type="text" class="input" name="features" placeholder="Special markings, collar, etc.">
          </label><br>
          <div class="grid-2">
            <label class="field">
              <span><input type="checkbox" id="rewardCheckbox">
              Offer reward for safe return</span>
            </label><br>
            <label class="field" id="rewardAmountWrap" style="display:none;">
              <span>Reward Amount</span>
              <input type="number" class="input" name="reward" step="0.01" placeholder="e.g. 5000.00">
            </label><br>
          </div><br>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn ghost" value="cancel">Cancel</button>
        <button class="btn primary" value="submit">Report</button>
      </footer>
    </form>
  </dialog>
  
  <!-- Delete Pet Confirmation Dialog -->
  <dialog id="deletePetDialog" class="dialog">
    <div class="dialog-card delete-confirm-card">
      <header class="dialog-header">
        <div class="delete-icon-wrapper">
          <svg class="delete-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            <line x1="10" y1="11" x2="10" y2="17"></line>
            <line x1="14" y1="11" x2="14" y2="17"></line>
          </svg>
        </div>
        <h3>Delete Pet Profile?</h3>
        <p class="dialog-subtitle">Are you sure you want to delete <strong id="deletePetName">this pet</strong>?</p>
      </header>
      <div class="dialog-body">
        <div class="warning-box">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
          </svg>
          <div>
            <p class="warning-title">This action cannot be undone</p>
            <p class="warning-text">The pet profile and all associated data will be permanently deleted from the system.</p>
          </div>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn ghost" type="button" value="cancel">Cancel</button>
        <button class="btn danger" type="button" id="confirmDeletePetBtn">Delete</button>
      </footer>
    </div>
  </dialog>
  
    <!-- Book Appointment Dialog -->
  <dialog id="bookAppointmentDialog" class="dialog">
    <form method="dialog" class="dialog-card" id="bookAppointmentForm">
      <header class="dialog-header">
        <h3 id="appointmentHeader">Book Appointment</h3>
        <p class="dialog-subtitle" id="appointmentPetInfo">Pet Info</p>
        <p class="dialog-subtitle" id="appointmentHealthNotes" style="color:#b91c1c;"></p>
      </header>

      <div class="dialog-body" id="appointmentFormContent">
        <!-- Section A: Appointment Type -->
        <div class="form-section">
          <h4 class="section-title">Appointment Type</h4>
          <label class="field">
            <span>Select Type *</span>
            <select class="select" name="appointment_type" id="appointmentType" required>
              <option value="">Select Appointment Type</option>
              <option value="routine">Routine Check-up</option>
              <option value="vaccination">Vaccination</option>
              <option value="dental">Dental Cleaning</option>
              <option value="illness">Illness/Injury Consultation</option>
              <option value="emergency">Emergency</option>
              <option value="other">Other</option>
            </select>
          </label>
        </div>

        <!-- Section B: Clinic Selection -->
        <div class="form-section">
          <h4 class="section-title">Select Clinic</h4>
          <label class="field">
            <span>Clinic Location *</span>
            <div id="clinicSelectorContainer"></div>
            <input type="hidden" name="clinic" id="clinicSelect" required>
          </label>
        </div>

        <!-- Section C: Veterinarian Selection -->
        <div class="form-section" id="vetSection" style="display:none;">
          <h4 class="section-title">Select Veterinarian</h4>
          <div id="vetsList" class="vets-grid">
            <!-- Vets will be loaded here dynamically -->
          </div>
          <input type="hidden" name="vet_id" id="selectedVetId">
        </div>

        <!-- Section D: Date Selection -->
        <div class="form-section" id="dateSection" style="display:none;">
          <h4 class="section-title">Select Date</h4>
          <div id="calendarWidget" class="calendar-widget">
            <!-- Calendar will be generated here -->
          </div>
          <input type="hidden" name="date" id="appointmentDate" required>
          <p style="margin:12px 0 0; font-size:11px; color:#64748b;">
            📅 Available dates: Next 30 days (excludes clinic closed days and blocked dates)
          </p>
        </div>

        <!-- Section E: Time Selection -->
        <div class="form-section" id="timeSection" style="display:none;">
          <h4 class="section-title">Select Time</h4>
          <div id="timeSlotsGrid" class="time-slots-grid">
            <!-- Time slots will be loaded dynamically -->
          </div>
          <input type="hidden" name="time" id="appointmentTime" required>
          <p style="margin:12px 0 0; font-size:11px; color:#64748b;">
            ⏱️ Slot duration: 20 minutes | Times show only available slots
          </p>
        </div>

        <!-- Important Notice -->
        <div class="form-section" id="noticeSection" style="display:none;">
          <div>
            <p>
              <strong>⚠️ Important:</strong> If you arrive late to your appointment, it may be cancelled. Please arrive 10 minutes early.
            </p>
          </div>
        </div>
      </div>

      <!-- Confirmation View (hidden initially) -->
      <div class="dialog-body" id="appointmentConfirmation" style="display:none;text-align:center;">
        <div style="font-size:64px; margin-bottom:16px;">📨</div>
        <h3 style="color:#2563eb; margin-bottom:8px;">Appointment Requested!</h3>
        <p style="color:#64748b; font-size:14px; margin:0 0 20px 0;">Your appointment request has been submitted successfully. You'll be notified once the receptionist confirms your appointment.</p>
        <div id="appointmentSummary" style="text-align:left; background:#f8fafc; padding:20px; border-radius:8px; margin-top:20px;">
        </div>
      </div>

      <!-- Review/Confirmation View (before final confirmation) -->
      <div class="dialog-body" id="appointmentReview" style="display:none;">
        <div style="text-align:center; margin-bottom:20px;">
          <div style="font-size:48px; margin-bottom:12px;">📋</div>
          <h3 style="color:#1e293b; margin-bottom:8px; font-size:20px;">Review Your Appointment</h3>
          <p style="color:#64748b; font-size:13px; margin:0;">Please review the details before confirming</p>
        </div>
        <div id="appointmentReviewSummary" style="background:#f8fafc; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
        </div>
      </div>

      <footer class="dialog-actions">
        <button class="btn ghost" type="button" value="cancel" id="appointmentCancelBtn">Cancel</button>
        <button class="btn outline" type="button" id="appointmentBackBtn" style="display:none;">Back</button>
        <button class="btn primary" type="button" value="save" id="appointmentConfirmBtn" disabled>Confirm Appointment</button>
        <button class="btn primary" type="button" id="appointmentFinalConfirmBtn" style="display:none;">Yes, Book Appointment</button>
      </footer>
    </form>
  </dialog>

  <!-- Delete Pet Confirmation Dialog -->
  <dialog id="deletePetDialog" class="dialog">
    <form method="dialog" class="dialog-card">
      <header class="dialog-header">
        <h3>Delete Pet Profile</h3>
        <p class="dialog-subtitle">This action cannot be undone</p>
      </header>
      <div class="dialog-body">
        <p style="margin: 0 0 16px; color: #374151; font-size: 15px;">
          Are you sure you want to delete <strong id="deletePetName"></strong>'s profile?
        </p>
        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px;">
          <p style="margin: 0; color: #991b1b; font-size: 14px;">
            <strong>⚠️ Warning:</strong> This will permanently delete all pet information including medical records and appointment history.
          </p>
        </div>
      </div>
      <footer class="dialog-actions">
        <button class="btn ghost" value="cancel">Cancel</button>
        <button class="btn danger" id="confirmDeletePetBtn" value="confirm" type="button">Delete Pet</button>
      </footer>
    </form>
  </dialog>


  <?php
  // Pass pets array and clinics to JS
  echo '<script>window.petsData = ' . json_encode($pets) . ';</script>';
  echo '<script>window.clinicsData = ' . json_encode($clinics) . ';</script>';
  ?>
  <script src="/PETVET/public/js/pet-owner/booking-calendar.js"></script>
  <script src="/PETVET/public/js/pet-owner/clinic-distance.js"></script>
  <script src="/PETVET/public/js/pet-owner/clinic-selector.js"></script>
  <script src="/PETVET/public/js/pet-owner/my-pets.js"></script>
  <script>
    /* ========================================
       NOTIFICATION PANEL FUNCTIONALITY
       ======================================== */
    (function() {
      const notificationBell = document.getElementById('notificationBell');
      const notificationPanel = document.getElementById('notificationPanel');
      const notificationBadge = document.getElementById('notificationBadge');
      const markAllReadBtn = document.getElementById('markAllRead');
      const notificationList = document.getElementById('notificationList');

      // Toggle notification panel
      notificationBell.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationPanel.classList.toggle('show');
      });

      // Close panel when clicking outside
      document.addEventListener('click', function(e) {
        if (!notificationPanel.contains(e.target) && !notificationBell.contains(e.target)) {
          notificationPanel.classList.remove('show');
        }
      });

      // Prevent panel from closing when clicking inside
      notificationPanel.addEventListener('click', function(e) {
        e.stopPropagation();
      });

      // Mark single notification as read when clicked
      notificationList.addEventListener('click', function(e) {
        const notificationItem = e.target.closest('.notification-item');
        if (notificationItem && notificationItem.classList.contains('unread')) {
          notificationItem.classList.remove('unread');
          updateBadgeCount();
          
          // TODO: Backend - Send AJAX request to mark notification as read
          // Example:
          // const notificationId = notificationItem.dataset.id;
          // fetch('/PETVET/api/notifications/mark-read.php', {
          //   method: 'POST',
          //   headers: { 'Content-Type': 'application/json' },
          //   body: JSON.stringify({ notificationId: notificationId })
          // });
        }
      });

      // Mark all notifications as read
      markAllReadBtn.addEventListener('click', function() {
        const unreadNotifications = notificationList.querySelectorAll('.notification-item.unread');
        unreadNotifications.forEach(notification => {
          notification.classList.remove('unread');
        });
        updateBadgeCount();

        // TODO: Backend - Send AJAX request to mark all notifications as read
        // fetch('/PETVET/api/notifications/mark-all-read.php', {
        //   method: 'POST',
        //   headers: { 'Content-Type': 'application/json' }
        // });
      });

      // Update badge count
      function updateBadgeCount() {
        const unreadCount = notificationList.querySelectorAll('.notification-item.unread').length;
        if (unreadCount > 0) {
          notificationBadge.textContent = unreadCount;
          notificationBadge.classList.remove('hidden');
        } else {
          notificationBadge.classList.add('hidden');
        }
      }

      // Initialize badge count on page load
      updateBadgeCount();

      // TODO: Backend - Fetch notifications from server
      // This function should be called on page load and periodically to check for new notifications
      /*
      function fetchNotifications() {
        fetch('/PETVET/api/notifications/get-notifications.php')
          .then(response => response.json())
          .then(data => {
            renderNotifications(data.notifications);
            updateBadgeCount();
          })
          .catch(error => console.error('Error fetching notifications:', error));
      }

      function renderNotifications(notifications) {
        notificationList.innerHTML = '';
        
        notifications.forEach(notification => {
          const notificationItem = document.createElement('div');
          notificationItem.className = `notification-item ${notification.is_read ? '' : 'unread'}`;
          notificationItem.dataset.id = notification.id;
          notificationItem.dataset.type = notification.type;
          
          // Create icon based on notification type
          let iconSvg = '';
          let iconClass = '';
          
          switch(notification.type) {
            case 'appointment':
              iconClass = 'appointment-icon';
              iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
              break;
            case 'sitter':
              iconClass = 'sitter-icon';
              iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
              break;
            case 'trainer':
              iconClass = 'trainer-icon';
              iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>';
              break;
            case 'breeder':
              iconClass = 'breeder-icon';
              iconSvg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
              break;
          }
          
          notificationItem.innerHTML = `
            <div class="notification-icon ${iconClass}">
              ${iconSvg}
            </div>
            <div class="notification-content">
              <p class="notification-text">${notification.message}</p>
              <span class="notification-time">${notification.time_ago}</span>
            </div>
          `;
          
          notificationList.appendChild(notificationItem);
        });
      }

      // Fetch notifications on page load
      fetchNotifications();

      // Poll for new notifications every 30 seconds
      setInterval(fetchNotifications, 30000);
      */

    })();

    // Prevent background scroll when dialog is open
    (function() {
      const dialogs = document.querySelectorAll('dialog');
      
      dialogs.forEach(dialog => {
        // When dialog opens
        dialog.addEventListener('click', function(e) {
          if (e.target === dialog) {
            // Clicked on backdrop
            document.body.classList.remove('dialog-open');
          }
        });

        // MutationObserver to detect when dialog opens/closes
        const observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'open') {
              if (dialog.hasAttribute('open')) {
                document.body.classList.add('dialog-open');
              } else {
                document.body.classList.remove('dialog-open');
              }
            }
          });
        });

        observer.observe(dialog, {
          attributes: true
        });

        // Handle form close
        dialog.addEventListener('close', function() {
          document.body.classList.remove('dialog-open');
        });

        // Handle cancel button
        const cancelButtons = dialog.querySelectorAll('button[value="cancel"]');
        cancelButtons.forEach(btn => {
          btn.addEventListener('click', function() {
            document.body.classList.remove('dialog-open');
          });
        });
      });

      // Also handle programmatic showModal calls
      const originalShowModal = HTMLDialogElement.prototype.showModal;
      HTMLDialogElement.prototype.showModal = function() {
        originalShowModal.call(this);
        document.body.classList.add('dialog-open');
      };

      const originalClose = HTMLDialogElement.prototype.close;
      HTMLDialogElement.prototype.close = function() {
        originalClose.call(this);
        document.body.classList.remove('dialog-open');
      };
    })();


  </script>
</body>
</html>
