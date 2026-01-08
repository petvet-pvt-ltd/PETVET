# Vet Dashboard Design System - Quick Reference

## 🎨 Color Palette

### Primary Colors (Navy Theme - Matches Header)
```
Primary:       #17293F  ███████  Main brand color, buttons, headers
Primary Hover: #213a56  ███████  Button hover states
Primary Dark:  #0f1a2a  ███████  Deep accents
Secondary:     #2a4a6f  ███████  Secondary elements
Accent:        #3a5f8f  ███████  Highlights
```

### Status Colors
```
Success:  #10b981  ███████  Positive actions, completed
Warning:  #f59e0b  ███████  Caution, pending
Danger:   #ef4444  ███████  Errors, destructive actions
```

### Neutral Colors
```
Background: #f0f4f8  ███████  Page background
Card:       #ffffff  ███████  White cards
Border:     #d4dce5  ███████  Light borders
Line:       #e8ecf1  ███████  Separator lines
Text:       #17293F  ███████  Primary text (same as brand)
Muted:      #6b7c93  ███████  Secondary text
```

## 📐 Component Styles

### Buttons
```css
Primary Button:
  background: #17293F
  color: white
  padding: 10px 20px
  border-radius: 8px
  hover: #213a56

Secondary Button:
  background: #2a4a6f
  hover: lighter shade

Success Button:
  background: #10b981
```

### Input Fields
```css
Text Input / Select / Textarea:
  border: 2px solid #d4dce5
  border-radius: 8px
  padding: 12px 14px
  focus-border: #17293F
  focus-shadow: rgba(23, 41, 63, 0.1)
  focus-background: #fafbfc
```

### Search Bars
```css
Search Input:
  border: 2px solid #d4dce5
  padding-left: 40px (for icon)
  background: search icon (navy)
  focus: navy border + shadow
  placeholder: descriptive text
```

### Tables
```css
Header:
  background: linear-gradient(135deg, #17293F, #2a4a6f)
  color: white
  padding: 14px 16px

Row:
  hover: rgba(23, 41, 63, 0.04)
  border-bottom: 1px solid #e8ecf1
```

### Sections
```css
Section:
  background: white
  padding: 24px
  border-radius: 12px
  border: 1px solid #d4dce5
  shadow: rgba(23, 41, 63, 0.08)

Section Header:
  font-size: 20px
  font-weight: 700
  border-bottom: 2px solid #17293F
  color: #17293F
```

### Forms
```css
Form Section:
  background: linear-gradient(145deg, #ffffff, #fafbfc)
  border: 2px solid #d4dce5
  border-radius: 12px
  padding: 24px
  shadow: rgba(23, 41, 63, 0.06)
```

## 📏 Spacing & Sizing

### Typography
```
Page Title:      32px, bold, #17293F
Page Subtitle:   16px, medium, #6b7c93
Section Header:  20px, bold, #17293F
Body Text:       14px, regular, #17293F
Small Text:      12px
```

### Spacing
```
Section margin-bottom:  24px
Card gap:               20px
Form field gap:         16px
Standard padding:       24px
Compact padding:        16px
```

### Border Radius
```
Large elements:  12px
Medium elements: 8px
Small elements:  6px
Pills/badges:    12px
```

### Borders
```
Standard:  1px solid #d4dce5
Emphasis:  2px solid #d4dce5
Strong:    2px solid #17293F
```

## 🎯 Usage Examples

### Page Header
```html
<div class="page-header">
  <h1 class="page-title">Page Title</h1>
  <p class="page-subtitle">Description text</p>
</div>
```

### Search Bar
```html
<input id="searchBar" placeholder="Search appointments...">
```

### Primary Button
```html
<button class="btn primary">Save Record</button>
```

### Section
```html
<section>
  <h3>Section Title</h3>
  <!-- content -->
</section>
```

### Form
```html
<div class="form-section">
  <form>
    <div class="form-row">
      <label>
        Field Name
        <input type="text" name="field">
      </label>
    </div>
  </form>
</div>
```

### Table
```html
<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Column</th></tr>
    </thead>
    <tbody>
      <tr><td>Data</td></tr>
    </tbody>
  </table>
</div>
```

## 🔍 Focus States

All interactive elements use consistent focus styling:
```css
focus:
  outline: none
  border-color: #17293F
  box-shadow: 0 0 0 3px rgba(23, 41, 63, 0.1)
```

## 🎭 Hover States

### Buttons
```
transform: translateY(-2px)
shadow: enhanced
```

### Cards
```
transform: translateY(-4px)
shadow: large
```

### Table Rows
```
background: rgba(23, 41, 63, 0.04)
```

### Links
```
color: darker shade
text-decoration: underline
```

## 📊 Status Badges

```css
.status-ongoing:
  background: #fef3c7
  color: #92400e
  border: #fbbf24

.status-upcoming:
  background: rgba(23, 41, 63, 0.1)
  color: #17293F
  border: #2a4a6f

.status-completed:
  background: #dcfce7
  color: #166534
  border: #10b981

.status-cancelled:
  background: #fee2e2
  color: #991b1b
  border: #ef4444
```

## 📱 Responsive Breakpoints

```
Mobile:  max-width: 768px
  - Sidebar collapses
  - Single column layouts
  - Adjusted padding
  - Full-width search bars
```

## ✅ Consistency Checklist

When adding new elements:
- [ ] Use navy (#17293F) for primary actions
- [ ] Use 2px borders for inputs
- [ ] Add navy focus states
- [ ] Include descriptive placeholders
- [ ] Use consistent spacing (24px/16px)
- [ ] Apply 8-12px border radius
- [ ] Use navy-tinted shadows
- [ ] Match existing component styles
- [ ] Test hover and focus states
- [ ] Verify mobile responsiveness

## 🚀 Quick Start

To match the design system:

1. **Colors**: Always use CSS variables (`var(--vet-primary)`)
2. **Spacing**: Use 24px for sections, 16px for compact areas
3. **Borders**: 2px for emphasis, 1px for subtle
4. **Shadows**: Use navy transparency `rgba(23, 41, 63, 0.08)`
5. **Typography**: 14px body, 20px headers, 32px titles
6. **Focus**: Always include navy focus ring
7. **Hover**: Add subtle transform or background change

---

**Version**: 1.0
**Last Updated**: January 7, 2026
**Color System**: Navy (#17293F) - Header Aligned
