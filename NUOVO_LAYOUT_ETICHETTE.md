# NUOVO LAYOUT ETICHETTE PRODOTTO - AGGIORNAMENTO

## Specifiche Layout
- **Dimensioni**: 2.5cm (altezza) x 5cm (lunghezza)
- **Rapporto**: Formato orizzontale rettangolare

## Disposizione Elementi

### Sezione Superiore (55px altezza)
1. **QR Code** (sinistra, 50x50px)
   - Dimensioni: 50x50px
   - Bordo: 1px solid #ddd
   - Contiene: URL specifico del prodotto con domanda personalizzata

2. **Info Prodotto** (destra)
   - **Nome Prodotto**: Font 8px, bold, allineamento sinistra
   - **Prezzo**: Font 12px, bold, formato €X,XX

### Sezione Inferiore (30px altezza)
1. **Barcode EAN Lungo** (tutta larghezza)
   - Dimensioni: 100% larghezza x 20px altezza
   - Orientamento: **ORIZZONTALE LUNGO**
   - Scala: 1.2x larghezza, 0.8x altezza
   - **Colore**: Solo barre NERE su sfondo BIANCO
   - **Posizione**: Attraversa tutta l'etichetta

2. **Riga Informazioni** (sotto barcode)
   - **EAN Code** (sinistra): Font 5px, bold
   - **Codice Cliente** (destra): Font 5px, normale
   - **Layout**: Flex justify-space-between

## Struttura CSS

### Container Principale
```css
.label-container {
    width: 189px;  /* 5cm */
    height: 94px;  /* 2.5cm */
    border: 2px solid #333;
    display: flex;
}
```

### Layout Responsive
- **Struttura**: Flex column (2 sezioni verticali)
- **Sezione TOP**: QR + Info prodotto (orizzontale)
- **Sezione BOTTOM**: Barcode lungo + EAN/Cliente
- **Print-ready**: Mantiene dimensioni esatte per stampa
- **Screen preview**: Mostra anteprima fedele
- **Browser compatibility**: Flexbox per layout consistente

## File Modificati
1. `resources/views/admin/products/show.blade.php`
   - Aggiornato layout HTML etichetta
   - Nuovi stili CSS per layout 2.5x5cm
   - Stili stampa aggiornati

## QR Code Sistema
- **Tipo**: Product-specific (non più order-specific)
- **Naming**: "Nome Prodotto - Codice Cliente"
- **Domanda**: "Come si cura [Nome Prodotto]?"
- **EAN Storage**: Salvato correttamente in database

## Test e Verifica
- ✅ Dimensioni corrette (2.5cm x 5cm)
- ✅ QR code alto a sinistra (TOP)
- ✅ Info prodotto alto a destra (Nome + Prezzo)
- ✅ **Barcode LUNGO orizzontale** (attraversa tutta l'etichetta)
- ✅ **EAN Code a sinistra** + **Cliente a destra** (riga finale)
- ✅ Solo barre nere su sfondo bianco
- ✅ Compatibilità stampa con print-color-adjust

## URL Test
`http://localhost:8001/admin/products/1229`

---
**Data aggiornamento**: 17 Settembre 2025
**Stato**: Implementato e testato ✅
