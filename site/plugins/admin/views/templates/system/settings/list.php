<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Registry\Registry, Token\Token, Form\Form, Event\Event};
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')->display();
Themes::view('admin/views/partials/navbar')
    ->assign('links',   [
                            'settings' => [
                                                'link' => Http::getBaseUrl() . '/admin/settings',
                                                'title' => __('admin_system_settings_heading'),
                                                'attributes' => ['class' => 'navbar-item active']
                                          ]
                        ])
    ->assign('buttons', [
                            'settings_clear_cache' => [
                                                'link' => Http::getBaseUrl() . '/admin/settings?clear_cache=1&token='.Token::generate(),
                                                'title' => __('admin_system_clear_cache'),
                                                'attributes' => ['class' => 'float-right btn']
                                          ],
                              'save' => [
                                                  'link'       => 'javascript:;',
                                                  'title'      => __('admin_save'),
                                                  'attributes' => ['class' => 'js-save-form-submit float-right btn']
                                              ]
                        ])
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<?= Form::open(null, ['id' => 'form']) ?>
<?= Form::hidden('token', Token::generate()) ?>
<?= Form::hidden('action', 'save-form') ?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?= Form::label('title', __('admin_system_settings_site_title'), ['for' => 'systemSettingsSiteTitle']) ?>
            <?= Form::input('title', $settings['title'], ['class' => 'form-control', 'id' => 'systemSettingsSiteTitle', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('description', __('admin_system_settings_site_description'), ['for' => 'systemSettingsSiteDescription']) ?>
            <?= Form::textarea('description', $settings['description'], ['class' => 'form-control margin-hard-bottom', 'id' => 'systemSettingsSiteDescription']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('keywords', __('admin_system_settings_site_keywords'), ['for' => 'systemSettingsSiteKeywords']) ?>
            <?= Form::input('keywords', $settings['keywords'], ['class' => 'form-control', 'id' => 'systemSettingsSiteKeywords', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('robots', __('admin_system_settings_site_robots'), ['for' => 'systemSettingsSiteRobots']) ?>
            <?= Form::input('robots', $settings['robots'], ['class' => 'form-control', 'id' => 'systemSettingsSiteRobots', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('author[name]', __('admin_system_settings_site_author_name'), ['for' => 'systemSettingsSiteAuthorName']) ?>
            <?= Form::input('author[name]', $settings['author']['name'], ['class' => 'form-control', 'id' => 'systemSettingsSiteAuthorName', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('author[email]', __('admin_system_settings_site_author_email'), ['for' => 'systemSettingsSiteAuthorEmail']) ?>
            <?= Form::input('author[email]', $settings['author']['email'], ['class' => 'form-control', 'id' => 'systemSettingsSiteAuthorEmail', 'required']) ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <?= Form::label('timezone', __('admin_system_settings_system_timezone'), ['for' => 'systemSettingsSystemTimezone']) ?>
            <?= Form::input('timezone', $settings['timezone'], ['class' => 'form-control', 'id' => 'systemSettingsSystemTimezone', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('date_format', __('admin_system_settings_system_date_format'), ['for' => 'systemSettingsSystemDateFormat']) ?>
            <?= Form::input('date_format', $settings['date_format'], ['class' => 'form-control', 'id' => 'systemSettingsSystemDateFormat', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('charset', __('admin_system_settings_system_charset'), ['for' => 'systemSettingsSystemCharset']) ?>
            <?= Form::input('charset', $settings['charset'], ['class' => 'form-control', 'id' => 'systemSettingsSystemCharset', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('theme', __('admin_system_settings_system_theme'), ['for' => 'systemSettingsSystemTheme']) ?>
            <?= Form::input('theme', $settings['theme'], ['class' => 'form-control', 'id' => 'systemSettingsSystemTheme', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('locale', __('admin_system_settings_system_locale'), ['for' => 'systemSettingsSystemLocale']) ?>
            <?= Form::select('locale', $locales, $settings['locale'], ['class' => 'form-control', 'id' => 'entryTemplate']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('entries[main]', __('admin_system_settings_system_entries_main'), ['for' => 'systemSettingsSystemEntriesMain']) ?>
            <?= Form::input('entries[main]', $settings['entries']['main'], ['class' => 'form-control', 'id' => 'systemSettingsSystemEntriesMain', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('entries[error404][title]', __('admin_system_settings_system_entries_error404_title'), ['for' => 'systemSettingsSystemEntriesError404Title']) ?>
            <?= Form::input('entries[error404][title]', $settings['entries']['error404']['title'], ['class' => 'form-control', 'id' => 'systemSettingsSystemEntriesError404Title', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('entries[error404][description]', __('admin_system_settings_system_entries_error404_description'), ['for' => 'systemSettingsSystemEntriesError404Description']) ?>
            <?= Form::input('entries[error404][description]', $settings['entries']['error404']['description'], ['class' => 'form-control', 'id' => 'systemSettingsSystemEntriesError404Description', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('entries[error404][content]', __('admin_system_settings_system_entries_error404_content'), ['for' => 'systemSettingsSystemEntriesError404Content']) ?>
            <?= Form::input('entries[error404][content]', $settings['entries']['error404']['content'], ['class' => 'form-control', 'id' => 'systemSettingsSystemEntriesError404Content', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('entries[error404][template]', __('admin_system_settings_system_entries_error404_template'), ['for' => 'systemSettingsSystemEntriesError404Template']) ?>
            <?= Form::input('entries[error404][template]', $settings['entries']['error404']['template'], ['class' => 'form-control', 'id' => 'systemSettingsSystemEntriesError404Template', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('errors[display]', __('admin_system_settings_system_errors_display'), ['for' => 'systemSettingsSystemErrorsDisplay']) ?>
            <?= Form::select('errors[display]', [0 => 'false', 1 => 'true'], $settings['errors']['display'], ['class' => 'form-control', 'id' => 'systemSettingsSystemErrorsDisplay', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('cache[enabled]', __('admin_system_settings_system_cache_enabled'), ['for' => 'systemSettingsSystemCacheEnabled']) ?>
            <?= Form::select('cache[enabled]', [0 => 'false', 1 => 'true'], $settings['cache']['enabled'], ['class' => 'form-control', 'id' => 'systemSettingsSystemCacheEnabled', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('cache[prefix]', __('admin_system_settings_system_cache_prefix'), ['for' => 'systemSettingsSystemCachePrefix']) ?>
            <?= Form::input('cache[prefix]', $settings['cache']['prefix'], ['class' => 'form-control', 'id' => 'systemSettingsSystemCachePrefix', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('cache[driver]', __('admin_system_settings_system_cache_driver'), ['for' => 'systemSettingsSystemCacheDriver']) ?>
            <?= Form::input('cache[driver]', $settings['cache']['driver'], ['class' => 'form-control', 'id' => 'systemSettingsSystemCacheDriver', 'required']) ?>
        </div>
        <div class="form-group">
            <?= Form::label('cache[lifetime]', __('admin_system_settings_system_cache_lifetime'), ['for' => 'systemSettingsSystemCacheLifetime']) ?>
            <?= Form::input('cache[lifetime]', $settings['cache']['lifetime'], ['class' => 'form-control', 'id' => 'systemSettingsSystemCacheLifetime', 'required']) ?>
        </div>
    </div>
</div>
<?php echo Form::close(); ?>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
