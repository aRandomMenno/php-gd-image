  <div class="container mt-3">
    <fieldset class="border p-3 rounded bg-light">
      <legend class="w-auto px-2">Image uploader</legend>
      <form method="post" action="upload.php" enctype="multipart/form-data">
        <div class="mb-2">
          <label for="name" class="form-label">What's your name:</label>
          <input type="text" class="form-control" name="name" id="name">
        </div>
        <div class="mb-2">
          <label for="title" class="form-label">What's the title:</label>
          <input type="text" class="form-control" name="title" id="title">
        </div>
        <div class="mb-2">
          <label for="file" class="form-label">Upload a picture: (8MB max)</label>
          <input type="file" class="form-control" name="file" id="file"
            accept="image/gif, image/jpeg, image/jpg, image/png, image/webp, image/avif">
        </div>
        <button type="submit" class="btn btn-success" aria-label="verstuur">verstuur</button>
      </form>
    </fieldset>
  </div>