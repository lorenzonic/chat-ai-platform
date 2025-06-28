#!/usr/bin/env python3
"""
Advanced Plant E-commerce Scraper - Real Sites
Responsible scraping of Italian plant retailers with proper headers and delays
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import random
import argparse
from datetime import datetime
import re
from urllib.parse import urljoin, urlparse, quote
import logging
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class AdvancedPlantScraper:
    def __init__(self, use_selenium=False):
        self.use_selenium = use_selenium
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding': 'gzip, deflate, br',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
        })

        if use_selenium:
            self.setup_selenium()

        # Real Italian plant retailers
        self.target_sites = {
            'viridea': {
                'name': 'Viridea',
                'base_url': 'https://www.viridea.it',
                'search_path': '/catalogsearch/result/?q={}',
                'selectors': {
                    'products': '.product-item-info',
                    'name': '.product-item-link',
                    'price': '.price-wrapper .price',
                    'image': '.product-image-photo',
                    'link': '.product-item-link'
                },
                'enabled': True
            },
            'bakker': {
                'name': 'Bakker.com Italia',
                'base_url': 'https://www.bakker.com',
                'search_path': '/it/search/?q={}',
                'selectors': {
                    'products': '.product-item',
                    'name': '.product-title a',
                    'price': '.price .amount',
                    'availability': '.stock-status',
                    'link': '.product-title a'
                },
                'enabled': True
            },
            'mondo_piante': {
                'name': 'Mondo Piante',
                'base_url': 'https://www.mondopiante.it',
                'search_path': '/search?controller=search&orderby=position&orderway=desc&search_query={}',
                'selectors': {
                    'products': '.product-miniature',
                    'name': '.product-title a',
                    'price': '.price',
                    'availability': '.product-availability',
                    'link': '.product-title a'
                },
                'enabled': True
            },
            'euro3plast': {
                'name': 'Euro3plast Garden',
                'base_url': 'https://www.euro3plast.com',
                'search_path': '/it/search?controller=search&s={}',
                'selectors': {
                    'products': '.product-container',
                    'name': '.product-name a',
                    'price': '.price .product-price',
                    'availability': '.availability span',
                    'link': '.product-name a'
                },
                'enabled': True
            }
        }

        self.plant_searches = [
            'monstera', 'ficus', 'pothos', 'sansevieria', 'philodendron',
            'echeveria', 'aloe', 'cactus', 'succulente', 'piante grasse',
            'basilico', 'rosmarino', 'lavanda', 'gerani', 'petunie',
            'orchidee', 'bonsai', 'palme', 'piante da interno'
        ]

    def setup_selenium(self):
        """Setup Selenium WebDriver for JavaScript-heavy sites"""
        try:
            chrome_options = Options()
            chrome_options.add_argument('--headless')
            chrome_options.add_argument('--no-sandbox')
            chrome_options.add_argument('--disable-dev-shm-usage')
            chrome_options.add_argument('--disable-gpu')
            chrome_options.add_argument('--window-size=1920,1080')
            chrome_options.add_argument('--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36')

            self.driver = webdriver.Chrome(options=chrome_options)
            self.wait = WebDriverWait(self.driver, 10)
            logger.info("Selenium WebDriver initialized")
        except Exception as e:
            logger.error(f"Failed to initialize Selenium: {e}")
            self.use_selenium = False

    def scrape_with_requests(self, site_key, search_term):
        """Scrape using requests and BeautifulSoup"""
        site_config = self.target_sites[site_key]
        products = []

        try:
            search_url = site_config['base_url'] + site_config['search_path'].format(quote(search_term))
            logger.info(f"Scraping {site_config['name']}: {search_url}")

            # Respectful delay
            time.sleep(random.uniform(2, 4))

            response = self.session.get(search_url, timeout=15)
            response.raise_for_status()

            soup = BeautifulSoup(response.content, 'html.parser')
            product_elements = soup.select(site_config['selectors']['products'])

            logger.info(f"Found {len(product_elements)} product elements")

            for i, element in enumerate(product_elements[:15]):  # Limit results
                try:
                    # Extract product information
                    name_elem = element.select_one(site_config['selectors']['name'])
                    price_elem = element.select_one(site_config['selectors']['price'])

                    if not name_elem or not price_elem:
                        continue

                    name = name_elem.get_text(strip=True)
                    price_text = price_elem.get_text(strip=True)

                    # Extract availability if available
                    availability = 'Disponibile'
                    if 'availability' in site_config['selectors']:
                        availability_elem = element.select_one(site_config['selectors']['availability'])
                        if availability_elem:
                            availability = availability_elem.get_text(strip=True)

                    # Extract price
                    price = self.extract_price(price_text)

                    # Get product URL
                    product_url = ''
                    if 'link' in site_config['selectors']:
                        link_elem = element.select_one(site_config['selectors']['link'])
                        if link_elem and link_elem.get('href'):
                            product_url = urljoin(site_config['base_url'], link_elem.get('href'))

                    if name and price > 0:
                        products.append({
                            'name': name,
                            'price': price,
                            'availability': availability,
                            'category': search_term,
                            'source': site_config['name'],
                            'url': product_url,
                            'scraped_at': datetime.now().isoformat()
                        })

                except Exception as e:
                    logger.warning(f"Error parsing product {i}: {e}")
                    continue

            logger.info(f"Successfully scraped {len(products)} products from {site_config['name']}")
            return products

        except requests.RequestException as e:
            logger.error(f"Request failed for {site_config['name']}: {e}")
            return []
        except Exception as e:
            logger.error(f"Unexpected error scraping {site_config['name']}: {e}")
            return []

    def scrape_with_selenium(self, site_key, search_term):
        """Scrape using Selenium for JavaScript-heavy sites"""
        if not self.use_selenium:
            return []

        site_config = self.target_sites[site_key]
        products = []

        try:
            search_url = site_config['base_url'] + site_config['search_path'].format(quote(search_term))
            logger.info(f"Selenium scraping {site_config['name']}: {search_url}")

            self.driver.get(search_url)
            time.sleep(3)  # Wait for page load

            # Wait for products to load
            try:
                self.wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, site_config['selectors']['products'])))
            except:
                logger.warning(f"No products found or timeout for {search_term}")
                return []

            product_elements = self.driver.find_elements(By.CSS_SELECTOR, site_config['selectors']['products'])

            for element in product_elements[:15]:
                try:
                    name_elem = element.find_element(By.CSS_SELECTOR, site_config['selectors']['name'])
                    price_elem = element.find_element(By.CSS_SELECTOR, site_config['selectors']['price'])

                    name = name_elem.text.strip()
                    price_text = price_elem.text.strip()
                    price = self.extract_price(price_text)

                    if name and price > 0:
                        products.append({
                            'name': name,
                            'price': price,
                            'availability': 'Disponibile',
                            'category': search_term,
                            'source': site_config['name'],
                            'scraped_at': datetime.now().isoformat()
                        })

                except Exception as e:
                    continue

            logger.info(f"Selenium scraped {len(products)} products from {site_config['name']}")
            return products

        except Exception as e:
            logger.error(f"Selenium scraping failed for {site_config['name']}: {e}")
            return []

    def extract_price(self, price_text):
        """Extract numeric price from text"""
        # Remove common currency symbols and text
        cleaned = re.sub(r'[€$£¥₹]', '', price_text)
        cleaned = re.sub(r'[^\d,.\s]', '', cleaned)

        # Find price patterns
        price_patterns = [
            r'(\d{1,3}(?:[.,]\d{3})*[.,]\d{2})',  # 1.234,56 or 1,234.56
            r'(\d+[.,]\d{2})',                     # 12,34 or 12.34
            r'(\d+)'                               # 12
        ]

        for pattern in price_patterns:
            match = re.search(pattern, cleaned)
            if match:
                price_str = match.group(1)
                # Handle different decimal separators
                if ',' in price_str and '.' in price_str:
                    if price_str.rfind(',') > price_str.rfind('.'):
                        price_str = price_str.replace('.', '').replace(',', '.')
                    else:
                        price_str = price_str.replace(',', '')
                elif ',' in price_str:
                    price_str = price_str.replace(',', '.')

                try:
                    return float(price_str)
                except ValueError:
                    continue

        return 0.0

    def scrape_all_sites(self):
        """Scrape all enabled sites for all plant categories"""
        all_products = []
        scraping_stats = {
            'sites_scraped': 0,
            'total_products': 0,
            'categories_completed': 0,
            'errors': []
        }

        logger.info("Starting comprehensive plant e-commerce scraping...")

        for search_term in self.plant_searches:
            logger.info(f"Searching for: {search_term}")

            for site_key, site_config in self.target_sites.items():
                if not site_config['enabled']:
                    continue

                try:
                    # Try requests first, fallback to Selenium if needed
                    products = self.scrape_with_requests(site_key, search_term)

                    if not products and self.use_selenium:
                        products = self.scrape_with_selenium(site_key, search_term)

                    all_products.extend(products)
                    scraping_stats['total_products'] += len(products)

                except Exception as e:
                    error_msg = f"Failed to scrape {site_key} for {search_term}: {e}"
                    logger.error(error_msg)
                    scraping_stats['errors'].append(error_msg)

                # Be respectful with delays
                time.sleep(random.uniform(3, 6))

            scraping_stats['categories_completed'] += 1

            # Longer delay between categories
            time.sleep(random.uniform(5, 10))

        scraping_stats['sites_scraped'] = len([s for s in self.target_sites.values() if s['enabled']])

        # Generate insights
        insights = self.analyze_scraped_data(all_products)

        return {
            'products': all_products,
            'insights': insights,
            'stats': scraping_stats,
            'scraped_at': datetime.now().isoformat(),
            'total_products': len(all_products)
        }

    def analyze_scraped_data(self, products):
        """Analyze scraped data for market insights"""
        if not products:
            return {}

        insights = {
            'price_analysis': {},
            'availability_trends': {},
            'popular_plants': [],
            'price_ranges': {},
            'market_opportunities': [],
            'site_comparison': {}
        }

        # Group by category
        by_category = {}
        by_source = {}

        for product in products:
            category = product['category']
            source = product['source']

            if category not in by_category:
                by_category[category] = []
            by_category[category].append(product)

            if source not in by_source:
                by_source[source] = []
            by_source[source].append(product)

        # Price analysis by category
        for category, category_products in by_category.items():
            prices = [p['price'] for p in category_products if p['price'] > 0]

            if prices:
                avg_price = sum(prices) / len(prices)
                insights['price_analysis'][category] = {
                    'avg_price': round(avg_price, 2),
                    'min_price': min(prices),
                    'max_price': max(prices),
                    'product_count': len(category_products),
                    'price_std': round((sum([(p - avg_price) ** 2 for p in prices]) / len(prices)) ** 0.5, 2)
                }

        # Site comparison
        for source, source_products in by_source.items():
            prices = [p['price'] for p in source_products if p['price'] > 0]
            if prices:
                insights['site_comparison'][source] = {
                    'product_count': len(source_products),
                    'avg_price': round(sum(prices) / len(prices), 2),
                    'price_range': f"€{min(prices):.2f} - €{max(prices):.2f}"
                }

        return insights

    def cleanup(self):
        """Clean up resources"""
        if self.use_selenium and hasattr(self, 'driver'):
            self.driver.quit()

def main():
    parser = argparse.ArgumentParser(description='Advanced plant e-commerce scraper')
    parser.add_argument('--output', help='Output file path')
    parser.add_argument('--selenium', action='store_true', help='Use Selenium WebDriver')
    parser.add_argument('--category', help='Specific category to scrape')

    args = parser.parse_args()

    scraper = AdvancedPlantScraper(use_selenium=args.selenium)

    try:
        if args.category:
            # Scrape specific category from all sites
            all_products = []
            for site_key, site_config in scraper.target_sites.items():
                if site_config['enabled']:
                    products = scraper.scrape_with_requests(site_key, args.category)
                    all_products.extend(products)

            data = {
                'products': all_products,
                'category': args.category,
                'scraped_at': datetime.now().isoformat(),
                'total_products': len(all_products)
            }
        else:
            # Scrape all categories from all sites
            data = scraper.scrape_all_sites()

        # Output results
        result_json = json.dumps(data, indent=2, ensure_ascii=False)

        if args.output:
            with open(args.output, 'w', encoding='utf-8') as f:
                f.write(result_json)
            logger.info(f"Data saved to {args.output}")
        else:
            print(result_json)

    finally:
        scraper.cleanup()

if __name__ == '__main__':
    main()
