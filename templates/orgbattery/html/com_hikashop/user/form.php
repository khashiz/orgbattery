<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.4.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2021 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
global $Itemid;
$url_itemid='';
if(!empty($Itemid)){
	$url_itemid='&Itemid='.$Itemid;
}
?>
<div class="uk-container uk-container-xsmall ">
    <div class="hikashop_cpanel_main_interface">
        <div class="hikashop_dashboard" id="hikashop_dashboard" data-uk-grid>
            <div class="uk-width-1-1 uk-width-expand@m">
                <div>
                    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
                        <div>
                            <div class="uk-padding">
                                <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                                    <div>
                                        <form action="<?php echo hikashop_completeLink('user&task=register'.$url_itemid); ?>" method="post" name="hikashop_registration_form" enctype="multipart/form-data" onsubmit="hikashopSubmitForm('hikashop_registration_form'); return false;" class="regForm">
                                            <div class="hikashop_user_registration_page">
                                                <fieldset class="input uk-form-stacked uk-margin-remove uk-padding-remove">
                                                    <h2 class="uk-hidden"><?php echo $this->title;?></h2>
                                                    <?php
                                                    $this->setLayout('registration');
                                                    $this->registration_page=true;
                                                    $this->form_name = 'hikashop_registration_form';
                                                    $usersConfig = JComponentHelper::getParams('com_users');
                                                    $allowRegistration = $usersConfig->get('allowUserRegistration');
                                                    if ($allowRegistration || $this->simplified_registration == 2){
                                                        echo $this->loadTemplate();
                                                    }else{
                                                        echo JText::_('REGISTRATION_NOT_ALLOWED');
                                                    }
                                                    ?>
                                                </fieldset>
                                            </div>
                                        </form>
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