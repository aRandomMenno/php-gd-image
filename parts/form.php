<?php $scrf = bin2hex(random_bytes(48)); $_SESSION['csrf'] = $scrf; ?>
  <div class="container mt-3">
    <fieldset class="border p-3 rounded bg-light">
      <legend class="w-auto px-2">Image uploader</legend>
      <form method="post" action="upload.php" enctype="multipart/form-data">
        <div class="mb-2">
          <label for="name" class="form-label">What's your name:</label>
          <input type="text" class="form-control" name="name" id="name" required maxlength="24">
        </div>
        <div class="mb-2">
          <label for="title" class="form-label">What's the title:</label>
          <input type="text" class="form-control" name="title" id="title" required maxlength="32">
        </div>
        <div class="mb-2">
          <label for="file" class="form-label">Upload a picture: (8MB max)</label>
          <input type="file" class="form-control" name="file" id="file"
            accept="image/gif, image/jpeg, image/jpg, image/png, image/webp, image/avif" required>
        </div>
        <input type="text" name="csrf" id="csrf" value="<?= $_SESSION['csrf']; ?>" hidden>
        <button type="submit" class="btn btn-success" aria-label="send">send</button>
      </form>
    </fieldset>
  </div>