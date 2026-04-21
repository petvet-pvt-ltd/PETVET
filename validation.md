Here’s a clean **Markdown (.md) file** you can copy directly 👇

---

````md
# ✅ Inline HTML Validation (No JavaScript)

This document contains common HTML validation techniques that can be applied directly within input fields without using JavaScript.

---

## 🔹 1. Age Field Validation
```html
<input type="number" name="age" min="1" max="120" required>
````

* Prevents negative and unrealistic values

---

## 🔹 2. Phone Number Validation (Sri Lanka Format)

```html
<input type="tel" name="phone" pattern="07[0-9]{8}" placeholder="07XXXXXXXX" required>
```

* Must start with `07`
* Must be exactly 10 digits

---

## 🔹 3. Registration Date (Prevent Future Dates)

```html
<input type="date" name="regDate" max="2026-04-20" required>
```

* Ensures user cannot select a future date

---

## 🔹 4. Image File Upload Validation

```html
<input type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" required>
```

* Allows only image files (UI-level restriction)

---

## 🔹 5. PDF File Upload Validation

```html
<input type="file" accept="application/pdf" required>
```

* Allows only PDF files (UI-level restriction)

---

## 🔹 6. Vehicle Chassis Number Validation

```html
<input type="text" pattern="[A-Za-z][0-9]+[A-Za-z]" required>
```

* First and last characters must be letters
* Middle characters must be digits

---

## 🔹 7. NIC Validation (Old + New Formats)

```html
<input type="text" pattern="([0-9]{9}[vVxX]|[0-9]{12})" required>
```

* Old format: `123456789V`
* New format: `200012345678`

---

## 🔹 8. Country Code + Phone Number

```html
<select name="countryCode" required>
  <option value="">Select Code</option>
  <option value="+94">+94</option>
  <option value="+91">+91</option>
</select>

<input type="tel" pattern="[0-9]{9}" placeholder="XXXXXXXXX" required>
```

* Country code via dropdown
* Phone number must be 9 digits

---

## 🔹 9. Date (Restrict Past Dates)

```html
<input type="date" name="startDate" min="2026-04-20" required>
```

* Prevents selecting past dates

---

## 🔹 10. General Text Field Validation

```html
<input type="text" required minlength="3" maxlength="50">
```

* Minimum and maximum length control

---

## 🔹 11. Dropdown Required Validation

```html
<select required>
  <option value="">Select option</option>
  <option value="A">Option A</option>
</select>
```

* Ensures user selects a value

---

## 🔹 12. Numeric Range Validation

```html
<input type="number" min="0" max="1000000" required>
```

* Restricts value range

---

## 🔹 13. File Upload Required

```html
<input type="file" required>
```

* Ensures file is selected before submission

---

## 🔹 14. Time Period (Months)

```html
<input type="number" name="months" min="1" max="120" required>
```

* Accepts valid month ranges

---

# ⚠️ Important Notes

* HTML validation is **client-side only**
* It can be bypassed easily
* **Backend validation is always required for security**

---

# 🔥 Summary

| Attribute    | Purpose                          |
| ------------ | -------------------------------- |
| `required`   | Mandatory field                  |
| `pattern`    | Format validation                |
| `min`, `max` | Range control                    |
| `type`       | Input type validation            |
| `accept`     | File type filtering (not secure) |


