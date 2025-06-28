#!/usr/bin/env python3
"""
Plant E-commerce Real Scraper Test
Test and validate access to real Italian plant retailers
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import random
from datetime import datetime
import re
from urllib.parse import urljoin, urlparse, quote
import logging
import sys
import os

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class PlantSiteValidator:
    def __init__(self):
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language': 'it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding': 'gzip, deflate, br',
            'Connection': 'keep-alive',
            'Upgrade-Insecure-Requests': '1',
        })

        # Real Italian plant retailers with updated selectors
        self.target_sites = {
            'viridea': {
                'name': 'Viridea',
                'base_url': 'https://www.viridea.it',
                'test_search': 'monstera',
                'search_path': '/catalogsearch/result/?q={}',
            },
            'bakker': {
                'name': 'Bakker Italia',
                'base_url': 'https://www.bakker.com',
                'test_search': 'piante da interno',
                'search_path': '/it/search/?q={}',
            },
            'mygreenhelp': {
                'name': 'MyGreenHelp',
                'base_url': 'https://www.mygreenhelp.com',
                'test_search': 'ficus',
                'search_path': '/search?type=product&q={}',
            },
            'cipì': {
                'name': 'Cipì Vivai',
                'base_url': 'https://shop.vivai-cipi.it',
                'test_search': 'basilico',
                'search_path': '/search?q={}',
            }
        }

    def test_site_access(self, site_key):
        """Test if a site is accessible and responsive"""
        site = self.target_sites[site_key]

        try:
            logger.info(f"Testing access to {site['name']}: {site['base_url']}")

            # Test homepage first
            response = self.session.get(site['base_url'], timeout=10)
            response.raise_for_status()

            logger.info(f"✓ {site['name']} homepage accessible (status: {response.status_code})")

            # Test search page
            search_url = site['base_url'] + site['search_path'].format(quote(site['test_search']))

            time.sleep(random.uniform(1, 3))  # Respectful delay

            search_response = self.session.get(search_url, timeout=10)
            search_response.raise_for_status()

            logger.info(f"✓ {site['name']} search page accessible")

            # Parse and analyze content
            soup = BeautifulSoup(search_response.content, 'html.parser')

            # Look for common product elements
            potential_products = []

            # Common selectors to try
            selectors_to_try = [
                '.product', '.product-item', '.product-card', '.product-container',
                '.item', '.result', '.listing-item', '.grid-item',
                '[data-product]', '.search-result'
            ]

            for selector in selectors_to_try:
                elements = soup.select(selector)
                if elements:
                    potential_products.append({
                        'selector': selector,
                        'count': len(elements),
                        'sample_text': elements[0].get_text(strip=True)[:100] if elements else ''
                    })

            logger.info(f"Found potential product containers: {len(potential_products)}")

            return {
                'accessible': True,
                'homepage_status': response.status_code,
                'search_status': search_response.status_code,
                'potential_selectors': potential_products,
                'page_title': soup.title.string if soup.title else 'N/A',
                'content_length': len(search_response.content)
            }

        except requests.RequestException as e:
            logger.error(f"✗ Request failed for {site['name']}: {e}")
            return {'accessible': False, 'error': str(e)}
        except Exception as e:
            logger.error(f"✗ Unexpected error for {site['name']}: {e}")
            return {'accessible': False, 'error': str(e)}

    def scrape_sample_products(self, site_key, max_products=5):
        """Try to scrape a few sample products to test selectors"""
        site = self.target_sites[site_key]

        try:
            search_url = site['base_url'] + site['search_path'].format(quote(site['test_search']))
            logger.info(f"Scraping sample products from {site['name']}")

            time.sleep(random.uniform(2, 4))  # Respectful delay

            response = self.session.get(search_url, timeout=15)
            response.raise_for_status()

            soup = BeautifulSoup(response.content, 'html.parser')

            products = []

            # Try different common e-commerce selectors
            selectors_to_try = [
                {
                    'container': '.product-item',
                    'name': '.product-title, .product-name, h3, h4',
                    'price': '.price, .cost, .amount, .euro'
                },
                {
                    'container': '.product',
                    'name': '.title, .name, h3, h4, a',
                    'price': '.price, .cost, .amount, .euro'
                },
                {
                    'container': '.item',
                    'name': '.title, .name, h3, h4, a',
                    'price': '.price, .cost, .amount, .euro'
                }
            ]

            for selector_set in selectors_to_try:
                containers = soup.select(selector_set['container'])

                if containers:
                    logger.info(f"Found {len(containers)} containers with selector '{selector_set['container']}'")

                    for i, container in enumerate(containers[:max_products]):
                        try:
                            # Extract name
                            name_elem = container.select_one(selector_set['name'])
                            name = name_elem.get_text(strip=True) if name_elem else f"Product {i+1}"

                            # Extract price
                            price_elem = container.select_one(selector_set['price'])
                            price_text = price_elem.get_text(strip=True) if price_elem else "N/A"
                            price = self.extract_price(price_text)

                            # Get link if available
                            link_elem = container.select_one('a[href]')
                            product_url = ''
                            if link_elem and link_elem.get('href'):
                                href = link_elem.get('href')
                                if href.startswith('http'):
                                    product_url = href
                                else:
                                    product_url = urljoin(site['base_url'], href)

                            if name and name != f"Product {i+1}":  # Only add if we got a real name
                                products.append({
                                    'name': name,
                                    'price': price,
                                    'price_text': price_text,
                                    'url': product_url,
                                    'selector_used': selector_set['container'],
                                    'source': site['name']
                                })

                        except Exception as e:
                            logger.warning(f"Error parsing product {i}: {e}")
                            continue

                    if products:  # If we found products with this selector, stop trying others
                        break

            logger.info(f"Successfully extracted {len(products)} sample products from {site['name']}")
            return products

        except Exception as e:
            logger.error(f"Error scraping sample products from {site['name']}: {e}")
            return []

    def extract_price(self, price_text):
        """Extract numeric price from text"""
        if not price_text:
            return 0.0

        # Remove common currency symbols and text
        cleaned = re.sub(r'[^\d,.]', '', price_text)

        # Handle European number format (comma as decimal separator)
        if ',' in cleaned and '.' in cleaned:
            # Format like 1.234,56
            cleaned = cleaned.replace('.', '').replace(',', '.')
        elif ',' in cleaned:
            # Format like 12,34
            cleaned = cleaned.replace(',', '.')

        try:
            return float(cleaned)
        except ValueError:
            return 0.0

    def run_validation(self):
        """Run validation for all sites"""
        results = {}

        logger.info("Starting validation of Italian plant e-commerce sites...")

        for site_key in self.target_sites.keys():
            logger.info(f"\n{'='*50}")
            logger.info(f"Validating {self.target_sites[site_key]['name']}")
            logger.info(f"{'='*50}")

            # Test site access
            access_result = self.test_site_access(site_key)
            results[site_key] = access_result

            # If accessible, try to scrape sample products
            if access_result.get('accessible', False):
                sample_products = self.scrape_sample_products(site_key)
                results[site_key]['sample_products'] = sample_products
                results[site_key]['products_found'] = len(sample_products)

            time.sleep(random.uniform(3, 6))  # Respectful delay between sites

        return results

    def save_results(self, results, output_file=None):
        """Save validation results to JSON file"""
        if not output_file:
            output_file = os.path.join(os.path.dirname(__file__), '..', 'storage', 'app', 'temp', 'site_validation.json')

        os.makedirs(os.path.dirname(output_file), exist_ok=True)

        output_data = {
            'validation_date': datetime.now().isoformat(),
            'sites_tested': len(results),
            'accessible_sites': len([r for r in results.values() if r.get('accessible', False)]),
            'results': results
        }

        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(output_data, f, indent=2, ensure_ascii=False)

        logger.info(f"Results saved to: {output_file}")
        return output_file

def main():
    validator = PlantSiteValidator()

    try:
        results = validator.run_validation()

        # Save results
        output_file = validator.save_results(results)

        # Print summary
        print(f"\n{'='*60}")
        print("VALIDATION SUMMARY")
        print(f"{'='*60}")

        accessible_count = 0
        total_products = 0

        for site_key, result in results.items():
            site_name = validator.target_sites[site_key]['name']
            accessible = result.get('accessible', False)
            products_count = result.get('products_found', 0)

            status = "✓ ACCESSIBLE" if accessible else "✗ NOT ACCESSIBLE"
            print(f"{site_name:20} {status:15} Products: {products_count}")

            if accessible:
                accessible_count += 1
                total_products += products_count

        print(f"\nAccessible sites: {accessible_count}/{len(results)}")
        print(f"Total sample products found: {total_products}")
        print(f"Results saved to: {output_file}")

        return accessible_count > 0

    except KeyboardInterrupt:
        print("\nValidation interrupted by user")
        return False
    except Exception as e:
        logger.error(f"Validation failed: {e}")
        return False

if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
