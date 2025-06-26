web: NODE_OPTIONS="--max-old-space-size=2048" npm run build && php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
