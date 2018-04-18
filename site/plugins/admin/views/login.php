<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo Flextype\I18n::find('admin_login', 'admin', Flextype\Config::get('site.locale')); ?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/css/auth.css" rel="stylesheet">
  </head>
  <body class="text-center">
    <form class="form-signin" method="post">
      <label for="inputUsername" class="sr-only"><?php echo Flextype\I18n::find('admin_username', 'admin', Flextype\Config::get('site.locale')); ?></label>
      <input type="input" name="username" id="inputUsername" class="form-control" placeholder="<?php echo Flextype\I18n::find('admin_username', 'admin', Flextype\Config::get('site.locale')); ?>" required autofocus>
      <label for="inputPassword" class="sr-only"><?php echo Flextype\I18n::find('admin_password', 'admin', Flextype\Config::get('site.locale')); ?></label>
      <input type="password" name="password" id="inputPassword" class="form-control" placeholder="<?php echo Flextype\I18n::find('admin_password', 'admin', Flextype\Config::get('site.locale')); ?>" required>
      <button class="btn btn-lg btn-dark btn-block" name="login" type="submit"><?php echo Flextype\I18n::find('admin_login', 'admin', Flextype\Config::get('site.locale')); ?></button>
    </form>
  </body>
</html>
