<?php
defined('_JEXEC') or die;
/** @var JDocumentHtml $this */
$app  = JFactory::getApplication();
$user = JFactory::getUser();
// Output as HTML5
$this->setHtml5(true);
// Getting params from template
$params = $app->getTemplate(true)->params;
$menu = $app->getMenu();
$active = $menu->getActive();
$pageparams = $menu->getParams( $active->id );
$pageclass = $pageparams->get( 'pageclass_sfx' );
// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');
$netparsi = JTEXT::_('NETPARSI');

$lang = JFactory::getLanguage();
$languages = JLanguageHelper::getLanguages('lang_code');
$languageCode = $languages[ $lang->getTag() ]->sef;

// Add Stylesheets
if (JFactory::getLanguage()->isRtl()) {
    JHtml::_('stylesheet', 'uikit-rtl.min.css', array('version' => 'auto', 'relative' => true));
    JHtml::_('stylesheet', 'rtl/orgbattery-rtl.css', array('version' => 'auto', 'relative' => true));
} else {
    JHtml::_('stylesheet', 'uikit.min.css', array('version' => 'auto', 'relative' => true));
    JHtml::_('stylesheet', 'ltr/orgbattery-ltr.css', array('version' => 'auto', 'relative' => true));
}

// Add js
JHtml::_('script', 'uikit.min.js', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'uikit-icons.min.js', array('version' => 'auto', 'relative' => true));
if (JFactory::getLanguage()->isRtl()) {JHtml::_('script', 'persianumber.min.js', array('version' => 'auto', 'relative' => true));}

$socialsicons = json_decode( $params->get('socials'),true);
$total = count($socialsicons['icon']);
?>
<!DOCTYPE html>
<html lang="<?php echo JFactory::getLanguage()->getTag(); ?>" dir="<?php echo JFactory::getLanguage()->isRtl() ? 'rtl' : 'ltr'; ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta name="theme-color" content="<?php echo $params->get('presetcolor'); ?>">
    <jdoc:include type="head" />
