@echo off
echo =====================================================
echo Fixing XAMPP SSL Certificate Issue
echo =====================================================
echo.

echo Step 1: Downloading latest CA certificate bundle...
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://curl.se/ca/cacert.pem' -OutFile 'C:\xampp\apache\bin\curl-ca-bundle.crt'}"

if %errorlevel% neq 0 (
    echo ERROR: Failed to download certificate bundle
    echo Trying alternative method...
    powershell -Command "& {(New-Object System.Net.WebClient).DownloadFile('https://curl.se/ca/cacert.pem', 'C:\xampp\apache\bin\curl-ca-bundle.crt')}"
)

echo.
echo Step 2: Updating php.ini configuration...

echo Backing up php.ini...
copy C:\xampp\php\php.ini C:\xampp\php\php.ini.backup

powershell -Command "& {$content = Get-Content 'C:\xampp\php\php.ini'; $content = $content -replace ';curl.cainfo =', 'curl.cainfo = \"C:\xampp\apache\bin\curl-ca-bundle.crt\"'; $content = $content -replace ';openssl.cafile=', 'openssl.cafile=\"C:\xampp\apache\bin\curl-ca-bundle.crt\"'; Set-Content 'C:\xampp\php\php.ini' $content}"

echo.
echo =====================================================
echo SSL Certificate Fix Complete!
echo =====================================================
echo.
echo What was done:
echo 1. Downloaded latest CA certificate bundle
echo 2. Updated php.ini with certificate path
echo 3. Created backup of php.ini (php.ini.backup)
echo.
echo Next steps:
echo 1. Restart Apache in XAMPP Control Panel
echo 2. Your Stripe integration should now work!
echo.
echo No need to install Composer - the system now uses cURL directly.
echo Just add your Stripe API keys in config/stripe_config.php
echo.
pause
