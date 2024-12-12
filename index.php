
<?php
// Require autoload if using a library
require 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Ensure no previous output
ob_clean();

// Get the ID from the URL parameter
$id = isset($_GET['id']) ? $_GET['id'] : '';

// กรองเฉพาะตัวเลขและตัวอักษรภาษาอังกฤษ
$id = preg_replace('/[^a-zA-Z0-9]/', '', $id);

// Set headers for JPEG image
header('Content-Type: image/jpeg');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if (!empty($id)) {
    // Create temporary file to store QR code
    $tempFile = tempnam(sys_get_temp_dir(), 'qr_');

    // QR Code options
    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG, // QRCode library may not have direct JPEG output, so generate as PNG first
        'scale' => 5,
        'imageWidth' => 500,
        'imageHeight' => 500,
    ]);

    // Generate QR code
    $qrCode = new QRCode($options);
    $qrCode->render($id, $tempFile);

    // Create image resource from PNG file
    $image = imagecreatefrompng($tempFile);

    // Output the image as JPEG
    imagejpeg($image, null, 50); // 90 is the JPEG quality (0-100)

    // Clean up
    imagedestroy($image);
    unlink($tempFile);
} else {
    // Create a blank white image
    $image = imagecreatetruecolor(500, 500);
    $white = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $white);
    
    // Output the blank image as JPEG
    imagejpeg($image, null, 50);
    
    // Clean up
    imagedestroy($image);
}

// Stop further script execution
exit();
