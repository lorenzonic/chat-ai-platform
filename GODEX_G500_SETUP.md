# 🖨️ Guida Configurazione Stampante Godex G500

## 📋 Specifiche Etichetta
- **Formato**: 50mm x 25mm (5cm x 2.5cm)
- **Orientamento**: Landscape (orizzontale)
- **Tipo**: Etichetta termica diretta
- **Risoluzione**: 203 DPI

## 🔧 Configurazione Windows - Driver Stampante

### 1. Impostazioni Pagina (Page Setup)
```
Dimensioni Carta: 50mm x 25mm
Orientamento: Landscape (Orizzontale)
Margini: 0mm (tutti i lati)
```

### 2. Proprietà Stampante Godex G500

#### **Scheda "Paper" (Carta)**
- **Media Type**: Direct Thermal (Termica Diretta)
- **Paper Width**: 50mm
- **Paper Height**: 25mm
- **Label Gap**: 2mm (o secondo il rotolo usato)
- **Print Speed**: Medium (50-100 mm/s)
- **Darkness**: 10-12 (regolare secondo necessità)

#### **Scheda "Options" (Opzioni)**
- **Print Mode**: Thermal Transfer OFF (usa Direct Thermal)
- **Sensor Type**: Gap Sensor
- **Offset**: 0mm

#### **Scheda "Advanced" (Avanzate)**
- **Resolution**: 203 DPI
- **Print Quality**: High
- **Encoding**: UTF-8

### 3. Preferenze di Stampa Browser

#### Chrome / Edge
1. Ctrl+P per aprire dialogo stampa
2. **Destinazione**: Seleziona Godex G500
3. **Formato**: Custom 50 x 25 mm
4. **Margini**: Nessuno (None)
5. **Scala**: 100%
6. **Opzioni aggiuntive**:
   - ✅ Grafica di sfondo
   - ✅ Colori di sfondo
   - ❌ Intestazioni e piè di pagina

#### Firefox
1. File → Stampa
2. **Stampante**: Godex G500
3. **Orientamento**: Orizzontale
4. **Formato pagina**: Personalizzato 50mm x 25mm
5. **Margini**: 0mm
6. **Opzioni**:
   - ✅ Stampa sfondo

## 🎯 Configurazione Software Godex

### Utility EZ Label
Se usi il software Godex EZ Label o GoLabel:

```
Label Size:
- Width: 50mm
- Height: 25mm
- Gap: 2mm

Print Settings:
- Speed: 50-100 mm/s (4-6 IPS)
- Darkness: 10-12
- Print Direction: Normal
- Sensor Type: Gap

Advanced:
- Media Type: Direct Thermal
- Print Method: Direct Thermal
- Dithering: None
```

## 📐 Dimensioni Precise CSS

Il CSS già impostato nel template:
```css
@page {
    size: 50mm 25mm;  /* Esatta dimensione fisica */
    margin: 0;         /* Nessun margine */
}

.label-container {
    width: 189px;  /* 50mm @ 96 DPI */
    height: 94px;  /* 25mm @ 96 DPI */
}
```

## 🔍 Risoluzione Problemi Comuni

### Etichetta Tagliata
- ✅ Verifica che margini siano a 0mm
- ✅ Controlla "Fit to Page" sia disabilitato
- ✅ Scala al 100%

### Orientamento Sbagliato
- ✅ Imposta orientamento "Landscape" nel driver
- ✅ Verifica che il rotolo etichette sia caricato correttamente
- ✅ Controlla direzione di stampa nelle proprietà stampante

### Barcode Non Leggibile
- ✅ Aumenta "Darkness" (densità) a 12-15
- ✅ Riduci velocità di stampa
- ✅ Verifica qualità carta termica
- ✅ Pulisci testina termica

### QR Code Non Scansionabile
- ✅ Aumenta risoluzione a 300 DPI se disponibile
- ✅ Aumenta "Darkness"
- ✅ Assicurati che dimensione QR sia almeno 40x40px

### Testo Sfocato
- ✅ Usa font Arial o Helvetica
- ✅ Dimensioni minime font: 6px
- ✅ Aumenta "Darkness"
- ✅ Verifica allineamento testina

## 🛠️ Calibrazione Stampante

### Auto-Calibrazione
1. Spegni la stampante
2. Carica etichette
3. Tieni premuto FEED durante accensione
4. Attendi ciclo calibrazione (circa 10 secondi)
5. Stampante è calibrata per gap detection

### Calibrazione Manuale
Nel software Godex:
```
Tools → Calibration → Gap Sensor
- Imposta gap: 2mm
- Avvia calibrazione
```

## 📊 Test di Stampa

### Test Pattern
Stampa pagina di test dalla stampante:
1. Tieni premuto FEED per 3 secondi
2. Rilascia quando inizia stampa
3. Verifica allineamento e qualità

### Test Etichetta Web
1. Vai alla pagina etichetta prodotto
2. Clicca "Stampa Etichetta"
3. Verifica preview prima di stampare
4. Controlla che tutti gli elementi siano visibili

## 📱 Driver e Software

### Download Driver Godex G500
- **Sito ufficiale**: www.godexintl.com
- **Sezione**: Support → Downloads → G500
- **OS**: Windows 10/11 64-bit
- **Versione Driver**: Ultima disponibile (v7.x+)

### Software Consigliato
- **GoLabel**: Software ufficiale Godex per design etichette
- **BarTender**: Software professionale (opzionale)
- **Browser**: Chrome o Edge (migliore supporto CSS print)

## 🎨 Layout Etichetta Attuale

```
┌─────────────────────────────────────────────┐
│ ┌────┐  NOME PRODOTTO (max 25 char)       │ 50mm
│ │ QR │  € 12,50                            │ width
│ └────┘                                      │
│ ══════════════════════════════════════      │ 25mm
│ EAN: 8012345678901        Store Name        │ height
└─────────────────────────────────────────────┘
```

## ✅ Checklist Pre-Stampa

- [ ] Driver Godex G500 installato correttamente
- [ ] Formato carta: 50mm x 25mm
- [ ] Orientamento: Landscape
- [ ] Margini: 0mm
- [ ] Rotolo etichette caricato
- [ ] Stampante calibrata
- [ ] Test di stampa OK
- [ ] Barcode leggibile con scanner
- [ ] QR code scansionabile con smartphone

## 🆘 Supporto

In caso di problemi:
1. Verifica connessione USB/Rete
2. Riavvia stampante
3. Ricalibrare gap sensor
4. Aggiorna driver
5. Contatta supporto Godex: support@godexintl.com

---

**Ultimo aggiornamento**: Ottobre 2025
**Versione CSS**: 2.0 - Ottimizzato per Godex G500
