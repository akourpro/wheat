# القمح

القمح هي مكتبة PHP تقوم بتسهيل كتابة الأكواد للمطورين بدرجة كبيرة جدًا، حيث انها تمتاز بوظائف كثيرة ومختصرة وآمنة.

## الوظائف الرئيسية
مثال: جلب البيانات من قاعدة البيانات:

### `dbSelect($table, $selects, $where = null, $vars = null)`

#### المدخلات:

- `$table`: اسم الجدول المطلوب جلب البيانات منه.
- `$selects`: الخلايا المراد جلب بياناتها.
- `$where` (اختياري): شرط البحث.
- `$vars` (اختياري): متغيرات شرط البحث.

#### المخرجات:

- تقوم الدالة بتخزين النتائج في مصفوفة باسم `$rows`.
- يتم تخزين عدد الصفوف التي تم ايجادها في متغير باسم `$countrows`.

...

## كيفية الاستخدام

1. **الاستعلام عن البيانات:**
    ```php
    dbSelect($table, $selects, $where = null, $vars = null);
    ```
    نفس المثال بطريقة اخرى:
   ```php
    $columns = "column1, column2";
    $where = "WHERE name = ?";
    $value = ["wheat"];
    dbSelect('table', $columns, $where, $value);
    ```
   

3. **إدراج بيانات جديدة:**
    ```php
    dbInsert($table, $columns, $vars);
    ```
    مثال واقعي:
    ```php
    $columns = "name, email, password";
    $values = [$name, $email, $password];
    dbInsert("users", $columns, $values);
    ```

4. **تحديث البيانات:**

    ```php
    dbUpdate($table, $columns, $vars, $where = null);
    ```
    مثال واقعي:
    ```php
    $id = 1;
    $columns = "name = ?, email = ?, password = ?";
    $values = [$name, $email, $password, $id];
    $where = "WHERE id = ?";
    dbUpdate("users", $columns, $values, $where);
    ```

    5. . **حذف بيانات:**
  
   ```php
       dbDelete($table, $where = null, $vars = null);
    ```
    مثال واقعي:
    ```php
    $where = "WHERE name = ?";
    $values = ["Akour"];
    dbDelete("users", $where, $values);
    ```


## المتطلبات

- يتطلب PHP 7.1 أو أحدث.

## ترخيص

[GPL]
