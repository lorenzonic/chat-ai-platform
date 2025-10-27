# 🔧 FIX QR CODE URLs IN PRODUCTION

## 🚨 Problema Identificato
I QR codes in produzione hanno URL che puntano a `localhost` invece del dominio corretto di Railway. Questo impedisce il corretto funzionamento dei QR codes.

## ✅ Soluzioni Implementate

### 1. **Migliorato metodo getQrUrl() nel modello QrCode**
- **File**: `app/Models/QrCode.php`
- **Miglioramenti**:
  - Fallback automatico se `config('app.url')` non è configurato
  - Usa `request()->getSchemeAndHttpHost()` come backup
  - Forza HTTPS in ambiente production
  - Gestisce correttamente variabili non interpolate

### 2. **Comando Artisan per diagnosi**
- **File**: `app/Console/Commands/FixQrCodeUrls.php`
- **Comando**: `php artisan qr:fix-urls`
- **Opzioni**: `--regenerate` per rigenerare le immagini

### 3. **Script di verifica web**
- **File**: `public/check-qr-urls.php`
- **Accesso**: `https://your-app.railway.app/check-qr-urls.php`

### 4. **Script di fix forzato**
- **File**: `public/force-update-qr-urls.php`
- **Accesso**: `https://your-app.railway.app/force-update-qr-urls.php`

### 5. **Configurazione ENV aggiornata**
- **File**: `.env.railway`
- **Aggiornato**: `APP_URL=https://${RAILWAY_STATIC_URL}`

## 🚀 DEPLOY E FIX IN PRODUZIONE

### **Step 1: Deploy delle modifiche**

```bash
git add .
git commit -m "Fix: QR code URLs for production environment"
git push origin main
```

### **Step 2: Verifica in produzione (dopo deploy)**

1. **Accedi al script di verifica:**
   ```
   https://your-app-url.railway.app/check-qr-urls.php
   ```

2. **Questo mostrerà:**
   - Configurazione attuale APP_URL
   - Lista di tutti i QR codes con i loro URL
   - Identificazione di URL problematici

### **Step 3: Fix automatico via comando**

**Su Railway console:**
```bash
php artisan qr:fix-urls
php artisan qr:fix-urls --regenerate
```

### **Step 4: Fix alternativo via script web**

Se il comando non funziona, usa lo script web:
```
https://your-app-url.railway.app/force-update-qr-urls.php
```

Lo script automaticamente:
- Rileva il dominio corretto
- Aggiorna tutti gli URL dei QR codes
- Rigenera le immagini QR
- Mostra un report dettagliato

### **Step 5: Fix manuale con parametro URL**

Se l'auto-detection non funziona:
```
https://your-app-url.railway.app/force-update-qr-urls.php?url=https://your-actual-domain.railway.app
```

## 🧪 Test Locale Completato

✅ Metodo `getQrUrl()` migliorato  
✅ Comando artisan funzionante  
✅ Script di verifica testato  
✅ Fallback automatico implementato  

**Risultato test locale:**
- Identificati 14 QR codes con URL localhost
- Comando `qr:fix-urls` funziona correttamente
- Script di verifica mostra dettagli completi

## 📋 Cosa Verificare Dopo il Fix

### **1. Test URL QR Codes**
```bash
# Su Railway console
php artisan qr:fix-urls
```

**Output atteso:**
```
✅ All QR code URLs look correct
```

### **2. Test funzionalità QR**
1. Scansiona un QR code
2. Verifica che apra la pagina store corretta
3. Controlla che il parametro `ref` sia presente
4. Verifica che l'analytics funzioni

### **3. Test creazione nuovi QR**
1. Admin panel → QR Codes → Create
2. Genera un nuovo QR
3. Verifica che l'URL sia corretto

## 🔍 Debug Commands

```bash
# Verifica configurazione
php artisan tinker --execute="echo config('app.url')"

# Verifica URL QR codes
php artisan qr:fix-urls

# Test generazione URL
php artisan tinker --execute="echo App\Models\QrCode::first()->getQrUrl()"
```

## 🗑️ Cleanup Post-Fix

Dopo aver verificato che tutto funziona:

```bash
# Rimuovi script temporanei
rm public/check-qr-urls.php
rm public/force-update-qr-urls.php
```

## 💡 Prevenzione Futura

Il metodo `getQrUrl()` aggiornato include:
- ✅ Fallback automatico per environment production
- ✅ Rilevamento automatico dominio da request
- ✅ Forzatura HTTPS in production
- ✅ Gestione variabili non interpolate

Questo dovrebbe prevenire problemi futuri con gli URL.

---

**Status**: ✅ Fix implementato e pronto per deploy  
**Urgenza**: 🔴 Alta - I QR codes non funzionano correttamente  
**Impatto**: 📱 Tutti i QR codes esistenti devono essere aggiornati
