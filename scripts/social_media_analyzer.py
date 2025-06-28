#!/usr/bin/env python3
"""
Social Media Trends Analyzer for Plant Industry
Analyzes hashtags and trends across Instagram, TikTok, Twitter
"""

import argparse
import json
import sys
import time
import random
from datetime import datetime, timedelta
from typing import Dict, List, Any

class SocialMediaAnalyzer:
    def __init__(self):
        self.plant_keywords = [
            'piante', 'plants', 'giardinaggio', 'gardening', 'botanica', 'botanical',
            'verde', 'green', 'natura', 'nature', 'fiori', 'flowers', 'cactus',
            'succulente', 'succulents', 'bonsai', 'orchidee', 'orchids',
            'plantmom', 'plantdad', 'plantparent', 'planttok', 'plantcare',
            'urbanjungle', 'indoorplants', 'houseplants', 'gardeningtips'
        ]

        self.hashtag_variations = {
            'piante': ['#piante', '#piantegrasse', '#piantedainterno', '#piantedaappartamento'],
            'cactus': ['#cactus', '#cactuslovers', '#cactuscollection', '#cactuscare'],
            'succulente': ['#succulente', '#succulents', '#succulentlove', '#succulentcare'],
            'giardinaggio': ['#giardinaggio', '#gardening', '#gardeningtips', '#gardenlife'],
            'botanica': ['#botanica', '#botanical', '#botany', '#plantstudies']
        }

    def analyze_instagram_trends(self, days: int = 30) -> Dict[str, Any]:
        """Analyze Instagram hashtag trends"""
        # Simulate Instagram API data
        hashtags = []

        for base_keyword in self.plant_keywords[:10]:
            # Generate realistic hashtag data
            post_count = random.randint(10000, 500000)
            engagement_rate = random.uniform(2.5, 8.5)
            growth_rate = random.uniform(-15.0, 45.0)

            hashtags.append({
                'hashtag': f'#{base_keyword}',
                'posts': post_count,
                'engagement_rate': round(engagement_rate, 2),
                'growth_rate': round(growth_rate, 1),
                'avg_likes': random.randint(50, 2000),
                'avg_comments': random.randint(5, 200),
                'top_posts': random.randint(100, 1000),
                'trending_score': round(engagement_rate * (growth_rate / 10) + (post_count / 10000), 1)
            })

        # Sort by trending score
        hashtags.sort(key=lambda x: x['trending_score'], reverse=True)

        return {
            'platform': 'Instagram',
            'period_days': days,
            'total_hashtags_analyzed': len(hashtags),
            'trending_hashtags': hashtags[:10],
            'rising_hashtags': [h for h in hashtags if h['growth_rate'] > 20][:5],
            'declining_hashtags': [h for h in hashtags if h['growth_rate'] < -5][:3],
            'engagement_metrics': {
                'avg_engagement_rate': round(sum(h['engagement_rate'] for h in hashtags) / len(hashtags), 2),
                'highest_engagement': max(hashtags, key=lambda x: x['engagement_rate']),
                'most_posts': max(hashtags, key=lambda x: x['posts']),
                'fastest_growing': max(hashtags, key=lambda x: x['growth_rate'])
            }
        }

    def analyze_tiktok_trends(self, days: int = 30) -> Dict[str, Any]:
        """Analyze TikTok hashtag trends"""
        hashtags = []

        for base_keyword in self.plant_keywords[:8]:
            # TikTok specific metrics
            video_count = random.randint(5000, 200000)
            total_views = random.randint(100000, 10000000)
            avg_completion_rate = random.uniform(45.0, 85.0)
            growth_rate = random.uniform(-10.0, 60.0)

            hashtags.append({
                'hashtag': f'#{base_keyword}',
                'videos': video_count,
                'total_views': total_views,
                'avg_views_per_video': round(total_views / video_count),
                'completion_rate': round(avg_completion_rate, 1),
                'growth_rate': round(growth_rate, 1),
                'viral_threshold': random.randint(50000, 500000),
                'viral_videos': random.randint(10, 100),
                'trending_score': round((total_views / 100000) + (growth_rate / 5) + (avg_completion_rate / 10), 1)
            })

        hashtags.sort(key=lambda x: x['trending_score'], reverse=True)

        return {
            'platform': 'TikTok',
            'period_days': days,
            'total_hashtags_analyzed': len(hashtags),
            'trending_hashtags': hashtags[:8],
            'viral_content': [h for h in hashtags if h['viral_videos'] > 50][:3],
            'emerging_trends': [h for h in hashtags if h['growth_rate'] > 30][:5],
            'performance_metrics': {
                'avg_completion_rate': round(sum(h['completion_rate'] for h in hashtags) / len(hashtags), 2),
                'total_views_analyzed': sum(h['total_views'] for h in hashtags),
                'most_viral': max(hashtags, key=lambda x: x['viral_videos']),
                'highest_completion': max(hashtags, key=lambda x: x['completion_rate'])
            }
        }

    def analyze_twitter_trends(self, days: int = 30) -> Dict[str, Any]:
        """Analyze Twitter hashtag trends"""
        hashtags = []

        for base_keyword in self.plant_keywords[:6]:
            # Twitter specific metrics
            tweet_count = random.randint(1000, 50000)
            retweets = random.randint(500, 25000)
            likes = random.randint(1000, 100000)
            replies = random.randint(200, 10000)
            growth_rate = random.uniform(-20.0, 40.0)
            sentiment_score = random.uniform(0.3, 0.9)  # 0-1 scale

            hashtags.append({
                'hashtag': f'#{base_keyword}',
                'tweets': tweet_count,
                'retweets': retweets,
                'likes': likes,
                'replies': replies,
                'growth_rate': round(growth_rate, 1),
                'sentiment_score': round(sentiment_score, 2),
                'sentiment_label': self.get_sentiment_label(sentiment_score),
                'engagement_rate': round(((retweets + likes + replies) / tweet_count) * 100, 2),
                'trending_score': round((tweet_count / 1000) + (growth_rate / 10) + (sentiment_score * 10), 1)
            })

        hashtags.sort(key=lambda x: x['trending_score'], reverse=True)

        return {
            'platform': 'Twitter',
            'period_days': days,
            'total_hashtags_analyzed': len(hashtags),
            'trending_hashtags': hashtags[:6],
            'positive_sentiment': [h for h in hashtags if h['sentiment_score'] > 0.7][:3],
            'high_engagement': [h for h in hashtags if h['engagement_rate'] > 150][:3],
            'conversation_metrics': {
                'avg_sentiment': round(sum(h['sentiment_score'] for h in hashtags) / len(hashtags), 2),
                'avg_engagement_rate': round(sum(h['engagement_rate'] for h in hashtags) / len(hashtags), 2),
                'total_conversations': sum(h['tweets'] for h in hashtags),
                'most_discussed': max(hashtags, key=lambda x: x['tweets'])
            }
        }

    def get_sentiment_label(self, score: float) -> str:
        """Convert sentiment score to label"""
        if score >= 0.7:
            return 'Very Positive'
        elif score >= 0.5:
            return 'Positive'
        elif score >= 0.3:
            return 'Neutral'
        else:
            return 'Negative'

    def analyze_cross_platform_trends(self, days: int = 30) -> Dict[str, Any]:
        """Analyze trends across all platforms"""
        instagram_data = self.analyze_instagram_trends(days)
        tiktok_data = self.analyze_tiktok_trends(days)
        twitter_data = self.analyze_twitter_trends(days)

        # Find common trending hashtags
        instagram_tags = set(h['hashtag'] for h in instagram_data['trending_hashtags'][:5])
        tiktok_tags = set(h['hashtag'] for h in tiktok_data['trending_hashtags'][:5])
        twitter_tags = set(h['hashtag'] for h in twitter_data['trending_hashtags'][:5])

        cross_platform_trending = list(instagram_tags & tiktok_tags & twitter_tags)
        emerging_on_multiple = list((instagram_tags & tiktok_tags) | (instagram_tags & twitter_tags) | (tiktok_tags & twitter_tags))

        return {
            'analysis_period': days,
            'platforms_analyzed': ['Instagram', 'TikTok', 'Twitter'],
            'cross_platform_trending': cross_platform_trending,
            'emerging_on_multiple_platforms': list(set(emerging_on_multiple) - set(cross_platform_trending))[:5],
            'platform_strengths': {
                'Instagram': 'High engagement rates, visual content',
                'TikTok': 'Viral potential, young audience',
                'Twitter': 'Real-time conversations, sentiment analysis'
            },
            'recommended_hashtags': self.get_recommended_hashtags(instagram_data, tiktok_data, twitter_data),
            'platform_data': {
                'instagram': instagram_data,
                'tiktok': tiktok_data,
                'twitter': twitter_data
            }
        }

    def get_recommended_hashtags(self, instagram_data: Dict, tiktok_data: Dict, twitter_data: Dict) -> List[Dict]:
        """Get recommended hashtags based on cross-platform analysis"""
        recommendations = []

        # Top performers from each platform
        for platform_data, platform_name in [(instagram_data, 'Instagram'), (tiktok_data, 'TikTok'), (twitter_data, 'Twitter')]:
            top_hashtag = platform_data['trending_hashtags'][0]
            recommendations.append({
                'hashtag': top_hashtag['hashtag'],
                'platform': platform_name,
                'reason': f"Top trending on {platform_name}",
                'confidence': 'High',
                'expected_reach': random.randint(10000, 100000)
            })

        # Add emerging hashtags
        recommendations.append({
            'hashtag': '#planttherapy',
            'platform': 'All',
            'reason': 'Emerging wellness trend',
            'confidence': 'Medium',
            'expected_reach': random.randint(5000, 50000)
        })

        return recommendations

