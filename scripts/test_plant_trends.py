#!/usr/bin/env python3
"""
Script semplificato per testare l'aggiornamento Google Trends.
Inserisce alcuni dati di test nel database per verificare la funzionalità.
"""

import os
import sys
import logging
from datetime import datetime
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv

# Carica le variabili d'ambiente
load_dotenv()

# Configurazione logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.StreamHandler(sys.stdout)
    ]
)
logger = logging.getLogger(__name__)

def connect_to_database():
    """Stabilisce la connessione al database MySQL."""
    try:
        connection = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'localhost'),
            database=os.getenv('DB_DATABASE', 'chat_ai_platform'),
            user=os.getenv('DB_USERNAME', 'root'),
            password=os.getenv('DB_PASSWORD', ''),
            port=int(os.getenv('DB_PORT', 3306)),
            charset='utf8mb4',
            collation='utf8mb4_unicode_ci'
        )

        if connection.is_connected():
            logger.info("Connessione al database MySQL stabilita con successo")
            return connection

    except Error as e:
        logger.error(f"Errore nella connessione al database: {e}")
        return None

def keyword_exists_today(connection, keyword, region):
    """Verifica se una keyword per una regione è già stata salvata oggi."""
    try:
        cursor = connection.cursor()
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

def save_trending_keyword(connection, keyword, score, region):
    """Salva una keyword trending nel database."""
    try:
        # Verifica se esiste già oggi
        if keyword_exists_today(connection, keyword, region):
            logger.debug(f"Keyword '{keyword}' per regione '{region}' già esistente per oggi")
            return True

        cursor = connection.cursor()

        insert_query = """
        INSERT INTO trending_keywords (keyword, score, region, collected_at, created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s, %s)
        """

        now = datetime.now()
        values = (
            keyword,
            score,
            region,
            now,
            now,
            now
        )

        cursor.execute(insert_query, values)
        connection.commit()
        cursor.close()

        logger.info(f"Salvata keyword: {keyword} (score: {score}, region: {region})")
        return True

    except Error as e:
        logger.error(f"Errore nel salvare keyword {keyword}: {e}")
        return False

def main():
    """Funzione principale dello script di test."""
    logger.info("=== Avvio test aggiornamento Google Trends piante ===")

    connection = connect_to_database()
    if not connection:
        logger.error("Impossibile connettersi al database")
        sys.exit(1)

    # Dati di test simulati per Google Trends
    test_keywords = [
        {"keyword": "piante da appartamento", "score": 85, "region": "IT"},
        {"keyword": "giardinaggio", "score": 72, "region": "IT"},
        {"keyword": "piante grasse", "score": 68, "region": "IT"},
        {"keyword": "orchidee", "score": 55, "region": "IT"},
        {"keyword": "bonsai", "score": 45, "region": "IT"},
        {"keyword": "piante aromatiche", "score": 63, "region": "IT-25"},
        {"keyword": "orto domestico", "score": 58, "region": "IT-25"},
        {"keyword": "cura delle piante", "score": 41, "region": "IT-21"},
        {"keyword": "fertilizzanti naturali", "score": 35, "region": "IT-62"},
        {"keyword": "compost", "score": 28, "region": "IT-62"}
    ]

    total_saved = 0

    try:
        for keyword_data in test_keywords:
            if save_trending_keyword(
                connection,
                keyword_data["keyword"],
                keyword_data["score"],
                keyword_data["region"]
            ):
                total_saved += 1

        logger.info(f"Test completato. Totale keywords salvate: {total_saved}")

    except Exception as e:
        logger.error(f"Errore durante il test: {e}")
        sys.exit(1)

    finally:
        if connection and connection.is_connected():
            connection.close()
            logger.info("Connessione al database chiusa")

    logger.info("=== Test completato con successo ===")
    sys.exit(0)

if __name__ == "__main__":
    main()
