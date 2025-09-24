# 🚀 Railway Deployment Guide - ChatAI Plants Platform

## Preparazione Pre-Deployment ✅

### 1. File di Configurazione Railway
- ✅ `railway.json` - Configurazione completa build e deployment
- ✅ `nixpacks.toml` - Configurazione Nixpacks per multi-language support
- ✅ `Procfile` - Comando di avvio
- ✅ `start.sh` - Script di inizializzazione robusto
- ✅ `Dockerfile` - Opzionale, per deployment Docker se necessario

### 2. Ottimizzazioni Frontend
- ✅ `vite.config.js` - Configurazione produzione con chunk splitting
- ✅ Build test completato: `npm run build` - 6 assets generati
- ✅ Terser installato per minificazione
- ✅ Vue 3 + custom elements configurati

### 3. Database PostgreSQL
- ✅ `config/database.php` - Configurazione PostgreSQL con variabili Railway
- ✅ Tutte le migrazioni applicate (39 migrazioni)
- ✅ Support per `DATABASE_URL` Railway
- ✅ SSL e timeout configurati

### 4. Ambiente Python/NLP
- ✅ `requirements.txt` - Dipendenze core minimizzate
- ✅ Script spaCy testato e funzionante
- ✅ Modello `it_core_news_sm` configurato nel build
- ✅ Encoding UTF-8 per compatibilità Windows/Linux

## 🎯 Deployment Railway - Step by Step

### Step 1: Creare Progetto Railway
```bash
# 1. Vai su railway.app
# 2. "New Project" > "Deploy from GitHub repo"
# 3. Seleziona: chat-ai-platform repository
# 4. Railway rileverà automaticamente Laravel + Node.js + Python
```

### Step 2: Configurare Variabili d'Ambiente
```bash
# Railway fornirà automaticamente:
DATABASE_URL=postgresql://username:password@host:port/database
PGHOST=...
PGPORT=5432
PGDATABASE=railway
PGUSER=postgres
PGPASSWORD=...

# Aggiungere manualmente:
APP_NAME="ChatAI Plants"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... # Generare con: php artisan key:generate --show
APP_URL=https://your-app-name.up.railway.app

# Laravel specifiche
LOG_CHANNEL=stack
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database

# AI/NLP (opzionale)
GEMINI_API_KEY=your_gemini_key_here
```

### Step 3: Deploy
```bash
# Railway farà automaticamente:
# 1. Build: composer install --no-dev --optimize-autoloader
# 2. Build: npm ci && npm run build  
# 3. Build: pip install -r requirements.txt
# 4. Build: python -m spacy download it_core_news_sm
# 5. Deploy: chmod +x start.sh && ./start.sh
```

## 🔍 Post-Deployment Testing

### 1. Verifiche Base
- [ ] Health check: `https://your-app.up.railway.app/health.php`
- [ ] Homepage caricamento: `https://your-app.up.railway.app`
- [ ] Admin login: `https://your-app.up.railway.app/admin`

### 2. Test Multi-Auth System
- [ ] Admin: `/admin` (super admin)
- [ ] Store: `/store` (garden centers)  
- [ ] Grower: `/grower` (product suppliers)

### 3. Test Funzionalità Core
- [ ] Import CSV: `/admin/import/orders`
- [ ] QR Code generation: `/admin/qr-codes`
- [ ] Chatbot pubblico: `/{store-slug}`
- [ ] Bulk printing: `/grower/products-stickers`

### 4. Test Integrazione Python
- [ ] Chatbot AI responses (with spaCy NLP)
- [ ] Intent detection funzionante
- [ ] Error handling per Python scripts

### 5. Database Functionality
- [ ] PostgreSQL connessione
- [ ] Foreign keys integrità
- [ ] JSON fields (chat_suggestions)
- [ ] UTF-8 encoding

## 📊 Costi Stimati Railway

### Starter Plan ($5/mese)
- ✅ 512MB RAM, 1GB storage
- ✅ Adatto per testing e piccolo traffico
- ✅ Custom domain incluso

### Developer Plan ($10/mese)  
- ✅ 1GB RAM, 5GB storage
- ✅ Ideale per produzione media
- ✅ Più CPU allocation

### Team Plan ($20/mese)
- ✅ 2GB RAM, 10GB storage
- ✅ Per traffico alto e database grandi
- ✅ Priority support

## 🚀 Deploy Commands

```bash
# Se usi Railway CLI:
railway login
railway link
railway up

# Per monitoraggio:
railway logs
railway status
```

## 🎯 Final Check

- ✅ Multi-tenant architecture (Admin/Store/Grower)
- ✅ B2B marketplace per garden centers
- ✅ QR code + barcode system
- ✅ Bulk import/export ordini
- ✅ AI chatbot con NLP italiano
- ✅ Label printing system
- ✅ PostgreSQL production database
- ✅ Ottimizzazioni asset produzione
- ✅ Multi-language build system (PHP/Node/Python)

**Tutto pronto per il deployment! 🎉**
