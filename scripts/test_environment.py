#!/usr/bin/env python3
"""
Test script per verificare che l'ambiente Python funzioni su Railway
"""
import sys
import json
import os

def test_environment():
    results = {
        "python_version": f"{sys.version_info.major}.{sys.version_info.minor}.{sys.version_info.micro}",
        "platform": sys.platform,
        "tests": {}
    }

    # Test 1: Import base
    try:
        import json
        import os
        import sys
        results["tests"]["basic_imports"] = "✅ OK"
    except Exception as e:
        results["tests"]["basic_imports"] = f"❌ ERROR: {e}"

    # Test 2: spaCy
    try:
        import spacy
        results["tests"]["spacy_import"] = "✅ OK"

        # Test modello italiano
        try:
            nlp = spacy.load("it_core_news_sm")
            doc = nlp("Ciao, questa è una prova.")
            results["tests"]["spacy_italian_model"] = f"✅ OK - {len(doc)} tokens"
        except OSError:
            results["tests"]["spacy_italian_model"] = "❌ Modello it_core_news_sm non trovato"
        except Exception as e:
            results["tests"]["spacy_italian_model"] = f"❌ ERROR: {str(e)}"

    except ImportError as e:
        results["tests"]["spacy_import"] = f"❌ spaCy non installato: {str(e)}"

    # Test 3: Altri moduli essenziali
    modules_to_test = ["requests", "pandas", "numpy", "python_dotenv"]

    for module in modules_to_test:
        try:
            __import__(module)
            results["tests"][f"{module}_import"] = "✅ OK"
        except ImportError:
            results["tests"][f"{module}_import"] = f"❌ {module} non installato"

    # Test 4: Environment variables
    env_vars = ["HOME", "PATH"]
    if os.name == 'nt':  # Windows
        env_vars.extend(["USERPROFILE", "TEMP"])

    results["environment"] = {}
    for var in env_vars:
        results["environment"][var] = os.environ.get(var, "Non impostato")[:50] + "..."

    return results

if __name__ == "__main__":
    try:
        results = test_environment()
        print(json.dumps(results, indent=2, ensure_ascii=False))
    except Exception as e:
        print(json.dumps({"error": str(e)}, ensure_ascii=False))
