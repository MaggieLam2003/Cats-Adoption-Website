<?php

$show_confirmation = False;

// age, breed, color, and gender are categories of tags not the value themselves,
$form_values = array(
  'cat_name' => NULL,
  'cat_description' => NULL,
  'age' => NULL,
  'breed' => NULL,
  'color' => NULL,
  'gender' => NULL
);

$db = init_sqlite_db('db/site.sqlite', 'db/init.sql');

define("MAX_FILE_SIZE", 1000000);
$upload_feedback = array(
  'general_error' => False,
  'too_large' => False
);
$upload_source = NULL;
$upload_file_name = NULL;
$upload_file_ext = NULL;

if (isset($_POST['submit-cat'])) {
  $form_values['cat_name'] = ($_POST['cat_name'] == '' ? NULL : trim($_POST['cat_name'])); // untrusted
  $form_values['cat_description'] = ($_POST['cat_description'] == '' ? NULL : trim($_POST['cat_description'])); // untrusted
  $form_values['age'] = ($_POST['age'] == '' ? NULL : (int)$_POST['age']); // untrusted
  $form_values['breed'] = ($_POST['breed'] == '' ? NULL : (int)$_POST['breed']); // untrusted
  $form_values['color'] = ($_POST['color'] == '' ? NULL : (int)$_POST['color']); // untrusted
  $form_values['gender'] = ($_POST['gender'] == '' ? NULL : (int)$_POST['gender']); // untrusted

  $form_valid = True;

  $upload_source = trim($_POST['source']); // untrusted
  if (empty($upload_source)) {
    $upload_source = NULL;
  }
  $upload = $_FILES['jpeg-file']; //step 1

  if ($upload['error'] == UPLOAD_ERR_OK) { //step 2

    $upload_file_name = basename($upload['name']); //step 3
    $upload_file_ext = strtolower(pathinfo($upload_file_name, PATHINFO_EXTENSION));

    if (!in_array($upload_file_ext, array('jpeg'))) {
      $form_valid = False;
      $upload_feedback['general_error'] = True;
    }
  } else if (($upload['error'] == UPLOAD_ERR_INI_SIZE) || ($upload['error'] == UPLOAD_ERR_FORM_SIZE)) {
    $form_valid = False;
    $upload_feedback['too_large'] = True;
  } else {
    $form_valid = False;
    $upload_feedback['general_error'] = True;
  }

  if ($form_valid) {
  $show_confirmation = True;

    $result = exec_sql_query(
      $db,
      "INSERT INTO cats (cat_name, cat_description, file_name, file_ext, source) VALUES (:cat_name, :cat_description, :file_name, :file_ext, :source);",
      array(
        ':cat_name' => $form_values['cat_name'], // tainted
        ':cat_description' => $form_values['cat_description'], // tainted
        ':file_name' => $upload_file_name,
        ':file_ext' => $upload_file_ext,
        ':source' => $upload_source
      )
    );

    if ($result) {
      $cat_id = $db->lastInsertId();
      $upload_storage_path = 'public/uploads/cats/' . $cat_id . '.' . $upload_file_ext;
      if (move_uploaded_file($upload["tmp_name"], $upload_storage_path) == false) {
        error_log("Failed to permanently store the uploaded file on the file server. Please check that the server folder exists.");
      }
    }

    $tag_types = exec_sql_query($db, "SELECT DISTINCT tag_type FROM tags")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tag_types as $tag_type) {
      $tag_value = $_POST[$tag_type];

      $tag_id = exec_sql_query(
        $db,
        "SELECT id FROM tags WHERE tag_type = :tag_type AND tag_value = :tag_value;",
        array(
          ':tag_type' => $tag_type,
          ':tag_value' => $tag_value
        )
      )->fetchColumn();

      $result = exec_sql_query(
        $db,
        "INSERT INTO cats_tags (cat_id, tag_id) VALUES (:cat_id, :tag_id);",
        array(
          ':cat_id' => $cat_id,
          ':tag_id' => $tag_id
        )
      );
    }



  }
}
$age_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'age'")->fetchAll(PDO::FETCH_COLUMN);
$breed_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'breed'")->fetchAll(PDO::FETCH_COLUMN);
$color_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'color'")->fetchAll(PDO::FETCH_COLUMN);
$gender_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'gender'")->fetchAll(PDO::FETCH_COLUMN);

$sql_select_clause = "SELECT cats.file_name, cats.file_ext, cats.source, cats.id, cats.cat_name, cats.cat_description,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'age' THEN tags.tag_value ELSE NULL END) AS age,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'breed' THEN tags.tag_value ELSE NULL END) AS breed,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'color' THEN tags.tag_value ELSE NULL END) AS color,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'gender' THEN tags.tag_value ELSE NULL END) AS gender
FROM cats
JOIN cats_tags ON cats.id = cats_tags.cat_id
JOIN tags ON cats_tags.tag_id = tags.id
GROUP BY cats.id";

$result = exec_sql_query($db, $sql_select_clause);
$records = $result->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>FurryTail Endings @ Form</title>
  <link rel="stylesheet" type="text/css" href="/public/styles/site.css">
</head>

<body>

<?php include('includes/header.php'); ?>

