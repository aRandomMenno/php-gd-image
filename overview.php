<?php

session_start();

require_once "config.php";

$query = "SELECT * FROM `uploads` ORDER BY `id` DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$images = $stmt->fetchAll();
$imagesAmount = count($images);

include_once "./parts/head.php";
include_once "./parts/header.php";
include_once "./parts/messages.php";
include_once "./parts/overview.php";
include_once "./parts/footer.php";
