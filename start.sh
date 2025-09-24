#!/bin/bash

# Script di inizializzazione per Railway
echo "🚀 Avvio applicazione Laravel..."

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
    php artisan migrate --force || echo "⚠️ Migrazioni fallite - continuiamo senza"

    # Seed admin if needed
    echo "👤 Verifica admin account..."
    php artisan db:seed --class=AdminSeeder --force || echo "⚠️ Admin seed failed"
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
python -c "import spacy; print('✅ spaCy OK')" || echo "⚠️ spaCy non disponibile"

# Avvia server
echo "🎯 Avvio server Laravel su porta ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
