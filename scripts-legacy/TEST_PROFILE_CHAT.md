# Test Chat con Informazioni Profilo

## Come testare:

1. Vai al profilo dello store (`/store/profile/edit`)
2. Aggiungi alcune informazioni come:
   - Telefono: +39 123 456 7890
   - Indirizzo: Via Roma 123
   - Città: Milano
   - Stato: MI
   - CAP: 20121
   - Paese: Italia
   - Sito web: https://www.example.com

3. Salva le modifiche

4. Vai al chatbot (`/{store-slug}`)

5. Prova queste domande:
   - "Qual è il vostro numero di telefono?"
   - "Dove siete ubicati?"
   - "Come posso contattarvi?"
   - "Qual è il vostro indirizzo?"
   - "Avete un sito web?"

## Il chatbot dovrebbe essere in grado di:
- Fornire numero di telefono se richiesto
- Fornire indirizzo completo se richiesto
- Fornire informazioni di contatto
- Suggerire di visitare il sito web se disponibile
- Non fornire email, username o password (escluse per sicurezza)

## Esempio di risposta attesa:
**Domanda**: "Come posso contattarvi?"
**Risposta**: "Puoi contattarci al **+39 123 456 7890** 📞 oppure venire a trovarci presso il nostro negozio in **Via Roma 123, Milano, MI 20121, Italia** 📍. Puoi anche visitare il nostro sito web: **https://www.example.com** 🌐"
