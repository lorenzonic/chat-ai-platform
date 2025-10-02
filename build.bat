@echo off
REM Build script per gestire il manifest Vite su Windows

echo Building Vite assets...
call npx vite build --mode production

echo Checking manifest location...
if exist "public\build\.vite\manifest.json" if not exist "public\build\manifest.json" (
    echo Copying manifest from .vite directory...
    copy "public\build\.vite\manifest.json" "public\build\manifest.json" >nul
    echo Manifest copied successfully!
) else if exist "public\build\manifest.json" (
    echo Manifest already in correct location!
) else (
    echo Warning: No manifest found!
)

echo Build completed!
