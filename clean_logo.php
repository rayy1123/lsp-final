<?php
if (!extension_loaded("gd")) {
    echo "NO_GD\n";
    exit(1);
}

$src = __DIR__ . "/public/images/logo.png";
$img = imagecreatefrompng($src);
if (!$img) {
    echo "FAILED_LOAD\n";
    exit(1);
}

$w = imagesx($img);
$h = imagesy($img);

$out = imagecreatetruecolor($w, $h);
imagealphablending($out, false);
imagesavealpha($out, true);

$transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
imagefilledrectangle($out, 0, 0, $w, $h, $transparent);

for ($x = 0; $x < $w; $x++) {
    for ($y = 0; $y < $h; $y++) {
        $rgba = imagecolorat($img, $x, $y);
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;

        $maxVal = max($r, max($g, $b));
        if ($maxVal < 32) {
            imagesetpixel($out, $x, $y, $transparent);
        } else {
            // Smooth edge: if between 32 and 60, scale alpha
            if ($maxVal < 60) {
                $alpha = (int)(127 * (1 - ($maxVal - 32) / 28));
                $color = imagecolorallocatealpha($out, $r, $g, $b, $alpha);
            } else {
                $color = imagecolorallocatealpha($out, $r, $g, $b, 0);
            }
            imagesetpixel($out, $x, $y, $color);
        }
    }
}

copy($src, __DIR__ . "/public/images/logo-orig-backup.png");
imagepng($out, $src);
imagedestroy($img);
imagedestroy($out);

echo "DONE\n";
