# Add Cover Photo CSS to all service provider settings
$providers = @("sitter", "trainer", "breeder")

$coverPhotoCss = @"
/* Cover Photo Section */
.cover-photo-section{margin-bottom:24px}
.cover-photo-preview{width:100%; height:220px; border-radius:14px; overflow:hidden; position:relative; background:var(--bg-soft); border:2px dashed var(--border)}
.cover-photo-preview img{width:100%; height:100%; object-fit:cover; display:block}
.cover-photo-overlay{position:absolute; inset:0; background:rgba(15,23,42,0.4); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease}
.cover-photo-preview:hover .cover-photo-overlay{opacity:1}
.cover-photo-overlay .btn{backdrop-filter:blur(8px); background:rgba(255,255,255,0.95); border:1px solid rgba(255,255,255,0.8); box-shadow:0 4px 12px rgba(0,0,0,0.15)}
.profile-left{display:flex; flex-direction:column; align-items:center}
.profile-right{flex:1}

"@

foreach ($provider in $providers) {
    $file = "c:\xampp\htdocs\petvet\public\css\$provider\settings.css"
    $content = Get-Content $file -Raw
    
    # Add cover photo CSS before Role Management Styles
    if ($content -notmatch "cover-photo-section") {
        $content = $content -replace "(/\* Role Management Styles \*/)", "$coverPhotoCss`$1"
        
        # Add mobile responsive CSS for cover photo
        $content = $content -replace "(@media \(max-width:600px\)\{)", "@media (max-width:900px){`n  .cover-photo-preview{height:180px}`n}`n`$1"
        
        # Add cover photo mobile styles in 600px breakpoint
        $content = $content -replace "(\.image-preview-list\.avatar\.big \.image-preview-item\{width:160px; height:160px\})", "`$1`n  .cover-photo-preview{height:140px; border-radius:10px}`n  .cover-photo-overlay .btn{font-size:13px; padding:8px 14px}"
        
        # Add cover photo mobile styles in 380px breakpoint
        $content = $content -replace "(@media \(max-width:380px\)\{[^}]+\.image-preview-list\.avatar\.big \.image-preview-item\{width:140px; height:140px\})", "`$1`n  .cover-photo-preview{height:120px}`n  .cover-photo-overlay .btn{font-size:12px; padding:7px 12px}"
        
        Set-Content -Path $file -Value $content -Force
        Write-Host "Updated $file" -ForegroundColor Green
    } else {
        Write-Host "$file already has cover photo styles" -ForegroundColor Yellow
    }
}

Write-Host "`nAll settings.css files updated!" -ForegroundColor Cyan
