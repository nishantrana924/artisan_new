<?php

function analyzeImage($path) {
    echo "=== Analyzing: " . basename($path) . " ===\n";
    if (!file_exists($path)) {
        echo "File does not exist\n";
        return;
    }
    
    // Check with GD
    if (!function_exists('imagecreatefrompng')) {
        echo "GD not enabled, trying Imagick or binary scan\n";
        return;
    }

    $im = @imagecreatefrompng($path);
    if (!$im) {
        echo "Failed to load PNG with GD\n";
        return;
    }

    $w = imagesx($im);
    $h = imagesy($im);
    echo "Dimensions: {$w}x{$h}\n";

    $colors = [];
    $stepX = max(1, (int)($w / 120));
    $stepY = max(1, (int)($h / 120));

    for ($x = 0; $x < $w; $x += $stepX) {
        for ($y = 0; $y < $h; $y += $stepY) {
            $rgba = imagecolorat($im, $x, $y);
            $alpha = ($rgba & 0x7F000000) >> 24;
            if ($alpha > 120) continue; // mostly transparent

            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;

            // Skip pure/near white backgrounds
            if ($r > 245 && $g > 245 && $b > 245) continue;

            $hex = sprintf("#%02X%02X%02X", $r, $g, $b);
            $colors[$hex] = ($colors[$hex] ?? 0) + 1;
        }
    }
    imagedestroy($im);

    arsort($colors);
    $top = array_slice($colors, 0, 30, true);
    foreach ($top as $hex => $count) {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        // Categorize
        $cat = 'Other';
        if ($r < 40 && $g < 40 && $b < 40) $cat = 'Black/Dark';
        else if ($r > 200 && $g > 70 && $b < 80) $cat = 'ORANGE';
        else if ($r > 180 && $g > 140 && $b < 100) $cat = 'Gold/Yellow';

        echo "$hex (RGB: $r, $g, $b) [Count: $count] -> $cat\n";
    }
}

analyzeImage(__DIR__ . '/public/assets/images/logo/artizen.png');
analyzeImage(__DIR__ . '/public/assets/images/logo/Artizen_logo.png');
analyzeImage(__DIR__ . '/public/assets/images/logo/artizen1.png');
analyzeImage(__DIR__ . '/public/assets/images/logo/artizen3.png');
