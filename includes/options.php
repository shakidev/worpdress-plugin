<?php defined('ABSPATH') or die("Protected By WT!");?>
<div class="ww-page__content ww-page__content--normal">
        <div class="ww-title"><img class="ww-icon" src="<?= wtsec_getImagePath("options.4f7dbf4e.svg") ?>"
                                   alt=""><span><?=wtsec_locale("options")?></span></div>

        <?php if(isset($arguments['wa'])){ ?>
        <div class="ww-option">
            <div class="ww-option__head"><label class="ww-option__name" for="checkbox-0"><?=wtsec_locale("availability")?></label>
                <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                data-host_id="<?=$arguments['host_id']?>"
                                data-config_id="<?=$arguments['wa']['config_id']?>"
                                class="v-change ww-checkbox__input" type="checkbox" id="checkbox-0" <?=$arguments['wa']['is_active'] ? "checked" : ""?>>
                        <div class="ww-checkbox__name"></div>
                    </label></div>
            </div>
            <div class="ww-option__text"><?=wtsec_locale("descriptions.availability")?>
            </div>
        </div>
        <?php } ?>
        <?php if(isset($arguments['dec'])){ ?>
        <div class="ww-option">
            <div class="ww-option__head"><label class="ww-option__name" for="checkbox-1"><?=wtsec_locale("domain")?></label>
                <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                data-host_id="<?=$arguments['host_id']?>"
                                data-config_id="<?=$arguments['dec']['config_id']?>"
                                class="v-change ww-checkbox__input"
                                                                                   type="checkbox" id="checkbox-1" <?=$arguments['dec']['is_active'] ? "checked" : ""?>>
                        <div class="ww-checkbox__name"></div>
                    </label></div>
            </div>
            <div class="ww-option__text"><?=wtsec_locale("descriptions.domain")?>
            </div>
        </div>
        <?php } ?>
        <?php if(isset($arguments['cms'])){ ?>
        <div class="ww-option">
            <div class="ww-option__head"><label class="ww-option__name" for="checkbox-2"><?=wtsec_locale("malicious_scripts")?></label>
                <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                data-host_id="<?=$arguments['host_id']?>"
                                data-config_id="<?=$arguments['cms']['config_id']?>"
                                class="v-change ww-checkbox__input"
                                                                                   type="checkbox" id="checkbox-2" <?=$arguments['cms']['is_active'] ? "checked" : ""?>>
                        <div class="ww-checkbox__name"></div>
                    </label></div>
            </div>
            <div class="ww-option__text"><?=wtsec_locale("descriptions.malicious")?>
            </div>
        </div>
        <?php } ?>
        <?php if(isset($arguments['dc'])){ ?>
        <div class="ww-option">
            <div class="ww-option__head"><label class="ww-option__name" for="checkbox-3"><?=wtsec_locale("deface_scanner")?></label>
                <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                data-host_id="<?=$arguments['host_id']?>"
                                data-config_id="<?=$arguments['dc']['config_id']?>"
                                class="v-change ww-checkbox__input"
                                                                                   type="checkbox" id="checkbox-3" <?=$arguments['dc']['is_active'] ? "checked" : ""?>>
                        <div class="ww-checkbox__name"></div>
                    </label></div>
            </div>
            <div class="ww-option__text"><?=wtsec_locale("descriptions.deface")?>
            </div>
        </div>
        <?php } ?>
        <?php if(isset($arguments['ssl'])){ ?>
            <div class="ww-option">
                <div class="ww-option__head"><label class="ww-option__name" for="checkbox-5"><?=wtsec_locale("ssl")?></label>
                    <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                    data-host_id="<?=$arguments['host_id']?>"
                                    data-config_id="<?=$arguments['ssl']['config_id']?>"
                                    class="v-change ww-checkbox__input" type="checkbox" id="checkbox-5" <?=$arguments['ssl']['is_active'] ? "checked" : ""?>>
                            <div class="ww-checkbox__name"></div></label></div>
                </div>
                <div class="ww-option__text"><?=wtsec_locale("descriptions.ssl")?>
                </div>
            </div>
        <?php } ?>

        <?php if(isset($arguments['vc'])){ ?>
            <div class="ww-option">
                <div class="ww-option__head"><label class="ww-option__name" for="checkbox-5"><?=wtsec_locale("remote_antivirus")?></label>
                    <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                    data-host_id="<?=$arguments['host_id']?>"
                                    data-config_id="<?=$arguments['vc']['config_id']?>"
                                    class="v-change ww-checkbox__input" type="checkbox" id="checkbox-5" <?=$arguments['vc']['is_active'] ? "checked" : ""?>>
                            <div class="ww-checkbox__name"></div></label></div>
                </div>
                <div class="ww-option__text"><?=wtsec_locale("descriptions.antivirus")?>
                </div>
            </div>
        <?php } ?>


        <?php if(isset($arguments['waf'])){ ?>
            <div class="ww-option">
                <div class="ww-option__head"><label class="ww-option__name" for="checkbox-5"><?=wtsec_locale("firewall")?></label>
                    <div class="ww-option__checkbox"><label class="ww-checkbox"><input
                                    data-host_id="<?=$arguments['host_id']?>"
                                    data-config_id="<?=$arguments['waf']['config_id']?>"
                                    class="v-change ww-checkbox__input" type="checkbox" id="checkbox-5" <?=$arguments['waf']['is_active'] ? "checked" : ""?>>
                            <div class="ww-checkbox__name"></div></label></div>
                </div>
                <div class="ww-option__text"><?=wtsec_locale("descriptions.firewall")?>
                </div>
            </div>
        <?php } ?>
    </div>
