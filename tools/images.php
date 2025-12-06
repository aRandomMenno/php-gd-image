<?php

// Function to detect if the webp is animated (copied from the internet)
function isWebpAnimated($fn): bool {
  $result = false;
  $fh = fopen($fn, "rb");
  fseek($fh, 12);
  if (fread($fh, 4) === 'VP8X') {
    fseek($fh, 16);
    $myByte = fread($fh, 1);
    $result = ((ord($myByte) >> 1) & 1) ? true : false;
  }
  fclose($fh);
  return $result;
}

function createThumbnail($source, $destination, $desiredWidth): void {
  $type = exif_imagetype($source);
  switch ($type) {
    case IMAGETYPE_JPEG:
      $img = imagecreatefromjpeg($source);
      break;
    case IMAGETYPE_PNG:
      $img = imagecreatefrompng($source);
      break;
    case IMAGETYPE_WEBP:
      $img = imagecreatefromwebp($source);
      break;
    case IMAGETYPE_GIF:
      $img = imagecreatefromgif($source);
      break;
    case IMAGETYPE_AVIF:
      $img = imagecreatefromavif($source);
      break;
    default:
      throw new Exception("Unsupported image type for thumbnail creation. (should've already been caught earlier)");
  }
  $width = imagesx($img);
  $height = imagesy($img);
  $desiredHeight = floor($height * ($desiredWidth / $width));
  $virtual_image = imagecreatetruecolor($desiredWidth, $desiredHeight);
  imagecopyresampled($virtual_image, $img, 0, 0, 0, 0, $desiredWidth, $desiredHeight, $width, $height);

  if (!imageavif($virtual_image, $destination, 40, 2)) {
    throw new Exception("Failed to save thumbnail as AVIF.");
  }
  imagedestroy($img);
  imagedestroy($virtual_image);
}