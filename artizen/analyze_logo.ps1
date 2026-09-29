Add-Type -AssemblyName System.Drawing

function Analyze-Logo($imagePath) {
    Write-Output "========================================"
    Write-Output "ANALYZING: $imagePath"
    Write-Output "========================================"
    if (-not (Test-Path $imagePath)) {
        Write-Output "Not found: $imagePath"
        return
    }

    $bmp = [System.Drawing.Bitmap]::FromFile($imagePath)
    Write-Output "Dimensions: $($bmp.Width) x $($bmp.Height)"

    $colorMap = @{}
    $stepX = [Math]::Max(1, [int]($bmp.Width / 150))
    $stepY = [Math]::Max(1, [int]($bmp.Height / 150))

    for ($x = 0; $x -lt $bmp.Width; $x += $stepX) {
        for ($y = 0; $y -lt $bmp.Height; $y += $stepY) {
            $pixel = $bmp.GetPixel($x, $y)
            if ($pixel.A -gt 100) {
                # Skip pure white
                if ($pixel.R -gt 245 -and $pixel.G -gt 245 -and $pixel.B -gt 245) {
                    continue
                }
                $hex = "#{0:X2}{1:X2}{2:X2}" -f $pixel.R, $pixel.G, $pixel.B
                if ($colorMap.ContainsKey($hex)) {
                    $colorMap[$hex]++
                } else {
                    $colorMap[$hex] = 1
                }
            }
        }
    }
    $bmp.Dispose()

    $sorted = $colorMap.GetEnumerator() | Sort-Object -Property Value -Descending | Select-Object -First 30
    foreach ($item in $sorted) {
        $hex = $item.Key
        $count = $item.Value
        Write-Output "$hex -> count: $count"
    }
}

Analyze-Logo "d:\busniess\Artizen_business\artizen new\artizen\public\assets\images\logo\artizen.png"
Analyze-Logo "d:\busniess\Artizen_business\artizen new\artizen\public\assets\images\logo\Artizen_logo.png"
Analyze-Logo "d:\busniess\Artizen_business\artizen new\artizen\public\assets\images\logo\artizen1.png"
Analyze-Logo "d:\busniess\Artizen_business\artizen new\artizen\public\assets\images\logo\artizen3.png"
