#!/bin/bash

# Script di inizializzazione per Railway
echo "🚀 Avvio applicazione Laravel..."

# Pulizia cache esistente
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Esegui migrazioni se necessario
if [ "$APP_ENV" = "production" ]; then
    echo "📊 Esecuzione migrazioni database..."
    php artisan migrate --force || echo "⚠️ Migrazioni fallite - continuiamo senza"
fi

# Ottimizzazioni per produzione
echo "⚡ Ottimizzazione per produzione..."
php artisan config:cache || echo "⚠️ Config cache fallita"
php artisan route:cache || echo "⚠️ Route cache fallita"

# Avvia server
echo "🎯 Avvio server Laravel su porta $PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
