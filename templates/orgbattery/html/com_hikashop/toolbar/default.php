<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php if(empty($this->data)) return; ?>
<?php $toolbar_classname = $this->config->get('front_toolbar_btn_classname', 'hikabtn'); ?>
<?php if (empty($toolbar_classname)) $toolbar_classname = 'hikabtn'; ?>
<div class="uk-text-zero">
    <div class="uk-grid-small" data-uk-grid>
        <?php foreach($this->data as $key => $tool) { ?>
            <?php if (empty($tool['url']) && !empty($tool['sep'])) { ?>
                <?php continue; ?>
            <?php } ?>
        <?php

	$content = '';
	if(!empty($tool['icon'])) { $content .= '<span><img src="'.JURI::base().'images/sprite.svg#'.$tool['icon'].'" width="16" height="16" alt=""></span>'; }
	if(!empty($tool['fa'])) {
		$fa_size = !empty($tool['fa']['size']) ? (int)$tool['fa']['size'] : 2;
		$fa_stack = is_array($tool['fa']['html']) ? 'fa-stack ': '';
		$fa_content = is_array($tool['fa']['html']) ? implode('', $tool['fa']['html']) : $tool['fa']['html'];

		$content = '';

		$tool['dropdown']['options']['fa'] = $tool['fa'];
	}
	if(!empty($tool['name'])) { $content .=  '<span>' . $tool['name'] . '</span>'; }

	if(!empty($tool['url']) || !empty($tool['javascript'])) {
		if(empty($tool['popup'])) {
			if(empty($tool['url']))
				$tool['url'] = '#';
			if(empty($tool['linkattribs']))
				$tool['linkattribs'] = '';
			if(!empty($tool['javascript']))
				$tool['linkattribs'].= ' onclick="' . $tool['javascript'] . '"';
			echo '<div class="'.$tool['grid'].'"><a class="uk-button '.$tool['class'].' uk-border-rounded uk-width-1-1 uk-width-medium@m font" href="'.$tool['url'].'" '.$tool['linkattribs'].'>' . $content . '</a></div>';
		} else {
			$attr = $this->popupHelper->getAttr(@$tool['linkattribs'], 'uk-button uk-button uk-button-default uk-width-1-1 uk-border-rounded font uk-border-rounded uk-width-1-1 uk-width-medium@m font');
			echo '<div class="uk-width-1-1 uk-width-1-3@m">'.$this->popupHelper->display(
				$content,
				@$tool['name'],
				$tool['url'],
				$tool['popup']['id'],
				$tool['popup']['width'],
				$tool['popup']['height'],
				$attr, '', 'link'
			).'</div>';
		}
	}elseif(!empty($tool['dropdown'])) {
		if(is_array($tool['dropdown'])) {
			$tool['dropdown']['options']['main_class'] = $toolbar_classname;
			echo $this->dropdownHelper->display(
				$tool['dropdown']['label'],
				$tool['dropdown']['data'],
				$tool['dropdown']['options']
			);
		}else {
			echo $tool['dropdown'];
		}
	}else {
		echo '<div data-fffffff>' . $content . '</div>';
	}
	unset($content);
}
?>
	</div>
</div>