     <div class="ww-main ww-main--grid">
                <div class="ww-main__graph">
                    <div class="ww-title"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-shield-check.432cf8d4.svg")?>"
                                               alt=""><span><?=wtotemsec_locale("firewall")?></span>
                        <?php if(isset($arguments["waf"]["description"])){?>
                        <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["waf"]["description"]?>"></div>
                        <?php } ?>
                    </div>
                    <div class="ww-graph ww-graph--line">
                        <div class="ww-graph__name"><?=wtotemsec_locale("attacks_blocked_weekly")?></div><canvas id="lineChart" width="420"
                                                                                       height="200"></canvas>
                    </div>
                </div>
                <div class="ww-main__stats">
                    <div class="ww-title"><span><?=wtotemsec_locale("stats")?></span></div>
                    <div class="ww-info ww-info--column">
                        <div class="ww-info__item">
                            <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                            <div class="ww-info__value"><?=$arguments["waf"]["status"]["text"]?></div>
                        </div>
                        <div class="ww-info__item">
                            <div class="ww-info__name"><?=wtotemsec_locale("last_test")?></div>
                            <div class="ww-info__value"><?=$arguments["waf"]["time_of_the_last_check"]?></div>
                        </div>
                        <div class="ww-info__item ww-info__item--highlight">
                            <div class="ww-info__name"><?=wtotemsec_locale("attacks_blocked")?></div>
                            <div class="ww-info__value"><?=$arguments["waf"]["signatures"]?></div>
                        </div>
                    </div>
                    <?php if(isset($arguments['waf']['actions']['first'])){?>
                    <?=$arguments['waf']['actions']['first']?>
                    <?php }?>
                    <?php if(isset($arguments['waf']['actions']['second'])){?>
                        <?=$arguments['waf']['actions']['second']?>
                    <?php }?>
                </div>
            </div>
            <div class="ww-grid ww-grid--small-box">
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-diagnostic.3882a66e.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("availability")?></span>
                                <?php if(isset($arguments["wa"]["description"])){?>
                                <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["wa"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["wa"]["pause"]?>
                        </div>
                        <div class="ww-graph ww-graph--circle"><canvas id="circleChart" width="120" height="45"></canvas>
                            <div class="ww-graph__info">
                                <div class="ww-graph__percent"><?=$arguments["wa"]["availability_percent"]?>%</div>
                                <div class="ww-graph__title"><?=wtotemsec_locale("available")?></div>
                            </div>
                        </div>
                        <div class="ww-info ww-info--column">
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-info__value"><?=$arguments["wa"]["status"]["text"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("downtime")?></div>
                                <div class="ww-info__value"><?=$arguments["wa"]["downtime"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("response_time")?></div>
                                <div class="ww-info__value"><?=$arguments["wa"]["response_time"]?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-ssl.e6bef731.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("ssl")?></span>
                                <?php if(isset($arguments["ssl"]["description"])){?>
                                    <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["ssl"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["ssl"]["pause"]?>
                        </div>
                        <div class="ww-clipboard <?=$arguments["ssl"]["status"]["color"]?>">
                            <div class="ww-clipboard__icon">
                                <svg width="45" height="42" viewBox="0 0 45 42" fill="none"
                                                                 xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M29.8612 30.625C29.8612 29.6585 30.5743 28.875 31.4538 28.875H37.8242C38.7038 28.875 39.4168 29.6585 39.4168 30.625V40.8859C39.4168 41.6473 38.5932 42.0454 38.0824 41.5309L35.1771 38.6045C34.8726 38.2978 34.4054 38.2978 34.1009 38.6045L31.1956 41.5309C30.6849 42.0454 29.8612 41.6473 29.8612 40.8859V30.625Z"
                                          class="ww-clipboard__path <?=$arguments["ssl"]["status"]["color"]?>"></path>
                                    <path
                                            d="M8.95829 5.25H14.3333V6.125C14.3333 7.57475 15.5365 8.75 17.0208 8.75H25.9791C27.4634 8.75 28.6666 7.57475 28.6666 6.125V5.25H34.0416C35.0311 5.25 35.8333 6.0335 35.8333 7V25.125C35.8333 26.2296 34.9379 27.125 33.8333 27.125H30.7708C29.1139 27.125 27.7708 28.4681 27.7708 30.125V38.25C27.7708 39.3546 26.8754 40.25 25.7708 40.25H8.95829C7.96878 40.25 7.16663 39.4665 7.16663 38.5V7C7.16663 6.0335 7.96878 5.25 8.95829 5.25Z"
                                            class="ww-clipboard__path <?=$arguments["ssl"]["status"]["color"]?>"></path>
                                    <path opacity="0.4"
                                          d="M23.2117 22.6823C22.9202 22.3975 22.9202 21.9358 23.2117 21.6511C23.5033 21.3663 23.9759 21.3663 24.2675 21.6511L27.2536 24.5677C27.5451 24.8525 27.5451 25.3142 27.2536 25.5989C26.962 25.8837 26.4894 25.8837 26.1978 25.5989L23.2117 22.6823Z"
                                          fill="white"></path>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                          d="M15.5278 18.5208C15.5278 21.3398 17.8674 23.625 20.7535 23.625C23.6396 23.625 25.9792 21.3398 25.9792 18.5208C25.9792 15.7019 23.6396 13.4167 20.7535 13.4167C17.8674 13.4167 15.5278 15.7019 15.5278 18.5208ZM24.4862 18.5208C24.4862 20.5344 22.815 22.1667 20.7535 22.1667C18.692 22.1667 17.0209 20.5344 17.0209 18.5208C17.0209 16.5073 18.692 14.875 20.7535 14.875C22.815 14.875 24.4862 16.5073 24.4862 18.5208Z"
                                          fill="white"></path>
                                    <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd"
                                          d="M23.2916 3.50001C23.2916 3.5 23.2916 3.5 23.2916 3.5C23.2916 2.5335 22.4895 1.75 21.5 1.75C20.5105 1.75 19.7083 2.5335 19.7083 3.5C19.7083 3.5 19.7083 3.5 19.7083 3.50001H16.625C16.3489 3.50001 16.125 3.72386 16.125 4.00001V6.50001C16.125 6.77615 16.3489 7.00001 16.625 7.00001H26.375C26.6511 7.00001 26.875 6.77615 26.875 6.50001V4.00001C26.875 3.72386 26.6511 3.50001 26.375 3.50001H23.2916Z"
                                          class="ww-clipboard__path <?=$arguments["ssl"]["status"]["color"]?>"></path>
                                </svg>
                            </div>
                            <div class="ww-clipboard__name"><?=$arguments["ssl"]["status"]["text"]?></div>
                        </div>
                        <div class="ww-info ww-info--column">
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-info__value"><?=$arguments["ssl"]["status"]["text"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("issue_date")?></div>
                                <div class="ww-info__value"><?=$arguments["ssl"]["issue_date"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("expiry_date")?></div>
                                <div class="ww-info__value"><?=$arguments["ssl"]["expiry_date"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("days_left")?></div>
                                <div class="ww-info__value"><?=$arguments["ssl"]["days_left"]?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-shield.0208ca15.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("domain")?></span>
                                <?php if(isset($arguments["dec"]["description"])){?>
                                <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["dec"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["dec"]["pause"]?>
                        </div>
                        <div class="ww-domain">
                            <div class="ww-domain__item">
                                <div class="ww-domain__name"><?=wtotemsec_locale("days_left")?></div>
                                <div class="ww-domain__value ww-domain__value--numb"><?=$arguments["dec"]["days_left"]?></div>
                            </div>
                            <div class="ww-domain__item">
                                <div class="ww-domain__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-domain__value">
                                    <div class="ww-icon ww-icon--status <?=$arguments["dec"]["status"]["icon"]?>"></div>
                                </div>
                            </div>
                            <div class="ww-domain__item">
                                <div class="ww-domain__name"><?=wtotemsec_locale("expiry_date")?></div>
                                <div class="ww-domain__value is--status--ok"><?=$arguments["dec"]["expiry_date"]?></div>
                            </div>
                        </div>
                        <div class="ww-info ww-info--column">
                            <?php if(isset($arguments["dec"]["registrar"])){?>
                                <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("registrar")?></div>
                                <div class="ww-info__value ww-info__value--small"><?=$arguments["dec"]["registrar"]?></div>
                            </div>
                            <?php } ?>
                            <?php if(isset($arguments["dec"]["owner"])){?>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("owner")?></div>
                                <div class="ww-info__value ww-info__value--small"><?=$arguments["dec"]["owner"]?></div>
                            </div>
                            <?php } ?>
                            <?php if(isset($arguments["dec"]["email"])){?>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("email")?></div>
                                <div class="ww-info__value ww-info__value--small"><?=$arguments["dec"]["email"]?></div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-star.ce261205.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("reputation")?></span>
                                <?php if(isset($arguments["av"]["description"])){?>
                                <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["av"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["av"]["pause"]?>
                        </div>
                        <div class="ww-info ww-info--column">
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-info__value"><?=$arguments["av"]["status"]["text"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("blacklists_entries")?></div>
                                <div class="ww-info__value"><?=$arguments["av"]["blacklists_entries"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("last_test")?></div>
                                <div class="ww-info__value"><?=$arguments["av"]["time_of_the_last_test"]?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-script.b34b3af7.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("malicious_scripts")?></span>
                                <?php if(isset($arguments["cms"]["description"])){?>
                                <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["cms"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["cms"]["pause"]?>
                        </div>
                        <div class="ww-info ww-info--column">
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-info__value"><?=$arguments["cms"]["status"]["text"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("last_test")?></div>
                                <div class="ww-info__value"><?=$arguments["cms"]["time_of_the_last_test"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("detected_keywords")?></div>
                                <div class="ww-info__value <?=$arguments["cms"]["detected_keywords"] > 0 ?'ww-info__value--bg' : ''?>"><?=$arguments["cms"]["detected_keywords"]?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-script.b34b3af7.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("deface_scanner")?></span>
                                <?php if(isset($arguments["dc"]["description"])){?>
                                <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["dc"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["dc"]["pause"]?>
                        </div>
                        <div class="ww-info ww-info--column">
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-info__value is--status--warning"><?=$arguments["dc"]["status"]["text"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("last_test")?></div>
                                <div class="ww-info__value"><?=$arguments["dc"]["time_of_the_last_test"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("number")?></div>
                                <div class="ww-info__value <?=$arguments["dc"]["number"] > 0 ?'ww-info__value--bg' : ''?>"><?=$arguments["dc"]["number"]?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ww-grid__3">
                    <div class="ww-main ww-main--small">
                        <div class="ww-content__header">
                            <div class="ww-title ww-title--small"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-antivirus.2c163fba.svg")?>"
                                                                       alt=""><span><?=wtotemsec_locale("remote_antivirus")?></span>
                                <?php if(isset($arguments["vc"]["description"])){?>
                                <div class="ww-icon ww-icon--info js--tooltip" data-tlite="w" title="<?=$arguments["vc"]["description"]?>"></div>
                                <?php } ?>
                            </div>
                            <?=$arguments["vc"]["pause"]?>
                        </div>
                        <div class="ww-info ww-info--column antivirus_form">
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("status")?></div>
                                <div class="ww-info__value"><?=$arguments["vc"]["status"]["text"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("signatures")?><?php if($arguments["vc"]["signatures"] > 0){?>
                                    <a href="<?=wtotemsec_getUrl('vc') ?>" class="eye-vc"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-eye.5d74dcb4.svg")?>" alt=""></a><?php } ?></div>
                                <div class="ww-info__value <?=$arguments["vc"]["signatures"] > 0 ?'ww-info__value--bg' : ''?>"><?=$arguments["vc"]["signatures"]?></div>
                            </div>
                            <div class="ww-info__item">
                                <div class="ww-info__name"><?=wtotemsec_locale("changes")?><?php if($arguments["vc"]["changes"] > 0){?>
                                    <a href="<?=wtotemsec_getUrl('vc') ?>" class="eye-vc"><img class="ww-icon" src="<?=wtotemsec_getImagePath("icon-eye.5d74dcb4.svg")?>" alt=""></a><?php } ?></div>
                                <div class="ww-info__value"><?=$arguments["vc"]["changes"]?></div>
                            </div>
                            <?php if(isset($arguments['vc']['actions']['first'])){?>
                                <?=$arguments['vc']['actions']['first']?>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
<script>
    var availability_percent = "<?=$arguments["wa"]["availability_percent"]?>";
    var waf_chart = <?=$arguments["waf"]["chart"]?>;
</script>