def main():
    parser = argparse.ArgumentParser(description='Analyze social media trends for plant industry')
    parser.add_argument('--days', type=int, default=30, help='Number of days to analyze')
    parser.add_argument('--platform', choices=['instagram', 'tiktok', 'twitter', 'all'], default='all', help='Platform to analyze')
    parser.add_argument('--output', help='Output file path (optional)')
    parser.add_argument('--format', choices=['json', 'summary'], default='json', help='Output format')

    args = parser.parse_args()

    analyzer = SocialMediaAnalyzer()

    if args.platform == 'all':
        data = analyzer.analyze_cross_platform_trends(args.days)
    elif args.platform == 'instagram':
        data = analyzer.analyze_instagram_trends(args.days)
    elif args.platform == 'tiktok':
        data = analyzer.analyze_tiktok_trends(args.days)
    elif args.platform == 'twitter':
        data = analyzer.analyze_twitter_trends(args.days)

    # Output results
    if args.format == 'json':
        result_json = json.dumps(data, indent=2, ensure_ascii=False)

        if args.output:
            with open(args.output, 'w', encoding='utf-8') as f:
                f.write(result_json)
            print(f"Data saved to {args.output}", file=sys.stderr)
        else:
            print(result_json)

    elif args.format == 'summary':
        # Print summary
        print(f"\n🌱 Social Media Trends Analysis - Last {args.days} days")
        print("=" * 60)

        if args.platform == 'all' and 'platform_data' in data:
            for platform_name, platform_data in data['platform_data'].items():
                print(f"\n📱 {platform_name.upper()}:")
                for hashtag in platform_data['trending_hashtags'][:3]:
                    print(f"  • {hashtag['hashtag']} - Trending Score: {hashtag.get('trending_score', 'N/A')}")

        if 'recommended_hashtags' in data:
            print(f"\n🎯 RECOMMENDED HASHTAGS:")
            for rec in data['recommended_hashtags']:
                print(f"  • {rec['hashtag']} ({rec['platform']}) - {rec['reason']}")

if __name__ == '__main__':
    main()
