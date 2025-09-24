#!/usr/bin/env python3
import sys
import json

print(json.dumps({
    "python_version": sys.version,
    "platform": sys.platform,
    "executable": sys.executable
}, indent=2))
