<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="<?php echo url("admin_assets/css/sb-admin-2.min.css") ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo url("admin_assets/css/style.css") ?>">
</head>
<body style="background-color: #f0f0f1;">
   <div id="login">
        <h1>
            <a href="<?php echo url("/"); ?>">
                <img style="width: 150px;" src="<?php echo url("client_assets/images/logo.png") ?>" alt="">
            </a>
        </h1>
  <form
    id="loginform"
    action="<?php echo url("admin/login"); ?>"
    method="post"
  >
    <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Username</label>
        <input value="<?php echo $old['login_key'] ?? "" ?>" type="text" name="login_key" class="form-control">
    </div>
    <?php if(isset($errors['login_key'])): ?>
      <?php foreach($errors['login_key'] as $message): ?>
        <p class="error"><?php echo $message; ?></p>
      <?php endforeach; ?>
    <?php endif; ?>
    <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Password</label>
        <input type="password" name="password" class="form-control">
    </div>
    <?php if(isset($errors['password'])): ?>
      <?php foreach($errors['password'] as $message): ?>
        <p class="error"><?php echo $message; ?></p>
      <?php endforeach; ?>
    <?php endif; ?>
    <?php if(isset($message['error'])): ?>
      <p class="error"><?php echo $message['error']; ?></p>
    <?php endif; ?>
    <p class="submit mb-0">
      <button style="font-size: 14px;border-radius:3px" class="btn btn-primary w-100">Log In</button>
      <?php if(get_query("redirect_to")): ?>
        <input
        type="hidden"
        name="redirect_to"
        value="<?php echo get_query("redirect_to") ?? ""; ?>"
        />
      <?php endif; ?> 
    </p>
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
  </form>

  <!-- <p id="nav">
    <a
      class="wp-login-lost-password"
      href="http://localhost/wordpress/wp-login.php?action=lostpassword"
      >Lost your password?</a
    >
  </p> -->

  <p id="backtoblog">
    <a href="<?php echo url("/") ?>">← Go to my website</a>
  </p>
</div>

</body>
</html>