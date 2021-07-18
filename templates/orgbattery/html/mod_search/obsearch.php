<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Including fallback code for the placeholder attribute in the search field.
JHtml::_('jquery.framework');
JHtml::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));

if ($width)
{
	$moduleclass_sfx .= ' ' . 'mod_search' . $module->id;
	$css = 'div.mod_search' . $module->id . ' input[type="search"]{ width:auto; }';
	JFactory::getDocument()->addStyleDeclaration($css);
	$width = ' size="' . $width . '"';
}
else
{
	$width = '';
}
?>
<div class="uk-flex uk-flex-middle uk-height-1-1 search<?php echo $moduleclass_sfx; ?>">
    <a href="#" class="searchToggler uk-text-gray hoverAccent transition"><img src="<?php echo JURI::base().'images/sprite.svg#search' ?>" width="18" height="18" data-uk-svg></a>
    <div class="searchDrop" data-uk-drop="mode:click;offset:25;delay-hide:200;animation:uk-animation-slide-bottom-small;duration:200;pos:bottom-left;">
        <div class="uk-card uk-card-body uk-card-default uk-padding-remove uk-box-shadow-small">
            <div class="uk-padding-small searchModContainer">
                <form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="uk-search uk-search-default uk-display-block uk-width-1-1 form-inline" role="search">
                    <?php
                    $output = '<input type="search" name="searchword" id="mod-search-searchword' . $module->id . '" class="uk-search-input uk-padding-small uk-width-1-1 uk-text-small uk-text-dark font inputbox search-query input-medium" type="search"';
                    $output .= ' placeholder="' . $text . '" />';

                    if ($button) :
                        if ($imagebutton) :
                            $btn_output = ' <input type="image" alt="' . $button_text . '" class="button" src="' . $img . '" onclick="this.form.searchword.focus();"/>';
                        else :
                            $btn_output = ' <button class="button btn btn-primary" onclick="this.form.searchword.focus();">' . $button_text . '</button>';
                        endif;

                        switch ($button_pos) :
                            case 'top' :
                                $output = $btn_output . '<br />' . $output;
                                break;

                            case 'bottom' :
                                $output .= '<br />' . $btn_output;
                                break;

                            case 'right' :
                                $output .= $btn_output;
                                break;

                            case 'left' :
                            default :
                                $output = $btn_output . $output;
                                break;
                        endswitch;
                    endif;

                    echo $output;
                    ?>
                    <input type="hidden" name="task" value="search" />
                    <input type="hidden" name="option" value="com_search" />
                    <input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
                </form>
            </div>
        </div>
    </div>
</div>