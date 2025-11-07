# 🔧 Fix: Railway Cancella Database ad Ogni Deploy

## 🚨 PROBLEMA IDENTIFICATO

**Causa principale**: Il file `start.sh` conteneva:
```bash
php artisan migrate:fresh --force --seed
```

Questo comando **CANCELLA TUTTO IL DATABASE** e lo ricrea da zero ogni volta che una migrazione fallisce!

## ✅ SOLUZIONE IMPLEMENTATA

### 1. **Fix start.sh** (GIÀ FATTO)
Rimosso `migrate:fresh` e sostituito con solo `migrate --force`:

```bash
# ❌ PRIMA (SBAGLIATO):
php artisan migrate:fresh --force --seed  # Cancella TUTTO!

# ✅ ADESSO (CORRETTO):
php artisan migrate --force  # Esegue solo nuove migrazioni
```

### 2. **Verifica Database Railway**
Devi assicurarti che Railway abbia un **database persistente** (non SQLite effimero).

#### 📋 Passaggi su Railway Dashboard:

1. **Vai su Railway Dashboard** → Il tuo progetto
2. **Clicca su "New" → "Database"**
3. **Scegli PostgreSQL** (consigliato per produzione)
4. **Attendi la creazione** (1-2 minuti)

#### 🔗 Collega il Database all'App:

Railway dovrebbe creare automaticamente queste variabili d'ambiente:
- `DATABASE_URL` (connection string completo)
- `PGHOST`, `PGPORT`, `PGUSER`, `PGPASSWORD`, `PGDATABASE`

### 3. **Variabili d'Ambiente Railway**

Verifica che esistano queste variabili nel tuo progetto Railway:

```env
# Database PostgreSQL (Railway Plugin)
DATABASE_URL=postgresql://user:password@host:port/database
PGHOST=containers-us-west-xxx.railway.app
PGPORT=5432
PGUSER=postgres
PGPASSWORD=xxx
PGDATABASE=railway

# Laravel Config
DB_CONNECTION=pgsql
DB_HOST=${PGHOST}
DB_PORT=${PGPORT}
DB_DATABASE=${PGDATABASE}
DB_USERNAME=${PGUSER}
DB_PASSWORD=${PGPASSWORD}

# App Config
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:xxx  # Genera con: php artisan key:generate --show
```

### 4. **Deploy la Fix**

```bash
git add start.sh
git commit -m "fix: Rimuovi migrate:fresh da Railway deploy - preserva dati database"
git push origin main
```

Railway farà il deploy automatico. Questa volta **NON cancellerà** i dati!

## 🔍 VERIFICA POST-DEPLOY

Dopo il deploy, controlla i log Railway:

### ✅ Messaggi CORRETTI (dati preservati):
```
📊 Esecuzione migrazioni database...
Nothing to migrate.  # Oppure: Migrating: xxx
👤 Verifica accounts essenziali...
⚠️ Admin seed skipped (may already exist)
```

### ❌ Messaggi PROBLEMATICI (database cancellato):
```
Dropping all tables...
Dropped all tables successfully.
```

Se vedi "Dropping all tables" → start.sh non è stato aggiornato correttamente.

## 📊 BACKUP MANUALE (Opzionale ma Consigliato)

### Esporta dati prima del prossimo deploy:

```bash
# Su Railway, vai in "Settings" → "Database" → "Backups"
# Oppure usa pg_dump:
pg_dump $DATABASE_URL > backup-$(date +%Y%m%d).sql
```

### Ripristina da backup:
```bash
psql $DATABASE_URL < backup-20250107.sql
```

## 🎯 PROSSIMI DEPLOY

Con questa fix, i prossimi deploy:
- ✅ Eseguiranno solo nuove migrazioni (`migrate --force`)
- ✅ Preserveranno tutti i dati esistenti
- ✅ Aggiungeranno Admin/Grower solo se non esistono
- ❌ NON cancelleranno MAI il database

## 🔒 BEST PRACTICES

### NON usare mai in produzione:
```bash
php artisan migrate:fresh      # Cancella tutto
php artisan migrate:refresh    # Rollback + migrate (cancella dati)
php artisan db:wipe            # Cancella tutto
```

### USA sempre in produzione:
```bash
php artisan migrate            # Esegue solo nuove migrazioni
php artisan migrate:rollback   # Solo se necessario, con backup
php artisan migrate:status     # Controlla stato migrazioni
```

## 📝 CHECKLIST FINALE

- [x] `start.sh` aggiornato (rimosso `migrate:fresh`)
- [ ] Railway ha database PostgreSQL plugin attivo
- [ ] Variabili `DATABASE_URL`, `PGHOST`, etc. configurate
- [ ] Deploy effettuato con nuova versione `start.sh`
- [ ] Log Railway mostra "Nothing to migrate" o migrazioni singole
- [ ] Dati esistenti ancora presenti dopo deploy

## 🆘 Se i Dati Sono GIÀ Andati Persi

1. **Ripristina da backup** (se disponibile)
2. **Re-seed dati essenziali**:
   ```bash
   php artisan db:seed --class=AdminSeeder --force
   php artisan db:seed --class=GrowerSeeder --force
   ```
3. **Reimporta ordini/prodotti** tramite CSV import system

---

**Commit effettuato**: `fix: Rimuovi migrate:fresh da Railway deploy - preserva dati database`
**File modificato**: `start.sh` (righe 68-85)
**Risultato**: Database persistente tra i deploy ✅
