
# Explore Pets - Workflow - weight field adding

---

## JS (Frontend)
1. `pet-listings-modern.js` *(Admin)* ✔️  
2. `explore-pets.js` *(Pet Owner)* ✔️  

---

## Model
1. `SelPetListingModel.php`  
   - Create  
   - Update  
   - Delete ✔️  

2. `ExplorePetsModel.php`(Frontend cards)-> responsible for viewing also
   - Select queries  (with different where conditions to filter)

3. `GuestExplorePetsModel.php`  
   - Responsible for views  
   - Handles queries in guest

## API (Requests)
1. `add.php`  
2. `update.php`  
3. `list-adoption-pet.php`  

*(Handles incoming requests from frontend)*

---

## View
1. ~~sell-pets~~ → **Not used ❌**  
2. `explore-pets` → Input handled ✔️ *(Guest / petowner)*  
3. `pet-listings-modern.php` → Display HTML ✔️ *(Admin)*  

---

## API (Responses)
1. `get-all-listings.php` ✔️ *(Admin - optional condition-based query)*  
2. `get-my-listings.php` ✔️  

---
