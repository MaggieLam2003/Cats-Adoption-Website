<?php
$title = 'Cat Entries';
$nav_cats_class = 'active_page';

$sort_param = [];
$sort_param['age'] = $_GET['age_tag'] ?? NULL; // untrusted
$sort_param['breed'] = $_GET['age_breed'] ?? NULL; // untrusted
$sort_param['color'] = $_GET['age_color'] ?? NULL; // untrusted
$sort_param['gender'] = $_GET['age_gender'] ?? NULL; // untrusted


$sort_css_classes = array(
  'age' => '',
  'breed' => '',
  'color' => '',
  'gender' => '',
);

$sql_select_clause = "SELECT cats.source, cats.id, cats.cat_name, cats.cat_description,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'age' THEN tags.tag_value ELSE NULL END) AS age,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'breed' THEN tags.tag_value ELSE NULL END) AS breed,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'color' THEN tags.tag_value ELSE NULL END) AS color,
  GROUP_CONCAT(CASE WHEN tags.tag_type = 'gender' THEN tags.tag_value ELSE NULL END) AS gender
FROM cats
JOIN cats_tags ON cats.id = cats_tags.cat_id
JOIN tags ON cats_tags.tag_id = tags.id
GROUP BY cats.id";

$sql_order_clause = "";
$sql_where_clause = "";
$params = [];

if (in_array($sort_param, array('age', 'breed', 'color', 'gender'))) {
  $sort_css_classes[$sort_param] = 'active';

  if ($sort_param['age']) {
      $params[':age_tag'] = $sort_param['age'];
      $sql_order_clause = " ORDER BY age";
      $sql_where_clause = " WHERE age = :age_tag";
  } else if ($sort_param['breed']) {
      $params[':breed_tag'] = $sort_param['breed'];
      $sql_order_clause = " ORDER BY breed";
      $sql_where_clause = " WHERE breed = :breed_tag";
  } else if ($sort_param['color']) {
      $params[':color_tag'] = $sort_param['color'];
      $sql_order_clause = " ORDER BY color";
      $sql_where_clause = " WHERE color = :color_tag";
  } else if ($sort_param['gender']) {
      $params[':gender_tag'] = $sort_param['gender'];
      $sql_order_clause = " ORDER BY gender";
      $sql_where_clause = " WHERE gender = :gender_tag";
  }
}

$age_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'age'")->fetchAll(PDO::FETCH_COLUMN);
$breed_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'breed'")->fetchAll(PDO::FETCH_COLUMN);
$color_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'color'")->fetchAll(PDO::FETCH_COLUMN);
$gender_tags = exec_sql_query($db, "SELECT DISTINCT tag_value FROM tags WHERE tag_type = 'gender'")->fetchAll(PDO::FETCH_COLUMN);

// query the database
$db = init_sqlite_db('db/site.sqlite', 'db/init.sql');

$sql = $sql_select_clause . $sql_order_clause;
$records = exec_sql_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>FurryTail Endings @ Home</title>
  <link rel="stylesheet" type="text/css" href="/public/styles/site.css">
</head>

<body>


