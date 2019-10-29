<?php defined('ABSPATH') or die("Protected By WT!");?>
<div class="ww-page__content ww-page__content--normal">
    <div class="ww-content__header">
        <div class="ww-title"><img class="ww-icon" src="<?= wtsec_getImagePath("icon-antivirus.2c163fba.svg") ?>"
                                   alt=""><span><?= wtsec_locale("remote_antivirus") ?></span>
        </div>
        <div class="is--show_desktop">
            <?php if(isset($arguments['vc']['actions']['first'])):?>
                <?= $arguments['vc']['actions']['first'] ?>
            <?endif?>
            <?php if(isset($arguments['vc']['actions']['second'])):?>
                <?= $arguments['vc']['actions']['second'] ?>
            <?endif?>
        </div>
    </div>
    <div class="ww-info">
        <div class="ww-info__item">
            <div class="ww-info__name"><?= wtsec_locale("status") ?></div>
            <div class="ww-info__value"><?= $arguments['vc']['status']['text'] ?></div>
        </div>
        <div class="ww-info__item">
            <div class="ww-info__name"><?= wtsec_locale("changes") ?></div>
            <div class="ww-info__value"><?= $arguments["vc"]["changes"] ?></div>
        </div>
        <div class="ww-info__item">
            <div class="ww-info__name"><?= wtsec_locale("detected_signatures") ?></div>
            <div class="ww-info__value <?= $arguments["vc"]["signatures"] > 0 ? 'ww-info__value--bg' : '' ?>"><?= $arguments["vc"]["signatures"] ?></div>
        </div>
    </div>
    <div class="is--flex_grow is--show_tablet">
        <?php if(isset($arguments['vc']['actions']['first'])):?>
        <?= $arguments['vc']['actions']['first'] ?>
        <?endif?>
        <?php if(isset($arguments['vc']['actions']['second'])):?>
        <?= $arguments['vc']['actions']['second'] ?>
        <?endif?>
    </div>
</div>
<div class="ww-page__content ww-page__content--normal">
    <div class="ww-title"><img class="ww-icon" src="<?= wtsec_getImagePath("icon-file.ad3ba7b0.svg") ?>"
                               alt=""><span>
                <?php if ($arguments["vc"]["signatures"] > 0 || $arguments["vc"]["changes"] > 0) { ?>
                    <?= wtsec_locale("results_found") ?>
                <?php } else { ?>
                    <?= wtsec_locale("viruses_not_found") ?>
                <?php } ?>
            </span></div>
    <div class="ww-result ww-result--margin">
        <div class="ww-result__row ww-result__row--head">
            <div class="ww-result__cell" title="" data-tlite=""><?= wtsec_locale("file") ?></div>
            <div class="ww-result__cell" title="" data-tlite=""><?= wtsec_locale("antivirus.signature") ?></div>
            <div class="ww-result__cell" title="" data-tlite=""><?= wtsec_locale("description") ?></div>
            <div class="ww-result__cell"><?= wtsec_locale("status") ?></div>
        </div>
        <?php foreach ($arguments["vc"]["list"] as $list) {
            $signature = "";
            $description = "";
            $details = [];
            $changes = true;
            if (isset($list['matches']) && !empty($list['matches'])) {
                $changes = false;
                $signature_data = json_decode($list['matches'], true);
                if (isset($signature_data[0]["Rule"])) {
                    $signature = $signature_data[0]["Rule"];
                }
                if (isset($signature_data[0]["Meta"]["description"])) {
                    $description = $signature_data[0]["Meta"]["description"];
                }
                if (isset($signature_data[0]["Strings"])) {
                    $details = $signature_data[0]["Strings"];
                }
            }
            $error = wtsec_locale("antivirus.status.warning");
            $class = "is--info--carantin";
            $filepath = $list['filePath'];
            if(!$changes){
                $error = wtsec_locale("antivirus.status.critical");
                $class = "is--info--critical";
                $filepath = '<div class="ww-file is--attention" title="" data-tlite=""><img class="ww-file__icon" src="'.wtsec_getImagePath('icon-fire.d93085b8.svg').'" alt="" title="" data-tlite=""><span class="ww-file__name" title="" data-tlite="">'.$list['filePath'].'</span></div>';
            }else{
                $description = wtsec_locale("file_changed");
            }
            ?>
            <div class="ww-result__row" title="" data-tlite="">
                <div class="ww-result__cell" title="" data-tlite=""><?= $filepath ?></div>
                <div class="ww-result__cell" title="" data-tlite=""><?= $signature ?></div>
                <div class="ww-result__cell" title="" data-tlite=""><?= $description ?></div>
                <div class="ww-result__cell" title="" data-tlite=""><span
                            class="is--info <?=$class?>"></span><span><?=$error?></span></div>
            </div>
            <div class="ww-data" title="" data-tlite="">
                <?php foreach ($details as $detail) { ?>
                    <div class="ww-data__item" title="" data-tlite="">
                        <div class="ww-data__name"><?= wtsec_locale("name") ?></div>
                        <div class="ww-data__text"><?= $detail["Name"] ?></div>
                    </div>
                    <div class="ww-data__item" title="" data-tlite="">
                        <div class="ww-data__name" title=""
                             data-tlite=""><?= wtsec_locale("antivirus.offset") ?></div>
                        <div class="ww-data__text" title="" data-tlite=""><?= $detail["Offset"] ?></div>
                    </div>
                    <div class="ww-data__item">
                        <div class="ww-data__name" title=""
                             data-tlite=""><?= wtsec_locale("antivirus.code") ?></div>
                        <div class="ww-data__text" title=""
                             data-tlite=""><?= htmlspecialchars(base64_decode($detail["Data"])) ?></div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
    <a style="text-decoration: none!important;" href="https://wtotem.com" target="_blank">
        <div class="ww-premium">
            <div class="ww-premium__content">
                <div class="ww-premium__title"><?= wtsec_locale("get_a_pro_ask") ?></div>
                <div class="ww-premium__text"><?= wtsec_locale("get_a_pro_desc") ?></div>
            </div>
            <div class="ww-button ww-button--brand" type="button"><?= wtsec_locale("get_a_pro") ?></div>
        </div>
    </a>
</div>



