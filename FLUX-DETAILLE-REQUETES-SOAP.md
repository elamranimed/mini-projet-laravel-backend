# 📍 FLUX DÉTAILLÉ DES REQUÊTES SOAP - Par Ordre Chronologique

> **Ce document décrit le chemin complet d'une requête SOAP à travers le projet**  
> **Chaque étape indique le fichier exact, le dossier, la fonction et la ligne de code**

---

## 🔵 FLUX COMPLET - EXEMPLE : READ ALL (getAllBooks)

### Opération : Charger tous les livres
**Action utilisateur :** Clique sur le bouton "📚 CHARGER TOUS LES LIVRES"

---

## ⏱️ ÉTAPE 1 : INTERFACE UTILISATEUR (Frontend)

### 📂 Dossier : `c:\wamp64\www\laravel-frontend\`
### 📄 Fichier : `src/App.vue`

**Source :** [laravel-frontend/src/App.vue](laravel-frontend/src/App.vue#L41) - Bouton utilisateur :
```vue
<button @click="loadAllBooks" class="btn-read">📚 CHARGER TOUS LES LIVRES</button>
```

**Source :** [laravel-frontend/src/App.vue](laravel-frontend/src/App.vue#L244-L254) - Méthode `loadAllBooks()` déclenchée :
```javascript
async loadAllBooks() {
  try {
    this.showAlert('📚 READ ALL: Chargement...', 'info');
    const response = await this.soapRequest('getAllBooks');
    // ...
}
```

**Étape suivante :** Appel de la méthode `soapRequest('getAllBooks')` → [laravel-frontend/src/App.vue](laravel-frontend/src/App.vue#L157-L180)

---

## ⏱️ ÉTAPE 2 : CONSTRUCTION ENVELOPPE SOAP (Frontend)

### 📂 Dossier : `c:\wamp64\www\laravel-frontend\`
### 📄 Fichier : `src/App.vue`

**Source :** [laravel-frontend/src/App.vue](laravel-frontend/src/App.vue#L157-L180) - Méthode `soapRequest(method, params = {})` :

```javascript
async soapRequest(method, params = {}) {
  console.log(`[SOAP] ${method}`, params);
  
  // Ligne 162-165 : Construire les paramètres XML
  const paramsXml = Object.entries(params)
    .filter(([key, value]) => value !== '' && value !== null && value !== undefined)
    .map(([key, value]) => `<${key}>${this.escapeXml(value)}</${key}>`)
    .join('');

  // Ligne 167 : Créer l'enveloppe SOAP
  const soapEnvelope = `<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:ns1="urn:BookService"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
    SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
    <SOAP-ENV:Body>
        <ns1:${method}>${paramsXml}</ns1:${method}>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>`;
```

**Pour getAllBooks, l'enveloppe générée :**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
    xmlns:ns1="urn:BookService">
    <SOAP-ENV:Body>
        <ns1:getAllBooks></ns1:getAllBooks>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

**Étape suivante :** Envoyer la requête HTTP

---

## ⏱️ ÉTAPE 3 : REQUÊTE HTTP (Frontend)

### 📂 Dossier : `c:\wamp64\www\laravel-frontend\`
### 📄 Fichier : `src/App.vue`

**Source :** [laravel-frontend/src/App.vue](laravel-frontend/src/App.vue#L181-L191) - Envoi avec `fetch()` :

```javascript
const response = await fetch(this.soapUrl, {  // URL : http://localhost:8000/soap
  method: 'POST',
  headers: {
    'Content-Type': 'text/xml; charset=utf-8',
    'SOAPAction': `urn:BookService#getAllBooks`
  },
  body: soapEnvelope
});
```

**Réseau :**
```
POST http://localhost:8000/soap
Content-Type: text/xml; charset=utf-8
SOAPAction: urn:BookService#getAllBooks

[Corps : enveloppe SOAP XML]
```

**Étape suivante :** Routeur Laravel reçoit la requête

---

## ⏱️ ÉTAPE 4 : ROUTAGE LARAVEL

### 📂 Dossier : `c:\wamp64\www\laravel-backend\`
### 📄 Fichier : `routes/web.php`

**Ligne 13** - Route enregistrée :

```php
Route::post('/soap', [SoapServerController::class, 'handle']);
```

**La requête POST /soap est routée vers :**
- **Contrôleur :** `SoapServerController`
- **Méthode :** `handle()`

**Étape suivante :** Exécution du contrôleur

---

## ⏱️ ÉTAPE 5 : INFRASTRUCTURE SOAP (SoapServerController)

### 📂 Dossier : `c:\wamp64\www\laravel-backend\app\Http\Controllers\`
### 📄 Fichier : `SoapServerController.php`

**Ligne 7-17** - Méthode `handle()` :

```php
public function handle()
{
    // Ligne 9-13 : Créer un serveur SOAP
    $server = new \SoapServer(null, [
        'uri' => 'urn:BookService',
        'encoding' => 'UTF-8',
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    
    // Ligne 14 : Déléguer à BookSoapController
    $server->setObject(new BookSoapController());
    
    // Ligne 16-17 : Traiter la requête SOAP
    ob_start();
    $server->handle();  // ← Parse XML et appelle getAllBooks()
    return response(ob_get_clean(), 200)
           ->header('Content-Type', 'text/xml; charset=utf-8');
}
```

**Ce qui se passe :**
1. **SoapServer** parse l'enveloppe SOAP reçue
2. Identifie la méthode appelée : `getAllBooks`
3. Cherche cette méthode dans `BookSoapController`
4. Appelle `BookSoapController->getAllBooks()`

**Étape suivante :** Exécution de la logique métier

---

## ⏱️ ÉTAPE 6 : LOGIQUE MÉTIER (BookSoapController)

### 📂 Dossier : `c:\wamp64\www\laravel-backend\app\Http\Controllers\`
### 📄 Fichier : `BookSoapController.php`

**Ligne 14-21** - Méthode `getAllBooks()` :

```php
public function getAllBooks()
{
    try {
        return $this->respond([
            'status' => 'success',
            'data' => Book::all()->toArray(),  // Ligne 18 : Requête Eloquent
        ]);
    } catch (\Throwable $e) {
        return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
```

**Étape clé :** `Book::all()`
- **Classe :** `Eloquent Model`
- **Appel :** Exécute une requête SELECT SQL

**Étape suivante :** Accès à la base de données

---

## ⏱️ ÉTAPE 7 : MODÈLE ELOQUENT (Book)

### 📂 Dossier : `c:\wamp64\www\laravel-backend\app\Models\`
### 📄 Fichier : `Book.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'published_year', 'genre'];
    protected $casts = ['published_year' => 'integer'];
}
```

**Configuration :**
- **Table :** `books` (par défaut, le nom du modèle au pluriel)
- **Colonnes remplissables :** `title`, `author`, `published_year`, `genre`
- **Propriétés :** `id`, `created_at`, `updated_at` (automatiques)

**Appel :** `Book::all()` génère cette requête SQL :

```sql
SELECT * FROM books;
```

**Étape suivante :** Exécution dans la base de données

---

## ⏱️ ÉTAPE 8 : BASE DE DONNÉES MySQL

### Configuration (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_soap
DB_USERNAME=root
DB_PASSWORD=
```

**Serveur :** MySQL WAMP sur `127.0.0.1:3306`  
**Base :** `laravel_soap`  
**Table :** `books`

**Requête exécutée :**
```sql
SELECT * FROM books;
```

**Résultat (exemple) :**
```
id | title          | author        | published_year | genre       | created_at | updated_at
---+----------------+---------------+----------------+-------------+------------+------------
1  | 1984           | George Orwell | 1949           | Dystopie    | ...        | ...
2  | Le Seigneur... | Tolkien       | 1954           | Fantasy     | ...        | ...
```

**Étape suivante :** Retour des données

---

## ⏱️ ÉTAPE 9 : CONVERSION JSON (BookSoapController)

### 📂 Dossier : `c:\wamp64\www\laravel-backend\app\Http\Controllers\`
### 📄 Fichier : `BookSoapController.php`

**Ligne 10-12** - Méthode helper `respond(array $payload)` :

```php
private function respond(array $payload): string
{
    return json_encode($payload);  // Encode en JSON string
}
```

**Entrée (PHP Array) :**
```php
[
    'status' => 'success',
    'data' => [
        ['id' => 1, 'title' => '1984', 'author' => 'George Orwell', ...],
        ['id' => 2, 'title' => 'Le Seigneur...', 'author' => 'Tolkien', ...],
    ]
]
```

**Sortie (JSON String) :**
```json
{"status":"success","data":[{"id":1,"title":"1984","author":"George Orwell",...},{"id":2,...}]}
```

**Étape suivante :** Encapsulation SOAP

---

## ⏱️ ÉTAPE 10 : ENCAPSULATION SOAP (SoapServer)

### 📂 Dossier : `c:\wamp64\www\laravel-backend\app\Http\Controllers\`
### 📄 Fichier : `SoapServerController.php` (ligne 16-17)

Le **SoapServer** reçoit la réponse JSON string et l'encapsule dans l'enveloppe SOAP de réponse :

**Réponse SOAP XML générée :**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <ns1:getAllBooksResponse xmlns:ns1="urn:BookService">
            <return>{"status":"success","data":[...]}</return>
        </ns1:getAllBooksResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

**🔑 Point important :** 
- Le JSON est **à l'intérieur** de la balise `<return>`
- C'est du XML qui contient du JSON !

**Étape suivante :** Retour au Frontend

---

## ⏱️ ÉTAPE 11 : RÉCEPTION RÉPONSE SOAP (Frontend)

### 📂 Dossier : `c:\wamp64\www\laravel-frontend\`
### 📄 Fichier : `src/App.vue`

**Ligne 192-205** - Traitement de la réponse :

```javascript
const text = await response.text();  // Récupère le XML
console.log('[SOAP Response]', text);

if (!response.ok) {
  throw new Error(`HTTP ${response.status}`);
}

// Ligne 197-203 : Parser le XML
const parser = new DOMParser();
const xmlDoc = parser.parseFromString(text, 'text/xml');
const returnElement = xmlDoc.getElementsByTagName('return')[0];

if (!returnElement) {
  throw new Error('Pas de réponse valide');
}

// Ligne 206 : Extraire et parser le JSON
return JSON.parse(returnElement.textContent);
```

**Opérations :**
1. `DOMParser` analyse le XML SOAP
2. `getElementsByTagName('return')[0]` extrait l'élément `<return>`
3. `.textContent` récupère le contenu : `{"status":"success","data":[...]}`
4. `JSON.parse()` convertit en objet JavaScript

**Résultat final (objet JavaScript) :**
```javascript
{
  status: 'success',
  data: [
    { id: 1, title: '1984', author: 'George Orwell', ... },
    { id: 2, title: 'Le Seigneur...', author: 'Tolkien', ... }
  ]
}
```

**Étape suivante :** Mise à jour de l'interface

---

## ⏱️ ÉTAPE 12 : MISE À JOUR INTERFACE (Frontend)

### 📂 Dossier : `c:\wamp64\www\laravel-frontend\`
### 📄 Fichier : `src/App.vue`

**Ligne 244-254** - Continuation de `loadAllBooks()` :

```javascript
async loadAllBooks() {
  try {
    this.showAlert('📚 READ ALL: Chargement...', 'info');
    const response = await this.soapRequest('getAllBooks');
    
    if (response.status !== 'success') {
      throw new Error(response.message || 'Erreur');
    }

    this.books = response.data || [];  // Ligne 252 : Mise à jour réactive
    this.showAlert(`✅ READ ALL: ${this.books.length} livre(s) chargé(s)`, 'success');
  } catch (error) {
    this.showAlert('❌ READ ALL: Erreur de chargement', 'error');
  }
}
```

**Ligne 252 :** `this.books = response.data`
- **Propriété réactive Vue.js :** changement détecté automatiquement
- **Déclaration :** Ligne 115 dans `data()`
- Mise à jour du DOM via la boucle `v-for` (lignes 108-125)

**Affichage :**
```vue
<div v-for="book in books" :key="book.id" class="book-item">
  <div class="book-title">{{ book.title }}</div>
  <div class="book-details">
    👤 {{ book.author || 'N/A' }} | 
    📅 {{ book.published_year || 'N/A' }} | 
    🎭 {{ book.genre || 'N/A' }} | 
    🆔 {{ book.id }}
  </div>
</div>
```

**Résultat :** Les livres s'affichent dans le navigateur ! ✅

---

## 📊 RÉSUMÉ VISUAL - FLUX COMPLET

```
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 1: FRONTEND - Clic utilisateur (App.vue:41)                     │
│ Appel loadAllBooks() → soapRequest('getAllBooks')                     │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 2: FRONTEND - Construction SOAP (App.vue:157-180)              │
│ Génère enveloppe XML avec <ns1:getAllBooks>                           │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 3: HTTP POST (App.vue:181-191)                                  │
│ fetch('http://localhost:8000/soap')                                   │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
         ┏━━━━━━━━━━━━━━━ RÉSEAU HTTP ━━━━━━━━━━━━━━━┓
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 4: ROUTE LARAVEL (routes/web.php:13)                            │
│ Route::post('/soap', [SoapServerController::class, 'handle'])         │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 5: SOAP INFRASTRUCTURE (SoapServerController.php:7-17)          │
│ new SoapServer() → setObject(BookSoapController)                      │
│ $server->handle() → Parse XML → Appelle getAllBooks()                 │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 6: LOGIQUE MÉTIER (BookSoapController.php:14-21)                │
│ public function getAllBooks()                                          │
│ → Book::all()->toArray()                                              │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 7: MODÈLE ELOQUENT (Book.php)                                   │
│ class Book extends Model                                              │
│ → SELECT * FROM books;                                                │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
         ┏━━━━━━━━━━━━━━ BASE DE DONNÉES ━━━━━━━━━━━━━━┓
         │ MySQL WAMP - laravel_soap - Table: books     │
         └────────────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 8: RÉPONSE DB                                                   │
│ Retourne : [{"id":1,"title":"1984",...}, {"id":2,...}]               │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 9: CONVERSION JSON (BookSoapController.php:10-12)               │
│ private function respond(array $payload)                              │
│ return json_encode($payload);                                         │
│ Retourne : {"status":"success","data":[...]}                         │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 10: ENCAPSULATION SOAP (SoapServerController.php:16-17)         │
│ SoapServer encapsule JSON dans <return>...</return>                   │
│ Retourne enveloppe SOAP XML                                           │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
         ┏━━━━━━━━━━━━━━ RÉSEAU HTTP ━━━━━━━━━━━━━━━┓
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 11: PARSE RÉPONSE (App.vue:192-207)                             │
│ DOMParser.parseFromString() → Extrait <return>                        │
│ JSON.parse(returnElement.textContent)                                 │
│ Résultat : objet JS {status, data}                                    │
└─────────────────────────────┬──────────────────────────────────────────┘
                              ↓
┌────────────────────────────────────────────────────────────────────────┐
│ ÉTAPE 12: AFFICHAGE (App.vue:252 + template:108-125)                  │
│ this.books = response.data (réactif)                                  │
│ v-for affiche chaque livre dans le DOM                                │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 🔴 FLUX ALTERNATIVE : CREATE (createBook)

### 1️⃣ Frontend - Construction paramètres

**Fichier :** `laravel-frontend/src/App.vue`  
**Ligne :** 222-240

```javascript
async createBook() {
  const params = {
    title: this.form.title,
    author: this.form.author || '',
    published_year: this.form.published_year || '',
    genre: this.form.genre || ''
  };

  try {
    const response = await this.soapRequest('createBook', params);
    // ...
  }
}
```

### 2️⃣ SOAP - Enveloppe avec paramètres

L'enveloppe générée par `soapRequest()` (Ligne 162-165) inclut les paramètres :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="..." xmlns:ns1="urn:BookService">
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

### 3️⃣ Backend - Logique métier

**Fichier :** `laravel-backend/app/Http/Controllers/BookSoapController.php`  
**Ligne :** 37-57

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

        $book = Book::create($data);  // INSERT dans la BD

        return $this->respond([
            'status' => 'success',
            'data' => $book->toArray(),
        ]);
    } catch (\Throwable $e) {
        return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
```

### 4️⃣ Modèle Eloquent

**Fichier :** `laravel-backend/app/Models/Book.php`  
**Ligne :** 10

```php
protected $fillable = ['title', 'author', 'published_year', 'genre'];
```

Eloquent génère :
```sql
INSERT INTO books (title, author, published_year, genre, created_at, updated_at) 
VALUES ('1984', 'George Orwell', 1949, 'Dystopie', NOW(), NOW());
```

### 5️⃣ Réponse

Le livre créé est retourné en JSON dans l'enveloppe SOAP :

```json
{"status":"success","data":{"id":1,"title":"1984","author":"George Orwell",...}}
```

---

## 🟢 FLUX ALTERNATIVE : UPDATE (updateBook)

**Fichier :** `laravel-backend/app/Http/Controllers/BookSoapController.php`  
**Ligne :** 59-81

```php
public function updateBook($id, $title = null, $author = null, $published_year = null, $genre = null)
{
    try {
        $book = Book::find($id);  // SELECT WHERE id = ?
        if (!$book) {
            return $this->respond(['status' => 'error', 'message' => 'Book not found']);
        }

        $data = array_filter(
            ['title' => $title, 'author' => $author, ...],
            fn($v) => $v !== null  // Ignorer les valeurs null
        );

        $book->update($data);  // UPDATE ... SET ...

        return $this->respond(['status' => 'success', 'data' => $book->fresh()->toArray()]);
    } catch (\Throwable $e) {
        return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
```

**SQL généré :**
```sql
SELECT * FROM books WHERE id = 1;
UPDATE books SET title = '1984 (Édition Spéciale)', updated_at = NOW() WHERE id = 1;
```

---

## 🟣 FLUX ALTERNATIVE : DELETE (deleteBook)

**Fichier :** `laravel-backend/app/Http/Controllers/BookSoapController.php`  
**Ligne :** 83-95

```php
public function deleteBook($id)
{
    try {
        $book = Book::find($id);  // SELECT WHERE id = ?
        if (!$book) {
            return $this->respond(['status' => 'error', 'message' => 'Book not found']);
        }

        $book->delete();  // DELETE WHERE id = ?

        return $this->respond(['status' => 'success']);
    } catch (\Throwable $e) {
        return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
```

**SQL généré :**
```sql
SELECT * FROM books WHERE id = 1;
DELETE FROM books WHERE id = 1;
```

---

## 🟡 FLUX ALTERNATIVE : READ ONE (getBook)

**Fichier :** `laravel-backend/app/Http/Controllers/BookSoapController.php`  
**Ligne :** 23-33

```php
public function getBook($id)
{
    try {
        $book = Book::find($id);  // SELECT WHERE id = ?
        return $book
            ? $this->respond(['status' => 'success', 'data' => $book->toArray()])
            : $this->respond(['status' => 'error', 'message' => 'Book not found']);
    } catch (\Throwable $e) {
        return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
```

**SQL généré :**
```sql
SELECT * FROM books WHERE id = 1 LIMIT 1;
```

**Réponse :**
- Si trouvé : `{"status":"success","data":{"id":1,"title":"..."}}`
- Si non trouvé : `{"status":"error","message":"Book not found"}`

---

## 📋 RÉCAPITULATIF DES FICHIERS IMPLIQUÉS

| Opération | Frontend | Backend | Modèle |
|-----------|----------|---------|--------|
| **CREATE** | `App.vue:222-240` | `BookSoapController:37-57` | `Book::create()` |
| **READ ALL** | `App.vue:244-254` | `BookSoapController:14-21` | `Book::all()` |
| **READ ONE** | `App.vue:256-265` | `BookSoapController:23-33` | `Book::find()` |
| **UPDATE** | `App.vue:267-280` | `BookSoapController:59-81` | `$book->update()` |
| **DELETE** | `App.vue:289-298` | `BookSoapController:83-95` | `$book->delete()` |

---

## 🔑 POINTS CLÉS À RETENIR

### Architecture
1. **SOAP Server** = Infrastructure (parse XML, délègue)
2. **BookSoapController** = Logique métier (5 méthodes CRUD)
3. **Book Model** = Accès à la BD avec Eloquent
4. **Frontend Vue.js** = Interface + construction enveloppes SOAP

### Communication
- **Frontend → Backend :** Enveloppe SOAP XML avec `<ns1:methodName>`
- **Backend → Frontend :** Enveloppe SOAP XML avec JSON dans `<return>`

### Base de données
- **Connexion :** MySQL WAMP (`127.0.0.1:3306`)
- **Base :** `laravel_soap`
- **Table :** `books`
- **Accès :** Via Eloquent ORM

### Sécurité
- **Échappement XML :** `escapeXml()` pour les paramètres (ligne 152-156)
- **Gestion erreurs :** Try/catch partout
- **Validation :** Champs requis vs optionnels

---

## 📞 AIDE À LA LECTURE

**Pour trouver une fonction :**
1. Recherchez le numéro de ligne entre crochets `[123]`
2. Ouvrez le fichier indiqué
3. Allez à la ligne (Ctrl+G)

**Pour suivre une requête :**
1. Commencez par l'étape 1 (Frontend)
2. Suivez les numéros (1→2→3...)
3. Consultez le flux visuel si besoin

**Pour identifier une erreur :**
1. Lisez le message d'erreur
2. Trouvez l'étape correspondante
3. Cherchez le fichier et la ligne

---

**🎓 FIN DU DOCUMENT - FLUX DÉTAILLÉ**

> Chaque ligne de code citée peut être consultée directement dans l'éditeur  
> Toutes les références sont exactes au 17 janvier 2026
