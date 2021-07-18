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
<?php if(empty($this->row->comment_enabled) || $this->row->comment_enabled == 0) return; ?>
<?php $row =& $this->row; ?>
<?php if($row->access_vote == 'registered' && hikashop_loadUser() == null) { ?>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
        <div class="uk-padding">
            <div class="uk-text-center">
                <div class="uk-margin-bottom"><img src="<?php echo JURI::base().'images/sprite.svg#user-lock'; ?>" width="128" height="128" alt="<?php echo $sitename; ?>" data-uk-svg></div>
                <p class="uk-text-danger uk-text-small uk-text-bold font"><?php echo JTEXT::_('ONLY_REGISTERED_CAN_COMMENT'); ?></p>
                <div><a href="<?php echo JRoute::_("index.php?Itemid=166"); ?>" class="uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button uk-button-success uk-height-1-1"><?php echo JText::sprintf('JLOGIN'); ?></a></div>
            </div>
        </div>
    </div>
    <?php return; ?>
<?php } ?>
<?php if($row->access_vote == 'buyed' && $row->purchased == 0) { ?>
    <div class="uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
        <div class="uk-padding">
            <div class="uk-text-center">
                <div class="uk-margin-bottom"><img src="<?php echo JURI::base().'images/sprite.svg#user-lock'; ?>" width="128" height="128" alt="<?php echo $sitename; ?>" data-uk-svg></div>
                <p class="uk-margin-remove uk-text-danger uk-text-small uk-text-bold font"><?php echo JTEXT::_('MUST_HAVE_BUY_TO_VOTE'); ?></p>
            </div>
        </div>
    </div>
    <?php return; ?>
<?php } ?>

<?php
$row->hikashop_vote_average_score = (float)hikashop_toFloat($row->hikashop_vote_average_score);
if($row->hikashop_vote_total_vote == '0') {
	$tooltip = JText::_('HIKA_NO_VOTE');
} else {
	$user_rating = JText::_('HIKA_NO_VOTE');
	if(isset($this->user_vote->vote_rating))
		$user_rating = $this->user_vote->vote_rating;
	$tooltip = JText::sprintf('HIKA_VOTE_TOOLTIP', $row->hikashop_vote_average_score, $row->hikashop_vote_total_vote, $user_rating);
}
?>
<div class="hikashop_vote_form regForm uk-card uk-card-default uk-border-rounded uk-overflow-hidden uk-box-shadow-small">
    <div class="uk-padding">
        <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h5 font"><?php echo JText::_('HIKASHOP_LET_A_COMMENT'); ?></h3>
        <div class="uk-child-width-1-1 uk-grid-small" data-uk-grid>
            <div>
                <?php if($row->vote_enabled == 1) { ?>
                    <div class="hikashop_vote_stars uk-background-muted uk-border-rounded uk-padding-small uk-text-zero">
                        <div data-uk-grid>
                            <div class="uk-width-expand uk-text-tiny">
                                <span class="uk-text-secondary font"><?php echo JText::_('YOUR_VOTE'); ?></span>
                            </div>
                            <div class="uk-width-auto uk-text-tiny"><input type="hidden" name="hikashop_vote_rating" data-max="<?php echo $row->hikashop_vote_nb_star; ?>" data-votetype="<?php echo $row->type_item; ?>" data-ref="<?php echo $row->vote_ref_id; ?>" data-rate="<?php echo $row->hikashop_vote_average_score_rounded; ?>" data-original-title="<?php echo $tooltip ?>" id="hikashop_vote_rating_id" /></div>
                        </div>
                    </div>
                <?php } else { ?>
                    <input type="hidden" name="hikashop_vote_rating" data-votetype="<?php echo $row->type_item; ?>" data-ref="<?php echo $row->vote_ref_id; ?>" id="hikashop_vote_rating_id" />
                <?php } ?>
                <div id='hikashop_vote_status_form' class="hikashop_vote_notification uk-hidden"></div>
                <?php if(hikashop_loadUser() == null) { ?>
                    <table class="hikashop_comment_form">
                        <tr class="hikashop_comment_form_name">
                            <td><?php echo JText::_('HIKA_USERNAME'); ?>:</td>
                            <td><input  type='text' name="pseudo_comment" id='pseudo_comment' /></td>
                        </tr>
                        <?php if ($row->email_comment == 1) { ?>
                            <tr class="hikashop_comment_form_mail">
                                <td><?php echo JText::_('HIKA_EMAIL'); ?>:</td>
                                <td><input  type='text' name="email_comment" id='email_comment' value=''/></td>
                            </tr>
                        <?php } else { ?>
                            <input type='hidden' name="email_comment" id='email_comment' value='0'/>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <input type='hidden' name="pseudo_comment" id='pseudo_comment' value='0'/>
                    <input type='hidden' name="email_comment" id='email_comment' value='0'/>
                <?php } ?>
            </div>
            <div>
                <textarea type="text" name="hikashop_vote_comment" id="hikashop_vote_comment" class="uk-width-1-1 uk-border-rounded font rsform-text-box uk-textarea" rows="5"></textarea>
            </div>
            <div>
                <input class="uk-width-1-1 uk-border-rounded uk-box-shadow-small font uk-button uk-button-success uk-height-1-1" type="button" value="<?php echo JText::_('HIKASHOP_SEND_COMMENT'); ?>" onclick="hikashop_send_comment();"/>
            </div>
        </div>
    </div>
</div>