#!/usr/bin/env python3
"""
Script per aggiornare automaticamente i Google Trends relativi al mondo delle piante.
Recupera le query di tendenza, filtra quelle in crescita e le salva nel database MySQL.
"""

import os
import sys
import json
import logging
from datetime import datetime, timedelta
from typing import List, Dict, Any, Optional
import mysql.connector
from mysql.connector import Error
from pytrends.request import TrendReq
from dotenv import load_dotenv

# Carica le variabili d'ambiente
load_dotenv()

# Configurazione logging su file e stdout
log_file = os.getenv('PLANT_TRENDS_LOG', 'plant_trends.log')
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(sys.stdout),
        logging.FileHandler(log_file, mode='a', encoding='utf-8')
    ]
)
logger = logging.getLogger(__name__)

class PlantTrendsUpdater:
    """Classe per gestire l'aggiornamento dei trends delle piante."""

    def __init__(self):
        """Inizializza il client Google Trends e la connessione al database."""
        self.pytrends = None
        self.connection = None

        # Configurazione dinamica da file JSON se esiste
        config_path = os.getenv('PLANT_TRENDS_CONFIG', 'plant_trends_config.json')
        if os.path.exists(config_path):
            with open(config_path, 'r', encoding='utf-8') as f:
                config = json.load(f)
            self.plant_keywords = config.get('plant_keywords', [])
            self.regions = config.get('regions', [])
        else:
            # Keywords relative al mondo delle piante
            self.plant_keywords = [
                'piante da appartamento',
                'giardinaggio',
                'piante grasse',
                'orchidee',
                'bonsai',
                'piante aromatiche',
                'giardino verticale',
                'piante carnivore',
                'coltivare piante',
                'cura delle piante',
                'semina',
                'potatura',
                'fertilizzanti naturali',
                'compost',
                'orto domestico',
                'terreno per piante',
                'annaffiatura piante',
                'malattie delle piante',
                'propagazione piante',
                'vasi per piante'
            ]
            self.regions = [
                'IT', 'IT-25', 'IT-21', 'IT-62', 'IT-72', 'IT-45', 'IT-78', 'IT-52', 'IT-75', 'IT-82', 'IT-88', 'IT-34', 'IT-42'
            ]

        # Soglia minima per considerare una keyword "trending"
        self.min_score_threshold = 20
        self.real_count = 0
        self.simulated_count = 0

    def connect_to_database(self) -> bool:
        """Stabilisce la connessione al database MySQL."""
        try:
            self.connection = mysql.connector.connect(
                host=os.getenv('DB_HOST', 'localhost'),
                database=os.getenv('DB_DATABASE', 'chat_ai_platform'),
                user=os.getenv('DB_USERNAME', 'root'),
                password=os.getenv('DB_PASSWORD', ''),
                port=int(os.getenv('DB_PORT', 3306)),
                charset='utf8mb4',
                collation='utf8mb4_unicode_ci'
            )

            if self.connection.is_connected():
                logger.info("Connessione al database MySQL stabilita con successo")
                return True

        except Error as e:
            logger.error(f"Errore nella connessione al database: {e}")
            return False

        return False

    def initialize_pytrends(self) -> bool:
        """Inizializza il client Google Trends."""
        try:
            # Configurazione più semplice per evitare problemi di compatibilità
            self.pytrends = TrendReq(
                hl='it-IT',
                tz=360,
                timeout=(10, 25)
            )
            logger.info("Client Google Trends inizializzato con successo")
            return True

        except Exception as e:
            logger.error(f"Errore nell'inizializzazione di Google Trends: {e}")
            return False

    def get_trending_keywords(self, region: str = 'IT') -> List[Dict[str, Any]]:
        trending_keywords = []
        real_found = 0
        simulated_found = 0
        try:
            for batch_start in range(0, len(self.plant_keywords), 3):
                batch_keywords = self.plant_keywords[batch_start:batch_start + 3]
                try:
                    self.pytrends.build_payload(
                        batch_keywords,
                        cat=0,
                        timeframe='now 7-d',
                        geo=region,
                        gprop=''
                    )
                    interest_data = self.pytrends.interest_over_time()
                    if not interest_data.empty:
                        for keyword in batch_keywords:
                            if keyword in interest_data.columns:
                                recent_values = interest_data[keyword].tail(3)
                                avg_score = recent_values.mean()
                                if avg_score >= self.min_score_threshold:
                                    trending_keywords.append({
                                        'keyword': keyword,
                                        'region': region,
                                        'score': int(avg_score),
                                        'type': 'monitored_keyword'
                                    })
                                    real_found += 1
                    import time
                    time.sleep(3)
                except Exception as e:
                    logger.warning(f"Errore nel processare batch {batch_keywords}: {e}")
                    continue
        except Exception as e:
            logger.error(f"Errore generale nel recuperare trending keywords per {region}: {e}")
        # Se non si trova nulla, fallback solo se nessun dato reale
        if not trending_keywords:
            logger.info("Utilizzo dati simulati come fallback (nessun dato reale trovato)")
            import random
            for keyword in self.plant_keywords[:10]:
                simulated_score = random.randint(25, 80)
                trending_keywords.append({
                    'keyword': keyword,
                    'region': region,
                    'score': simulated_score,
                    'type': 'fallback_data'
                })
            simulated_found += len(self.plant_keywords[:10])
        self.real_count += real_found
        self.simulated_count += simulated_found
        return trending_keywords

    def get_region_name(self, region_code: str) -> str:
        """
        Restituisce il nome leggibile della regione dal codice.

        Args:
            region_code: Codice della regione (es. 'IT-25')

        Returns:
            Nome della regione in italiano
        """
        region_names = {
            'IT': 'Italia',
            'IT-25': 'Lombardia',
            'IT-21': 'Piemonte',
            'IT-62': 'Lazio',
            'IT-72': 'Campania',
            'IT-45': 'Emilia-Romagna',
            'IT-78': 'Calabria',
            'IT-52': 'Toscana',
            'IT-75': 'Puglia',
            'IT-82': 'Sicilia',
            'IT-88': 'Sardegna',
            'IT-34': 'Veneto',
            'IT-42': 'Liguria',
            'IT-77': 'Basilicata',
            'IT-65': 'Abruzzo',
            'IT-55': 'Umbria',
            'IT-57': 'Marche',
            'IT-32': 'Trentino-Alto Adige',
            'IT-36': 'Friuli-Venezia Giulia',
            'IT-23': 'Valle d\'Aosta',
            'IT-67': 'Molise'
        }
        return region_names.get(region_code, region_code)

    def is_plant_related(self, keyword: str) -> bool:
        """
        Verifica se una keyword è correlata al mondo delle piante.

        Args:
            keyword: La keyword da verificare

        Returns:
            True se la keyword è correlata alle piante
        """
        plant_terms = [
            'pianta', 'piante', 'giardino', 'giardinaggio', 'fiore', 'fiori',
            'orto', 'coltiv', 'semina', 'potatura', 'fertilizzante', 'compost',
            'vaso', 'terra', 'terreno', 'annaffia', 'innaffia', 'botanica',
            'grassa', 'grasse', 'succulenta', 'bonsai', 'orchidea', 'orchidee',
            'rosa', 'rose', 'tulip', 'narciso', 'primula', 'geranio',
            'basilico', 'rosmarino', 'prezzemolo', 'salvia', 'timo',
            'pomodoro', 'lattuga', 'carota', 'peperone', 'zucchina'
        ]

        keyword_lower = keyword.lower()
        return any(term in keyword_lower for term in plant_terms)

    def keyword_exists_today(self, keyword: str, region: str) -> bool:
        """
        Verifica se una keyword per una regione è già stata salvata oggi.

        Args:
            keyword: La keyword da verificare
            region: La regione

        Returns:
            True se esiste già un record per oggi
        """
        try:
            cursor = self.connection.cursor()
            today = datetime.now().date()

            query = """
            SELECT COUNT(*) FROM trending_keywords
            WHERE keyword = %s AND region = %s AND DATE(collected_at) = %s
            """

            cursor.execute(query, (keyword, region, today))
            count = cursor.fetchone()[0]
            cursor.close()

            return count > 0

        except Error as e:
            logger.error(f"Errore nel verificare duplicati: {e}")
            return False

    def discover_and_save_keywords(self, base_keyword, region, depth=1, max_depth=2, processed=None):
        """
        Ricerca e salva keyword correlate e argomenti correlati (related topics) ricorsivamente.
        """
        import time
        if processed is None:
            processed = set()
        if depth > max_depth or base_keyword in processed:
            return
        processed.add(base_keyword)

        try:
            # Usa periodo 12 mesi e Italia
            self.pytrends.build_payload([base_keyword], cat=0, timeframe='today 12-m', geo='IT')
            related = self.pytrends.related_queries().get(base_keyword, {})
            topics = self.pytrends.related_topics().get(base_keyword, {})
            logger.debug(f"related_queries for {base_keyword}: {related}")
            logger.debug(f"related_topics for {base_keyword}: {topics}")
        except Exception as e:
            logger.warning(f"Errore pytrends per '{base_keyword}': {e}")
            return

        # Serializza i related topics (solo titoli)
        related_topics_list = []
        for rel_type in ['top', 'rising']:
            if rel_type in topics and topics[rel_type] is not None and hasattr(topics[rel_type], 'to_dict'):
                for row in topics[rel_type].to_dict('records'):
                    if row.get('topic_title'):
                        related_topics_list.append(row['topic_title'])
        related_topics_str = ', '.join(set(related_topics_list)) if related_topics_list else None

        # Salva la base_keyword come trending (se non già salvata)
        base_data = {
            'keyword': base_keyword,
            'score': 0,
            'region': region,
            'parent_keyword': None,
            'related_topics': related_topics_str
        }
        self.save_trending_keyword(base_data)

        # Related queries: salva e ricorsione
        for rel_type in ['top', 'rising']:
            if rel_type in related and related[rel_type] is not None and hasattr(related[rel_type], 'to_dict'):
                for row in related[rel_type].to_dict('records'):
                    keyword = row.get('query')
                    if not keyword or not self.is_plant_related(keyword):
                        continue
                    keyword_data = {
                        'keyword': keyword,
                        'score': int(row.get('value', 0)),
                        'region': region,
                        'parent_keyword': base_keyword,
                        'related_topics': related_topics_str
                    }
                    self.save_trending_keyword(keyword_data)
                    # Ricorsione su nuove keyword
                    self.discover_and_save_keywords(keyword, region, depth+1, max_depth, processed)
        time.sleep(2)

    def save_trending_keyword(self, keyword_data: Dict[str, Any]) -> bool:
        """
        Salva una keyword trending nel database (esteso: parent_keyword, related_topics).
        """
        try:
            if self.keyword_exists_today(keyword_data['keyword'], keyword_data['region']):
                logger.debug(f"Keyword '{keyword_data['keyword']}' per regione '{keyword_data['region']}' già esistente per oggi")
                return True
            cursor = self.connection.cursor()
            insert_query = """
            INSERT INTO trending_keywords (keyword, score, region, parent_keyword, related_topics, collected_at, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
            """
            now = datetime.now()
            values = (
                keyword_data['keyword'],
                keyword_data['score'],
                keyword_data['region'],
                keyword_data.get('parent_keyword'),
                keyword_data.get('related_topics'),
                now,
                now,
                now
            )
            cursor.execute(insert_query, values)
            self.connection.commit()
            cursor.close()
            logger.info(f"Salvata keyword: {keyword_data['keyword']} (score: {keyword_data['score']}, region: {keyword_data['region']}, parent: {keyword_data.get('parent_keyword')})")
            return True
        except Error as e:
            if 'trending_keywords' in str(e):
                logger.error("La tabella trending_keywords non esiste! Esegui la migration in Laravel.")
            else:
                logger.error(f"Errore nel salvare keyword {keyword_data}: {e}")
            return False

    def cleanup_old_data(self, days_to_keep: int = 90) -> None:
        """
        Rimuove i dati più vecchi di N giorni per mantenere il database pulito.

        Args:
            days_to_keep: Numero di giorni di dati da mantenere
        """
        try:
            cursor = self.connection.cursor()
            cutoff_date = datetime.now() - timedelta(days=days_to_keep)

            delete_query = "DELETE FROM trending_keywords WHERE collected_at < %s"
            cursor.execute(delete_query, (cutoff_date,))

            deleted_rows = cursor.rowcount
            self.connection.commit()
            cursor.close()

            if deleted_rows > 0:
                logger.info(f"Rimossi {deleted_rows} record più vecchi di {days_to_keep} giorni")

        except Error as e:
            logger.error(f"Errore nella pulizia dati obsoleti: {e}")

    def close_connection(self) -> None:
        """Chiude la connessione al database."""
        if self.connection and self.connection.is_connected():
            self.connection.close()
            logger.info("Connessione al database chiusa")

    def run(self) -> bool:
        """
        Esegue l'intero processo di aggiornamento dei trends (ora con keyword discovery ricorsiva).
        """
        success = True
        total_saved = 0
        try:
            if not self.connect_to_database():
                return False
            if not self.initialize_pytrends():
                return False
            logger.info("Inizio aggiornamento Google Trends per piante...")
            for region in self.regions:
                logger.info(f"Processando regione: {region}")
                processed = set()
                for base_keyword in self.plant_keywords:
                    self.discover_and_save_keywords(base_keyword, region, depth=1, max_depth=2, processed=processed)
                    # Pausa tra keyword principali
                    import time
                    time.sleep(2)
            self.cleanup_old_data()
            logger.info(f"Aggiornamento completato.")
        except Exception as e:
            logger.error(f"Errore generale nell'aggiornamento trends: {e}")
            success = False
        finally:
            self.close_connection()
        return success

def main():
    """Funzione principale dello script."""
    logger.info("=== Avvio aggiornamento Google Trends piante ===")

    updater = PlantTrendsUpdater()
    success = updater.run()

    if success:
        logger.info("=== Aggiornamento completato con successo ===")
        sys.exit(0)
    else:
        logger.error("=== Aggiornamento completato con errori ===")
        sys.exit(1)

if __name__ == "__main__":
    main()
