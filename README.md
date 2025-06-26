<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


/*
Questo progetto è una piattaforma SaaS che permette ai negozi di creare chatbot AI personalizzate, accessibili tramite QR code o link dedicati.
Tecnologie usate: Laravel (PHP) per il backend, MySQL come database, Vue.js per il frontend.

🔧 Funzionalità principali:

Multi-tenancy:

Ogni negozio ha il suo spazio dedicato, accessibile via /nomeNegozio (es. /botanicaverde) o sottodominio (in produzione).

Backend separato per i negozi, con login personalizzato.

Chatbot AI personalizzabile:

Ogni negozio può inserire testi, FAQ, descrizioni per addestrare l’AI.

Le chatbot rispondono usando solo i dati forniti dal negozio.

Supporta suggerimenti predefiniti e FAQ dinamiche cliccabili.

Chat accessibile via link o QR code.

QR Code:

Ogni codice può contenere una domanda precompilata.

Tracciamento del ref_code (es. qr123) e della geolocalizzazione.

QR generator disponibile solo per admin centrale.

Lead Generation:

Raccolta email e WhatsApp per iscrizione a newsletter direttamente nella chat.

Possibilità di creare una newsletter (newsletter maker) per gli utenti registrati (feature premium).

Analytics:

Tracciamento di:

IP

Localizzazione

ref_code

device

comportamento utente

Parte delle analytics è accessibile solo con account premium.

Admin panel (centrale):

Gestione di tutti i negozi

Generazione QR

Creazione articoli blog

Chatbot AI globale con accesso a tutte le informazioni dei negozi

Caricamento immagini (fase successiva):

L’utente può caricare la foto di un ambiente (es. soggiorno)

L’AI consiglia quali piante stanno meglio in quello spazio

Integrazioni previste:

Google Trends (analisi trend di ricerca piante)

Claude / Gemini / OpenAI API per chatbot

Supporto iframe/script per integrare la chat in altri siti

🔐 Multi-auth:

admin → gestisce tutto il sistema

negozio → accede solo alla propria dashboard

utente finale → interagisce solo con la chat

🧪 Ambiente di sviluppo locale:

Test sottodomini con /etc/hosts o usare /nomeNegozio

Chat visibile anche da sottodominio in locale

Autenticazione Laravel + middleware personalizzati

Usato Copilot + VSCode per lo sviluppo assistito

📦 Roadmap attuale:

 Setup Laravel e MySQL

 Multi-auth per negozi/admin

 Backend dashboard negozio

 QR Generator (admin)

 Chatbot frontend + AI API

 Analytics e newsletter

 Upload immagine e AI visiva (fase 2)

*/

/*
FASE 2 – QR Code Generator + AI Chatbot Integration con Gemini + Tracking Analytics

Obiettivi:

1. QR Code Generator (solo admin)
   - Creare migration e modello QRCode con campi: id, store_id, question, qr_code_image, created_at, updated_at
   - Admin può creare QR code associati a un negozio e a una domanda precompilata (question)
   - Usare la libreria Laravel SimpleSoftwareIO\QrCode per generare QR code che puntano a: http://tuosito/{store_slug}?question={encoded_question}

2. AI Chatbot Integration con Gemini
   - Creare un servizio GeminiService che:
       • invia richieste all’API Gemini con messaggio e contesto (es. info store)
       • riceve risposta testuale da Gemini
       • gestisce errori e fallback
   - Creare API controller ChatbotController che:
       • riceve messaggi da frontend
       • chiama GeminiService per ottenere risposta AI
       • salva log conversazione (user_message, ai_response, timestamp, store_id, question)
       • ritorna risposta al frontend

3. Tracking e Analytics
   - Salvare ogni scan QR con dati: id, store_id, qr_code_id, ip, user_agent, geo_location (se possibile), timestamp
   - Log conversazioni chatbot per analisi future

4. Frontend Vue.js
   - Creare UI chatbot base che:
       • prende question da query param URL e mostra domanda precompilata
       • invia messaggi all’API backend ChatbotController
       • mostra risposte AI dinamicamente

5. Configurazione .env e config/services.php
   - Aggiungere chiavi e endpoint Gemini

6. Middleware e protezione
   - QR Code generator accessibile solo da admin (middleware isAdmin)
   - API chatbot pubblica ma registra dati per analytics

7. Documentazione inline e codice chiaro

*/
// Richiedi a Copilot di implementare migration, modello, controller, service e frontend Vue basati su questo spec.
