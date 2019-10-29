<?php defined('ABSPATH') or die("Protected By WT!");?>
<div class="ww-page">
    <div class="ww-page__head">
        <div class="ww-header">
            <div class="ww-header__logo"><img src="<?=wtsec_getImagePath("logo.b37d81ec.svg")?>" alt="logo">
                <div class="ww-header__version">Version <?=WTSEC_PLUGIN_INFORMATION_VERSION?></div>
            </div>
<!--            <a class="ww-header__button" href="#">Get a service</a>-->
        </div>
    </div>

    <div class="ww-content ww-content--top ww-grid">
        <div class="ww-grid__6">
        <?php WTSEC_LIBRARY_Session::notifications() ?>
        <?php include $body;?>
        </div>
        <?php include $faq;?>
    </div>
</div>
