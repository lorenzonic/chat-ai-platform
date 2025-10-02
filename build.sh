#!/bin/bash
# Build script per gestire il manifest Vite

echo "Building Vite assets..."
npx vite build --mode production

echo "Checking manifest location..."
if [ -f "public/build/.vite/manifest.json" ] && [ ! -f "public/build/manifest.json" ]; then
    echo "Copying manifest from .vite directory..."
    cp "public/build/.vite/manifest.json" "public/build/manifest.json"
    echo "Manifest copied successfully!"
elif [ -f "public/build/manifest.json" ]; then
    echo "Manifest already in correct location!"
else
    echo "Warning: No manifest found!"
fi

echo "Build completed!"
