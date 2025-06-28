#!/usr/bin/env python3
"""
Setup script for Python trends analytics
This script installs required Python packages and sets up the environment
"""

import subprocess
import sys
import os

def install_requirements():
    """Install Python requirements"""
    print("Installing Python requirements...")

    try:
        # Install requirements
        subprocess.check_call([
            sys.executable, "-m", "pip", "install", "-r", "requirements.txt"
        ])
        print("✅ Requirements installed successfully!")

    except subprocess.CalledProcessError as e:
        print(f"❌ Error installing requirements: {e}")
        return False

    return True

def test_imports():
    """Test if required packages can be imported"""
    print("Testing package imports...")

    required_packages = [
        'pytrends',
        'pandas',
        'numpy',
        'requests',
        'dateutil',
    ]

    for package in required_packages:
        try:
            __import__(package)
            print(f"✅ {package} imported successfully")
        except ImportError as e:
            print(f"❌ Failed to import {package}: {e}")
            return False

    return True

def create_cache_directory():
    """Create cache directory for trends data"""
    cache_dir = os.path.join(os.path.dirname(__file__), 'cache')

    if not os.path.exists(cache_dir):
        os.makedirs(cache_dir)
        print(f"✅ Created cache directory: {cache_dir}")
    else:
        print(f"✅ Cache directory already exists: {cache_dir}")

def main():
    """Main setup function"""
    print("🌱 Setting up Plant Trends Analytics Environment")
    print("=" * 50)

    # Check Python version
    if sys.version_info < (3, 7):
        print("❌ Python 3.7 or higher is required")
        sys.exit(1)

    print(f"✅ Python version: {sys.version}")

    # Install requirements
    if not install_requirements():
        sys.exit(1)

    # Test imports
    if not test_imports():
        print("❌ Some packages failed to import. Please check the installation.")
        sys.exit(1)

    # Create cache directory
    create_cache_directory()

    print("\n🎉 Setup completed successfully!")
    print("\nYou can now run the trends analytics:")
    print("  python scripts/google_trends.py --keywords 'piante,giardinaggio' --days 30")

if __name__ == "__main__":
    main()
