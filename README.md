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
    dbSelect('table', 'column1, column2', 'WHERE name = ?', [wheat]);
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
    dbInsert('my_table', 'column1, column2', [$value1, $value2]);
    ```
    مثال واقعي:
    ```php
    $columns = "name, email, password";
    $values = [$name, $email, $password];
    dbInsert("users", $columns, $values);
    ```

...

## المتطلبات

- يتطلب PHP 7.1 أو أحدث.

## ترخيص

[GPL]
