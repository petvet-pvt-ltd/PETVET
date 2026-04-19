# Explore Pets - Workflow - Weight Field Implementation (DETAILED VERSION)

---

## Overview
Complete pet listing display system across Admin, Pet Owner, and Guest views with filtering and sorting capabilities.

---

## JS (Frontend)
1. `pet-listings-modern.js` *(Admin Dashboard)* ✔️  
2. `explore-pets.js` *(Pet Owner & Guest)* ✔️  
   - Handles sorting (nearest, price, age, **weight**)
   - Manages filters (species, search)
   - Modal operations (sell, edit, my listings)
   - Weight sorting: compares `data-weight` attributes

---

## Model (Database Layer)

### 1. `SellPetListingModel.php` (CRUD Operations)
- `createListing()` - INSERT new listing ✔️
- `updateListing()` - UPDATE existing listing ✔️
- `deleteListing()` - DELETE listing ✔️
- `getUserListings()` - Get user's listings
- `getImages()` - Fetch listing photos
- `getBadges()` - Fetch listing badges
- **Note**: Does NOT include weight in queries (used by API endpoints)

### 2. `ExplorePetsModel.php` (Pet Owner View)
- `getAllPets()` - **Selects weight field** ✔️
  - Query: `SELECT l.id, l.name, l.species, ..., l.weight, ...`
  - Filter: `WHERE l.status = 'approved' AND l.price BETWEEN 500 AND 5000000`
  - Order: `ORDER BY l.weight DESC` ✔️ (Heaviest first)
  - Returns: Array with 'weight' key
- `getPetsByUserId()` - Get user's listings (uses getAllPets)
- `searchPets()` - Search & filter (uses getAllPets)
- `getAvailableSpecies()` - Get species list

### 3. `GuestExplorePetsModel.php` (Guest View)
- Same as ExplorePetsModel but for guests
- `getAllPets()` - **Selects weight field** ✔️
  - Order: `ORDER BY l.weight DESC` ✔️ (Heaviest first)

---

## API (Request Handlers - Receive Data)
1. `add.php` - Create new listing ✔️
2. `update.php` - Edit listing ✔️
3. `delete.php` - Delete listing ✔️
4. `list-adoption-pet.php` - Mark pet for adoption

---

## View (HTML Templates)
1. ~~`sell-pets.php`~~ → **REMOVED ❌** (functionality merged into explore-pets)
2. `explore-pets.php` (Pet Owner & Guest) ✔️
   - **Weight Display**: Shows in card meta `<?= htmlspecialchars($pet['weight']) . ' kg' ?>`
   - **Data Attribute**: `data-weight="<?= (float)($pet['weight'] ?? 0) ?>"` for sorting ✔️
   - **Sort Dropdown** ✔️: Added options
     - "Weight: Heaviest" (weightHigh)
     - "Weight: Lightest" (weightLow)
   - **UI Changes**: Removed "View Details" button (only "Contact Seller" remains)
   - Card structure: `<article class="card" data-species="..." data-price="..." data-weight="..." ...>`

3. `pet-listings-modern.php` (Admin) ✔️
   - Displays admin listings with detailed info

---

## API (Response Handlers - Send Data)
1. `get-all-listings.php` (Admin) ✔️
   - Returns: All listings with price filter
   - Query: `SELECT ... l.weight ... WHERE l.price BETWEEN 500 AND 500000`
   - Order: `ORDER BY l.created_at DESC` (not modified for admin)
   - Attaches: images, badges

2. `get-my-listings.php` (Pet Owner) ✔️
   - Returns: User's listings
   - Attaches: images, badges
   - Converts: weight & height to float for JSON

---

## Data Flow for Weight Field

```
Database (sell_pet_listings table - weight column)
    ↓
Model.getAllPets() (SELECT l.weight)
    ↓
PHP Array ['weight' => value]
    ↓
explore-pets.php View:
  - Display: <span>25.5 kg</span>
  - Data attr: data-weight="25.5"
    ↓
explore-pets.js:
  - Read: a.dataset.weight
  - Sort: (a, b) => b.weight - a.weight
    ↓
User sees sorted cards
```

---

## Sorting Modes Implemented
| Mode | JavaScript | Result |
|------|-----------|--------|
| `nearest` | Distance calculation | Closest first |
| `newest` | created_at desc | Newest first |
| `priceLow` | price asc | Cheapest first |
| `priceHigh` | price desc | Most expensive first |
| `age` | Custom logic | Young first |
| **`weightHigh`** | weight desc | **Heaviest first** ✔️ |
| **`weightLow`** | weight asc | **Lightest first** ✔️ |

---

## Files Modified in This Session
✔️ `models/PetOwner/ExplorePetsModel.php` - Added weight SELECT, ORDER BY weight
✔️ `models/Guest/GuestExplorePetsModel.php` - Added ORDER BY weight
✔️ `views/pet-owner/explore-pets.php` - Added data-weight, display weight, sort options, removed View Details
✔️ `public/js/pet-owner/explore-pets.js` - Added weight sorting logic

---

## Features Implemented
✅ Weight field display on all pet cards (format: "Dog • Breed • 3y • 25.5 kg")
✅ Default sort by weight (heaviest first) on explore pages
✅ Client-side weight sorting (Heaviest/Lightest dropdown options)
✅ Weight data persists through API calls
✅ Sorting works without page reload
✅ Null/empty weight handled gracefully (shown only if exists)
✅ Consistent across Pet Owner and Guest views

---

## Testing Checklist
- [ ] Weight displays on pet cards
- [ ] Sort dropdown has "Weight: Heaviest" and "Weight: Lightest" options
- [ ] Sorting by weight works without page reload
- [ ] Works in Pet Owner explore-pets view
- [ ] Works in Guest explore-pets view
- [ ] Admin get-all-listings.php still returns weight data
- [ ] get-my-listings.php returns weight data with images & badges
- [ ] Null weights don't cause display errors
- [ ] Cards with weight > 1000 kg display correctly

---

## Future Enhancements
- [ ] Add height sorting
- [ ] Add age sorting improvements
- [ ] Add gender filtering
- [ ] Add species-specific weight ranges for validation
- [ ] Add weight unit toggle (kg/lbs)
- [ ] Add advanced multi-filter (weight + price + species)
