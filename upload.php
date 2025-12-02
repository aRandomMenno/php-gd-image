<?php

require_once "tools/goto.php";
require_once "tools/webp.php";

session_start();

$uploadsFolder = __DIR__ . "/.uploads/";
if (!is_dir($uploadsFolder))
  mkdir($uploadsFolder, 0755, true);
$uploadedFile = $_FILES["file"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  goToPageWithMessage("index.php", "That's not a POST request!", "error");
}

if (empty($_POST) && empty($_FILES)) {
  goToPageWithMessage("index.php", "The picture you uploaded is too big!", "error");
} else if ($uploadedFile["size"] > 8 * 1024 * 1024) {
  goToPageWithMessage("index.php", "The picture you uploaded is too big!", "error");
}

$uploadTitle = $_POST["title"];
if (!isset($uploadTitle) || empty($uploadTitle)) {
  goToPageWithMessage("index.php", "You didn't give a title.", "warning");
} else if (strlen($uploadTitle) > 32) {
  goToPageWithMessage("index.php", "The title can not be longer than 32 characters.", "warning");
}

$uploadName = $_POST["name"];
if (!isset($uploadName) || empty($uploadName)) {
  goToPageWithMessage("index.php", "You didn't give a name.", "warning");
} else if (strlen($uploadName) > 24) {
  goToPageWithMessage("index.php", "Your name can not be longer than 24 characters.", "warning");
}

if ($uploadedFile["error"] !== 0) {
  switch ($uploadedFile["error"]) {
    case 3:
      goToPageWithMessage("index.php", "The file was partially uploaded, try again.", "warning");
    case 4:
      goToPageWithMessage("index.php", "You must upload an image.", "warning");
    case 6:
    case 7:
    case 8:
      goToPageWithMessage("index.php", "An internal server error has occurred, try again later.", "error");
      die("Internal server error");
    default:
      goToPageWithMessage("index.php", "An unknown error has occurred, please try again (later).", "error");
  }
}

$imageHash = hash_file("sha3-224", $uploadedFile["tmp_name"]);
$imageName = str_replace(" ", "_", $uploadedFile["name"]);
$imageFilename = basename($imageHash . "-" . $imageName);
if (strtolower(str_ends_with($imageName, ".jpg"))) $imageFilename = preg_replace('/.jpg$/', '.avif', $imageFilename);
if (strtolower(str_ends_with($imageName, ".jpeg"))) $imageFilename = preg_replace('/.jpeg$/', '.avif', $imageFilename);
if (strtolower(str_ends_with($imageName, ".png"))) $imageFilename = preg_replace('/.png$/', '.avif', $imageFilename);

if (!file_exists($uploadsFolder . $imageFilename)) {
  // Bekijken het type foto.
  $imageType = exif_imagetype($uploadedFile["tmp_name"]);
  $gdImage = false;
  // Zorg dat het een GDImage object is. (tenzij het geanimeerd is)
  switch ($imageType) {
    case IMAGETYPE_JPEG:
      $gdImage = imagecreatefromjpeg($uploadedFile["tmp_name"]);
      break;
    case IMAGETYPE_PNG:
      $gdImage = imagecreatefrompng($uploadedFile["tmp_name"]);
      break;
    case IMAGETYPE_GIF:
      // Php heeft geen animation support voor images.
      $gdImage = "animated";
      break;
    case IMAGETYPE_WEBP:
      // Php heeft geen animation support voor images.
      if (!isWebpAnimated($uploadedFile["tmp_name"])) {
        if (strtolower(str_ends_with($imageName, ".webp"))) $imageFilename = preg_replace('/.webp$/', '.avif', $imageFilename);
        $gdImage = imagecreatefromwebp($uploadedFile["tmp_name"]);
      }
      $gdImage = "animated";
      break;
    case IMAGETYPE_AVIF:
      $gdImage = "avif";
      break;
    default:
      // Deze type foto is niet ondersteund.
      goToPageWithMessage("index.php", "Unsupported image type. Please upload a JPEG, PNG, GIF, AVIF, or WebP image.", "error");
  }
  if ($gdImage === "animated" || $gdImage === "avif") {
    // Gif's en geanimeerde webp's worden niet omgezet. Of als het al een avif image is.
    move_uploaded_file($uploadedFile["tmp_name"], $uploadsFolder . $imageFilename);
  }
  else if ($gdImage === false) {
    goToPageWithMessage("index.php", "Failed to process the uploaded image.", "error");
  }
  if (!imageavif($gdImage, $uploadsFolder . $imageFilename, 40, 4)) {
    // Er is wat fout gegaan bij het maken van de avif foto.
    goToPageWithMessage("index.php", "Failed to save image as AVIF.", "error");
  }
  // Verwijder het GDImage object.
  imagedestroy(image: $gdImage);
}

try {
  require "config.php";
  $query = "INSERT INTO `uploads` (`uploader`, `title`, `image`) VALUES (:uploader, :title, :image)";
  $stmt = $conn->prepare($query);
  $stmt->bindValue(":uploader", $uploadName);
  $stmt->bindValue(":title", $uploadTitle);
  $stmt->bindValue(":image", $imageFilename);
  $stmt->execute();
} catch (PDOException $error) {
  goToPageWithMessage("index.php", "An error occurred trying to add image to the DB. " . $error->getMessage(), "error");
}

// Als het goed is, is alles goed gegaan als we hier zijn.
goToPageWithMessage("index.php", "Added the image to the Database.");