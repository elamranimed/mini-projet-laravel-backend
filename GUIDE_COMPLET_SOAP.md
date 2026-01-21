# 📚 Guide Simple - SOAP Book Service

## Exemple : Créer un Livre (CREATE)

### Étape 1 : Ouvrir le client HTML
```
http://localhost:8000/books-crud.html
```

### Étape 2 : Remplir le formulaire
- Titre : "1984"
- Auteur : "George Orwell"
- Année : 1949
- Genre : "Dystopie"

### Étape 3 : Cliquer sur "CRÉER LE LIVRE"

---

## Ce qui se passe en coulisse

### 1. JavaScript envoie XML SOAP
**Fichier : `public/books-crud.html`**

```xml
POST http://localhost:8000/soap

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
                   xmlns:ns1="urn:BookService">
    <SOAP-ENV:Body>
        <ns1:createBook>
            <title>1984</title>
            <author>George Orwell</author>
            <published_year>1949</published_year>
            <genre>Dystopie</genre>
        </ns1:createBook>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

---

### 2. Route reçoit la requête
**Fichier : `routes/web.php`**

```php
Route::post('/soap', [SoapServerController::class, 'handle']);
```

---

### 3. SoapServer traite la requête
**Fichier : `app/Http/Controllers/SoapServerController.php`**

```php
public function handle()
{
    $server = new \SoapServer(null, [
        'uri' => 'urn:BookService',
        'encoding' => 'UTF-8',
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    $server->setObject(new BookSoapController());
    
    ob_start();
    $server->handle();
    return response(ob_get_clean(), 200)->header('Content-Type', 'text/xml; charset=utf-8');
}
```

**Ce que ça fait :**
- Crée un serveur SOAP PHP natif
- Délègue à `BookSoapController`
- Appelle automatiquement `createBook()`

---

### 4. BookSoapController crée le livre
**Fichier : `app/Http/Controllers/BookSoapController.php`**

```php
public function createBook($title, $author = null, $published_year = null, $genre = null)
{
    try {
        $data = [
            'title' => $title,
            'author' => $author ?: null,
            'published_year' => $published_year ?: null,
            'genre' => $genre ?: null,
        ];

        $book = Book::create($data);  // ← INSERT dans la BD

        return $this->respond([
            'status' => 'success',
            'data' => $book->toArray(),
        ]);
    } catch (\Throwable $e) {
        return $this->respond([
            'status' => 'error',
            'message' => $e->getMessage(),
        ]);
    }
}

private function respond(array $payload): string
{
    return json_encode($payload);  // Retourne JSON en string
}
```

**Ce que ça fait :**
- Reçoit les paramètres du SOAP
- Insère dans la base de données avec `Book::create()`
- Retourne JSON : `{"status":"success","data":{...}}`

---

### 5. Réponse SOAP au client
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <return>{"status":"success","data":{"id":1,"title":"1984","author":"George Orwell","published_year":1949,"genre":"Dystopie"}}</return>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

---

### 6. JavaScript affiche le résultat
**Fichier : `public/books-crud.html`**

```javascript
const parser = new DOMParser();
const xmlDoc = parser.parseFromString(text, 'text/xml');
const returnElement = xmlDoc.getElementsByTagName('return')[0];
const jsonData = JSON.parse(returnElement.textContent);

// jsonData = {status: "success", data: {id: 1, title: "1984", ...}}

alert(`✅ Livre créé (ID: ${jsonData.data.id})`);
```

---

## Résumé du flux

```
Client HTML  →  POST /soap (XML)
                      ↓
             SoapServerController
                      ↓
             BookSoapController::createBook()
                      ↓
             Book::create() → MySQL
                      ↓
             JSON: {"status":"success","data":{...}}
                      ↓
             Réponse SOAP (XML avec JSON dedans)
                      ↓
Client HTML  ←  Parse et affiche
```

---

## Les 5 opérations SOAP

| Opération | Méthode | Paramètres |
|-----------|---------|-----------|
| 1️⃣ CREATE | `createBook()` | `title, author, published_year, genre` |
| 2️⃣ READ ALL | `getAllBooks()` | - |
| 3️⃣ SEARCH | `getBooksByAuthor()` | `author` |
| 4️⃣ UPDATE | `updateBook()` | `id, title, author, published_year, genre` |
| 5️⃣ DELETE | `deleteBook()` | `id` |

### Bonus : Recherche par ID
- **Méthode** : `getBook()`
- **Paramètres** : `id`
- **Utilité** : Récupérer un livre spécifique par son ID

---

## Commandes essentielles

```bash
# Démarrer le serveur
php artisan serve

# Ouvrir le client
http://localhost:8000/books-crud.html

# Tester avec script PHP
php test_soap.php
```

---

## Fichiers importants

```
routes/web.php                           → Routes SOAP
app/Http/Controllers/
  ├── SoapServerController.php          → Infrastructure SOAP + WSDL
  └── BookSoapController.php            → 6 méthodes (5 CRUD + 1 bonus)
app/Models/Book.php                     → Modèle Eloquent
public/books-crud.html                  → Client HTML/JS statique
laravel-frontend/src/App.vue            → Client Vue.js
```

**Dernière mise à jour :** 21 Janvier 2026

---

## 🎯 Changements récents

### Version 2.0 - Recherche par Auteur
- ✅ **Opération 3** : Changée de "READ ONE (par ID)" à "SEARCH (par Auteur)"
- ✅ **Nouvelle méthode** : `getBooksByAuthor($author)` - Recherche tous les livres d'un auteur
- ✅ **Bonus** : `getBook($id)` - Récupère un livre spécifique par ID
- ✅ **Frontend mis à jour** : Vue.js et HTML reflètent les changements
