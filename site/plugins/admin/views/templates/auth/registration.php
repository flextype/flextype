<?php
namespace Flextype;

use Flextype\Component\Registry\Registry;
use Flextype\Component\Http\Http;
use Flextype\Component\Form\Form;
use Flextype\Component\Token\Token;
use function Flextype\Component\I18n\__;

Themes::view('admin/views/partials/head')
    ->assign('main_panel_class', 'width-full')
    ->display();
Themes::view('admin/views/partials/content-start')->display();
?>

<form action="" method="post">
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
    <div class="row">
        <div class="col-3 float-center">
            <h3 class="h3 auth-header text-center"><?php echo __('admin_users_create_new'); ?></h3>
            <div class="form-group">
                <label><?php echo __('admin_username'); ?></label>
                <input type="text" name="username" value="" class="form-control" placeholder="lowercase chars only, e.g. admin" pattern="^[a-z0-9_-]{3,16}$" required="required">
            </div>
            <div class="form-group">
                <label for="email"><?php echo __('admin_email'); ?></label>
                <input type="text" name="email" value="" class="form-control" placeholder="valid email address" required="required">
            </div>
            <div class="form-group">
                <label for="password"><?php echo __('admin_password'); ?></label>
                <input type="password" name="password" value="" class="form-control" placeholder="complex string at least 8 chars long" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required="required">
            </div>
            <div class="form-group">
                <input type="submit" name="registration" value="<?php echo __('admin_users_create'); ?>" class="btn btn-black btn-block">
            </div>
        </div>
    </div>
</form>

<?php
Themes::view('admin/views/partials/content-end')->display();
Themes::view('admin/views/partials/footer')->display();
?>
