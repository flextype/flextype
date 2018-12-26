<?php
namespace Flextype;

use Flextype\Component\{Http\Http, Registry\Registry, Token\Token};
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'plugins'          => [
                                                        'link'       => Http::getBaseUrl() . '/admin/plugins',
                                                        'title'      => __('admin_plugins_heading'),
                                                        'attributes' => ['class' => 'navbar-item active']
                                                  ]
                        ])
    ->assign('buttons', [
                            'plugins_get_more' => [
                                                'link' => 'http://flextype.org/download/plugins',
                                                'title' => __('admin_plugins_get_more_plugins'),
                                                'attributes' => ['class' => 'float-right btn', 'target' => '_blank']
                                            ]
                        ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<form>
    <input type="hidden" name="url" value="<?= Http::getBaseUrl() . '/admin/plugins' ?>">
</form>

<table class="table no-margin">
  <thead>
      <tr>
          <th><?= __('admin_plugins_name') ?></th>
          <th></th>
          <th width="90" class="text-right"><?= __('admin_plugins_status') ?></th>
      </tr>
  </thead>
  <tbody>
      <?php foreach ($plugins_list as $key => $plugin): ?>
      <tr>
          <td><?= $plugin['name'] ?></td>
          <td class="text-right">
              <a href="javascript:;" class="btn js-plugins-info" data-toggle="modal" data-target="#pluginInfoModal"
                  data-name="<?= $plugin['name'] ?>"
                  data-version="<?= $plugin['version'] ?>"
                  data-description="<?= $plugin['description'] ?>"
                  data-author-name="<?= $plugin['author']['name'] ?>"
                  data-author-email="<?= $plugin['author']['email'] ?>"
                  data-author-url="<?= $plugin['author']['url'] ?>"
                  data-homepage="<?= $plugin['homepage'] ?>"
                  data-bugs="<?= $plugin['bugs']; ?>"
                  data-license="<?= $plugin['license'] ?>"
                  ><?= __('admin_plugins_info') ?></a>
          </td>
          <td class="text-right">
              <div class="form-group no-margin">
                <span class="switch switch-sm">
                  <input id="switch-sm-<?= $plugin['name'] ?>" type="checkbox" class="switch js-switch" data-plugin="<?= $key ?>" data-token="<?= Token::generate() ?>" <?php if ($plugin['enabled'] == 'true') echo 'checked'; else echo ''; ?> <?php if ($key == 'admin') { ?>disabled<?php } ?>>
                  <label for="switch-sm-<?= $plugin['name'] ?>"></label>
                </span>
              </div>
          </td>
      </tr>
      <?php endforeach ?>
  </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="pluginInfoModal" tabindex="-1" role="dialog" aria-labelledby="pluginInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pluginInfoModalLabel"><?= __('admin_plugins_info') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p><b><?= __('admin_plugins_name') ?>: </b><span class="js-plugin-name-placeholder"></span></p>
          <p><b><?= __('admin_plugins_version') ?>: </b><span class="js-plugin-version-placeholder"></span></p>
          <p><b><?= __('admin_plugins_description') ?>: </b><span class="js-plugin-description-placeholder"></span></p>
          <p><b><?= __('admin_plugins_author_name') ?>: </b><span class="js-plugin-author-name-placeholder"></span></p>
          <p><b><?= __('admin_plugins_author_email') ?>: </b><span class="js-plugin-author-email-placeholder"></span></p>
          <p><b><?= __('admin_plugins_author_url') ?>: </b><span class="js-plugin-author-url-placeholder"></span></p>
          <p><b><?= __('admin_plugins_homeentry') ?>: </b><span class="js-plugin-homepage-placeholder"></span></p>
          <p><b><?= __('admin_plugins_bugs') ?>: </b><span class="js-plugin-bugs-placeholder"></span></p>
          <p><b><?= __('admin_plugins_license') ?>: </b><span class="js-plugin-license-placeholder"></span></p>
      </div>
    </div>
  </div>
</div>

<?php Themes::view('admin/views/partials/content-end')->display() ?>
<?php Themes::view('admin/views/partials/footer')->display() ?>
