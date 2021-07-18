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
<div>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
        <div>
            <div class="uk-padding">
                <div id="hikashop_order_listing">
                    <?php echo $this->toolbarHelper->process($this->toolbar, $this->title); ?>
                    <form action="<?php echo hikashop_completeLink('order'); ?>" method="post" name="adminForm" id="adminForm">

                        <div>
                            <div class="hikashop_search_zone uk-flex-between" data-uk-grid>
                                <div class="uk-width-1-1 uk-width-expand@m">
                                    <div>
                                        <div class="uk-grid-small" data-uk-grid>
                                            <div class="uk-width-1-1 uk-width-1-3@m">
                                                <input type="text" name="search" id="hikashop_search" value="<?php echo $this->escape($this->pageInfo->search);?>" placeholder="<?php echo JText::_('HIKA_SEARCH'); ?>" class="uk-width-1-1 uk-border-rounded font rsform-input-box uk-input" onchange="this.form.submit();" />
                                            </div>
                                            <div class="uk-width-1-1 uk-width-auto@m">
                                                <button class="uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button uk-button-success uk-height-1-1" onclick="this.form.submit();"><?php echo JText::_('FIND'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php foreach($this->leftFilters as $name => $filterObj) {
                                        if(is_string($filterObj))
                                            echo $filterObj;
                                        else
                                            echo $filterObj->displayFilter($name, $this->pageInfo->filter);
                                    }
                                    ?>
                                </div>
                                <div class="hikashop_order_sort uk-width-1-1 uk-width-auto@m">
                                    <?php foreach($this->rightFilters as $name => $filterObj) { ?>
                                        <?php if(is_string($filterObj)) { ?>
                                            <?php echo $filterObj; ?>
                                        <?php } else { ?>
                                            <?php echo $filterObj->displayFilter($name, $this->pageInfo->filter); ?>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <hr class="uk-margin-medium-top uk-margin-medium-bottom">

                        <div class="hikashop_order_listing">
                            <div class="uk-child-width-1-1 uk-grid-divider" data-uk-grid>
                                <?php
                                $url_itemid = (!empty($this->Itemid) ? '&Itemid=' . $this->Itemid : '');
                                $cancel_orders = false;
                                $print_invoice = false;
                                $cancel_url = '&cancel_url='.base64_encode(hikashop_currentURL());

                                $i = 0;
                                $k = 0;
                                foreach($this->rows as &$row) {
                                    $order_link = hikashop_completeLink('order&task=show&cid='.$row->order_id.$url_itemid.$cancel_url);
                                    ?>
                                    <div data-order-container="<?php echo (int)$row->order_id; ?>">
                                        <div class="uk-position-relative">
                                            <div class="uk-text-zero">
                                                <div>
                                                    <span class="uk-display-block uk-text-tiny uk-text-muted uk-margin-small-bottom font"><?php echo JText::sprintf('ORDERNUMBERX', $row->order_number); ?></span>
                                                    <div>
                                                        <div class="uk-grid-small" data-uk-grid>
                                                            <div class="uk-width-auto uk-flex uk-flex-middle">
                                                                <div>
                                                                    <div class="uk-grid-small uk-child-width-auto uk-grid-divider uk-text-small" data-uk-grid>
                                                                        <div>
                                                                            <span class="uk-text-<?php echo $row->order_status; ?> font myOrderMetaItem status"><?php echo hikashop_orderStatus($row->order_status); ?></span>
                                                                        </div>
                                                                        <div>
                                                                            <time class="uk-text-secondary font myOrderMetaItem" datetime="<?php echo hikashop_getDate((int)$row->order_created, '%d %B %Y %H:%M'); ?>"><?php echo hikashop_getDate((int)$row->order_created, 'D ، j M Y'); ?></time>
                                                                        </div>
                                                                        <div>
                                                                            <span class="uk-text-secondary font myOrderMetaItem"><?php echo $this->currencyClass->format($row->order_full_price, $row->order_currency_id); ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="uk-position-top-left">
                                                <div class="uk-grid-small uk-child-width-auto" data-uk-grid>
                                                    <div>
                                                        <div class="uk-text-secondary cursorPointer"><img src="<?php echo JURI::base().'images/sprite.svg#ellipsis-v'; ?>" width="20" height="20" alt="" data-uk-svg></div>
                                                        <div data-uk-drop="pos: right-top" class="ellipsisDrop">
                                                            <?php /* if(!empty($row->extraData->topLeft)) { echo implode("\r\n", $row->extraData->topLeft); } */ ?>
                                                            <?php /* if(!empty($row->extraData->bottomLeft)) { echo implode("\r\n", $row->extraData->bottomLeft); } */ ?>
                                                            <?php /* if(!empty($row->extraData->beforeInfo)) { echo implode("\r\n", $row->extraData->beforeInfo); } */ ?>
                                                            <?php /* if(!empty($row->extraData->afterInfo)) { echo implode("\r\n", $row->extraData->afterInfo); } */ ?>
                                                            <?php /* if(!empty($row->extraData->topMiddle)) { echo implode("\r\n", $row->extraData->topMiddle); } */ ?>
                                                            <?php /* if(!empty($row->extraData->bottomMiddle)) { echo implode("\r\n", $row->extraData->bottomMiddle); } */ ?>
                                                            <?php /* if(!empty($row->extraData->topRight)) { echo implode("\r\n", $row->extraData->topRight); } */ ?>
                                                            <?php /* if(!empty($row->extraData->bottomRight)) { echo implode("\r\n", $row->extraData->bottomRight); } */ ?>
                                                            <div>
                                                                <?php
                                                                $dropData = array();
                                                                $dropData[] = array(
                                                                    'name' => JText::_('HIKA_DETAILS'),
                                                                    'link' => $order_link
                                                                );

                                                                if(!empty($row->show_print_button)) {
                                                                    $print_invoice = true;
                                                                    $dropData[] = array(
                                                                        'name' => JText::_('PRINT_INVOICE'),
                                                                        'link' => '#print_invoice',
                                                                        'click' => 'return window.localPage.printInvoice('.(int)$row->order_id.');',
                                                                    );
                                                                }
                                                                if(!empty($row->show_cancel_button)) {
                                                                    $cancel_orders = true;
                                                                    $dropData[] = array(
                                                                        'name' => JText::_('CANCEL_ORDER'),
                                                                        'link' => '#cancel_order',
                                                                        'click' => 'return window.localPage.cancelOrder('.(int)$row->order_id.',\''.$row->order_number.'\');',
                                                                    );
                                                                }
                                                                if(!empty($row->show_payment_button) && bccomp($row->order_full_price, 0, 5) > 0) {
                                                                    $url_param = ($this->payment_change) ? '&select_payment=1' : '';
                                                                    $url = hikashop_completeLink('order&task=pay&order_id='.$row->order_id.$url_param.$url_itemid);
                                                                    if($this->config->get('force_ssl',0) && strpos('https://',$url) === false)
                                                                        $url = str_replace('http://','https://', $url);
                                                                    $dropData[] = array(
                                                                        'name' => JText::_('PAY_NOW'),
                                                                        'link' => $url
                                                                    );
                                                                }
                                                                if($this->config->get('allow_reorder', 0)) {
                                                                    $url = hikashop_completeLink('order&task=reorder&order_id='.$row->order_id.$url_itemid);
                                                                    if($this->config->get('force_ssl',0) && strpos('https://',$url) === false)
                                                                        $url = str_replace('http://','https://', $url);
                                                                    $dropData[] = array(
                                                                        'name' => JText::_('REORDER'),
                                                                        'link' => $url
                                                                    );
                                                                }

                                                                if(!empty($row->actions)) {
                                                                    $dropData = array_merge($dropData, $row->actions);
                                                                }

                                                                if(!empty($dropData)) {
                                                                    /* echo $this->dropdownHelper->display( JText::_('HIKASHOP_ACTIONS'), $dropData, array('type' => 'btn',  'right' => true, 'up' => false) ); */
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="uk-card uk-card-default uk-box-shadow-small uk-border-rounded uk-padding-small">
                                                                <ul class="uk-list uk-margin-remove uk-padding-remove">
                                                                    <?php if(!empty($row->show_payment_button) && bccomp($row->order_full_price, 0, 5) > 0) { ?>
                                                                        <li>
                                                                            <a class="uk-button uk-button uk-text-tiny uk-button-success uk-border-rounded uk-display-block uk-width-1-1 font" href="<?php echo $url; ?>"><?php echo JText::_('PAY_NOW'); ?></a>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="uk-margin-remove">
                                                                        </li>
                                                                    <?php } ?>
                                                                    <li>
                                                                        <a class="uk-button uk-button-small uk-text-tiny uk-button-default uk-border-rounded uk-display-block uk-width-1-1 font" href="<?php echo $order_link; ?>"><?php echo JText::_('HIKA_ORDER_DETAILS'); ?></a>
                                                                    </li>
                                                                    <?php if(!empty($row->show_cancel_button)) { ?>
                                                                        <li>
                                                                            <a class="uk-button uk-button-small uk-text-tiny uk-button-danger uk-border-rounded uk-display-block uk-width-1-1 font" href="#cancel_order" onclick="<?php echo 'return window.localPage.cancelOrder('.(int)$row->order_id.',\''.$row->order_number.'\');'; ?>"><?php echo JText::_('CANCEL_ORDER'); ?></a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <?php if($row->order_id == $this->row->order_id) { ?>
                                                            <a data-uk-tooltip="<?php echo $this->escape(JText::_('HIDE_PRODUCTS')); ?>" class="uk-text-secondary font myOrderMetaItem" href="#" onclick="return window.localPage.handleDetails(this, <?php echo (int)$row->order_id; ?>);"><img src="<?php echo JURI::base()."images/sprite.svg#chevron-circle-up"; ?>" width="20" height="20" alt="" data-uk-svg></a>
                                                        <?php } else { ?>
                                                            <a data-uk-tooltip="<?php echo $this->escape(JText::_('DISPLAY_PRODUCTS')); ?>" class="uk-text-accent hoverAccent cursorPointer font myOrderMetaItem" href="#" onclick="return window.localPage.handleDetails(this, <?php echo (int)$row->order_id; ?>);"><img src="<?php echo JURI::base().'images/sprite.svg#chevron-circle-down'; ?>" width="20" height="20" alt="" data-uk-svg></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <div>
                                        <?php
                                        if($row->order_id == $this->row->order_id) {
                                            $this->setLayout('order_products');
                                            echo $this->loadTemplate();
                                        }
                                        ?>
                                    </div>
                                    </div>
                                    <?php
                                    $i++;
                                    $k = 1 - $k;
                                }
                                unset($row);
                                ?>
                                <div class="hikashop_orders_footer">
                                    <div class="pagination">
                                        <?php $this->pagination->form = '_bottom'; echo $this->pagination->getListFooter(); ?>
                                        <?php echo $this->pagination->getResultsCounter(); ?>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
                            <input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
                            <input type="hidden" name="task" value="listing" />
                            <input type="hidden" name="ctrl" value="<?php echo hikaInput::get()->getCmd('ctrl'); ?>" />
                            <input type="hidden" name="boxchecked" value="0" />
                            <?php echo JHTML::_('form.token'); ?>
                        </div>
                    </form>
                    <script type="text/javascript">
                        if(!window.localPage) window.localPage = {};
                        window.localPage.handleDetails = function(btn, id) {
                            var d = document, details = d.getElementById('hika_order_'+id+'_details');

                            if(details) {
                                details.style.display = (details.style.display == 'none' ? '' : 'none');
                                if(details.style.display) {
                                    btn.innerHTML = '<img src="<?php echo JURI::base()."images/sprite.svg#chevron-circle-down"; ?>" width="20" height="20" alt="" data-uk-svg>';
                                    btn.setAttribute('data-uk-tooltip','<?php echo $this->escape(JText::_('DISPLAY_PRODUCTS')); ?>');
                                    btn.className = 'uk-text-accent hoverAccent font myOrderMetaItem';
                                } else{
                                    btn.innerHTML = '<img src="<?php echo JURI::base()."images/sprite.svg#chevron-circle-up"; ?>" width="20" height="20" alt="" data-uk-svg>';
                                    btn.setAttribute('data-uk-tooltip','<?php echo $this->escape(JText::_('HIDE_PRODUCTS')); ?>');
                                    btn.className = 'uk-text-secondary font myOrderMetaItem';
                                }
                                return false;
                            }

                            return window.localPage.loadOrderDetails(btn, id);
                        };
                        window.localPage.loadOrderDetails = function(btn, id) {
                            var d = document, o = window.Oby, el = d.querySelector('[data-order-container="'+id+'"]');
                            if(!el) return false;
                            btn.classList.add('hikadisabled');
                            btn.disabled = true;
                            btn.blur();
                            // btn.innerHTML = '<div data-uk-spinner></div>';
                            var c = d.createElement('div');
                            o.xRequest("<?php echo hikashop_completeLink('order&task=order_products', 'ajax', false, true); ?>", {mode:'POST',data:'cid='+id},function(xhr){
                                if(!xhr.responseText || xhr.status != 200) {
                                    btn.innerHTML = '<i class="fas fa-angle-down"></i>gtgttggt';
                                    return;
                                }
                                btn.classList.remove('hikadisabled');
                                btn.disabled = false;
                                var resp = o.trim(xhr.responseText);
                                c.innerHTML = resp;
                                el.appendChild(c.querySelector('#hika_order_'+id+'_details'));
                                btn.innerHTML = '<img src="<?php echo JURI::base()."images/sprite.svg#chevron-circle-up"; ?>" width="20" height="20" alt="" data-uk-svg>';
                                btn.setAttribute('data-uk-tooltip','<?php echo $this->escape(JText::_('HIDE_PRODUCTS')); ?>');
                                btn.className = 'uk-text-secondary font myOrderMetaItem';
                            });
                            return false;
                        };
                    </script>
                    <?php

                    if(!empty($this->rows) && ($print_invoice || $cancel_orders)) {
                        echo $this->popupHelper->display(
                            '',
                            'INVOICE',
                            hikashop_completeLink('order&task=invoice'.$url_itemid.'',true),
                            'hikashop_print_popup',
                            760, 480, '', '', 'link'
                        );
                        ?>
                        <script>
                            if(!window.localPage) window.localPage = {};
                            window.localPage.cancelOrder = function(id, number) {
                                var d = document, form = d.getElementById('hikashop_cancel_order_form');
                                if(!form || !form.elements['order_id']) {
                                    console.log('Error: Form not found, cannot cancel the order');
                                    return false;
                                }
                                if(!confirm('<?php echo JText::_('HIKA_CONFIRM_CANCEL_ORDER', true); ?>'.replace(/ORDER_NUMBER/, number)))
                                    return false;
                                form.elements['order_id'].value = id;
                                form.submit();
                                return false;
                            };
                            window.localPage.printInvoice = function(id) {
                                hikashop.openBox('hikashop_print_popup','<?php
                                    $u = hikashop_completeLink('order&task=invoice'.$url_itemid,true);
                                    echo $u;
                                    echo (strpos($u, '?') === false) ? '?' : '&';
                                    ?>order_id='+id);
                                return false;
                            };
                        </script>
                        <form action="<?php echo hikashop_completeLink('order&task=cancel_order&email=1'); ?>" name="hikashop_cancel_order_form" id="hikashop_cancel_order_form" method="POST">
                            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
                            <input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
                            <input type="hidden" name="task" value="cancel_order" />
                            <input type="hidden" name="email" value="1" />
                            <input type="hidden" name="order_id" value="" />
                            <input type="hidden" name="ctrl" value="order" />
                            <input type="hidden" name="redirect_url" value="<?php echo hikashop_currentURL(); ?>" />
                            <?php echo JHTML::_('form.token'); ?>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
