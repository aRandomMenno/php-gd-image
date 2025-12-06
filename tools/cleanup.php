<?php

function cleanUpAfterError($newImageFile, $imageHash, $conn): void {
  $uploadsFolder = __DIR__ . "/../uploads/";
  $thumbnailsFolder = __DIR__ . "/../thumbnails/";
  $query = "SELECT COUNT(*) AS `amount` FROM `uploads` WHERE `image` = :image";
  $stmt = $conn->prepare($query);
  $stmt->bindValue(":image", $newImageFile);
  $stmt->execute();
  $result = $stmt->fetch();
  if ($result['amount'] == 0) {
    if (file_exists($uploadsFolder . $newImageFile)) {
      unlink($uploadsFolder . $newImageFile);
    }
    if (file_exists($thumbnailsFolder . $imageHash . ".avif")) {
      unlink($thumbnailsFolder . $imageHash . ".avif");
    }
  }
}