<main class="file-uploads">
<section class="gallery">
  <?php
  if (count($records) > 0) { ?>
    <ul>
      <?php
      foreach ($records as $record) {
        $file_url = '/public/uploads/cats/' . $record['id'] . '.' . $record['file_ext'];
      ?>
        <li>
          <a class = "link" href="<?php echo htmlspecialchars($file_url) ?>" title="Download <?php echo htmlspecialchars($record['file_name']); ?>" download>
            <div class="thumbnail">
               <!-- Source: https://unsplash.com/s/photos/cute-cat -->
              <!-- Source: https://www.vecteezy.com/free-photos/cat -->
              <!-- Source: https://www.verywellmind.com/are-some-people-really-afraid-of-cats-2671757 -->
              <!-- Source: https://hakaimagazine.com/features/its-10-pm-do-you-know-where-your-cat-is/ -->
              <!-- Source: https://www.nature.com/articles/494009a -->
              <!-- Source: https://www.cats.org.uk/ -->

               <!--Source: <?php echo htmlspecialchars($record['source']); ?> -->
              <img src="<?php echo htmlspecialchars($file_url); ?>" alt="<?php echo htmlspecialchars($record['file_name']); ?>">
              <p><?php echo htmlspecialchars($record['file_name']); ?></p>
            </div>
            <span class="cite"><?php echo htmlspecialchars($record['source']); ?></span>
            <!-- CITATION HERE -->

            <div class="overlay">
              <img alt="" src="/public/images/download-icon.svg">
            </div>
          </a>
        </li>
      <?php
      } ?>
    </ul>
  <?php
  } else { ?>
    <p>Your cat image entries collection is empty. Try uploading some entries.</p>
  <?php } ?>
</section>

<div class="form">

    <h2>
        Employee CATalog
    </h2>

    <table class="bottom-margin">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Description</th>
        <th>Age</th>
        <th>Breed</th>
        <th>Color</th>
        <th>Gender</th>
      </tr>

      <?php
      // write a table row for each record
      foreach ($records as $record) { ?>
        <tr>
          <td><?php echo htmlspecialchars($record["id"]); ?></td>
          <td><?php echo htmlspecialchars($record["cat_name"]); ?></td>
          <td><?php echo htmlspecialchars($record["cat_description"]); ?></td>
          <td><?php echo htmlspecialchars($record["age"]); ?></td>
          <td><?php echo htmlspecialchars($record["breed"]); ?></td>
          <td><?php echo htmlspecialchars($record["color"]); ?></td>
          <td><?php echo htmlspecialchars($record["gender"]); ?></td>

        </tr>
      <?php } ?>
    </table>


    <?php if ($show_confirmation) { ?>
      <section class="confirmation">
        <h2>Cat added!</h2>
        <p>
          Just added <?php echo htmlspecialchars($form_values['cat_name']); ?> to the CATalog!
        </p>
      </section>
      <?php } ?>

    <form action="/form" method="post" enctype="multipart/form-data">

    <h2>Register New Cats</h2>

    <div class="label-input">
      <label for="name_field">Name:</label>
      <input id="name_field" type="text" name="cat_name" value="">
    </div>

    <div class="label-input">
      <label for="description_field">Description:</label>
      <input id="description_field" type="text" name="cat_description" value="">
    </div>

    <div class="label-input">
    <label for="age">Age:</label>
      <select id="age" name="age">
        <option value="">Select Age</option>
        <?php foreach ($age_tags as $tag) { ?>
          <option value="<?php echo $tag ?>"><?php echo $tag ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="label-input">
        <label for="breed">Breed:</label>
        <select id="breed" name="breed">
        <option value="">Select Breed</option>
        <?php foreach ($breed_tags as $tag) { ?>
          <option value="<?php echo $tag ?>"><?php echo $tag ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="label-input">
        <label for="color">Color:</label>
        <select id="color" name="color">
        <option value="">Select Color</option>
        <?php foreach ($color_tags as $tag) { ?>
          <option value="<?php echo $tag ?>"><?php echo $tag ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="label-input">
        <label for="gender">Gender:</label>
        <select id="gender" name="gender">
        <option value="">Select Gender</option>
        <?php foreach ($gender_tags as $tag) { ?>
          <option value="<?php echo $tag ?>"><?php echo $tag ?></option>
        <?php } ?>
      </select>
    </div>

    <section class="upload">
      <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>">

      <?php if ($upload_feedback['too_large']) { ?>
        <p class="feedback">We're sorry. The file failed to upload because it was too big. Please select a file that&apos;s no larger than 1MB.</p>
      <?php } ?>

      <?php if ($upload_feedback['general_error']) { ?>
        <p class="feedback">We're sorry. Something went wrong. Please select an JPEG file to upload.</p>
      <?php } ?>

      <div class="label-input">
        <label for="upload-file">JPEG File:</label>
        <!-- This site only accepts JPEG files! -->
        <input id="upload-file" type="file" name="jpeg-file" accept=".jpeg,image/jpeg+xml">
      </div>

      <div class="label-input">
        <label for="upload-source" class="optional">Source URL:</label>
        <input id='upload-source' type="url" name="source" placeholder="URL where found. (optional)">
      </div>
    </section>`

      <div class="align-right">
        <input type="submit" value="Register Cat" name="submit-cat">
      </div>
    </form>

</div>



</main>

<?php include('includes/footer.php'); ?>

</body>

</html>
