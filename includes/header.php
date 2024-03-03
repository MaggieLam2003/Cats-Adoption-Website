<header class= "header-design">

  <h1><a href="/">FurryTail Endings</a></h1>



  <?php if (is_user_logged_in()) { ?>

    <h1 class="logout"><a href="<?php echo logout_url(); ?>">Sign Out</a></h1>
  <?php } ?>

</header>
