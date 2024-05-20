# Wheat Library

Wheat is a PHP library that greatly simplifies code writing for developers. It is characterized by many concise and secure functions.

## Library Import
- You can download the library directly from Github.
- Alternatively, you can download it using the following command:
```bash
git clone https://github.com/akourpro/wheat.git
```

## Key Functions

Example: Retrieving data from the database:

### `dbSelect($table, $selects, $where = null, $vars = null)`

#### Inputs:

- `$table`: The name of the table from which to retrieve data.
- `$selects`: The columns to retrieve data from.
- `$where` (optional): Search condition.
- `$vars` (optional): Search condition variables.

#### Outputs:

- The function stores the results in an array named `$rows`.
- The number of rows found is stored in a variable named `$countrows`.

## How to Use

1. **Querying Data:**

```php
dbSelect($table, $selects, $where = null, $vars = null);
```

Real-life example:

```php
$columns = "column1, column2";
$where = "WHERE name = ?";
$value = ["wheat"];
dbSelect('table', $columns, $where, $value);
```

3. **Inserting New Data:**

```php
dbInsert($table, $columns, $vars);
```

Real-life example:

```php
$columns = "name, email, password";
$values = [$name, $email, $password];
dbInsert("users", $columns, $values);
```

4. **Updating Data:**

```php
dbUpdate($table, $columns, $vars, $where = null);
```

Real-life example:

```php
$id = 1;
$columns = "name = ?, email = ?, password = ?";
$values = [$name, $email, $password, $id];
$where = "WHERE id = ?";
dbUpdate("users", $columns, $values, $where);
```

5. **Deleting Data:**

```php
dbDelete($table, $where = null, $vars = null);
```

Real-life example:

```php
$where = "WHERE name = ?";
$values = ["Akour"];
dbDelete("users", $where, $values);
```

## File Uploads

In the Wheat library, a custom system has been developed to handle file uploads on the server.

```php
up($name, $input, $dir, $max_size);
```

- `$name`: Changes the original file name to the specified name.
- `$input`: Name of the HTML Form input file field.
- `$max_size`: Maximum size in megabytes for the file.
- `$dir`: Upload path (location to save the file on the server).

### Generating Unique Random Numbers in the Database

```php
genCode($table, $column, $type, $size);
```

- Creates a token code for a specific operation, such as email verification or password reset.
- Checks whether the code already exists in the database.
- `$size`: Token size, can be 8, 16, or more for increased complexity.
- `$table`: Table to check.
- `$column`: Column to check.
- `$type`: Accepts two types: token or id.

You can use the random code generator with the file upload function.

## Input Protection (Texts and Numbers)

Wheat library provides two functions for input protection: `safer` for texts and `numer` for numbers.

Example:

```php
$name = safer($_POST['name']); // For text protection
$number = numer($_POST['your_age']); // For number protection
```

## Validations

You can validate inputs using the `check` function in the Wheat library.

```php
check($var, $type);
```

This function checks the type of inputs and returns false if there is an error.

- `$var`: Variable to receive the values.
- `$type`: Specifies the type of values. Validation types in the Wheat library are:

1.  Numbers (num)
2.  Email (email)
3.  Texts (txt)
4.  Arabic characters (ar)
5.  Latin characters (en)
6.  URLs (url)

## Site Settings

To handle site settings using the Wheat library, assume you have a database table named `settings` with three columns: `id`, `name`, and `value`. The `name` column contains the key for Wheat library, and the `value` column contains the value for that key.

To interact with site settings, use the following command:

```php
gsite();
```

This command retrieves data from the database.

Example of creating a `settings` table in the database:

```sql
CREATE TABLE settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  value TEXT NOT NULL
);
```

## Alerts

Wheat library uses [SweetAlerts](https://github.com/sweetalert2/sweetalert2) for notifications during operations.

```php
sweet($type, $title, $text, $link = null);
```

This function accepts three required and one optional variable.

- `$type`: Specifies the type of notification (error, success, warning, info, question).
- `$title`: Specifies the main title of the message.
- `$text`: Specifies the message text; HTML codes can be used.
- `$link`: Optional parameter for redirecting the user to a specific path after displaying the notification. Use the word `here` to redirect to the current page.

Real-life example:

```php
$type = "error";
$title = "Error";
$text = "All fields are required!";
sweet($type, $title, $text);
```

## Conclusion

This is a concise overview to highlight the basic aspects of this library. For more details, refer to the `includes/functions.php` file, where each function has a general explanation.

You can also watch tutorials on YouTube for further understanding.

## Requirements

- Requires PHP 7.3 or later.

## Developers

This library was developed by:

- [Mohammad Akour](https://github.com/akourpro)
- [Boudjelida Abdelhak](https://github.com/abdelhakpro)

## License

[GPL]

## Contribution

The door is open to all developers to contribute to the development of the library
Plant a spike to wheat library