<div class='black'>
  <div class= "top-containter">

  <?php if (!is_user_logged_in()) { ?>

    <a class='login-button' href='/login'>Login </a>

  <?php } ?>

  <?php if (is_user_logged_in()) { ?>

    <a class= 'float-left' href ="/form"> Insert Cat </a>

    <a class="login-button" href="<?php echo logout_url(); ?>">Sign Out</a>

  <?php } ?>

    <div class= "inside-top-container">
      <h1 class= "title"> FurryTail Endings</h1>
      <p class= "subtext">
        find your happily ever after with a furry friend
      </p>
    </div>

    <!-- SOURCE: https://pngimg.com/image/50491 -->
    <img src="/public/images/catbanner.png" alt="threecats">
    <cite><a class='cite-banner' href="https://pngimg.com/image/50491">Source</a></cite>


  </div>


  <div class="border">
    <p>
      Help us help you gain a new cat to fill that void in your life!
    </p>

    <div class="row">
      <h3>
        America's Best Adoption Agency
      </h3>

      <!-- SOURCE: https://www.clipartmax.com/middle/m2i8i8A0m2N4b1N4_paw-print-outline-clip-art-white-paw-print-transparent/ -->
      <img class='space' src="/public/images/paw.png" alt="white paw">
      <cite><a class='cite-banner2' href="https://www.clipartmax.com/middle/m2i8i8A0m2N4b1N4_paw-print-outline-clip-art-white-paw-print-transparent/">Source</a></cite>
    </div>

  </div>
  </div>

  <div class="content">

  <h2> View our CATalog of furry friends!</h2>

    <div class="filter">
      <h2> Sort By</h2>

      <form method="get">
        <label for="age-tag">Age:</label>
        <select name="age_tag" id="age-tag" class="<?php echo $sort_css_classes['age']; ?>">
          <option value="" <?php if ($sort_param['age'] === NULL) { echo "selected"; } ?>>All</option>
          <?php foreach ($age_tags as $tag) { ?>
            <option value="<?php echo htmlspecialchars($tag); ?>" <?php if ($sort_param['age'] === $tag) { echo "selected"; } ?>><?php echo htmlspecialchars($tag); ?></option>
          <?php } ?>
        </select>

        <label for="breed-tag">Breed:</label>
        <select name="breed_tag" id="breed-tag" class="<?php echo $sort_css_classes['breed']; ?>">
          <option value="" <?php if ($sort_param['breed'] === NULL) { echo "selected"; } ?>>All</option>
          <?php foreach ($breed_tags as $tag) { ?>
            <option value="<?php echo htmlspecialchars($tag); ?>" <?php if ($sort_param['breed'] === $tag) { echo "selected"; } ?>><?php echo htmlspecialchars($tag); ?></option>
          <?php } ?>
        </select>

        <label for="color-tag">Color:</label>
        <select name="color_tag" id="color-tag" class="<?php echo $sort_css_classes['color']; ?>">
          <option value="" <?php if ($sort_param['color'] === NULL) { echo "selected"; } ?>>All</option>
          <?php foreach ($color_tags as $tag) { ?>
            <option value="<?php echo htmlspecialchars($tag); ?>" <?php if ($sort_param['color'] === $tag) { echo "selected"; } ?>><?php echo htmlspecialchars($tag); ?></option>
          <?php } ?>
        </select>

        <label for="gender-tag">Gender:</label>
        <select name="gender_tag" id="gender-tag" class="<?php echo $sort_css_classes['gender']; ?>">
          <option value="" <?php if ($sort_param['gender'] === NULL) { echo "selected"; } ?>>All</option>
          <?php foreach ($gender_tags as $tag) { ?>
            <option value="<?php echo htmlspecialchars($tag); ?>" <?php if ($sort_param['gender'] === $tag) { echo "selected"; } ?>><?php echo htmlspecialchars($tag); ?></option>
          <?php } ?>
        </select>

        <input type="submit" value="Filter" name="filter-cat">

      </form>

    </div>

    <div class="sort-entries">

      <?php foreach ($records as $record) { ?>
        <?php
          if (isset($_GET['age_tag']) && $_GET['age_tag'] != "" && $record['age'] != $_GET['age_tag']) {
            continue;
          }
          if (isset($_GET['breed_tag']) && $_GET['breed_tag'] != "" && $record['breed'] != $_GET['breed_tag']) {
            continue;
          }
          if (isset($_GET['color_tag']) && $_GET['color_tag'] != "" && $record['color'] != $_GET['color_tag']) {
            continue;
          }
          if (isset($_GET['gender_tag']) && $_GET['gender_tag'] != "" && $record['gender'] != $_GET['gender_tag']) {
            continue;
          }
        ?>


        <a class="cat-name" href="/details?id=<?php echo $record['id']; ?>">
        <div class="entry">
          <!-- Source: https://unsplash.com/s/photos/cute-cat -->
          <!-- Source: https://www.vecteezy.com/free-photos/cat -->
          <!-- Source: https://www.verywellmind.com/are-some-people-really-afraid-of-cats-2671757 -->
          <!-- Source: https://hakaimagazine.com/features/its-10-pm-do-you-know-where-your-cat-is/ -->
          <!-- Source: https://www.nature.com/articles/494009a -->
          <!-- Source: https://www.cats.org.uk/ -->

           <!--Source: <?php echo htmlspecialchars($record['source']); ?> -->
          <img src="/public/uploads/cats/<?= htmlspecialchars($record["id"]) . '.' . 'jpeg' ?>" alt="cat seed images">
          <h2><?php echo htmlspecialchars($record['cat_name']); ?>
          <span class="cite"><?php echo htmlspecialchars($record['source']); ?></span>


        </h2>

        </div>
        </a>

      <?php } ?>

    </div>


  </div>

  <?php include('includes/footer.php'); ?>

</body>

</html>
