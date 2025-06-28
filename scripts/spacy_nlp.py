#!/usr/bin/env python3
"""
Advanced spaCy NLP Script per Chatbot Plants
- Estrae entità, parole chiave, intenti avanzati e sentiment
- Modello: it_core_news_sm + custom plant entities
- Features: Intent detection, Named Entity Recognition, Sentiment Analysis, Keywords extraction
"""
import sys
import spacy
import json
import re
import os
from typing import Dict, List, Tuple

# Set UTF-8 encoding for Windows compatibility
if os.name == 'nt':  # Windows
    import codecs
    sys.stdout = codecs.getwriter('utf-8')(sys.stdout.buffer, 'strict')
    sys.stderr = codecs.getwriter('utf-8')(sys.stderr.buffer, 'strict')

try:
    nlp = spacy.load("it_core_news_sm")
except OSError:
    print(json.dumps({"error": "Modello spaCy it_core_news_sm non trovato. Installare con: python -m spacy download it_core_news_sm"}))
    sys.exit(1)

# Pattern di intent più avanzati con pesi e regex
INTENT_PATTERNS = {
    "cura": {
        "keywords": ["cura", "curare", "trattamento", "malattia", "foglie gialle", "marciume", "parassiti", "concime", "annaffiare", "innaffiare", "potare", "rinvaso"],
        "patterns": [r"come.*cur", r"problemi.*con", r"foglie.*gial", r"sta.*morendo", r"malata", r"concim"],
        "weight": 1.0
    },
    "acquisto": {
        "keywords": ["comprare", "acquistare", "prezzo", "costo", "vendita", "negozio", "shop", "store", "ordinare", "spedizione"],
        "patterns": [r"quanto.*cost", r"dove.*compr", r"prezz", r"acquist", r"vend"],
        "weight": 1.0
    },
    "identificazione": {
        "keywords": ["che pianta", "identifica", "nome", "specie", "varietà", "tipo", "riconoscere", "cos'è"],
        "patterns": [r"che.*piant", r"identific", r"che.*tipo", r"nome.*piant", r"cos.*questa"],
        "weight": 1.0
    },
    "consiglio": {
        "keywords": ["consiglio", "suggerisci", "quale", "migliore", "adatta", "perfetta", "ideale", "raccomandi"],
        "patterns": [r"quale.*piant", r"consigli", r"suggerisc", r"miglior", r"adatt", r"perfett"],
        "weight": 1.0
    },
    "ambiente": {
        "keywords": ["sole", "ombra", "luce", "interno", "esterno", "balcone", "giardino", "appartamento", "umidità", "temperatura"],
        "patterns": [r"piant.*interno", r"piant.*esterno", r"poc.*luce", r"molt.*sole", r"ambient"],
        "weight": 0.8
    },
    "problemi": {
        "keywords": ["aiuto", "problema", "urgente", "emergenza", "soccorso", "salvare", "sta morendo"],
        "patterns": [r"aiut", r"problem", r"urgent", r"sta.*morend", r"salvar"],
        "weight": 1.2
    }
}

# Entità personalizzate per piante
PLANT_ENTITIES = {
    "PLANT_NAME": [
        "ficus", "monstera", "pothos", "sansevieria", "aloe", "basilico", "rosmarino", "lavanda",
        "geranio", "orchidea", "rosa", "tulipano", "girasole", "cactus", "succulenta", "edera",
        "begonia", "violetta", "azalea", "camelia", "magnolia", "ibisco", "gelsomino", "bouganville"
    ],
    "PLANT_PART": [
        "foglie", "foglia", "radici", "fiori", "fiore", "stelo", "ramo", "rami", "frutti", "frutto",
        "petali", "sepali", "pistillo", "stami", "corteccia", "gemme", "boccioli"
    ],
    "PLANT_PROBLEM": [
        "foglie gialle", "marciume", "parassiti", "afidi", "ragnetto rosso", "oidio", "peronospora",
        "macchie nere", "ingiallimento", "appassimento", "caduta foglie", "muffa", "cocciniglia"
    ],
    "PLANT_CARE": [
        "annaffiare", "potare", "rinvasare", "concimare", "fertilizzare", "trapianto", "propagazione",
        "talea", "semina", "irrigazione", "drenaggio", "esposizione"
    ]
}

