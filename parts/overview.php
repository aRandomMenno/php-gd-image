  <div class="container py-5">
    <h1 class="mb-4">Image Gallery</h1>

    <?php if ($imagesAmount > 0): ?>
      <div class="cards-grid">
        <?php foreach ($images as $image):
          $fileParts = explode(".", $image["image"]);
          if (file_exists(".thumbnails/" . $fileParts[0] . ".avif")) {
            $thumbnail = ".thumbnails/" . $fileParts[0] . ".avif";
          } else {
            $thumbnail = ".uploads/" . $image["image"];
          }
          ?>
            <div class="card mb-3">
              <img src="<?= htmlspecialchars($thumbnail) ?>" class="card-img-top"
                alt="<?= htmlspecialchars($image['title']); ?>">
              <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($image['title']); ?></h3>
                <p class="card-text text-muted">Uploaded by: <?= htmlspecialchars($image['uploader']); ?></p>
              </div>
            </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info" role="alert">
        No images have been uploaded yet! <a href="index.php" class="alert-link">Be the first one</a>!
      </div>
    <?php endif; ?>
  </div>