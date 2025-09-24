#!/bin/bash

# Script di inizializzazione per Railway
echo "🚀 Avvio applicazione Laravel..."

# Install Python dependencies if not already installed
if [ -f "requirements.txt" ]; then
    echo "🐍 Installazione dipendenze Python..."
    python3 -m pip install -r requirements.txt --quiet || echo "⚠️ Python dependencies installation failed"
    python3 -m pip install spacy --quiet || echo "⚠️ spaCy installation failed"
    python3 -m spacy download it_core_news_sm --quiet || echo "⚠️ spaCy model download failed"
fi

# Assicurati che le directories esistano
mkdir -p storage/logs
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p public/qr_codes
mkdir -p public/storage

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public/qr_codes

# Pulizia cache esistente
echo "🧹 Pulizia cache..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Setup storage link
echo "🔗 Setup storage link..."
php artisan storage:link || echo "⚠️ Storage link già esistente"

# Esegui migrazioni se necessario
if [ "$APP_ENV" = "production" ]; then
    echo "📊 Esecuzione migrazioni database..."
    php artisan migrate:status || echo "⚠️ Cannot check migration status"
    # Forza l'esecuzione delle migrazioni pending
    php artisan migrate --force || {
        echo "⚠️ Standard migration failed, trying Railway fix..."
        php artisan railway:migrate-fix || {
            echo "⚠️ Railway fix failed, trying fresh migration..."
            php artisan migrate:fresh --force --seed || echo "⚠️ Fresh migration also failed"
        }
    }

    # Seed admin and essential data if needed
    echo "👤 Verifica accounts essenziali..."
    php artisan db:seed --class=AdminSeeder --force || echo "⚠️ Admin seed failed"
    php artisan db:seed --class=GrowerSeeder --force || echo "⚠️ Grower seed failed"

    # Seed admin and essential data if needed
    echo "👤 Verifica accounts essenziali..."
    php artisan db:seed --class=AdminSeeder --force || echo "⚠️ Admin seed failed"
    php artisan db:seed --class=GrowerSeeder --force || echo "⚠️ Grower seed failed"
fi

# Ottimizzazioni per produzione
if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then
    echo "⚡ Ottimizzazione per produzione..."
    php artisan config:cache || echo "⚠️ Config cache fallita"
    php artisan route:cache || echo "⚠️ Route cache fallita"
    php artisan view:cache || echo "⚠️ View cache fallita"
fi

# Test Python/spaCy
echo "🐍 Test Python environment..."
python3 -c "import sys; print(f'✅ Python {sys.version_info.major}.{sys.version_info.minor} OK')" || echo "⚠️ Python non disponibile"
python3 -c "import spacy; print('✅ spaCy OK')" 2>/dev/null || echo "⚠️ spaCy non disponibile (ma l'app può funzionare senza)"

# Avvia server
echo "🎯 Avvio server Laravel su porta ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