def detect_intent(text: str) -> Tuple[str, float]:
    """
    Rileva l'intent con maggiore precisione usando pattern multipli e scoring
    """
    text_lower = text.lower()
    intent_scores = {}

    for intent, config in INTENT_PATTERNS.items():
        score = 0.0

        # Score da keywords
        for keyword in config["keywords"]:
            if keyword in text_lower:
                score += config["weight"]

        # Score da pattern regex
        for pattern in config["patterns"]:
            if re.search(pattern, text_lower):
                score += config["weight"] * 1.5  # I pattern regex hanno peso maggiore

        intent_scores[intent] = score

    # Trova l'intent con score più alto
    best_intent = max(intent_scores.items(), key=lambda x: x[1])

    if best_intent[1] > 0:
        return best_intent[0], best_intent[1]
    else:
        return "altro", 0.0

def extract_plant_entities(text: str) -> List[Dict]:
    """
    Estrae entità specifiche del dominio piante
    """
    text_lower = text.lower()
    entities = []

    for entity_type, terms in PLANT_ENTITIES.items():
        for term in terms:
            if term in text_lower:
                start = text_lower.find(term)
                end = start + len(term)
                entities.append({
                    "text": text[start:end],
                    "label": entity_type,
                    "start": start,
                    "end": end,
                    "confidence": 0.8
                })

    return entities

def analyze_sentiment(text: str) -> Dict:
    """
    Analisi del sentiment di base usando keywords
    """
    text_lower = text.lower()

    positive_words = ["bene", "bello", "perfetto", "grazie", "ottimo", "eccellente", "fantastico", "felice", "contento"]
    negative_words = ["male", "brutto", "problema", "aiuto", "morendo", "malata", "triste", "preoccupato", "urgent"]
    neutral_words = ["come", "cosa", "dove", "quando", "perché", "quale"]

    pos_count = sum(1 for word in positive_words if word in text_lower)
    neg_count = sum(1 for word in negative_words if word in text_lower)
    neu_count = sum(1 for word in neutral_words if word in text_lower)

    total = pos_count + neg_count + neu_count

    if total == 0:
        return {"sentiment": "neutral", "confidence": 0.5, "scores": {"positive": 0.33, "negative": 0.33, "neutral": 0.34}}

    pos_score = pos_count / total
    neg_score = neg_count / total
    neu_score = neu_count / total

    if pos_score > neg_score and pos_score > neu_score:
        sentiment = "positive"
        confidence = pos_score
    elif neg_score > pos_score and neg_score > neu_score:
        sentiment = "negative"
        confidence = neg_score
    else:
        sentiment = "neutral"
        confidence = neu_score

    return {
        "sentiment": sentiment,
        "confidence": round(confidence, 2),
        "scores": {
            "positive": round(pos_score, 2),
            "negative": round(neg_score, 2),
            "neutral": round(neu_score, 2)
        }
    }

def extract_advanced_keywords(doc) -> List[str]:
    """
    Estrae keywords più intelligenti usando POS tagging e dependency parsing
    """
    keywords = []

    # Estrai sostantivi, aggettivi e verbi importanti
    for token in doc:
        if (token.pos_ in ("NOUN", "PROPN", "ADJ", "VERB") and
            not token.is_stop and
            not token.is_punct and
            len(token.text) > 2 and
            not token.like_url and
            not token.like_email):

            # Usa il lemma per normalizzare
            lemma = token.lemma_.lower()
            if lemma not in keywords:
                keywords.append(lemma)

    # Estrai anche compound nouns (sostantivi composti)
    for chunk in doc.noun_chunks:
        if len(chunk.text.split()) > 1:
            keywords.append(chunk.text.lower())

    return keywords[:10]  # Limita a 10 keywords più rilevanti

