# Individual Contribution Report (Pet Owner + Service Providers)

Name: ______________________
Date: ______________________
Project: PETVET

## 1) What I worked on (high level)
I implemented and maintained the **Pet Owner module** and the **Service Provider modules** (**Trainer, Sitter, Breeder, Groomer, Vet**) including:
- Role-based routing and page flows (module/page pattern)
- Frontend pages (PHP views) + role JS files
- Backend APIs (role-based `/api/...` endpoints) and controller actions
- Database read/write logic via models and SQL
- Notification creation for provider decisions (accepted/declined/completed)

## 2) How the system works (simple explanation)
Think of the app like a restaurant:
- **View (PHP page)** = the menu you see
- **JavaScript** = the waiter taking your order (button clicks)
- **API endpoint / Controller action** = the waiter sending the order to the kitchen
- **Model + SQL** = the kitchen cooking (read/write DB tables)
- **JSON response** = the waiter bringing the result back

Routing pattern:
- `index.php?module=<role>&page=<page>` chooses a controller + view
- Views live in `views/<role>/<page>.php`
- JS often lives in `public/js/<role>/*.js`
- APIs live in `api/<role>/*.php`

## 3) Exact files I own (modification map)

### A) Pet Owner pages (frontend)
- views/pet-owner/my-pets.php
- views/pet-owner/medical-records.php
- views/pet-owner/appointments.php
- views/pet-owner/services.php
- views/pet-owner/lost-found.php
- views/pet-owner/explore-pets.php
- views/pet-owner/sell-pets.php
- views/pet-owner/settings.php
- views/pet-owner/shop.php
- views/pet-owner/shop-clinic.php
- views/pet-owner/shop-product.php
- views/pet-owner/orders.php

### B) Pet Owner JS (frontend logic)
- public/js/pet-owner/my-pets.js
- public/js/pet-owner/appointments.js
- public/js/pet-owner/lost-found.js
- public/js/pet-owner/settings.js
- public/js/pet-owner/cart-manager.js
- public/js/pet-owner/clinic-selector.js
- public/js/pet-owner/clinic-distance.js
- public/js/pet-owner/shop-clinics.js
- public/js/pet-owner/shop-product.js
- public/js/pet-owner/explore-pets.js

### C) Pet Owner APIs (backend)
Core request + booking APIs:
- api/pet-owner/create-training-request.php
- api/pet-owner/create-sitter-request.php
- api/pet-owner/create-breeding-request.php
- api/pet-owner/get-my-bookings.php
- api/pet-owner/cancel-booking.php

Pets:
- api/pet-owner/get-my-pets.php
- api/pet-owner/pets/add.php
- api/pet-owner/pets/update.php
- api/pet-owner/pets/delete.php
- api/pet-owner/pets/permanent-delete.php
- api/pet-owner/mark-pet-missing.php

Notifications:
- api/pet-owner/get-notifications.php
- api/pet-owner/mark-notification-read.php

Lost/Found:
- api/pet-owner/get-reports.php
- api/pet-owner/get-my-reports.php
- api/pet-owner/get-reports-by-distance.php
- api/pet-owner/submit-report.php
- api/pet-owner/update-report.php
- api/pet-owner/delete-report.php

Account:
- api/pet-owner/update-profile.php
- api/pet-owner/update-password.php

Shop + orders:
- api/pet-owner/cart.php
- api/pet-owner/calculate-delivery.php
- api/pet-owner/orders.php
- api/pet-owner/download-invoice.php
- api/pet-owner/download-invoice-pdf.php
- api/pet-owner/shop-wishlist.php
- api/pet-owner/shop-favorites.php

Location helpers:
- api/pet-owner/reverse-geocode.php
- api/pet-owner/get-clinics-by-distance.php
- api/pet-owner/get-vets.php
- api/pet-owner/get-clinics.php
- api/pet-owner/get-groomers-by-distance.php
- api/pet-owner/get-sitters-by-distance.php
- api/pet-owner/get-breeders-by-distance.php


