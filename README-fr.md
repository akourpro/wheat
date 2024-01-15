# Bibliothèque Wheat

Wheat est une bibliothèque PHP qui simplifie considérablement l'écriture de code pour les développeurs. Elle se caractérise par de nombreuses fonctions concises et sécurisées.

## Fonctions clés
Exemple : Récupération de données depuis la base de données :

### `dbSelect($table, $selects, $where = null, $vars = null)`

#### Entrées :

- `$table` : Le nom de la table à partir de laquelle récupérer des données.
- `$selects` : Les colonnes à partir desquelles récupérer des données.
- `$where` (optionnel) : Condition de recherche.
- `$vars` (optionnel) : Variables de condition de recherche.

#### Sorties :

- La fonction stocke les résultats dans un tableau nommé `$rows`.
- Le nombre de lignes trouvées est stocké dans une variable nommée `$countrows`.

## Comment utiliser

1. **Interrogation des données :**

```php
dbSelect($table, $selects, $where = null, $vars = null);
```

Exemple concret :

```php
$columns = "column1, column2";
$where = "WHERE name = ?";
$value = ["wheat"];
dbSelect('table', $columns, $where, $value);
```

3. **Insertion de nouvelles données :**

```php
dbInsert($table, $columns, $vars);
```

Exemple concret :

```php
$columns = "name, email, password";
$values = [$name, $email, $password];
dbInsert("users", $columns, $values);
```

4. **Mise à jour des données :**

```php
dbUpdate($table, $columns, $vars, $where = null);
```

Exemple concret :

```php
$id = 1;
$columns = "name = ?, email = ?, password = ?";
$values = [$name, $email, $password, $id];
$where = "WHERE id = ?";
dbUpdate("users", $columns, $values, $where);
```

5. **Suppression de données :**

```php
dbDelete($table, $where = null, $vars = null);
```

Exemple concret :

```php
$where = "WHERE name = ?";
$values = ["Akour"];
dbDelete("users", $where, $values);
```

## Téléchargements de fichiers
Dans la bibliothèque Wheat, un système personnalisé a été développé pour gérer les téléchargements de fichiers sur le serveur.

```php
up($name, $input, $dir, $max_size);
```

- `$name` : Modifie le nom original du fichier en le nom spécifié.
- `$input` : Nom du champ de fichier d'entrée HTML du formulaire.
- `$max_size` : Taille maximale en mégaoctets pour le fichier.
- `$dir` : Chemin de téléchargement (emplacement pour enregistrer le fichier sur le serveur).

### Génération de nombres aléatoires uniques dans la base de données
```php
genCode($table, $column, $type, $size);
```

- Crée un code jeton pour une opération spécifique, comme la vérification par e-mail ou la réinitialisation de mot de passe.
- Vérifie si le code existe déjà dans la base de données.
- `$size` : Taille du jeton, peut être 8, 16, ou plus pour une complexité accrue.
- `$table` : Table à vérifier.
- `$column` : Colonne à vérifier.
- `$type` : Accepte deux types : token ou id.

Vous pouvez utiliser le générateur de code aléatoire avec la fonction de téléchargement de fichiers.

## Protection des entrées (textes et chiffres)
La bibliothèque Wheat propose deux fonctions pour la protection des entrées : `safer` pour les textes et `numer` pour les chiffres.

Exemple :

```php
$name = safer($_POST['name']); // Pour la protection du texte
$number = numer($_POST['your_age']); // Pour la protection des chiffres
```

## Validations
Vous pouvez valider les entrées à l'aide de la fonction `check` dans la bibliothèque Wheat.

```php
check($var, $type);
```

Cette fonction vérifie le type des entrées et renvoie false s'il y a une erreur.

- `$var` : Variable pour recevoir les valeurs.
- `$type` : Spécifie le type de valeurs. Les types de validation dans la bibliothèque Wheat sont :
  1. Chiffres (num)
  2. E-mail (email)
  3. Textes (txt)
  4. Caractères arabes (ar)
  5. Caractères latins (en)
  6. URLs (url)

## Paramètres du site
Pour gérer les paramètres du site avec la bibliothèque Wheat, supposez que vous ayez une table de base de données nommée `settings` avec trois colonnes : `id`, `name`, et `value`. La colonne `name` contient la clé pour la bibliothèque Wheat, et la colonne `value` contient la valeur pour cette clé.

Pour interagir avec les paramètres du site, utilisez la commande suivante :

```php
gsite();
```

Cette commande récupère les données de la base de données.

Exemple de création d'une table `settings` dans la base de données :

```sql
CREATE TABLE settings (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  value TEXT NOT NULL
);
```

## Alertes
La bibliothèque Wheat utilise [SweetAlerts](https://github.com/sweetalert2/sweetalert2) pour les notifications lors des opérations.

```php
sweet($type, $title, $text, $link = null);
```

Cette fonction accepte trois variables obligatoires et une facultative.

- `$type` : Spécifie le type de notification (error, success, warning, info, question).
- `$title` : Spécifie le titre principal du message.
- `$text` : Spécifie le texte du message ; des codes HTML peuvent être utilisés.
- `$link` : Paramètre facultatif pour rediriger l'utilisateur vers un chemin spécifique après l'affichage de la notification. Utilisez le mot `here` pour rediriger vers la page actuelle.

Exemple concret :

```php
$type = "error";
$title = "Erreur";
$text = "Tous les champs sont obligatoires !";
sweet($type, $title, $text);
```

## Conclusion
Il s'agit d'une vue d'ensemble concise pour mettre en évidence les aspects de base de cette bibliothèque. Pour plus de détails, consultez le fichier `includes/functions.php`, où chaque fonction a une explication générale

.

Vous pouvez également regarder des tutoriels sur YouTube pour une compréhension plus approfondie.

## Exigences
- Nécessite PHP 7.3 ou une version ultérieure.

## Développeurs
Cette bibliothèque a été développée par :
- [Mohammad Akour](https://github.com/akourpro)
- [Boudjelida Abdelhak](https://github.com/abdelhakpro)

## Licence
[GPL]
