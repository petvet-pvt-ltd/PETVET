# 🔐 Test Accounts - All Roles

## 📋 Account Credentials

All accounts use the same password for testing: **`password123`**

| Role | Email | Name | Password |
|------|-------|------|----------|
| **Admin** | admin@petvet.com | Admin User | Admin@123 |
| **Pet Owner** | john.doe@example.com | John Doe | password123 |
| **Vet** | dr.sarah@happypaws.lk | Dr. Sarah Smith | password123 |
| **Clinic Manager** | manager@happypaws.lk | Michael Manager | password123 |
| **Trainer** | trainer@gmail.com | Tom Trainer | password123 |
| **Sitter** | sitter@gmail.com | Sam Sitter | password123 |
| **Breeder** | breeder@gmail.com | Bob Breeder | password123 |
| **Groomer** | groomer@gmail.com | Grace Groomer | password123 |
| **Receptionist** | receptionist@gmail.com | Rita Receptionist | password123 |

---

## 🚀 Quick Import

### **Option 1: Import via phpMyAdmin**
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Select `petvet` database
3. Click "Import" tab
4. Choose file: `database/migrations/002_add_test_accounts_all_roles.sql`
5. Click "Go"

### **Option 2: MySQL Command Line**
```bash
mysql -u root -p petvet < "C:\xampp\htdocs\PETVET\database\migrations\002_add_test_accounts_all_roles.sql"
```

### **Option 3: Copy-Paste in phpMyAdmin SQL Tab**
1. Open phpMyAdmin
2. Select `petvet` database
3. Click "SQL" tab
4. Open the SQL file and copy all contents
5. Paste and execute

---

## ✅ What Gets Created

### **5 New Users:**
- ✅ Trainer (Tom Trainer)
- ✅ Sitter (Sam Sitter)
- ✅ Breeder (Bob Breeder)
- ✅ Groomer (Grace Groomer)
- ✅ Receptionist (Rita Receptionist)

### **User Details:**
- All accounts are **active** and **email verified**
- All roles are **approved** (no pending verification)
- All have **phone numbers** and **addresses**
- Service providers have **business profiles** with ratings

### **Service Provider Profiles:**
- **Trainer**: Pro Pet Training (4.8★ rating, 45 reviews)
- **Sitter**: Caring Pet Sitters (4.9★ rating, 67 reviews)
- **Breeder**: Premium Breeders (4.7★ rating, 23 reviews)
- **Groomer**: Pawfect Grooming (5.0★ rating, 89 reviews)

### **Receptionist Profile:**
- Linked to clinic_id 1
- Shift: Monday-Friday 8AM-4PM

---

## 🧪 Testing Checklist

After importing, test each role:

- [ ] Login as **trainer@gmail.com** → Should see Trainer dashboard
- [ ] Login as **sitter@gmail.com** → Should see Sitter dashboard
- [ ] Login as **breeder@gmail.com** → Should see Breeder dashboard
- [ ] Login as **groomer@gmail.com** → Should see Groomer dashboard
- [ ] Login as **receptionist@gmail.com** → Should see Receptionist dashboard

---

## 📝 Notes

- **No overlapping roles** - Each user has only ONE role
- **All approved** - No pending verification required
- **Same password** - Easy to remember for testing: `password123`
- **Realistic data** - Each has business name, ratings, reviews, etc.

---

## 🔄 If You Need to Reset

To delete these test accounts and start over:

```sql
DELETE FROM users WHERE email IN (
    'trainer@gmail.com',
    'sitter@gmail.com',
    'breeder@gmail.com',
    'groomer@gmail.com',
    'receptionist@gmail.com'
);
```

(Note: This will cascade delete related records due to foreign key constraints)
