<?php
// Carica l'immagine originale (PNG)
$sourceImage = @imagecreatefrompng('public/images/dbai_logo.jpg');
if (!$sourceImage) {
    die('Impossibile caricare l\'immagine sorgente. Assicurati che il file esista e sia un\'immagine valida.');
}

// Crea un'immagine quadrata 32x32
$favicon = imagecreatetruecolor(32, 32);

// Rendi lo sfondo trasparente
$transparent = imagecolorallocatealpha($favicon, 0, 0, 0, 127);
imagefill($favicon, 0, 0, $transparent);
imagesavealpha($favicon, true);

// Ridimensiona mantenendo le proporzioni
$sourceWidth = imagesx($sourceImage);
$sourceHeight = imagesy($sourceImage);
$minSize = min($sourceWidth, $sourceHeight);
$sourceX = ($sourceWidth - $minSize) / 2;
$sourceY = ($sourceHeight - $minSize) / 2;

imagecopyresampled(
    $favicon,          // Immagine di destinazione
    $sourceImage,      // Immagine sorgente
    0, 0,              // Punto di destinazione (x, y)
    $sourceX,          // Punto sorgente (x)
    $sourceY,          // Punto sorgente (y)
    32, 32,            // Dimensione di destinazione (larghezza, altezza)
    $minSize,          // Larghezza sorgente
    $minSize           // Altezza sorgente
);

// Salva come favicon.ico
if (imagepng($favicon, 'public/favicon.png')) {
    // Converti in .ico usando ImageMagick se disponibile
    if (extension_loaded('imagick')) {
        $imagick = new Imagick();
        $imagick->readImage('public/favicon.png');
        $imagick->setImageFormat('ico');
        $imagick->writeImage('public/favicon.ico');
        unlink('public/favicon.png'); // Rimuovi il file png temporaneo
        echo "Favicon.ico generato con successo!\n";
    } else {
        // Se Imagick non è disponibile, usa il png come favicon
        rename('public/favicon.png', 'public/favicon.ico');
        echo "Favicon.ico generato come PNG (senza trasparenza).\n";
        echo "Per una migliore qualità, installa l'estensione Imagick.\n";
    }
} else {
    echo "Errore nel salvataggio del favicon.\n";
}

// Libera la memoria
imagedestroy($sourceImage);
if (isset($favicon)) imagedestroy($favicon);
