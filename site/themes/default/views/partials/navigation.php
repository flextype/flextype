<?php namespace Flextype ?>
<?php use Flextype\Component\{Http\Http, Registry\Registry, Arr\Arr} ?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom box-shadow">
<div class="container">
  <a class="navbar-brand" href="<?= Http::getBaseUrl() ?>"><?= Registry::get('settings.title') ?></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
    <ul class="navbar-nav">
        <?php foreach (Arr::sort(Menus::get('default')['items'], 'order') as $item): ?>
        <li class="nav-item">
            <a class="nav-link <?php if ($item['url'] !== '' && strpos(Http::getUriString(), $item['url']) !== false): ?>active<?php endif ?>" href="<?= Http::getBaseUrl() . '/' . $item['url'] ?>"><?= $item['title'] ?></a>
        </li>
        <?php endforeach ?>
    </ul>
  </div>
</div>
</nav>
