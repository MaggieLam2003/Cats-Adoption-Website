<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Log In</title>

  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all">
</head>

<body>
  <?php include 'includes/header.php'; ?>

  <main class='login-main'>
    <h2>Log in</h2>

    <?php if (is_user_logged_in()) { ?>
      <p>Welcome <strong><?php echo htmlspecialchars($current_user['name']); ?></strong>!</p>
    <?php } ?>


    <?php if (is_user_logged_in()) { ?>

    <p>Furrytail endings start here - welcome to our cat adoption hub!</p>

    <?php } ?>


    <?php if (!is_user_logged_in()) { ?>

      <p>Only cool cats allowed! Staff entrance located here</p>
      <h2>Sign In</h2>

    <?php echo login_form('/login', $session_messages);
    } ?>

  </main>

  <?php include 'includes/footer.php'; ?>
</body>

</html>
