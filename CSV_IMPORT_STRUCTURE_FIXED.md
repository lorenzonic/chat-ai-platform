# CSV Import Structure - UPDATED

## 🔧 **Problema Risolto**
Separazione corretta dei campi per codice cliente e codice prodotto nel CSV.

## 📊 **Nuova Struttura CSV**

### Headers Required:
```csv
Fornitore,Prodotto,Quantità,CODE,CODICE,EAN,H,Categoria,Cliente,CC,PIA,PRO,Trasporto,Data,Note,€ Vendita,Indirizzo,Telefono
```

### Significato Campi:
- **CODE** = 🏢 **Codice Cliente** (obbligatorio) - determina assegnazione al store
- **CODICE** = 📦 **Codice Prodotto** (opzionale) - identificazione interna prodotto

## 💡 **Esempio CSV Corretto**

```csv
Fornitore,Prodotto,Quantità,CODE,CODICE,EAN,H,Categoria,Cliente,CC,PIA,PRO,Trasporto,Data,Note,€ Vendita,Indirizzo,Telefono
"Vivai Rossi","Rosa Rossa",25,"CLI001","ROSE001","1234567890123",30.5,"Roses","Garden Center Milano","CC001","PIA001","PRO001",15.50,"2025-08-01","Premium","18.99","Via Roma 123","123-456-789"
"Vivai Verdi","Tulipano",30,"CLI002","TUL001","9876543210987",20.0,"Tulips","Florist Rome","CC002","PIA002","PRO002",12.00,"2025-08-05","Standard","15.50","Via Torino 456","987-654-321"
"Vivai Blu","Orchidea",10,"CLI001",,"1122334455667",25.0,"Orchids","Garden Center Milano","CC001","PIA001","PRO001",20.00,"2025-08-10","Exotic","45.00","Via Roma 123","123-456-789"
```

## 🎯 **Logica di Processing**

1. **CODE (Cliente)**: 
   - Cerca store esistente con `client_code = CODE`
   - Se non esiste, crea nuovo store (disattivato)
   - Assegna ordine al store corretto

2. **CODICE (Prodotto)**:
   - Salva nel campo `code` del prodotto
   - Può essere stringa, numero, o vuoto
   - Serve per identificazione interna

## ✅ **Validazione Aggiornata**

- **CODE**: obbligatorio, stringa, max 100 caratteri
- **CODICE**: opzionale, qualsiasi tipo, max 100 caratteri  
- **Prodotto**: obbligatorio, nome del prodotto
- Altri campi numerici validati correttamente

## 🚀 **Risultato Import**

Per il CSV di esempio sopra:
- **3 ordini importati**
- **2 clienti**: CLI001 (esistente o nuovo), CLI002 (nuovo), CLI003 (nuovo)
- **3 fornitori**: Vivai Rossi, Vivai Verdi, Vivai Blu
- **Nuovi store creati disattivati** per approvazione admin

## ⚠️ **Note Importanti**

1. **Sempre usare CODE per cliente**, mai CODICE
2. **CODICE prodotto è opzionale** - può essere lasciato vuoto
3. **Nuovi clienti sono disattivati** - admin deve attivarli
4. **Template CSV aggiornato** - scaricare nuova versione

Il sistema è ora completamente funzionale con la separazione corretta dei codici! 🎉
