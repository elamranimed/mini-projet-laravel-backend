<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>📚 CRUD Librairie - Vue.js + Laravel SOAP</title>
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            text-align: center;
            font-size: 2em;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
            font-weight: bold;
        }
        .crud-section {
            background: #f8f9fa;
            border-left: 5px solid #667eea;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .crud-section h2 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        button {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            margin: 5px;
            transition: all 0.3s;
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-create { background: #28a745; color: white; }
        .btn-create:hover:not(:disabled) { background: #218838; transform: translateY(-2px); }
        
        .btn-read { background: #17a2b8; color: white; }
        .btn-read:hover:not(:disabled) { background: #138496; transform: translateY(-2px); }
        
        .btn-update { background: #ffc107; color: #333; }
        .btn-update:hover:not(:disabled) { background: #e0a800; transform: translateY(-2px); }
        
        .btn-delete { background: #dc3545; color: white; }
        .btn-delete:hover:not(:disabled) { background: #c82333; transform: translateY(-2px); }
        
        .btn-search { background: #6f42c1; color: white; }
        .btn-search:hover:not(:disabled) { background: #5a32a3; transform: translateY(-2px); }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            animation: slideIn 0.3s;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .alert-info { background: #d1ecf1; color: #0c5460; border-left: 4px solid #17a2b8; }
        
        .books-list {
            margin-top: 20px;
        }
        .book-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 2px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }
        .book-item:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        .book-info {
            flex: 1;
        }
        .book-title {
            font-weight: bold;
            color: #667eea;
            font-size: 1.2em;
            margin-bottom: 5px;
        }
        .book-details {
            color: #666;
            font-size: 0.9em;
        }
        .book-actions {
            display: flex;
            gap: 10px;
        }
        .operation-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            margin-right: 10px;
        }
        .badge-create { background: #28a745; color: white; }
        .badge-read { background: #17a2b8; color: white; }
        .badge-update { background: #ffc107; color: #333; }
        .badge-delete { background: #dc3545; color: white; }
        .badge-search { background: #6f42c1; color: white; }
        
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            animation: slideUp 0.3s;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            font-size: 1.5em;
            color: #667eea;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .modal-footer {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #667eea;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container">
            <h1>📚 Système CRUD Complet - Gestion de Librairie</h1>
            <p class="subtitle">✨ Vue.js 3 + Laravel SOAP - 5 Opérations CRUD ✨</p>
            
            <!-- Alerts -->
            <div v-if="alert" :class="'alert alert-' + alert.type">
                @{{ alert.message }}
            </div>

            <!-- OPÉRATION 1: CREATE -->
            <div class="crud-section">
                <h2><span class="operation-badge badge-create">1️⃣ CREATE</span> Ajouter un Nouveau Livre</h2>
                <form @submit.prevent="createBook">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="title">📖 Titre *</label>
                            <input type="text" id="title" v-model="newBook.title" required placeholder="Ex: Le Petit Prince">
                        </div>
                        <div class="form-group">
                            <label for="author">✍️ Auteur</label>
                            <input type="text" id="author" v-model="newBook.author" placeholder="Ex: Antoine de Saint-Exupéry">
                        </div>
                        <div class="form-group">
                            <label for="year">📅 Année</label>
                            <input type="number" id="year" v-model="newBook.published_year" min="1000" max="2100" placeholder="Ex: 1943">
                        </div>
                        <div class="form-group">
                            <label for="genre">🎭 Genre</label>
                            <input type="text" id="genre" v-model="newBook.genre" placeholder="Ex: Fiction">
                        </div>
                    </div>
                    <button type="submit" class="btn-create" :disabled="loading">
                        ✅ @{{ loading ? 'CRÉATION...' : 'CRÉER LE LIVRE' }}
                    </button>
                </form>
            </div>

            <!-- OPÉRATION 2: READ ALL -->
            <div class="crud-section">
                <h2><span class="operation-badge badge-read">2️⃣ READ ALL</span> Afficher Tous les Livres</h2>
                <button @click="loadAllBooks" class="btn-read" :disabled="loading">
                    📚 @{{ loading ? 'CHARGEMENT...' : 'CHARGER TOUS LES LIVRES' }}
                </button>
            </div>

            <!-- OPÉRATION 3: READ ONE -->
            <div class="crud-section">
                <h2><span class="operation-badge badge-search">3️⃣ READ ONE</span> Rechercher un Livre par ID</h2>
                <div style="display: flex; gap: 10px; align-items: end;">
                    <div class="form-group" style="flex: 1;">
                        <label for="searchId">🔍 ID du Livre</label>
                        <input type="number" id="searchId" v-model="searchId" min="1" placeholder="Ex: 1">
                    </div>
                    <button @click="searchBook" class="btn-search" :disabled="loading">
                        🔎 @{{ loading ? 'RECHERCHE...' : 'RECHERCHER' }}
                    </button>
                </div>
            </div>

            <!-- OPÉRATION 4 & 5: UPDATE & DELETE -->
            <div class="crud-section">
                <h2>
                    <span class="operation-badge badge-update">4️⃣ UPDATE</span> 
                    <span class="operation-badge badge-delete">5️⃣ DELETE</span> 
                    Liste des Livres
                </h2>
                <p style="color: #666; margin-bottom: 15px;">
                    ℹ️ Les boutons MODIFIER (UPDATE) et SUPPRIMER (DELETE) apparaissent sur chaque livre
                </p>
                
                <!-- Loading -->
                <div v-if="loading" class="loading">
                    <div class="spinner"></div>
                    Chargement...
                </div>
                
                <!-- Empty state -->
                <div v-else-if="books.length === 0" class="books-list" style="text-align: center; color: #999; padding: 40px;">
                    Aucun livre trouvé. Ajoutez votre premier livre ci-dessus!
                </div>
                
                <!-- Books list -->
                <div v-else class="books-list">
                    <div v-for="book in books" :key="book.id" class="book-item">
                        <div class="book-info">
                            <div class="book-title">{{ book.title }}</div>
                            <div class="book-details">
                                👤 {{ book.author || 'N/A' }} | 
                                📅 {{ book.published_year || 'N/A' }} | 
                                🎭 {{ book.genre || 'N/A' }} | 
                                🆔 {{ book.id }}
                            </div>
                        </div>
                        <div class="book-actions">
                            <button @click="openUpdateModal(book)" class="btn-update">✏️ MODIFIER</button>
                            <button @click="deleteBook(book.id)" class="btn-delete">🗑️ SUPPRIMER</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal UPDATE -->
        <div v-if="showUpdateModal" class="modal" @click="closeUpdateModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">✏️ Modifier le Livre</div>
                <form @submit.prevent="updateBook">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>📖 Titre *</label>
                            <input type="text" v-model="editBook.title" required>
                        </div>
                        <div class="form-group">
                            <label>✍️ Auteur</label>
                            <input type="text" v-model="editBook.author">
                        </div>
                        <div class="form-group">
                            <label>📅 Année</label>
                            <input type="number" v-model="editBook.published_year" min="1000" max="2100">
                        </div>
                        <div class="form-group">
                            <label>🎭 Genre</label>
                            <input type="text" v-model="editBook.genre">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn-update">✅ ENREGISTRER</button>
                        <button type="button" @click="closeUpdateModal" class="btn-delete">❌ ANNULER</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    books: [],
                    newBook: {
                        title: '',
                        author: '',
                        published_year: '',
                        genre: ''
                    },
                    editBook: {},
                    searchId: '',
                    alert: null,
                    loading: false,
                    showUpdateModal: false,
                    SOAP_URL: window.location.origin + '/soap'
                };
            },
            methods: {
                showAlert(message, type = 'success') {
                    this.alert = { message, type };
                    setTimeout(() => { this.alert = null; }, 5000);
                    console.log(`[${type.toUpperCase()}] ${message}`);
                },

                escapeXml(str) {
                    return String(str)
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&apos;');
                },

                async soapRequest(method, params = {}) {
                    console.log(`[SOAP] ${method}`, params);
                    
                    const paramsXml = Object.entries(params)
                        .filter(([key, value]) => value !== '' && value !== null && value !== undefined)
                        .map(([key, value]) => `<${key}>${this.escapeXml(value)}</${key}>`)
                        .join('');

                    const xmlDeclaration = '<' + '?xml version="1.0" encoding="UTF-8"?' + '>';
                    const soapEnvelope = xmlDeclaration + `
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

                    try {
                        const response = await fetch(this.SOAP_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'text/xml; charset=utf-8',
                                'SOAPAction': `urn:BookService#${method}`
                            },
                            body: soapEnvelope
                        });

                        const text = await response.text();
                        console.log('[SOAP Response]', text);

                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }

                        const parser = new DOMParser();
                        const xmlDoc = parser.parseFromString(text, 'text/xml');
                        const returnElement = xmlDoc.getElementsByTagName('return')[0];
                        
                        if (!returnElement) {
                            throw new Error('Pas de réponse valide');
                        }

                        return JSON.parse(returnElement.textContent);
                    } catch (error) {
                        console.error('[SOAP ERROR]', error);
                        this.showAlert(`Erreur: ${error.message}`, 'error');
                        throw error;
                    }
                },

                // OPÉRATION 1: CREATE
                async createBook() {
                    this.loading = true;
                    try {
                        const response = await this.soapRequest('createBook', this.newBook);
                        
                        if (response.status === 'success') {
                            this.showAlert(`✅ CREATE: Livre créé avec succès! (ID: ${response.data.id})`, 'success');
                            this.newBook = { title: '', author: '', published_year: '', genre: '' };
                            await this.loadAllBooks();
                        } else {
                            this.showAlert(`❌ CREATE: ${response.message}`, 'error');
                        }
                    } catch (error) {
                        this.showAlert('❌ CREATE: Erreur de création', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // OPÉRATION 2: READ ALL
                async loadAllBooks() {
                    this.loading = true;
                    try {
                        this.showAlert('📚 READ ALL: Chargement...', 'info');
                        const response = await this.soapRequest('getAllBooks');
                        
                        if (response.status === 'success') {
                            this.books = response.data || [];
                            this.showAlert(`✅ READ ALL: ${this.books.length} livre(s) chargé(s)`, 'success');
                        } else {
                            this.showAlert(`❌ READ ALL: ${response.message}`, 'error');
                            this.books = [];
                        }
                    } catch (error) {
                        this.showAlert('❌ READ ALL: Erreur de chargement', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // OPÉRATION 3: READ ONE (SEARCH)
                async searchBook() {
                    if (!this.searchId) {
                        this.showAlert('⚠️ SEARCH: Entrez un ID', 'error');
                        return;
                    }

                    this.loading = true;
                    try {
                        this.showAlert(`🔍 SEARCH: Recherche du livre ID ${this.searchId}...`, 'info');
                        const response = await this.soapRequest('getBook', { id: this.searchId });
                        
                        if (response.status === 'success') {
                            this.books = [response.data];
                            this.showAlert(`✅ SEARCH: Livre trouvé!`, 'success');
                        } else {
                            this.showAlert(`❌ SEARCH: ${response.message}`, 'error');
                            this.books = [];
                        }
                    } catch (error) {
                        this.showAlert('❌ SEARCH: Erreur de recherche', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // OPÉRATION 4: UPDATE
                openUpdateModal(book) {
                    this.editBook = { ...book };
                    this.showUpdateModal = true;
                },

                closeUpdateModal() {
                    this.showUpdateModal = false;
                    this.editBook = {};
                },

                async updateBook() {
                    this.loading = true;
                    try {
                        const response = await this.soapRequest('updateBook', this.editBook);
                        
                        if (response.status === 'success') {
                            this.showAlert(`✅ UPDATE: Livre ID ${this.editBook.id} modifié!`, 'success');
                            this.closeUpdateModal();
                            await this.loadAllBooks();
                        } else {
                            this.showAlert(`❌ UPDATE: ${response.message}`, 'error');
                        }
                    } catch (error) {
                        this.showAlert('❌ UPDATE: Erreur de modification', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                // OPÉRATION 5: DELETE
                async deleteBook(id) {
                    if (!confirm(`🗑️ Êtes-vous sûr de vouloir supprimer le livre ID ${id}?`)) return;

                    this.loading = true;
                    try {
                        const response = await this.soapRequest('deleteBook', { id: id });
                        
                        if (response.status === 'success') {
                            this.showAlert(`✅ DELETE: Livre ID ${id} supprimé!`, 'success');
                            await this.loadAllBooks();
                        } else {
                            this.showAlert(`❌ DELETE: ${response.message}`, 'error');
                        }
                    } catch (error) {
                        this.showAlert('❌ DELETE: Erreur de suppression', 'error');
                    } finally {
                        this.loading = false;
                    }
                }
            },
            mounted() {
                // Charger tous les livres au démarrage
                this.loadAllBooks();
            }
        }).mount('#app');
    </script>
</body>
</html>
