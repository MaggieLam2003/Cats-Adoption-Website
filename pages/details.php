
<?php

$db = init_sqlite_db('db/site.sqlite', 'db/init.sql');

$cat_id = $_GET['id'];

$result = exec_sql_query(
  $db,
  "SELECT cats.cat_name, cats.cat_description, tags.tag_type, tags.tag_value
  FROM cats
  JOIN cats_tags ON cats.id = cats_tags.cat_id
  JOIN tags ON tags.id = cats_tags.tag_id
  WHERE cats.id = :id;",
  array(':id' => $cat_id)
);

$records = $result->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Details</title>
  <link rel="stylesheet" type="text/css" href="/public/styles/site.css">
</head>

<body>

<?php include('includes/header.php'); ?>

<div class= "detail-container">

    <!-- Source: https://unsplash.com/s/photos/cute-cat -->
    <!-- Source: https://www.vecteezy.com/free-photos/cat -->
    <!-- Source: https://www.verywellmind.com/are-some-people-really-afraid-of-cats-2671757 -->
    <!-- Source: https://hakaimagazine.com/features/its-10-pm-do-you-know-where-your-cat-is/ -->
    <!-- Source: https://www.nature.com/articles/494009a -->
    <!-- Source: https://www.cats.org.uk/ -->
<!--Source: <?php echo htmlspecialchars($record['source']); ?> -->
<img src="/public/uploads/cats/<?= htmlspecialchars($cat_id) . '.' . 'jpeg' ?>" alt="cat seed images">

  <div class="details">
    <h3 class="detail-name"><?php echo htmlspecialchars($records[0]['cat_name']); ?></h3>
    <p class="detail-subtext"><?php echo htmlspecialchars($records[0]['cat_description']); ?></p>
     <!-- citation here -->
    <cite><a class="cite" href="<?php echo htmlspecialchars($record[0]['source']) ?>">Source</a></cite>


    <hr class="detail-divider">

    <?php foreach ($records as $record) { ?>
      <?php
      $tag_type = htmlspecialchars($record['tag_type']);
      $tag_value = htmlspecialchars($record['tag_value']);
      ?>
      <p><?php echo "{$tag_type}: {$tag_value}"; ?></p>

    <?php } ?>


  </div>

</div>

<?php include('includes/footer.php'); ?>


</body>
