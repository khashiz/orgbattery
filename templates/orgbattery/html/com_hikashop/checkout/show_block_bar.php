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
<div class="stepsWrapper uk-margin-medium-bottom">
	<div class="uk-child-width-1-3 uk-text-center uk-grid-collapse uk-text-zero" data-uk-grid>
<?php
	$workflow = $this->checkoutHelper->checkout_workflow;
	foreach($workflow['steps'] as $k => $step) {
		if($step['content'][0]['task'] == 'end' && empty($this->options['display_end']))
			continue;

		$stepClass = ($k == $this->workflow_step) ? 'hikashop_cart_step_current' : ($k < $this->workflow_step ? 'hikashop_cart_step_finished' : '');
		$badgeClass = ($k == $this->workflow_step) ? 'current' : ($k < $this->workflow_step ? 'past' : '');
		if(!empty($step['name'])){
			$key = strtoupper($step['name']);
			$trans = JText::_($key);
			if($trans == $key)
				$name = $step['name'];
			else
				$name = $trans;
		}else{
			$name = JText::_('HIKASHOP_CHECKOUT_'.strtoupper($step['content'][0]['task']));
		}

		if($k < $this->workflow_step) {
			$name = '<a href="'.$this->checkoutHelper->completeLink('&cid='.($k+1).$this->cartIdParam, false, false, false, $this->itemid).'">'.$name.'</a>';
		}

        if($k == 0){$icon = 'shipping-fast';}
        if($k == 1){$icon = 'receipt';}
        if($k == 2){$icon = 'clipboard-check';}
?>
		<div class="stepIconWrapper <?php echo $stepClass; ?> <?php echo $badgeClass; ?>">
            <div class="uk-position-relative item">
                <span class="uk-position-absolute uk-position-z-index uk-border-circle circle"></span>
                <span class="uk-display-block uk-margin-small-bottom <?php echo $badgeClass; ?>" data-step="<?php echo ($k + 1); ?>"><img src="<?php echo JURI::base().'images/sprite.svg#'.$icon; ?>" width="48" height="48" data-uk-svg></span>
                <span class="uk-text-small uk-text-bold font"><?php echo $name; ?></span>
            </div>
		</div>
<?php
	}
?>
	</div>
</div>
