$f = Join-Path $PSScriptRoot 'style.css'
$lines = Get-Content $f
$clean = $lines[0..5398]
[System.IO.File]::WriteAllLines($f, $clean, [System.Text.UTF8Encoding]::new($false))
Write-Host "Done. Lines: $($clean.Count)"
