<?php

declare(strict_types=1);

function roundedRectangle($image, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

function makeIcon(string $path, int $size, bool $maskable = false): void
{
    $image = imagecreatetruecolor($size, $size);
    imagesavealpha($image, true);
    imagealphablending($image, true);

    $blue = imagecolorallocate($image, 21, 91, 215);
    $white = imagecolorallocate($image, 255, 255, 255);
    $softWhite = imagecolorallocatealpha($image, 255, 255, 255, 45);
    $scale = $size / 512;

    roundedRectangle($image, 0, 0, $size, $size, $maskable ? 0 : (int) ($size * 0.19), $blue);

    imagesetthickness($image, max(8, (int) (34 * $scale)));
    imageline($image, (int) (116 * $scale), (int) (126 * $scale), (int) (396 * $scale), (int) (126 * $scale), $softWhite);

    foreach ([[122, 266, 66, 118], [223, 154, 66, 230], [324, 224, 66, 160]] as [$x, $y, $width, $height]) {
        roundedRectangle(
            $image,
            (int) ($x * $scale),
            (int) ($y * $scale),
            (int) (($x + $width) * $scale),
            (int) (($y + $height) * $scale),
            max(4, (int) (14 * $scale)),
            $white
        );
    }

    imagepng($image, $path);
}

$directory = __DIR__.'/../public/pwa';

if (! is_dir($directory)) {
    mkdir($directory, 0777, true);
}

$badFile = $directory.'/System.Drawing.Drawing2D.GraphicsPath';
if (is_file($badFile)) {
    unlink($badFile);
}

makeIcon($directory.'/icon-192.png', 192);
makeIcon($directory.'/icon-512.png', 512);
makeIcon($directory.'/icon-maskable-512.png', 512, true);