def analyze(text: str) -> Dict:
    """
    Analisi completa del testo
    """
    doc = nlp(text)

    # Entità standard di spaCy
    spacy_entities = [{"text": ent.text, "label": ent.label_, "start": ent.start_char, "end": ent.end_char} for ent in doc.ents]

    # Entità personalizzate per piante
    plant_entities = extract_plant_entities(text)

    # Combina tutte le entità
    all_entities = spacy_entities + plant_entities

    # Keywords avanzate
    keywords = extract_advanced_keywords(doc)

    # Intent detection con confidence
    intent, intent_confidence = detect_intent(text)

    # Sentiment analysis
    sentiment = analyze_sentiment(text)

    # Genera suggerimenti intelligenti
    suggestions = generate_smart_suggestions(intent, keywords, sentiment)

    return {
        "entities": all_entities,
        "keywords": keywords,
        "intent": intent,
        "intent_confidence": round(intent_confidence, 2),
        "sentiment": sentiment,
        "suggestions": suggestions,
        "text": text,
        "text_length": len(text),
        "token_count": len(doc),
        "source": "spacy_advanced"
    }

def generate_smart_suggestions(intent: str, keywords: List[str], sentiment: Dict) -> List[str]:
    """
    Genera suggerimenti intelligenti basati su intent, keywords e sentiment
    """
    suggestions = []

    # Suggerimenti basati sull'intent
    if intent == "cura":
        if sentiment["sentiment"] == "negative":
            suggestions.append("🚨 Sembra che tu abbia un problema urgente! Descrivimi i sintomi.")
        suggestions.extend([
            "💧 Vuoi sapere come innaffiare correttamente?",
            "🌱 Problemi con concimazione e fertilizzanti?",
            "🍃 Le foglie hanno problemi? Dimmi di più!"
        ])
    elif intent == "acquisto":
        suggestions.extend([
            "🛒 Vuoi vedere il nostro catalogo piante?",
            "💰 Cerchi offerte speciali?",
            "🚚 Informazioni su spedizione e consegna?"
        ])
    elif intent == "identificazione":
        suggestions.extend([
            "📸 Puoi caricare una foto della pianta?",
            "📝 Descrivimi foglie, fiori e dimensioni",
            "🏡 Dove hai visto questa pianta?"
        ])
    elif intent == "consiglio":
        suggestions.extend([
            "🏠 Piante per interno o esterno?",
            "⏰ Quanto tempo hai per la cura?",
            "☀️ Che tipo di luce è disponibile?"
        ])
    elif intent == "ambiente":
        suggestions.extend([
            "☀️ Piante per zone molto soleggiate?",
            "🌑 Piante che amano l'ombra?",
            "🏡 Piante perfette per appartamento?"
        ])
    elif intent == "problemi":
        suggestions.extend([
            "🆘 Descrivimi il problema nel dettaglio",
            "📸 Una foto aiuterebbe molto!",
            "⏰ Da quanto tempo noti il problema?"
        ])

    # Suggerimenti basati su keywords specifiche
    plant_keywords = ["ficus", "monstera", "pothos", "orchidea", "cactus", "succulenta"]
    for keyword in keywords:
        if keyword in plant_keywords:
            suggestions.append(f"🌿 Vuoi sapere tutto su {keyword}?")
            break

    # Suggerimenti basati sul sentiment
    if sentiment["sentiment"] == "negative" and intent != "problemi":
        suggestions.insert(0, "😟 Sembra che tu sia preoccupato. Posso aiutarti!")
    elif sentiment["sentiment"] == "positive":
        suggestions.append("😊 Che bello il tuo entusiasmo per le piante!")

    return suggestions[:5]  # Massimo 5 suggerimenti

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Nessun testo fornito"}, ensure_ascii=False))
        sys.exit(1)

    input_text = sys.argv[1]
    try:
        result = analyze(input_text)
        # Use ensure_ascii=False for proper Unicode support, but handle Windows encoding
        json_output = json.dumps(result, ensure_ascii=False)
        print(json_output)
    except Exception as e:
        error_result = {"error": f"Errore nell'analisi: {str(e)}", "source": "error"}
        print(json.dumps(error_result, ensure_ascii=False))
