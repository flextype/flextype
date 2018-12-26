<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Filesystem\Filesystem, Token\Token, Number\Number};
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links', [
                        'information' => [
                                            'link' => Http::getBaseUrl() . '/admin/information',
                                            'title' => __('admin_menu_system_information'),
                                            'attributes' => ['class' => 'navbar-item active']
                                         ],
                      ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<h3 class="h3"><?= __('admin_system_settings_system') ?></h3>

<table class="table no-margin">
    <tbody>
        <tr>
            <td width="200"><?= __('admin_flextype_core_version') ?></td>
            <td><?= Flextype::VERSION ?></td>
        </tr>
        <tr>
            <td width="200"><?= __('admin_flextype_admin_version') ?></td>
            <td><?= Registry::get('plugins.admin.version'); ?></td>
        </tr>
        <tr>
            <td><?= __('admin_debugging'); ?></td>
            <td><?php if (Registry::get('settings.errors.display')) echo __('admin_on'); else echo __('admin_off'); ?></td>
        </tr>
        <tr>
            <td><?= __('admin_cache'); ?></td>
            <td><?php if (Registry::get('settings.cache.enabled')) echo __('admin_on'); else echo __('admin_off'); ?></td>
        </tr>
    </tbody>
</table>
<br><br>


<h3 class="h3"><?=  __('admin_server'); ?></h3>

<table class="table no-margin">
    <tbody>
        <tr>
            <td width="180"><?= __('admin_php_version') ?></td>
            <td><?= PHP_VERSION; ?></td>
        </tr>
        <tr>
            <td><?= __('admin_php_built_on') ?></td>
            <td><?= php_uname(); ?></td>
        </tr>
        <tr>
            <td><?= __('admin_web_server'); ?></td>
            <td><?= (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : @getenv('SERVER_SOFTWARE'); ?></td>
        </tr>
        <tr>
            <td><?= __('admin_web_server_php_interface') ?></td>
            <td><?= php_sapi_name() ?></td>
        </tr>
        <?php
            if (function_exists('apache_get_modules')) {
                if ( ! in_array('mod_rewrite',apache_get_modules())) {
                    echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.__('admin_not_installed').'</td></tr>';
                } else {
                    echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.__('admin_installed').'</td></tr>';
                }
            } else {
                echo '<tr><td>'.'Apache Mod Rewrite'.'</td><td>'.__('admin_installed').'</td></tr>';
            }
        ?>
        <?php
            if (!function_exists('password_hash')) {
                echo '<tr><td>'.'password_hash()'.'</td><td>'.__('admin_not_installed').'</td></tr>';
            } else {
                echo '<tr><td>'.'password_hash()'.'</td><td>'.__('admin_installed').'</td></tr>';
            }
        ?>
        <?php
            if (!function_exists('password_verify')) {
                echo '<tr><td>'.'password_verify()'.'</td><td>'.__('admin_not_installed').'</td></tr>';
            } else {
                echo '<tr><td>'.'password_verify()'.'</td><td>'.__('admin_installed').'</td></tr>';
            }
        ?>
    </tbody>
</table>
<br>
<br>


<?php if (Filesystem::isFileWritable(ROOT_DIR . '/.htaccess') or
          Filesystem::isFileWritable(ROOT_DIR . '/index.php') or
          Registry::get('settings.errors.display') === true) { ?>

        <h3 class="h3"><?=  __('admin_security_check_results') ?></h3>

        <table class="table no-margin">
            <tbody>
                <?php if (Filesystem::isFileWritable(ROOT_DIR . '/.htaccess')) { ?>
                <tr>
                    <td><?= __('admin_security_check_results_htaccess', null, [':path' => ROOT_DIR . '/.htaccess']) ?></td>
                </tr>
                <?php } ?>
                <?php if (Filesystem::isFileWritable(ROOT_DIR . '/index.php')) { ?>
                <tr>
                    <td><?= __('admin_security_check_results_index', null, [':path' => ROOT_DIR . '/index.php']) ?></td>
                </tr>
                <?php } ?>
                <?php if (Registry::get('settings.errors.display') === true) { ?>
                <tr>
                    <td><?= __('admin_security_check_results_debug') ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <br><br>
<?php } ?>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
