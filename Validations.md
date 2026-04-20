# 📋 HTML Input Validation Patterns (No JavaScript)

## 🔤 Name (Letters + Spaces)

```html
<input type="text" pattern="[A-Za-z ]{3,30}" required>
```

* Only letters and spaces
* Length: 3–30 characters

---

## 📧 Email

```html
<input type="email" required>
```

### Custom pattern (optional stricter)

```html
<input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
```

---

## 📱 Phone Number

### Sri Lanka (Local)

```html
<input type="tel" pattern="0[0-9]{9}" required>
```

* Starts with `0`
* Exactly 10 digits

### International (+94)

```html
<input type="tel" pattern="\+94[0-9]{9}" required>
```

---

## 🔐 Password

### Basic (6+ chars, letter + number)

```html
<input type="password" pattern="(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}" required>
```

### Strong Password

```html
<input type="password" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}" required>
```

---

## 🔢 Only Numbers

```html
<input type="text" pattern="[0-9]+" required>
```

---

## 🔡 Only Letters

```html
<input type="text" pattern="[A-Za-z]+" required>
```

---

## 🔠 Alphanumeric

```html
<input type="text" pattern="[A-Za-z0-9]+" required>
```

---

## 🪪 NIC (Sri Lanka)

### Old NIC (9 digits + V/X)

```html
<input type="text" pattern="[0-9]{9}[vVxX]" required>
```

### New NIC (12 digits)

```html
<input type="text" pattern="[0-9]{12}" required>
```

---

## 🚗 Vehicle Number (Sri Lanka)

```html
<input type="text" pattern="[A-Z]{2,3}-[0-9]{4}" required>
```

* Example: `CAB-1234`

---

## 🔗 URL

```html
<input type="url" required>
```

---

## 📮 Postal Code (5 digits)

```html
<input type="text" pattern="[0-9]{5}" required>
```

---

## 🎯 Fixed Value (Specific Input Only)

```html
<input type="text" pattern="Golden Retriever" required>
```

---

## 📅 Date (Future Only Example)

```html
<input type="date" min="2026-04-20" required>
```

---

## 📂 File Upload (Images Only)

```html
<input type="file" accept="image/*" required>
```

---

## 🧠 Quick Regex Cheat Sheet

| Pattern       | Meaning                  |
| ------------- | ------------------------ |
| `[A-Za-z]`    | Letters only             |
| `[0-9]`       | Digits only              |
| `[A-Za-z0-9]` | Letters + numbers        |
| `{n}`         | Exactly n characters     |
| `{min,max}`   | Range of characters      |
| `+`           | One or more              |
| `*`           | Zero or more             |
| `(?=...)`     | Must include (lookahead) |

---

## ⚠️ Notes

* Works **without JavaScript**
* Browser handles validation automatically
* Can be bypassed → always validate in backend

---
