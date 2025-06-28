# 🌿 ChatAI Plants – SaaS Platform per Chatbot AI nei Garden Center

**Piattaforma SaaS** che consente ai negozi di piante di creare chatbot AI personalizzate, collegate a QR code, con funzioni di lead generation, suggerimenti automatici e analisi avanzate. Il sistema è scalabile, multitenant e supporta AI multimodale.

---

## 🎯 Obiettivo del Progetto

Fornire a ogni garden center o negozio di piante un assistente AI intelligente accessibile da un link tipo `/nomeNegozio` o via QR Code, capace di rispondere a domande su:
- Cura delle piante
- Ambiente ideale
- Tipologia
- Consigli visivi tramite immagini (fase avanzata)

Il sistema raccoglie dati in tempo reale e migliora le risposte tramite tecniche di NLP (es. spaCy) e AI generativa (es. Gemini).

---

## 🔧 Stack Tecnologico

- **Laravel 11** (PHP) – Backend
- **Vue 3 + Tailwind CSS** – Frontend
- **MySQL** – Database
- **Gemini API** – Chatbot AI
- **Python + spaCy** – Analisi testuale + training soft
- **Railway.app** – Hosting Dev
- **VPS/Docker-ready** – Hosting Prod
- **Optional**: ChromaDB, pgvector, Stripe, Vite, Laravel Echo

---

## ✅ Stato del Progetto per Fasi

### 🚀 FASE 1 – Setup Iniziale e Multi-Auth (✅ COMPLETATA)

- Auth separata per:
  - Admin → `/admin/login`
  - Store (negozio) → `/store/login`
  - User (futuro) → `/login`
- Route protette + Middleware
- Dashboard separate (Admin / Store)
- Chatbot frontend → `/botanicaverde`
- Seeder per Admin + 2 negozi demo
- Database MySQL completo

---

### 🤖 FASE 2 – AI Chatbot + QR Code + Analytics (✅ COMPLETATA)

- QR Code generator per Admin
- Gemini API (1.5 e 2.0 testato e funzionante)
- Vue.js chatbot integrato nel frontend store
- Tracciamento conversazioni
- API per logging, domande/risposte, entità

---

### 📬 FASE 3 – Lead Generation + Embed Chatbot (✅ COMPLETATA)

- Raccolta email / WhatsApp utenti durante l’uso
- Chatbot integrabile via iframe/script esterno
- Suggerimenti predefiniti dinamici (es. “Come si cura il ficus?”)
- Tracciamento utente da QR → `source_link_id`
- Collegamento alla dashboard per conversioni

---

### 📊 FASE 4 – Newsletter + Subscription (✅ COMPLETATA)

- Newsletter Maker per negozi (editor + salvataggio campagne)
- Invio testato via Mailtrap (in produzione SMTP/SendGrid)
- Lead management semplificato
- Piani a pagamento con:
  - Newsletter a pagamento
  - Accesso a funzionalità analytics avanzate
- Stripe integration (in corso)

---

### 🧠 FASE 5 – AI Learning e Ottimizzazione Chatbot (🔄 IN CORSO)

- Salvataggio log delle chat → `chat_logs`
- Rilevamento intenti con spaCy
- Classifica delle richieste più comuni (FAQ automatica)
- Training automatico + fallback su Gemini
- Embedding futuro su pgvector (opzionale)

---

### 🖼️ FASE 6 – Consigli Visivi e Generazione Immagini (🔜 POST-LAUNCH)

- Upload foto di spazi (es. angolo salotto)
- AI consiglia piante adatte per luce/estetica
- Generazione immagini ispirazionali (DALL·E / Gemini Vision)
- Possibile analisi con Vision API + finetuning

---

## 📂 Struttura Database (semplificata)

| Tabella         | Funzione                        |
|-----------------|---------------------------------|
| users           | Utenti finali                   |
| admins          | Super Admin                     |
| stores          | Negozi                          |
| leads           | Email/contatti raccolti         |
| newsletters     | Campagne inviate                |
| chat_logs       | Storico conversazioni AI        |
| analytics       | Eventi tracciati (click, open)  |
| faq_suggestions | FAQ auto-generate da pattern    |

---

## 🔍 Feature Speciali

| Feature                          | Stato    |
|----------------------------------|----------|
| Auth multi-ruolo                 | ✅       |
| AI Gemini + Fallback             | ✅       |
| QR Code Generator (solo admin)  | ✅       |
| Embed chatbot                    | ✅       |
| Newsletter builder               | ✅       |
| Form di raccolta contatti        | ✅       |
| Visual plant advisor (AI Vision)| 🔜       |
| Area Admin globale               | ✅       |
| Area Analytics base              | ✅       |
| Analytics premium                | 🔜       |
| Piano PRO per negozi            | 🛠️       |

---

## 🐍 Integrazione Python

- Utilizzato per NLP (riconoscimento intenti, sintomi, entità)
- Librerie:
  - `spaCy`, `pandas`, `matplotlib`, `sklearn`, `transformers`
- Esegue:
  - Analisi delle richieste chatbot
  - Generazione di nuove FAQ
  - Classifica delle piante più cercate

---

## 🧾 Esempio Prompt per Copilot (Laravel)

```php
// Laravel 11 – SaaS multi-tenant chatbot AI
// Obiettivo: Creare chatbot AI per ogni store.
// Usa Gemini per risposte, spaCy per analisi, Vue 3 per interfaccia.
// Admin può gestire newsletter, generare QR code, vedere analytics.
// Traccia tutte le chat, migliora suggerimenti, raccoglie lead.