### D) Pet Owner controller + models
- controllers/PetOwnerController.php
- models/PetOwner/* (MyPetsModel, ServicesModel, SettingsModel, LostFoundModel, etc.)


### E) Trainer (service provider)
Frontend:
- views/trainer/appointments.php
- public/js/trainer/appointments.js

Backend:
- controllers/TrainerController.php (action handler: `handleTrainingAction`)
- models/Trainer/AppointmentsModel.php
- api/trainer/poll-pending-requests.php (UI polling)
- api/trainer/get-settings.php + api/trainer/update-settings.php (settings)

DB tables (core):
- trainer_training_requests (status: pending/accepted/declined/completed)
- trainer_training_sessions (session history)


### F) Sitter (service provider)
Frontend:
- views/sitter/bookings.php
- public/js/shared/bookings.js (confirm accept/decline/complete)

Backend:
- controllers/SitterController.php (action handler: `handleBookingAction`)
- models/Sitter/BookingsModel.php
- api/sitter/poll-pending-bookings.php
- api/sitter/get-settings.php + api/sitter/update-settings.php

DB tables (core):
- sitter_service_requests (status: pending/accepted/completed/declined)


### G) Breeder (service provider)
Frontend:
- views/breeder/requests.php
- public/js/breeder/requests.js

Backend:
- api/breeder/poll-requests.php
- api/breeder/manage-requests.php (accept/decline/complete + get_active_pets)
- models/Breeder/DashboardModel.php

DB tables (core):
- breeding_requests (status: pending/approved/declined/completed)
- breeder_pets


### H) Groomer (service provider)
Frontend:
- views/groomer/services.php + public/js/groomer/services.js
- views/groomer/packages.php + public/js/groomer/packages.js

Backend:
- controllers/GroomerController.php (service/package action handlers)
- api/groomer/services.php
- api/groomer/packages.php
- api/groomer/get-settings.php + api/groomer/update-settings.php

DB tables: (groomer services/packages tables used by model SQL)


### I) Vet (service provider)
Frontend:
- views/vet/*
- public/js/vet/dashboard.js
- public/js/vet/medical-records.js
- public/js/vet/prescriptions.js
- public/js/vet/vaccinations.js

Backend APIs:
- api/vet/dashboard-data.php
- api/vet/appointments/update-status.php
- api/vet/medical-records/add.php
- api/vet/prescriptions/add.php
- api/vet/vaccinations/add.php

Controller + models:
- controllers/VetController.php
- models/Vet/*


### J) Notifications (shared)
- helpers/NotificationHelper.php
- views/shared/sidebar/notification-bell.php

## 4) Manual test cases I can demonstrate
Use these as your “test cases” section.

Pet Owner:
1. Add pet → Edit pet → Delete pet (verify DB + UI refresh).
2. Open Services page → Create trainer request (required fields + map location validation).
3. Create sitter request (single vs multiple duration) and verify it appears for sitter.
4. Create breeder request and verify availability validation + request appears for breeder.
5. Accept/decline/completed from provider side → verify notification appears for pet owner.
6. Lost/Found: submit report → update report → delete report.
7. Settings: update profile + update password.
8. Shop: add to cart → checkout flow → order appears in orders; invoice downloads.

Providers:
1. Trainer: accept request → complete session (notes + next session date/time) → notification.
2. Sitter: accept/decline/complete booking → notification.
3. Breeder: accept (choose breeding pet) → decline (reason) → complete → notification.
4. Groomer: add service → update price → toggle availability; add package.
5. Vet: update appointment status; add medical record/prescription/vaccination.

## 5) Common evaluation modifications (what file to change)

If they ask… “Change UI text / button / layout on a page”
- Edit the page in `views/<role>/<page>.php`.

If they ask… “Change what happens when clicking a button”
- Look in `public/js/<role>/*.js` for the `fetch()` call.

If they ask… “Change validation rules / business rules”
- Look in the backend endpoint:
  - Pet Owner: `api/pet-owner/*.php`
  - Provider: controller action (trainer/sitter) OR `api/<role>/*.php`

If they ask… “Change what is stored in DB or statuses”
- Find the model (`models/<role>/*.php`) and update the SQL.

## 6) Contribution percentage
My estimated share: _______%
(Explain based on the number of role modules + APIs + pages completed.)
