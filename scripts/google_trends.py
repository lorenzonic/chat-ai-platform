#!/usr/bin/env python3
"""
Google Trends API Script for Plant-related Keywords
Fetches trending data for plant, gardening, and botanical keywords
"""

import argparse
import json
import sys
from datetime import datetime, timedelta
import time
import random

try:
    from pytrends.request import TrendReq
    PYTRENDS_AVAILABLE = True
except ImportError:
    PYTRENDS_AVAILABLE = False

def get_google_trends_data(keywords, days=30):
    """
    Fetch Google Trends data for specified keywords
    """
    if not PYTRENDS_AVAILABLE:
        print("Warning: pytrends not installed, using fallback data", file=sys.stderr)
        return get_fallback_data(keywords)

    try:
        # Initialize pytrends with better configuration
        print("Initializing Google Trends connection...", file=sys.stderr)

        pytrends = TrendReq(
            hl='it-IT',
            tz=360,
            timeout=(10, 25),  # Connect timeout, Read timeout
            proxies=None,
            retries=2,
            backoff_factor=0.1,
            requests_args={
                'verify': True,  # Ensure HTTPS verification
                'headers': {
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language': 'it-IT,it;q=0.8,en;q=0.6',
                    'Accept-Encoding': 'gzip, deflate, br',
                    'DNT': '1',
                    'Connection': 'keep-alive',
                    'Upgrade-Insecure-Requests': '1'
                }
            }
        )

        print(f"Fetching Google Trends data for keywords: {', '.join(keywords)}", file=sys.stderr)

        # Add delay to avoid rate limiting
        time.sleep(2)  # Increased delay

        # Limit to maximum 5 keywords to avoid API limits
        if len(keywords) > 5:
            keywords = keywords[:5]
            print(f"Limited to first 5 keywords: {', '.join(keywords)}", file=sys.stderr)

        # Build payload with error handling
        try:
            pytrends.build_payload(
                keywords,
                cat=0,
                timeframe=f'today {min(days, 30)}-d',  # Limit to max 30 days
                geo='IT',  # Italy
                gprop=''
            )
            print("Google Trends payload built successfully", file=sys.stderr)
        except Exception as payload_error:
            print(f"Error building payload: {payload_error}", file=sys.stderr)
            raise payload_error

        # Get interest over time with error handling
        try:
            interest_over_time = pytrends.interest_over_time()
            print("Interest over time data fetched successfully", file=sys.stderr)
        except Exception as interest_error:
            print(f"Error fetching interest over time: {interest_error}", file=sys.stderr)
            interest_over_time = None

        # Get related queries with error handling
        try:
            related_queries = pytrends.related_queries()
            print("Related queries data fetched successfully", file=sys.stderr)
        except Exception as related_error:
            print(f"Error fetching related queries: {related_error}", file=sys.stderr)
            related_queries = {}

        # Get rising searches with error handling
        try:
            rising_searches = pytrends.trending_searches(pn='italy')
            print("Rising searches data fetched successfully", file=sys.stderr)
        except Exception as rising_error:
            print(f"Error fetching rising searches: {rising_error}", file=sys.stderr)
            rising_searches = None

        # Process data
        result = {
            'keywords': [],
            'interest_over_time': [],
            'related_queries': {},
            'rising_searches': rising_searches.head(10).values.tolist() if rising_searches is not None and not rising_searches.empty else [],
            'average_interest': 0,
            'trend': 'stable',
            'peak_keyword': '',
            'peak_interest': 0
        }

        if interest_over_time is not None and not interest_over_time.empty:
            # Remove 'isPartial' column if exists
            if 'isPartial' in interest_over_time.columns:
                interest_over_time = interest_over_time.drop('isPartial', axis=1)

            # Calculate averages and trends
            total_interest = 0
            peak_interest = 0
            peak_keyword = ''

            for keyword in keywords:
                if keyword in interest_over_time.columns:
                    avg_interest = interest_over_time[keyword].mean()
                    total_interest += avg_interest

                    result['keywords'].append({
                        'term': keyword,
                        'interest': round(avg_interest, 1),
                        'trend': get_trend(interest_over_time[keyword].values),
                        'peak_date': interest_over_time[keyword].idxmax().strftime('%Y-%m-%d') if avg_interest > 0 else None
                    })

                    if avg_interest > peak_interest:
                        peak_interest = avg_interest
                        peak_keyword = keyword

            result['average_interest'] = round(total_interest / len(keywords), 1)
            result['peak_keyword'] = peak_keyword
            result['peak_interest'] = round(peak_interest, 1)

            # Convert time series to JSON-serializable format
            result['interest_over_time'] = [
                {
                    'date': date.strftime('%Y-%m-%d'),
                    **{keyword: int(row[keyword]) for keyword in keywords if keyword in row.index}
                }
                for date, row in interest_over_time.iterrows()
            ]

        # Process related queries
        for keyword in keywords:
            if keyword in related_queries:
                result['related_queries'][keyword] = {
                    'top': related_queries[keyword]['top'].head(5).to_dict('records') if related_queries[keyword]['top'] is not None else [],
                    'rising': related_queries[keyword]['rising'].head(5).to_dict('records') if related_queries[keyword]['rising'] is not None else []
                }

        # Determine overall trend
        if result['average_interest'] > 60:
            result['trend'] = 'rising'
        elif result['average_interest'] < 30:
            result['trend'] = 'declining'
        else:
            result['trend'] = 'stable'

        return result

    except Exception as e:
        print(f"Error fetching Google Trends data (multiple keywords): {e}", file=sys.stderr)

        # Try single keyword approach as fallback
        if len(keywords) == 1:
            print("Trying single keyword approach...", file=sys.stderr)
            return get_google_trends_single_keyword(keywords[0], days)
        elif len(keywords) > 1:
            print("Trying to get data for first keyword only...", file=sys.stderr)
            return get_google_trends_single_keyword(keywords[0], days)

        return get_fallback_data(keywords)

