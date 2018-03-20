<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom box-shadow">
<div class="container">
  <a class="navbar-brand" href="<?php echo Url::getBase(); ?>"><?php echo Flextype\Config::get('site.title'); ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link <?php if (Url::getUriSegment(0) == 'home' || Url::getUriSegment(0) == '') echo 'active'; ?>" href="<?php echo Url::getBase(); ?>">Home</a>
      </li>
    </ul>
  </div>
</div>
</nav>
