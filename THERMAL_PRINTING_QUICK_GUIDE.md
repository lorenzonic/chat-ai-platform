# 🖨️ Guida Rapida: Stampa Etichette Termiche

**Sistema Universale - Compatibile con tutte le stampanti termiche**

---

## 📋 Configurazione Rapida Browser

### Chrome / Edge (Consigliato) ⭐
1. Apri la pagina di stampa: `Ctrl+P`
2. **Imposta questi parametri**:
   - **Stampante**: Seleziona la tua stampante termica (Godex/Zebra/Dymo/Brother)
   - **Formato carta**: Personalizzato → `50 x 25 mm`
   - **Margini**: `Nessuno` o `0mm`
   - **Scala**: `100%` (default, non modificare)
   - **Grafica di sfondo**: ✅ Attivata

3. **Clicca su "Stampa"** 🚀

### Firefox
1. File → Stampa → Proprietà stampante
2. **Formato**: 50mm x 25mm
3. **Margini**: 0mm tutti i lati
4. **Opzioni**: ✅ Stampa sfondo

---

## ⚙️ Configurazione Stampante (Una Tantum)

### Godex G500/G530
1. Pannello di controllo Windows → Dispositivi e stampanti
2. Click destro su Godex → **Preferenze di stampa**
3. **Imposta**:
   - Larghezza: `50mm`
   - Altezza: `25mm`
   - Margini: `0mm` (tutti)
   - Densità: `12` (default)
   - Velocità: `Medium` (100mm/s)
4. **Salva come predefinito** ✅

### Calibrazione Gap Sensor (Se necessario)
Se le etichette non si allineano:
1. Spegni stampante
2. Tieni premuto il tasto **FEED** e accendi
3. Attendi calibrazione automatica (~15 secondi)
4. Test: tieni **FEED** per 3 secondi → stampa pattern

---

## 🎯 Istruzioni d'Uso

### Passo 1: Apri la Pagina di Stampa
```
Vai su: Admin → Prodotti/Order Items → Lista Etichette
Clicca su "🖨️ Stampa Termica" per il prodotto desiderato
```

### Passo 2: Verifica Anteprima
- ✅ Controlla nome prodotto, prezzo, codice EAN
- ✅ Verifica QR code visibile
- ✅ Controlla barcode leggibile
- ✅ Conferma quantità corretta

### Passo 3: Stampa
```
Clicca sul pulsante: "🖨️ Stampa N Etichette"
→ Si apre dialog browser
→ Verifica formato 50x25mm
→ Clicca "Stampa"
```

**Scorciatoia**: `Ctrl+P` per aprire stampa rapida

---

## 🔧 Risoluzione Problemi

### ❌ Problema: Etichetta tagliata o sfalsata
**Soluzione**:
1. Verifica formato: deve essere esattamente `50mm x 25mm`
2. Margini: devono essere `0mm` (tutti i lati)
3. Se persiste: ricalibrare gap sensor (vedi sopra)

### ❌ Problema: Barcode non leggibile
**Soluzione**:
1. Aumenta densità di stampa: da 10 → 12
2. Pulisci testina termica con alcool
3. Verifica rotolo etichette non scaduto

### ❌ Problema: Font barcode non si vede
**Soluzione**:
1. Verifica file: `public/fonts/IDAutomationHC39M.ttf` esiste
2. Svuota cache browser: `Ctrl+Shift+Del`
3. Ricarica pagina: `Ctrl+F5`

### ❌ Problema: Orientamento sbagliato (verticale invece di orizzontale)
**Soluzione**:
1. Nelle proprietà stampante, imposta **orientamento predefinito**: Landscape
2. Nel browser, forza sempre orientamento orizzontale
3. Verifica che @page CSS non sia overridden da driver

### ❌ Problema: Scala errata (troppo grande/piccolo)
**Soluzione**:
1. Browser: scala deve essere `100%` (non "Adatta alla pagina")
2. Driver stampante: nessuna riduzione/ingrandimento automatico
3. Verifica dimensioni carta: esattamente 50x25mm nel driver

---

## ✅ Checklist Pre-Stampa

Prima di ogni sessione di stampa:

- [ ] Stampante accesa e online
- [ ] Rotolo etichette 50x25mm caricato
- [ ] Gap sensor calibrato (se primo utilizzo)
- [ ] Driver configurato: 50x25mm, margini 0mm
- [ ] Browser aperto su pagina stampa termica
- [ ] Anteprima mostra etichette corrette
- [ ] Font barcode visibile in anteprima
- [ ] Quantità corretta mostrata

---

## 📊 Specifiche Tecniche

| Parametro | Valore |
|-----------|--------|
| **Formato etichetta** | 50mm x 25mm |
| **Margini** | 0mm (tutti i lati) |
| **Padding interno** | 2mm |
| **QR Code** | 48px x 48px |
| **Font prodotto** | 11px Arial Bold |
| **Font prezzo** | 14px Arial Bold |
| **Font barcode** | IDAutomationHC39M 22px |
| **Font EAN** | 9px Arial Bold |
| **Densità consigliata** | 10-12 (Godex) |
| **Velocità** | Medium (100mm/s) |

---

## 🔄 Stampanti Compatibili

Sistema testato e funzionante con:

- ✅ **Godex**: G500, G530, EZ120, RT863i
- ✅ **Zebra**: ZD420, GK420d, GX420d
- ✅ **Dymo**: LabelWriter 450, 4XL
- ✅ **Brother**: QL-820NWB, QL-1110NWB
- ✅ **TSC**: TDP-225, TDP-247

**Nota**: Qualsiasi stampante termica con supporto 50x25mm dovrebbe funzionare.

---

## 🆘 Supporto

Se hai problemi non risolti:

1. **Verifica logs browser**: `F12` → Console (cerca errori rossi)
2. **Test pattern stampante**: Stampa pagina di test da driver
3. **Verifica connessione**: USB/Rete funzionante
4. **Aggiorna driver**: Scarica ultima versione dal sito produttore

---

## 💡 Tips & Tricks

### Velocizzare la Stampa
- Imposta formato 50x25mm come **predefinito** nel browser
- Salva preferenze di stampa per evitare configurazione ogni volta
- Usa `Ctrl+P` invece di click per aprire dialog più velocemente

### Migliorare Qualità
- **Densità**: 12 per testo nitido, 10 per barcode
- **Velocità**: Medium (100mm/s) = miglior compromesso
- **Temperatura**: Auto-adjust, non toccare manualmente

### Risparmiare Etichette
- **Preview sempre**: Verifica anteprima prima di stampare
- **Test su 1**: Stampa 1 etichetta di test prima di batch grandi
- **Calibrazione**: Gap sensor ben calibrato evita sprechi

---

**Ultima revisione**: 31 Ottobre 2025  
**Versione sistema**: 2.0 Universal
