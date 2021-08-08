<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$items = json_decode( $params->get('items'),true);
$total = count($items['title']);
?>
<?php if ($total > 0) { ?>
    <table class="uk-margin-remove uk-table uk-table-divider uk-table-middle uk-table-responsive">
        <thead>
        <tr>
            <th class="uk-text-nowrap font f500 uk-text-center uk-text-muted"><?php echo JText::sprintf('BANK'); ?></th>
            <th class="uk-text-nowrap font f500 uk-text-center uk-text-muted uk-table-shrink"><?php echo JText::sprintf('ACCOUNT'); ?></th>
            <th class="uk-text-nowrap font f500 uk-text-center uk-text-muted uk-table-shrink"><?php echo JText::sprintf('CARDNUMBER'); ?></th>
            <th class="uk-text-nowrap font f500 uk-text-center uk-text-muted uk-table-shrink"><?php echo JText::sprintf('SHEBA'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php for ($i=0;$i<$total;$i++) { ?>
            <?php if (!empty($items['account'][$i]) || !empty($items['card'][$i]) || !empty($items['sheba'][$i])) { ?>
                <tr>
                    <td class="uk-text-nowrap uk-table-shrink font f600 uk-text-center">
                        <img src="<?php echo JUri::base().'images/sprite.svg#'.$items['logo'][$i] ?>" width="32" height="32" alt="<?php echo $items['title'][$i]; ?>" class="uk-preserve-width" data-uk-svg>
                        <span class="uk-display-block uk-text-small uk-margin-small-top uk-text-dark font f600"><?php echo $items['title'][$i]; ?></span>
                    </td>
                    <td class="uk-text-nowrap uk-table-shrink font f600 uk-text-center uk-text-dark">
                        <?php $uniqueAccountID = 'account'.($i+1); ?>
                        <span class="uk-display-block uk-text-muted uk-text-small font f500 uk-text-center uk-hidden@m"><?php echo JText::sprintf('ACCOUNT'); ?></span>
                        <a href="javascript: void(0)" data-uk-tooltip="<?php echo JText::sprintf('COPY').' '.JText::sprintf('ACCOUNT'); ?>" id="<?php echo $uniqueAccountID; ?>" onclick="copyToClipboard('#<?php echo $uniqueAccountID; ?>');UIkit.notification({message: '<?php echo JText::sprintf("ACCOUNT")." ".JText::sprintf("COPIED"); ?>', status: 'success', pos: 'bottom-left'})" class="uk-text-dark uk-margin-small-left uk-margin-small-right hoverAccent font f600 uk-text-small"><?php echo $items['account'][$i]; ?></a>
                    </td>
                    <td class="uk-text-nowrap uk-table-shrink font f600 uk-text-center uk-text-dark">
                        <?php $uniqueCardID = 'card'.($i+1); ?>
                        <span class="uk-display-block uk-text-muted uk-text-small font f500 uk-text-center uk-hidden@m"><?php echo JText::sprintf('CARDNUMBER'); ?></span>
                        <a href="javascript: void(0)" data-uk-tooltip="<?php echo JText::sprintf('COPY').' '.JText::sprintf('CARDNUMBER'); ?>" id="<?php echo $uniqueCardID; ?>" onclick="copyToClipboard('#<?php echo $uniqueCardID; ?>');UIkit.notification({message: '<?php echo JText::sprintf("CARDNUMBER")." ".JText::sprintf("COPIED"); ?>', status: 'success', pos: 'bottom-left'})" class="uk-text-dark uk-margin-small-left uk-margin-small-right hoverAccent font f600 uk-text-small"><?php echo $items['card'][$i]; ?></a>
                    </td>
                    <td class="uk-text-nowrap uk-table-shrink font f600 uk-text-center uk-text-dark">
                        <?php $uniqueShebaID = "sheba".($i+1); ?>
                        <span class="uk-display-block uk-text-muted uk-text-small font f500 uk-text-center uk-hidden@m"><?php echo JText::sprintf('SHEBA'); ?></span>
                        <a href="javascript: void(0)" data-uk-tooltip="<?php echo JText::sprintf('COPY').' '.JText::sprintf('SHEBA'); ?>" id="<?php echo $uniqueShebaID; ?>" onclick="copyToClipboard('#<?php echo $uniqueShebaID; ?>');UIkit.notification({message: '<?php echo JText::sprintf("SHEBA")." ".JText::sprintf("COPIED"); ?>', status: 'success', pos: 'bottom-left'})" class="uk-text-dark uk-margin-small-left uk-margin-small-right hoverAccent font f600 uk-text-small"><?php echo $items['sheba'][$i]; ?></a>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
<?php } ?>
<script type="text/javascript">
    function copyToClipboard(element) {
        var jQuerytemp = jQuery("<input>");
        jQuery("body").append(jQuerytemp);
        jQuerytemp.val(jQuery(element).text()).select();
        document.execCommand("copy");
        jQuerytemp.remove();
    }
</script>
