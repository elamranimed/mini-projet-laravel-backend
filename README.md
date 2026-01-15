# 📚 Gestion de Librairie - Service Web SOAP Laravel

Une application complète de gestion de livres utilisant l'architecture **SOAP** (Simple Object Access Protocol) avec Laravel et MySQL WAMP.

## 🎯 Fonctionnalités

### 5 Opérations CRUD Complètes

| Opération | Description | Endpoint |
|-----------|-------------|----------|
| **CREATE** | 🆕 Créer un nouveau livre | `POST /soap` |
| **READ ALL** | 📚 Afficher tous les livres | `POST /soap` |
| **READ ONE** | 🔍 Rechercher un livre par ID | `POST /soap` |
| **UPDATE** | ✏️ Modifier un livre existant | `POST /soap` |
| **DELETE** | 🗑️ Supprimer un livre | `POST /soap` |

## 🏗️ Architecture

```
┌─────────────────────────────────────┐
│   Client Web (HTML/JavaScript)      │
│   books-crud.html                   │
└────────────┬────────────────────────┘
             │ SOAP Requests (XML)
             ▼
┌─────────────────────────────────────┐
│   SoapServerController              │
│   - Gère SoapServer                 │
│   - Génère WSDL dynamique           │
│   - Route: POST /soap               │
└────────────┬────────────────────────┘
             │ Délègue
             ▼
┌─────────────────────────────────────┐
│   BookSoapController                │
│   - getAllBooks()                   │
│   - getBook($id)                    │
│   - createBook(...)                 │
│   - updateBook(...)                 │
│   - deleteBook($id)                 │
└────────────┬────────────────────────┘
             │ Eloquent ORM
             ▼
┌─────────────────────────────────────┐
│   MySQL WAMP (laravel_soap)         │
│   - Table: books                    │
└─────────────────────────────────────┘
```

## 📊 Modèle de Données

### Table `books`
```sql
CREATE TABLE books (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NULLABLE,
    published_year SMALLINT UNSIGNED NULLABLE,
    genre VARCHAR(255) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🚀 Installation

### Prérequis
- **PHP** >= 8.2
- **Composer**
- **MySQL** (WAMP64)
- **Node.js** (optionnel)

### Étapes

1. **Cloner le projet**
```bash
git clone https://github.com/elamranimed/mini-projet-laravel.git
cd mini-projet-laravel
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Créer la base de données MySQL**
```bash
php create_db.php
```

5. **Exécuter les migrations**
```bash
php artisan migrate
```

6. **Démarrer le serveur**
```bash
php artisan serve
```

L'application sera accessible sur **http://localhost:8000**

## 🌐 Accès aux Interfaces

| URL | Description |
|-----|-------------|
| `http://localhost:8000/books-crud.html` | Interface complète CRUD |
| `http://localhost:8000/soap/wsdl` | WSDL généré dynamiquement |
| `http://localhost:8000/test-create.html` | Test simple de création |

## 📝 Configuration

### `.env` - Base de Données MySQL
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_soap
DB_USERNAME=root
DB_PASSWORD=
```

### Routes SOAP
```php
// routes/web.php
Route::post('/soap', [SoapServerController::class, 'handle']);
Route::get('/soap/wsdl', [SoapServerController::class, 'wsdl']);
```

## 🔌 Exemple de Requête SOAP

### CREATE - Créer un livre

**Requête:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
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

**Réponse:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
    <SOAP-ENV:Body>
        <return>{"status":"success","data":{"id":1,"title":"1984","author":"George Orwell","published_year":1949,"genre":"Dystopie","created_at":"2026-01-15T10:30:00Z","updated_at":"2026-01-15T10:30:00Z"}}</return>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

## 📁 Structure des Fichiers

```
mini-projet-laravel/
├── .github/
│   └── copilot-instructions.md    # Instructions IA pour développeurs
├── app/
│   ├── Http/Controllers/
│   │   ├── SoapServerController.php   # Infrastructure SOAP
│   │   └── BookSoapController.php     # Logique métier
│   └── Models/
│       └── Book.php                   # Modèle Eloquent
├── database/
│   └── migrations/
│       └── 2026_01_12_165844_create_books_table.php
├── public/
│   ├── books-crud.html           # Interface CRUD complète
│   ├── test-create.html          # Test simple
│   └── index.php
├── routes/
│   └── web.php                   # Routes SOAP
├── .env                          # Configuration
├── README.md                     # Ce fichier
├── composer.json
└── artisan
```

## 🛠️ Scripts Utiles

```bash
# Vider le cache de configuration
php artisan config:clear

# Afficher l'état de la base de données
php artisan db:show

# Tester SOAP (backend uniquement)
php test_mysql.php

# Nettoyer la base de données
php clean_db.php

# Tests unitaires
php artisan test

# Formatage du code (Laravel Pint)
php artisan pint
```

## 📋 Conventions du Projet

### Pattern de Réponse SOAP
```php
private function respond(array $payload): string
{
    return json_encode($payload);
}

// Utilisation
return $this->respond(['status' => 'success', 'data' => $books]);
return $this->respond(['status' => 'error', 'message' => 'Erreur']);
```

### Style WSDL
- **Type**: RPC/encoded
- **Namespace**: `urn:BookService`
- **SOAPAction**: `urn:BookService#methodName`
- **WSDL**: Généré dynamiquement (pas de fichier statique)

## 🔒 Points d'Attention

1. ✅ **UTF-8 requis** : Toutes les réponses utilisent `charset=utf-8`
2. ✅ **CSRF désactivé** : Les routes `/soap/*` n'ont pas de protection CSRF
3. ✅ **Cache WSDL désactivé** : `WSDL_CACHE_NONE` pour développement
4. ✅ **Output buffering** : Utilisé pour capturer les réponses SOAP
5. ✅ **MySQL WAMP** : Base de données configurée pour WAMP64

## 🌍 Technologies Utilisées

- **Backend**: Laravel 12 (PHP 8.2+)
- **SOAP**: PHP SoapServer/SoapClient
- **ORM**: Eloquent
- **Base de données**: MySQL (WAMP64)
- **Frontend**: HTML5 + JavaScript (Fetch API)
- **Contrôle de version**: Git & GitHub

## 📖 WSDL Généré

Le WSDL est généré dynamiquement via `GET /soap/wsdl` et définit:
- **Types complexes** : `Book` (id, title, author, published_year, genre)
- **5 opérations** : getAllBooks, getBook, createBook, updateBook, deleteBook
- **Messages** : Request/Response pour chaque opération
- **Binding RPC/encoded** : Style SOAP RPC avec encodage défini

## 🤝 Interface Web

L'interface `books-crud.html` offre:
- ✨ Design moderne avec gradients et animations
- 📱 Responsive (mobile-friendly)
- 🎨 Code couleur par opération (CREATE, READ, UPDATE, DELETE)
- 🔔 Notifications en temps réel
- ⌨️ Validation côté client
- 🔐 Sécurisation des paramètres XML (escapeXml)

## 📞 Support & Maintenance

Pour toute question ou problème:
1. Vérifier que MySQL WAMP est démarré
2. Vérifier que le serveur Laravel tourne (`php artisan serve`)
3. Ouvrir la console du navigateur (F12) pour voir les erreurs SOAP
4. Consulter `.github/copilot-instructions.md` pour détails techniques

## 📄 Licence

Ce projet est fourni à titre d'exemple éducatif.

---

**Version**: 1.0.0  
**Dernière mise à jour**: 15 janvier 2026  
**Créateur**: [elamranimed](https://github.com/elamranimed)
