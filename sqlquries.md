Here’s a clean **single Markdown file** you can copy directly:

````md
# SQL ALTER TABLE Queries

# SQL - ADD COLUMN with Different Data Types

This guide shows how to add columns with various data types using `ALTER TABLE`.

---

## Basic Syntax

```sql
ALTER TABLE table_name
ADD column_name datatype;
````

---

## 1. Add INTEGER column

```sql
ALTER TABLE students
ADD age INT;
```
ALTER TABLE lostfoundreport
ADD price INT NOT NULL DEFAULT 0 AFTER reward;
---

## 2. Add VARCHAR (text) column

```sql
ALTER TABLE students
ADD name VARCHAR(100);
```

---

## 3. Add DECIMAL (for money/precise values)

```sql
ALTER TABLE products
ADD price DECIMAL(10,2);
```

---

## 4. Add DATE column

```sql
ALTER TABLE employees
ADD birth_date DATE;
```

---

## 5. Add DATETIME / TIMESTAMP column

```sql
ALTER TABLE orders
ADD created_at DATETIME;
```

**MySQL (auto timestamp):**

```sql
ALTER TABLE orders
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

---

## 6. Add BOOLEAN column

```sql
ALTER TABLE users
ADD is_active BOOLEAN;
```

---

## 7. Add ENUM (MySQL only)

```sql
ALTER TABLE pets
ADD status ENUM('available', 'sold', 'pending');
```
<select name="reportType">
    <option value="lost">Lost</option>
    <option value="found">Found</option>
    <option value="urgent">Urgent</option> <!-- NEW -->
</select>

Make sure the database can store it (backend + DB)

If you're using a VARCHAR → nothing to change
If you're using ENUM → you must update it:

ALTER TABLE reports 
MODIFY reportType ENUM('lost', 'found', 'urgent');
---

## 8. Add TEXT column (for long content)

```sql
ALTER TABLE posts
ADD description TEXT;
```

---

## 9. Add column with DEFAULT value

```sql
ALTER TABLE students
ADD country VARCHAR(50) DEFAULT 'Sri Lanka';
```

---

## 10. Add NOT NULL column

```sql
ALTER TABLE students
ADD email VARCHAR(100) NOT NULL;
```

⚠️ Note: This may fail if the table already has rows and no default is provided.

---

## 11. Add UNIQUE column

```sql
ALTER TABLE users
ADD username VARCHAR(50) UNIQUE;
```

---

## 12. Add PRIMARY KEY column

```sql
ALTER TABLE users
ADD id INT PRIMARY KEY;
```

---

## 13. Add AUTO INCREMENT column (MySQL)

```sql
ALTER TABLE users
ADD id INT AUTO_INCREMENT PRIMARY KEY;
```

---

## 14. Add FOREIGN KEY column

```sql
ALTER TABLE orders
ADD user_id INT,
ADD CONSTRAINT fk_user
FOREIGN KEY (user_id) REFERENCES users(id);
```

---

## 15. Add Multiple Columns at Once

```sql
ALTER TABLE students
ADD (
    phone VARCHAR(15),
    address TEXT,
    age INT
);
```

---



## 2. Modify Column Datatype

```sql
ALTER TABLE table_name
MODIFY column_name new_datatype;
```

**Example:**

```sql
ALTER TABLE students
MODIFY age VARCHAR(3);
```

**SQL Server:**

```sql
ALTER TABLE students
ALTER COLUMN age VARCHAR(3);
```

---

## 3. Rename a Column

```sql
ALTER TABLE table_name
RENAME COLUMN old_name TO new_name;
```

**Example:**

```sql
ALTER TABLE students
RENAME COLUMN age TO student_age;
```

**MySQL (older versions):**


---

## 4. Delete (Drop) a Column

```sql
ALTER TABLE table_name
DROP COLUMN column_name;
```

**Example:**

```sql
ALTER TABLE students
DROP COLUMN age;
```

⚠️ Warning: This will permanently delete all data in that column.

---

## 5. Add a Constraint

```sql
ALTER TABLE table_name
ADD CONSTRAINT constraint_name UNIQUE (column_name);
```

**Example:**

```sql
ALTER TABLE students
ADD CONSTRAINT unique_email UNIQUE (email);
```

---

## 6. Drop a Constraint

```sql
ALTER TABLE table_name
DROP CONSTRAINT constraint_name;
```

**MySQL:**

```sql
ALTER TABLE students
DROP INDEX unique_email;
```

---

## 7. Rename a Table

```sql
ALTER TABLE old_table_name
RENAME TO new_table_name;
```

---

## Notes

* Renaming a column keeps existing data.
* Dropping a column deletes all its data permanently.
* Some operations may fail if constraints (primary key, foreign key, indexes) are involved.
* Always backup important data before altering tables.

`

## 1. Basic SELECT

```sql
SELECT * FROM table_name;
````

**Example:**

```sql
SELECT * FROM lostfoundreport;
```

---

## 2. SELECT with WHERE (Single Condition)

```sql
SELECT * FROM table_name
WHERE column_name = value;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE status = 'lost';
```

---

## 3. Multiple Conditions (AND)

```sql
SELECT * FROM table_name
WHERE condition1 AND condition2;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE status = 'lost' AND reward > 1000;
```

---

## 4. Multiple Conditions (OR)

```sql
SELECT * FROM table_name
WHERE condition1 OR condition2;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE status = 'lost' OR status = 'found';
```

---

## 5. Using Comparison Operators

```sql
SELECT * FROM table_name
WHERE column_name > value;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE reward > 500;
```

Operators:

* `=` equal
* `!=` or `<>` not equal
* `>` greater than
* `<` less than
* `>=`, `<=`

---

## 6. BETWEEN (Range)

```sql
SELECT * FROM table_name
WHERE column_name BETWEEN value1 AND value2;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE reward BETWEEN 500 AND 2000;
```

---

## 7. LIKE (Search pattern)

```sql
SELECT * FROM table_name
WHERE column_name LIKE 'pattern';
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE description LIKE '%dog%';
```

---

## 8. IN (Multiple values)

```sql
SELECT * FROM table_name
WHERE column_name IN (value1, value2);
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE status IN ('lost', 'pending');
```

---

## 9. IS NULL / IS NOT NULL

```sql
SELECT * FROM table_name
WHERE column_name IS NULL;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
WHERE price IS NULL;
```

---

## 10. ORDER BY (Sorting)

```sql
SELECT * FROM table_name
ORDER BY column_name ASC;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
ORDER BY reward DESC;
```

---

## 11. LIMIT (Restrict results)

```sql
SELECT * FROM table_name
LIMIT number;
```

**Example:**

```sql
SELECT * FROM lostfoundreport
LIMIT 5;
```

---

## 12. Combining Everything

```sql
SELECT * FROM lostfoundreport
WHERE status = 'lost'
AND reward > 500
ORDER BY reward DESC
LIMIT 10;
```

---

## Notes

* `WHERE` is used to filter data.
* `AND` = all conditions must be true.
* `OR` = any condition can be true.
* `LIKE` is useful for searching text.
* `LIMIT` is used in MySQL to restrict rows.

`
