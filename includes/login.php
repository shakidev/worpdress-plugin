<?php defined('ABSPATH') or die("Protected By WT!");?>
<div class="ww-page__content">
        <div class="ww-content__title"><?=wtsec_locale("activation")?></div>
        <div class="ww-content__text"><?=wtsec_locale("enter_api_key")?></div>
        <form method="post" class="ww-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <input type="hidden" name="action" value="login_form">
            <div class="ww-form__key-input"><input class="ww-input" name="key" type="text" placeholder="<?=wtsec_locale("activate_your_plugin")?>">
            </div>
            <div class="ww-form__block"><button class="ww-button ww-button--success" type="submit"><?=wtsec_locale("send")?></button></div>
        </form>
        <div class="ww-content__info"><?=wtsec_locale("activation_desc")?> <a
                    href="https://wtotem.com/cabinet" target="_blank">wtotem.com/cabinet</a></div>
        <div class="ww-content__title"><?=wtsec_locale("connection")?></div>
        <div class="ww-content__text"><?=wtsec_locale("auth_desc")?></div><a
                href="https://wtotem.com/cabinet" target="_blank"><img src="<?=wtsec_getImagePath("badge.62d05a1a.svg")?>" alt="badge logo"></a>
    </div>