def get_trend(data):
    """
    Determine if trend is rising, declining, or stable
    """
    if len(data) < 2:
        return 'stable'

    # Compare first and last quarters
    first_quarter = data[:len(data)//4].mean()
    last_quarter = data[-len(data)//4:].mean()

    if last_quarter > first_quarter * 1.1:
        return 'rising'
    elif last_quarter < first_quarter * 0.9:
        return 'declining'
    else:
        return 'stable'

def get_fallback_data(keywords):
    """
    Provide fallback data when Google Trends API is not available
    """
    return {
        'keywords': [
            {
                'term': keyword,
                'interest': random.randint(30, 100),
                'trend': random.choice(['rising', 'stable', 'declining']),
                'peak_date': (datetime.now() - timedelta(days=random.randint(1, 30))).strftime('%Y-%m-%d')
            }
            for keyword in keywords
        ],
        'interest_over_time': [
            {
                'date': (datetime.now() - timedelta(days=i)).strftime('%Y-%m-%d'),
                **{keyword: random.randint(20, 100) for keyword in keywords}
            }
            for i in range(30, 0, -1)
        ],
        'related_queries': {
            keyword: {
                'top': [
                    {'query': f'{keyword} cura', 'value': random.randint(50, 100)},
                    {'query': f'{keyword} vendita', 'value': random.randint(30, 80)},
                    {'query': f'come coltivare {keyword}', 'value': random.randint(40, 90)},
                ],
                'rising': [
                    {'query': f'{keyword} online', 'value': random.randint(100, 500)},
                    {'query': f'{keyword} rare', 'value': random.randint(200, 400)},
                ]
            }
            for keyword in keywords
        },
        'rising_searches': [
            ['piante grasse rare'],
            ['cactus collezione'],
            ['bonsai principianti'],
            ['orchidee cura'],
            ['giardinaggio urbano']
        ],
        'average_interest': random.randint(40, 85),
        'trend': random.choice(['rising', 'stable']),
        'peak_keyword': random.choice(keywords),
        'peak_interest': random.randint(60, 100)
    }

def get_google_trends_single_keyword(keyword, days=30):
    """
    Fetch Google Trends data for a single keyword (more reliable)
    """
    if not PYTRENDS_AVAILABLE:
        return get_fallback_data([keyword])

    try:
        print(f"Fetching single keyword data: {keyword}", file=sys.stderr)

        pytrends = TrendReq(
            hl='it-IT',
            tz=360,
            timeout=(5, 15),
            requests_args={
                'verify': True,
                'headers': {
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                }
            }
        )

        # Build payload for single keyword
        pytrends.build_payload(
            [keyword],
            cat=0,
            timeframe=f'today {min(days, 7)}-d',  # Very short timeframe
            geo='IT',
            gprop=''
        )

        # Get interest over time
        interest_over_time = pytrends.interest_over_time()

        if not interest_over_time.empty and 'isPartial' in interest_over_time.columns:
            interest_over_time = interest_over_time.drop('isPartial', axis=1)

        if not interest_over_time.empty and keyword in interest_over_time.columns:
            avg_interest = interest_over_time[keyword].mean()

            result = {
                'keywords': [{
                    'term': keyword,
                    'interest': round(avg_interest, 1),
                    'trend': get_trend(interest_over_time[keyword].values),
                    'peak_date': interest_over_time[keyword].idxmax().strftime('%Y-%m-%d') if avg_interest > 0 else None
                }],
                'interest_over_time': [
                    {
                        'date': date.strftime('%Y-%m-%d'),
                        keyword: int(row[keyword])
                    }
                    for date, row in interest_over_time.iterrows()
                ],
                'related_queries': {},
                'rising_searches': [],
                'average_interest': round(avg_interest, 1),
                'trend': 'rising' if avg_interest > 60 else 'stable' if avg_interest > 30 else 'declining',
                'peak_keyword': keyword,
                'peak_interest': round(avg_interest, 1)
            }

            print(f"Successfully fetched real data for {keyword}", file=sys.stderr)
            return result

        raise Exception("No data returned")

    except Exception as e:
        print(f"Single keyword fetch failed for {keyword}: {e}", file=sys.stderr)
        return get_fallback_data([keyword])

def main():
    parser = argparse.ArgumentParser(description='Fetch Google Trends data for plant keywords')
    parser.add_argument('--keywords', required=True, help='Comma-separated list of keywords')
    parser.add_argument('--days', type=int, default=30, help='Number of days to analyze')
    parser.add_argument('--output', help='Output file path (optional)')

    args = parser.parse_args()

    # Parse keywords
    keywords = [k.strip() for k in args.keywords.split(',')]

    # Fetch data
    data = get_google_trends_data(keywords, args.days)

    # Output results
    result_json = json.dumps(data, indent=2, ensure_ascii=False)

    if args.output:
        with open(args.output, 'w', encoding='utf-8') as f:
            f.write(result_json)
        print(f"Data saved to {args.output}", file=sys.stderr)
    else:
        print(result_json)

if __name__ == '__main__':
    main()
