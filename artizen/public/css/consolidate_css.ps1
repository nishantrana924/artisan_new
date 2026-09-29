$d = 'd:\busniess\artizen new\css'
$stylePath = "$d\style.css"

# Read current style.css - trim everything after line 1199 (before the duplicate reels section starts)
$lines = Get-Content -Path $stylePath
$cutLine = 1199
$base = ($lines[0..($cutLine-1)] -join "`r`n") + "`r`n"

# Function to apply variable replacements on CSS content
function Translate-CSS {
    param([string]$content)

    # Gradients
    $content = $content -replace 'linear-gradient\(135deg,\s*#D4AF37\s*0%,\s*#F3E5AB\s*50%,\s*#B8860B\s*100%\)', 'var(--accent-gradient)'
    $content = $content -replace 'linear-gradient\(125deg,\s*#C9A227\s*0%,\s*#D4AF37\s*30%,\s*#F5D568\s*55%,\s*#C9A227\s*80%,\s*#D4AF37\s*100%\)', 'var(--accent-gradient)'
    $content = $content -replace 'linear-gradient\(135deg,\s*#C9A227,\s*#D4AF37,\s*#F5D568\)', 'var(--accent-gradient)'
    $content = $content -replace 'linear-gradient\(135deg,\s*#C9A227\s*0%,\s*#D4AF37\s*50%,\s*#F5D568\s*100%\)', 'var(--accent-gradient)'
    $content = $content -replace 'linear-gradient\(90deg,\s*transparent\s*0%,\s*#D4AF37\s*40%,\s*#F5D568\s*60%,\s*transparent\s*100%\)', 'linear-gradient(90deg, transparent 0%, var(--accent) 40%, var(--accent-hover) 60%, transparent 100%)'

    # Transparent rgba glows - glow level
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.04\)', 'var(--accent-glow)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.05\)', 'var(--accent-glow)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.06\)', 'var(--accent-glow)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.08\)', 'var(--accent-glow)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.1\b\)', 'var(--accent-glow)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.12\)', 'var(--accent-glow)'
    # glow-strong level
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.15\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.2\b\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.25\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.28\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.3\b\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.35\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.4\b\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.42\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.5\b\)', 'var(--accent-glow-strong)'
    $content = $content -replace 'rgba\(212,\s*175,\s*55,\s*0\.6\b\)', 'var(--accent-glow-strong)'

    # Avatar bg
    $content = $content -replace '#fcebd9', 'var(--accent-avatar-bg)'
    $content = $content -replace '#2a2015', 'var(--accent-avatar-bg)'

    # Gold hex -> var(--accent)  [must be after gradient replacements]
    $content = $content -replace '#D4AF37', 'var(--accent)'
    $content = $content -replace '#d4af37', 'var(--accent)'
    $content = $content -replace '#C9A227', 'var(--accent)'
    $content = $content -replace '#c9a227', 'var(--accent)'
    $content = $content -replace '#e2b007', 'var(--accent)'

    # Gold hover
    $content = $content -replace '#B8860B', 'var(--accent-hover)'
    $content = $content -replace '#b8860b', 'var(--accent-hover)'
    $content = $content -replace '#c49c2a', 'var(--accent-hover)'
    $content = $content -replace '#ffdf7a', 'var(--accent-hover)'

    return $content
}

# Merge component CSS files
$components = @('faq', 'reels', 'events', 'packages', 'details')
$merged = $base

foreach ($f in $components) {
    $path = "$d\$f.css"
    Write-Host "Merging $f.css ..."
    $raw = Get-Content -Path $path -Raw
    $raw = Translate-CSS $raw
    $section = "`r`n`r`n/* ============================================================`r`n   SECTION: $($f.ToUpper())`r`n   ============================================================ */`r`n`r`n"
    $merged += $section + $raw
}

# Write final file (UTF-8 no BOM)
[System.IO.File]::WriteAllText($stylePath, $merged, [System.Text.UTF8Encoding]::new($false))

Write-Host "=== CSS Consolidation Complete! style.css updated ==="
Write-Host "Line count: $($merged.Split("`n").Count)"
