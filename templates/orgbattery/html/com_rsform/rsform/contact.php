<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');




$app  = JFactory::getApplication();
$user = JFactory::getUser();
// Getting params from template
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();
$pageparams = $menu->getParams( $active->id );
$pageclass = $pageparams->get( 'pageclass_sfx' );
// Detecting Active Variables
$itemid   = $app->input->getCmd('Itemid', '');

$socialsicons = json_decode( $params->get('socials'),true);
$total = count($socialsicons['icon']);


?>
<div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
    <div class="uk-padding">
        <div class="uk-grid-divider" data-uk-grid>
            <div class="uk-width-1-1 uk-width-expand@m"><?php echo RSFormProHelper::displayForm($this->formId); ?></div>
            <div class="uk-width-1-1 uk-width-1-3@m">
                <div>
                    <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                        <div>
                            <h2 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h4 font"><?php echo JText::sprintf('CONTACTINFO'); ?></h2>
                            <div>
                                <div>
                                    <div>
                                        <div class="uk-child-width-1-1 uk-grid-medium" data-uk-grid>
                                            <?php if (!empty($params->get('address')) || !empty($params->get('phone')) || !empty($params->get('fax')) || !empty($params->get('cellphone')) || !empty($params->get('email'))) { ?>
                                                <div>
                                                    <div>
                                                        <div class="uk-grid-small uk-text-zero" data-uk-grid>
                                                            <?php if (!empty($params->get('address'))) { ?>
                                                                <div class="uk-width-1-1">
                                                                    <div>
                                                                        <div class="uk-grid-small contactFields" data-uk-grid>
                                                                            <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#map' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                            <div class="uk-width-expand"><span class="uk-text-tiny uk-text-primary uk-text-bold value font"><?php echo $params->get('address'); ?></span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (!empty($params->get('phone'))) { ?>
                                                                <div class="uk-width-1-1 uk-width-auto@m">
                                                                    <div>
                                                                        <div class="uk-grid-small contactFields" data-uk-grid>
                                                                            <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#phone' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                            <div class="uk-width-expand"><span class="uk-text-small uk-text-primary uk-text-bold value font"><?php $array = preg_split('/\n|\r\n/', $params->get('phone')); echo $array[0]; ?></span></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (!empty($params->get('fax'))) { ?>
                                                                <div class="uk-width-1-1 uk-width-auto@m">
                                                                    <div class="uk-grid-small contactFields" data-uk-grid>
                                                                        <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#fax' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                        <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-primary uk-text-bold value font"><?php echo $params->get('fax'); ?></span></div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (!empty($params->get('cellphone'))) { ?>
                                                                <div class="uk-width-1-1 uk-width-auto@m">
                                                                    <div class="uk-grid-small contactFields" data-uk-grid>
                                                                        <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#mobile' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                        <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-primary uk-text-bold value font"><?php echo $params->get('cellphone'); ?></span></div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($params->get('lat')) && !empty($params->get('lng'))) { ?>
                            <div>
                                <h2 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h4 font"><?php echo JText::sprintf('PATHFINDER'); ?></h2>
                                <div>
                                    <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m" data-uk-grid>
                                        <div><a href="https://waze.com/ul?ll=<?php echo $params->get('lat'); ?>,<?php echo $params->get('lng'); ?>&navigate=yes" class="uk-width-1-1 uk-padding-small uk-button uk-button-default uk-border-rounded uk-text-zero" target="_blank"><img src="<?php echo JURI::base().'images/waze-logo.svg' ?>" width="100" alt=""></a></div>
                                        <div><a href="http://maps.google.com/maps?daddr=<?php echo $params->get('lat'); ?>,<?php echo $params->get('lng'); ?>" class="uk-width-1-1 uk-padding-small uk-button uk-button-default uk-border-rounded uk-text-zero" target="_blank"><img src="<?php echo JURI::base().'images/google-maps-logo.svg'; ?>" width="100" alt=""></a></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div>
                            <h2 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h4 uk-hidden@m font"><?php echo JText::sprintf('SOCIALMEDIA'); ?></h2>
                            <div>
                                <div class="uk-grid-small uk-child-width-1-3 uk-child-width-1-5@m uk-flex-center" data-uk-grid>
                                    <?php for($i=0;$i<$total;$i++) { ?>
                                        <?php if ($socialsicons['link'][$i] != '') { ?>
                                            <div><a href="<?php echo $socialsicons['link'][$i]; ?>" title="<?php echo $socialsicons['title'][$i]; ?>" class="uk-width-1-1 uk-padding-small uk-button uk-border-rounded uk-text-zero uk-lineheight-zero uk-button-<?php echo $socialsicons['icon'][$i]; ?>" target="_blank"><img src="<?php echo JURI::base().'images/sprite.svg#'.$socialsicons['icon'][$i]; ?>" alt="<?php echo $socialsicons['title'][$i]; ?>" width="20" height="20" data-uk-svg></a></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>