</head>
<body class="uk-background-muted <?php echo $view.' '.$layout.' '.$task; ?>">
<?php if ($pageclass != 'checkout') { ?>
<header class="uk-position-relative<?php if ($pageclass == 'home') echo ' uk-position-z-index'; ?>" id="header">
    <div class="uk-text-zero uk-visible@m topBar bgPrimary">
        <div class="uk-container">
            <div>
                <div data-uk-grid>
                    <div class="uk-width-expand"><jdoc:include type="modules" name="user" style="xhtml" /></div>
                    <div class="uk-width-auto uk-flex uk-flex-middle">
                        <div>
                            <div class="uk-grid-small uk-grid-divider" data-uk-grid>
                                <div class="uk-flex uk-flex-middle">
                                    <ul class="uk-grid-small socials" data-uk-grid>
                                        <?php for($i=0;$i<$total;$i++) { ?>
                                            <?php if ($socialsicons['link'][$i] != '') { ?>
                                                <li>
                                                    <a href="<?php echo $socialsicons['link'][$i]; ?>" class="uk-text-white hoverWhite" target="_blank" title="<?php echo $socialsicons['title'][$i]; ?>"><img src="<?php echo JURI::base().'images/sprite.svg#'.$socialsicons['icon'][$i] ?>" width="18" height="18" data-uk-svg></a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <jdoc:include type="modules" name="lang" style="xhtml" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-text-zero uk-box-shadow-small bgWhite stickyHeader" data-uk-sticky="top: 120; animation: uk-animation-slide-top;">
        <div class="uk-container">
            <div class="stickyEffect">
                <div class="uk-padding-small uk-padding-remove-horizontal">
                    <div class="uk-grid-small uk-flex-middle" data-uk-grid>
                        <div class="uk-width-auto uk-hidden@m">
                            <div>
                                <a href="#hamMenu" data-uk-toggle class="uk-text-muted uk-border-rounded uk-button uk-button-default uk-button-small uk-display-block uk-text-muted uk-text-zero hamMenuToggler"><img src="<?php echo JURI::base().'images/sprite.svg#bars'; ?>" width="20" height="20" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                            </div>
                        </div>
                        <div class="uk-width-auto">
                            <div>
                                <div class="uk-grid-small logoContainer" data-uk-grid>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="<?php echo JURI::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-text-accent hoverAccent" target="_self"><img src="<?php echo $params->get('logo'); ?>" width="100" height="38" alt="<?php echo $sitename; ?>"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-left">
                            <div>
                                <div class="uk-grid-divider uk-grid-small" data-uk-grid><jdoc:include type="modules" name="header" style="xhtml" /></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<?php } ?>
<?php if ($pageclass == 'checkout') { ?>
    <header class="uk-position-relative">
        <div class="uk-text-zero uk-box-shadow-small bgWhite stickyHeader" data-uk-sticky="top: 120; animation: uk-animation-slide-top;">
            <div class="uk-container">
                <div class="stickyEffect">
                    <div class="uk-padding-small uk-padding-remove-horizontal">
                        <div class="uk-grid-small uk-flex-middle uk-flex-center" data-uk-grid>
                            <div class="uk-width-auto uk-hidden@m">
                                <div>
                                    <a href="#hamMenu" data-uk-toggle class="uk-text-muted uk-border-rounded uk-button uk-button-default uk-button-small uk-display-block uk-text-muted uk-text-zero hamMenuToggler"><img src="<?php echo JURI::base().'images/sprite.svg#bars'; ?>" width="20" height="20" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                                </div>
                            </div>
                            <div class="uk-width-auto">
                                <div>
                                    <div class="uk-grid-small logoContainer" data-uk-grid>
                                        <div class="uk-width-auto uk-flex uk-flex-middle uk-visible@m">
                                            <a href="<?php echo JURI::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-text-accent hoverAccent" target="_self"><img src="<?php echo $params->get('logo'); ?>" width="100" height="38" alt="<?php echo $sitename; ?>"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="uk-width-expand uk-flex uk-flex-middle uk-flex-left uk-hidden">
                                <a href="<?php echo JURI::base(); ?>" title="" class="uk-text-tiny uk-text-bold uk-text-muted font">
                                    <span><?php echo JText::sprintf('BACK'); ?></span>
                                    <span class="uk-visible@m"><?php echo JText::sprintf('TOSHOP'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
<?php } ?>
<?php if ($pageparams->get('show_page_heading')) : ?>
    <section class="bgPrimary uk-padding uk-padding-remove-horizontal uk-text-zero pageHeading">
        <div class="uk-container">
            <div>
                <div class="uk-grid-small" data-uk-grid>
                    <div class="uk-width-1-1">
                        <h1 class="uk-margin-remove uk-text-white uk-text-center font"><?php echo $pageparams->get('page_heading'); ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<jdoc:include type="modules" name="pagetop" style="xhtml" />
<jdoc:include type="message" />
<main class="uk-overflow-hidden">
    <div class="uk-padding uk-padding-remove-horizontal">
        <jdoc:include type="modules" name="bodytop" style="xhtml" />
        <div>
            <div class="<?php echo $pageparams->get('gridsize', 'uk-container'); if ($pageclass == 'checkout') { echo ' uk-container-xsmall';} ?> ">
                <div class="hikashop_cpanel_main_interface">
                    <div class="hikashop_dashboard" id="hikashop_dashboard" data-uk-grid>
                        <?php if ($this->countModules( 'sidestart' )) { ?>
                            <div class="uk-width-1-1 uk-width-1-4@m">
                                <aside data-uk-sticky="offset: 110; bottom: true;">
                                    <div>
                                        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid><jdoc:include type="modules" name="sidestart" style="xhtml" /></div>
                                    </div>
                                </aside>
                            </div>
                        <?php } ?>
                        <div class="uk-width-1-1 uk-width-expand@m">
                            <div><jdoc:include type="component" /></div>
                        </div>
                        <?php if ($this->countModules( 'sideend' )) { ?>
                            <div class="uk-width-1-1 uk-width-1-4@m">
                                <aside data-uk-sticky="offset: 110; bottom: true;">
                                    <div>
                                        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid><jdoc:include type="modules" name="sideend" style="xhtml" /></div>
                                    </div>
                                </aside>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <jdoc:include type="modules" name="bodybottom" style="xhtml" />
    </div>
</main>
<jdoc:include type="modules" name="pagebottom" style="xhtml" />
<?php if ($pageclass != 'checkout') { ?>
<footer class="uk-text-zero">
    <div class="socials">
        <div class="uk-container">
            <div>
                <div data-uk-grid>
                    <div class="uk-width-1-1 uk-width-1-3@m uk-flex-last uk-flex-first@m">
                        <div class="uk-padding-small bgAccent footerSocials">
                            <div class="uk-grid-small uk-child-width-auto uk-flex-center" data-uk-grid>
                                <div class="uk-flex uk-flex-middle"><span class="uk-text-white uk-text-small font"><?php echo JTEXT::_('FOLLOWUS'); ?></span></div>
                                <div>
                                    <div>
                                        <div class="uk-child-width-auto uk-grid-small" data-uk-grid>
                                            <?php for($i=0;$i<$total;$i++) { ?>
                                                <?php if ($socialsicons['link'][$i] != '') { ?>
                                                    <div>
                                                        <a href="<?php echo $socialsicons['link'][$i]; ?>" class="uk-text-white hoverWhite" target="_blank" title="<?php echo $socialsicons['title'][$i]; ?>" data-uk-tooltip="offset: 28"><img src="<?php echo JURI::base().'images/sprite.svg#'.$socialsicons['icon'][$i] ?>" width="24" height="24" data-uk-svg></a>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($this->countModules( 'breadcrumbs' )) { ?>
                        <div class="uk-width-1-1 uk-width-expand@m uk-text-small uk-flex uk-flex-left uk-visible@m"><jdoc:include type="modules" name="breadcrumbs" style="xhtml" /></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-padding uk-padding-remove-horizontal uk-padding-remove-top footer bgDark">
        <div class="uk-container">
            <div data-uk-grid>
                <div class="uk-width-1-1 uk-width-1-3@m">
                    <div>
                        <div class="uk-padding bgWhite footerContact">
                            <div>
                                <div class="uk-child-width-1-1 uk-grid-medium" data-uk-grid>
                                    <div class="uk-hidden">
                                        <a href="<?php echo JURI::base(); ?>" title="<?php echo $sitename; ?>" class="uk-display-inline-block uk-text-primary hoverPrimary" target="_self"><img src="<?php echo JURI::base().'images/sprite.svg#logo'.$languageCode; ?>" width="150" alt="<?php echo $sitename; ?>" data-uk-svg></a>
                                    </div>
                                    <?php if (!empty($params->get('address')) || !empty($params->get('phone')) || !empty($params->get('fax')) || !empty($params->get('cellphone')) || !empty($params->get('email'))) { ?>
                                        <div>
                                            <div>
                                                <div class="uk-child-width-1-1 uk-grid-divider uk-grid-small" data-uk-grid>
                                                    <?php if (!empty($params->get('address'))) { ?>
                                                        <div>
                                                            <div>
                                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#map' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                    <div class="uk-width-expand"><span class="uk-text-tiny uk-text-primary value font"><?php echo $params->get('address'); ?></span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($params->get('phone')) || !empty($params->get('fax')) || !empty($params->get('cellphone'))) { ?>
                                                        <div>
                                                            <div>
                                                                <div class="uk-child-width-1-2" data-uk-grid>
                                                                    <?php if (!empty($params->get('phone'))) { ?>
                                                                        <div>
                                                                            <div>
                                                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#phone' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                                    <div class="uk-width-expand"><span class="uk-text-small uk-text-primary value font"><?php echo nl2br($params->get('phone')); ?></span></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <?php if (!empty($params->get('fax')) || !empty($params->get('cellphone'))) { ?>
                                                                        <div>
                                                                            <div>
                                                                                <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
                                                                                    <?php if (!empty($params->get('fax'))) { ?>
                                                                                        <div>
                                                                                            <div class="uk-grid-small contactFields" data-uk-grid>
                                                                                                <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#fax' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                                                <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-primary value font"><?php echo $params->get('fax'); ?></span></div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php } ?>
                                                                                    <?php if (!empty($params->get('cellphone'))) { ?>
                                                                                        <div>
                                                                                            <div class="uk-grid-small contactFields" data-uk-grid>
                                                                                                <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#mobile' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                                                <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-primary value font"><?php echo $params->get('cellphone'); ?></span></div>
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
                                                    <?php } ?>
                                                    <?php if (!empty($params->get('email'))) { ?>
                                                        <div>
                                                            <div>
                                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#envelope' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                                    <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-primary value font"><?php echo $params->get('email'); ?></span></div>
                                                                </div>
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
                <div class="uk-width-1-1 uk-width-2-3@m">
                    <div>
                        <div class="uk-padding uk-padding-remove-horizontal uk-padding-remove-bottom modulesWrapper">
                            <div>
                                <div class="uk-child-width-1-1 uk-child-width-1-2@m" data-uk-grid>
                                    <jdoc:include type="modules" name="footer" style="xhtml" />
                                    <div class="uk-flex uk-flex-column uk-flex-between">
                                        <div class="uk-padding-small uk-text-center uk-border-rounded uk-box-shadow-small enamadWrapper">
                                            <div class="uk-child-width-1-3 uk-grid-small" data-uk-grid>
                                                <div><img src="images/enamad.png" width="125" height="136"></div>
                                                <div><img src="images/enamad.png" width="125" height="136"></div>
                                                <div><img src="images/enamad.png" width="125" height="136"></div>
                                            </div>
                                        </div>
                                        <div class="uk-text-left uk-visible@m">
                                            <a class="transition uk-flex uk-flex-middle uk-flex-left font goToTop" href="#header" data-uk-scroll>
                                                <span><img src="<?php echo JURI::base().'images/sprite.svg#chevron-circle-up' ?>" width="12" height="12" alt="" data-uk-svg></span>
                                                <span class="uk-text-tiny">&ensp;<?php echo JTEXT::_('GOTOTOP'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright bgPrimary">
        <div class="uk-container">
            <div class="uk-padding-small uk-padding-remove-horizontal">
                <div class="uk-grid-small uk-text-white uk-text-center uk-text-<?php echo JFactory::getLanguage()->isRtl() ? 'right' : 'left' ?>@m font" data-uk-grid>
                    <div class="uk-width-1-1 uk-width-expand@m">
                        <p class="uk-text-tiny"><?php echo JTEXT::sprintf('COPYRIGHT', $sitename); ?></p>
                    </div>
                    <div class="uk-width-1-1 uk-width-auto@m">
                        <p class="uk-text-tiny"><?php echo JTEXT::sprintf('DESIGNER', '<a href="https://netparsi.com" class="uk-text-white hoverAccent" target="_blank" title="'.$netparsi.'">'.$netparsi.'</a>'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php } ?>
<?php if ($pageclass == 'checkout') { ?>
    <footer class="uk-text-zero uk-padding-small uk-padding-remove-horizontal">
        <div class="copyright">
            <div class="uk-container">
                <div class="uk-padding-small uk-padding-remove-horizontal uk-padding-remove-top">
                    <div class="uk-grid-small uk-text-white uk-text-center font" data-uk-grid>
                        <div class="uk-width-1-1">
                            <div>
                                <div class="uk-child-width-auto uk-grid-small uk-flex-center uk-grid-divider" data-uk-grid>
                                    <?php if (!empty($params->get('phone'))) { ?>
                                        <div>
                                            <div>
                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#phone' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                    <div class="uk-width-expand"><span class="uk-text-small uk-text-gray value font"><?php echo $params->get('phone'); ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($params->get('email'))) { ?>
                                        <div>
                                            <div>
                                                <div class="uk-grid-small contactFields" data-uk-grid>
                                                    <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#envelope' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                    <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-gray value font"><?php echo $params->get('email'); ?></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($params->get('cellphone'))) { ?>
                                        <div>
                                            <div class="uk-grid-small contactFields" data-uk-grid>
                                                <div class="uk-width-auto uk-text-accent"><img src="<?php echo JURI::base().'images/sprite.svg#mobile' ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                <div class="uk-width-expand uk-flex uk-flex-middle"><span class="uk-text-small uk-text-gray value font"><?php echo $params->get('cellphone'); ?></span></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <p class="uk-text-tiny uk-text-gray"><?php echo JTEXT::sprintf('COPYRIGHT', $sitename); ?></p>
                        </div>
                        <div class="uk-width-1-1">
                            <p class="uk-text-tiny uk-text-gray uk-margin-bottom"><?php echo JTEXT::sprintf('DESIGNER', '<a href="https://netparsi.com" class="uk-text-gray hoverAccent" target="_blank" title="'.$netparsi.'">'.$netparsi.'</a>'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
<?php } ?>

<div id="hamMenu" data-uk-offcanvas="overlay: true">
    <div class="uk-offcanvas-bar uk-card uk-card-default uk-padding-remove bgWhite">
        <div class="uk-flex uk-flex-column uk-height-1-1">
            <div class="uk-width-expand">
                <div class="offcanvasTop uk-box-shadow-small uk-position-relative uk-flex-stretch">
                    <div class="uk-grid-collapse uk-height-1-1 uk-grid uk-grid-stack" data-uk-grid="">
                        <div class="uk-flex uk-width-1-3 uk-flex uk-flex-center uk-flex-middle"><a onclick="UIkit.offcanvas('#hamMenu').hide();" class="uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-width-1-1 uk-margin-remove"><img src="<?php echo JURI::base().'images/sprite.svg#chevron-right'; ?>" width="24" height="24" data-uk-svg></a></div>
                        <div class="uk-flex uk-width-1-3 uk-flex uk-flex-center uk-flex-middle"><a href="<?php echo JRoute::_("index.php?Itemid=167"); ?>" class="uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-width-1-1 uk-margin-remove"><img src="<?php echo JURI::base().'images/sprite.svg#shopping-cart'; ?>" width="24" height="24" data-uk-svg></a></div>
                        <div class="uk-flex uk-width-1-3 uk-flex uk-flex-center uk-flex-middle"><a href="" class="uk-flex uk-flex-center uk-flex-middle uk-height-1-1 uk-width-1-1 uk-margin-remove"><img src="<?php echo JURI::base().'images/sprite.svg#phone'; ?>" width="24" height="24" data-uk-svg></a></div>
                    </div>
                </div>
                <div class="uk-padding-small"><jdoc:include type="modules" name="offcanvas" style="xhtml" /></div>
            </div>
        </div>
    </div>
</div>
</body>
</html>