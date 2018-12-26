<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token, Number\Number};
use Flextype\Component\Session\Session;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                        'information' => [
                                            'link' => Http::getBaseUrl() . '/admin/information',
                                            'title' => __('admin_menu_profile'),
                                            'attributes' => ['class' => 'navbar-item active']
                                         ],
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<div class="profile">
    <i class="fas fa-user-circle"></i>
    <?= __('admin_username') ?>: <?= Session::get('username') ?> <br>
    <?= __('admin_role') ?>: <?= Session::get('role') ?> <br>
    <br>
    <a class="btn btn-default" href="<?= Http::getBaseUrl() ?>/admin/logout?token=<?= Token::generate() ?>"><?= __('admin_menu_logout') ?></a>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
