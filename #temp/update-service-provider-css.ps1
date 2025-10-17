# Update Service Provider CSS Files Script
# This script copies trainer CSS as base and updates colors for each service provider

$baseAvailabilityCss = "c:\xampp\htdocs\petvet\public\css\trainer\availability.css"
$baseSettingsCss = "c:\xampp\htdocs\petvet\public\css\sitter\settings.css"

# Service provider color mappings
$providers = @{
    "sitter" = @{
        "name" = "Sitter"
        "primary" = "#17a2b8"
        "primary_dark" = "#138496"
    }
    "trainer" = @{
        "name" = "Trainer"
        "primary" = "#8b5cf6"
        "primary_dark" = "#7c3aed"
    }
    "breeder" = @{
        "name" = "Breeder"
        "primary" = "#f59e0b"
        "primary_dark" = "#d97706"
    }
    "groomer" = @{
        "name" = "Groomer"
        "primary" = "#14b8a6"
        "primary_dark" = "#0d9488"
    }
}

# Update availability CSS for each provider
foreach ($provider in $providers.Keys) {
    $config = $providers[$provider]
    $availabilityFile = "c:\xampp\htdocs\petvet\public\css\$provider\availability.css"
    
    # Read base availability CSS
    $content = Get-Content $baseAvailabilityCss -Raw
    
    # Update header comment
    $content = $content -replace "/\* Trainer Availability Management Styles \*/", "/* $($config.name) Availability Management Styles */"
    
    # Update CSS color variables to match provider
    # Keep variable names as --sitter-primary for consistency (they all use this)
    $content = $content -replace "--sitter-primary: #8b5cf6;", "--sitter-primary: $($config.primary);"
    $content = $content -replace "--sitter-primary-dark: #7c3aed;", "--sitter-primary-dark: $($config.primary_dark);"
    
    # Write to provider file
    Set-Content -Path $availabilityFile -Value $content -Force
    Write-Host "Updated $availabilityFile" -ForegroundColor Green
}

# Update settings CSS for each provider
foreach ($provider in $providers.Keys) {
    $config = $providers[$provider]
    $settingsFile = "c:\xampp\htdocs\petvet\public\css\$provider\settings.css"
    
    # Read base settings CSS
    $content = Get-Content $baseSettingsCss -Raw
    
    # Update primary color
    $content = $content -replace "--primary:#2563eb;", "--primary:$($config.primary);"
    $content = $content -replace "--primary-600:#1d4ed8;", "--primary-600:$($config.primary_dark);"
    
    # Update ring color based on primary
    switch ($provider) {
        "sitter" { $ring = "rgba(23, 162, 184, 0.15)" }
        "trainer" { $ring = "rgba(139, 92, 246, 0.15)" }
        "breeder" { $ring = "rgba(245, 158, 11, 0.15)" }
        "groomer" { $ring = "rgba(20, 184, 166, 0.15)" }
    }
    $content = $content -replace "--ring:#dbeafe;", "--ring:$ring;"
    
    # Write to provider file
    Set-Content -Path $settingsFile -Value $content -Force
    Write-Host "Updated $settingsFile" -ForegroundColor Green
}

Write-Host "`nAll CSS files updated successfully!" -ForegroundColor Cyan
