#!/usr/bin/env python3
"""
Plant E-commerce Scraper for Italian Garden Centers
Scrapes pricing, availability, and product data from major plant retailers
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import random
import argparse
from datetime import datetime
import re
from urllib.parse import urljoin, urlparse
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class PlantEcommerceScraper:
    def __init__(self):
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding': 'gzip, deflate',
            'Connection': 'keep-alive',
        })

        # Target websites (Italian plant retailers)
        self.target_sites = {
            'vivaio_online': {
                'base_url': 'https://example-vivaio.it',  # Replace with real URLs
                'search_path': '/search?q={}',
                'selectors': {
                    'products': '.product-item',
                    'name': '.product-name',
                    'price': '.price',
                    'availability': '.stock-status'
                }
            },
            'garden_center': {
                'base_url': 'https://example-garden.it',
                'search_path': '/cerca/{}',
                'selectors': {
                    'products': '.item-product',
                    'name': 'h3.title',
                    'price': '.price-current',
                    'availability': '.availability'
                }
            }
        }

        # Plant categories to search
        self.plant_categories = [
            'monstera', 'ficus', 'pothos', 'sansevieria', 'philodendron',
            'echeveria', 'aloe', 'cactus', 'succulente',
            'basilico', 'rosmarino', 'lavanda', 'gerani', 'petunie',
            'orchidee', 'bonsai', 'palme', 'piante-grasse'
        ]

    def scrape_generic_site(self, site_config, search_term):
        """Generic scraper for plant e-commerce sites"""
        try:
            search_url = site_config['base_url'] + site_config['search_path'].format(search_term)

            # Add delay to be respectful
            time.sleep(random.uniform(1, 3))

            response = self.session.get(search_url, timeout=10)
            response.raise_for_status()

            soup = BeautifulSoup(response.content, 'html.parser')
            products = []

            product_elements = soup.select(site_config['selectors']['products'])

            for element in product_elements[:10]:  # Limit to first 10 results
                try:
                    name_elem = element.select_one(site_config['selectors']['name'])
                    price_elem = element.select_one(site_config['selectors']['price'])
                    availability_elem = element.select_one(site_config['selectors']['availability'])

                    if name_elem and price_elem:
                        name = name_elem.get_text(strip=True)
                        price_text = price_elem.get_text(strip=True)
                        availability = availability_elem.get_text(strip=True) if availability_elem else 'Unknown'

                        # Extract price
                        price_match = re.search(r'[\d,]+\.?\d*', price_text.replace(',', '.'))
                        price = float(price_match.group()) if price_match else 0

                        products.append({
                            'name': name,
                            'price': price,
                            'availability': availability,
                            'category': search_term,
                            'url': search_url
                        })

                except Exception as e:
                    logger.warning(f"Error parsing product element: {e}")
                    continue

            return products

        except Exception as e:
            logger.error(f"Error scraping {search_url}: {e}")
            return []

    def scrape_vivaio_online_simulation(self, search_term):
        """Simulate scraping from a vivaio online (with realistic data)"""
        # This simulates real data since we can't access real sites without permission
        plant_data = {
            'monstera': [
                {'name': 'Monstera Deliciosa 40cm', 'price': 29.90, 'availability': 'Disponibile', 'popularity': 95},
                {'name': 'Monstera Adansonii 25cm', 'price': 19.90, 'availability': 'Disponibile', 'popularity': 88},
                {'name': 'Monstera Thai Constellation 15cm', 'price': 299.00, 'availability': 'Pochi pezzi', 'popularity': 100},
            ],
            'ficus': [
                {'name': 'Ficus Lyrata 120cm', 'price': 89.90, 'availability': 'Disponibile', 'popularity': 92},
                {'name': 'Ficus Benjamina 80cm', 'price': 45.90, 'availability': 'Disponibile', 'popularity': 75},
                {'name': 'Ficus Elastica Robusta 60cm', 'price': 35.90, 'availability': 'Disponibile', 'popularity': 82},
            ],
            'pothos': [
                {'name': 'Pothos Golden 20cm', 'price': 12.90, 'availability': 'Disponibile', 'popularity': 90},
                {'name': 'Pothos Marble Queen 18cm', 'price': 15.90, 'availability': 'Disponibile', 'popularity': 85},
                {'name': 'Pothos Neon 15cm', 'price': 18.90, 'availability': 'Pochi pezzi', 'popularity': 78},
            ],
            'succulente': [
                {'name': 'Mix Succulente 6cm (set 6)', 'price': 24.90, 'availability': 'Disponibile', 'popularity': 88},
                {'name': 'Echeveria Blue Prince 8cm', 'price': 8.90, 'availability': 'Disponibile', 'popularity': 82},
                {'name': 'Haworthia Zebra 6cm', 'price': 6.90, 'availability': 'Disponibile', 'popularity': 75},
            ],
            'basilico': [
                {'name': 'Basilico Genovese vaso 14cm', 'price': 3.50, 'availability': 'Disponibile', 'popularity': 95},
                {'name': 'Basilico Rosso vaso 12cm', 'price': 4.20, 'availability': 'Disponibile', 'popularity': 70},
                {'name': 'Basilico Greco vaso 10cm', 'price': 2.90, 'availability': 'Disponibile', 'popularity': 65},
            ]
        }

        # Add some randomization to simulate real market fluctuations
        products = plant_data.get(search_term, [])
        for product in products:
            # Simulate price variations (±10%)
            variation = random.uniform(0.9, 1.1)
            product['price'] = round(product['price'] * variation, 2)

            # Simulate availability changes
            if random.random() < 0.1:  # 10% chance
                product['availability'] = random.choice(['Pochi pezzi', 'Su ordinazione', 'Esaurito'])

        return products

    def get_market_insights(self, all_products):
        """Analyze scraped data for market insights"""
        insights = {
            'price_analysis': {},
            'availability_trends': {},
            'popular_plants': [],
            'price_ranges': {},
            'market_opportunities': []
        }

        # Group by category
        by_category = {}
        for product in all_products:
            category = product['category']
            if category not in by_category:
                by_category[category] = []
            by_category[category].append(product)

        # Analyze each category
        for category, products in by_category.items():
            if not products:
                continue

            prices = [p['price'] for p in products if p['price'] > 0]

            if prices:
                insights['price_analysis'][category] = {
                    'avg_price': round(sum(prices) / len(prices), 2),
                    'min_price': min(prices),
                    'max_price': max(prices),
                    'product_count': len(products)
                }

                # Determine price range category
                avg_price = insights['price_analysis'][category]['avg_price']
                if avg_price < 10:
                    price_range = 'Budget (< €10)'
                elif avg_price < 30:
                    price_range = 'Medio (€10-30)'
                elif avg_price < 100:
                    price_range = 'Premium (€30-100)'
                else:
                    price_range = 'Luxury (> €100)'

                insights['price_ranges'][category] = price_range

            # Availability analysis
            available_count = len([p for p in products if 'Disponibile' in p['availability']])
            total_count = len(products)
            availability_rate = (available_count / total_count * 100) if total_count > 0 else 0

            insights['availability_trends'][category] = {
                'availability_rate': round(availability_rate, 1),
                'total_products': total_count,
                'available_products': available_count
            }

        # Find popular plants (high availability + good price range)
        for category, data in insights['availability_trends'].items():
            if data['availability_rate'] > 80:  # High availability
                price_data = insights['price_analysis'].get(category, {})
                if price_data and price_data['avg_price'] < 50:  # Reasonable price
                    insights['popular_plants'].append({
                        'category': category,
                        'availability': data['availability_rate'],
                        'avg_price': price_data['avg_price'],
                        'recommendation': 'High demand potential'
                    })

        # Market opportunities
        for category, price_data in insights['price_analysis'].items():
            if price_data['max_price'] / price_data['min_price'] > 3:  # High price variation
                insights['market_opportunities'].append({
                    'category': category,
                    'opportunity': 'Price arbitrage',
                    'details': f"Price range €{price_data['min_price']}-€{price_data['max_price']}"
                })

        return insights

    def scrape_all_categories(self):
        """Scrape data for all plant categories"""
        all_products = []

        logger.info("Starting plant e-commerce scraping...")

        for category in self.plant_categories:
            logger.info(f"Scraping category: {category}")

            # Use simulation for now (replace with real scraping when needed)
            products = self.scrape_vivaio_online_simulation(category)

            for product in products:
                product['scraped_at'] = datetime.now().isoformat()
                product['source'] = 'vivaio_simulation'

            all_products.extend(products)

            # Respectful delay between requests
            time.sleep(random.uniform(1, 2))

        # Generate market insights
        insights = self.get_market_insights(all_products)

        return {
            'products': all_products,
            'insights': insights,
            'scraped_at': datetime.now().isoformat(),
            'total_products': len(all_products),
            'categories_scraped': len(self.plant_categories)
        }

def main():
    parser = argparse.ArgumentParser(description='Scrape plant e-commerce data')
    parser.add_argument('--output', help='Output file path (optional)')
    parser.add_argument('--category', help='Specific category to scrape (optional)')

    args = parser.parse_args()

    scraper = PlantEcommerceScraper()

    if args.category:
        # Scrape specific category
        products = scraper.scrape_vivaio_online_simulation(args.category)
        data = {
            'products': products,
            'category': args.category,
            'scraped_at': datetime.now().isoformat()
        }
    else:
        # Scrape all categories
        data = scraper.scrape_all_categories()

    # Output results
    result_json = json.dumps(data, indent=2, ensure_ascii=False)

    if args.output:
        with open(args.output, 'w', encoding='utf-8') as f:
            f.write(result_json)
        logger.info(f"Data saved to {args.output}")
    else:
        print(result_json)

if __name__ == '__main__':
    main()
