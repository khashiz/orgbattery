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
<?php $row =& $this->rows; ?>
<?php if($row->comment_enabled != 1) return; ?>
<?php if(($row->hikashop_vote_con_req_list == 1 && hikashop_loadUser() != "") || $row->hikashop_vote_con_req_list == 0) { ?>
    <div class="hikashop_listing_comment">
        <?php if($row->vote_comment_sort_frontend && count($this->elts)) { ?>
            <?php $sort = hikaInput::get()->getString('sort_comment',''); ?>
            <span class="hikashop_sort_listing_comment">
                <select name="sort_comment" onchange="refreshCommentSort(this.value); return false;">
                    <option <?php if($sort == 'date')echo "selected"; ?> value="date"><?php echo JText::_('HIKASHOP_COMMENT_ORDER_DATE');?></option>
                    <option <?php if($sort == 'helpful')echo "selected"; ?> value="helpful"><?php echo JText::_('HIKASHOP_COMMENT_ORDER_HELPFUL');?></option>
                </select>
            </span>
        <?php } ?>
        <?php if (count($this->elts)) { ?>
            <h3 class="uk-margin-bottom uk-text-accent uk-text-bold uk-h5 font"><?php echo JText::_('HIKASHOP_LISTING_COMMENT'); ?></h3>
        <?php } ?>
        <div class="uk-child-width-1-1 uk-grid-small uk-grid-divider" data-uk-grid>
            <?php
            $i = 0;
            $comments_count = 0;
            foreach($this->elts as $elt) {
                if(empty($elt->vote_comment))
                    continue;
                $comments_count++;
                $table_elements = '';
                $td_elements = '';
                $span_elements = '';
                $div_elements = '';
                if ($this->microData == true) {
                    $table_elements = ' itemprop="review" itemscope itemtype="https://schema.org/Review"';
                    $td_elements = ' itemprop="author" itemscope itemtype="https://schema.org/Person"';
                    $span_elements = ' itemprop="name"';
                    $div_elements= ' itemprop="reviewBody"';
                }
            ?>
                <div <?php echo $table_elements; ?> class="hika_comment_listing">
                    <div>
                        <div class="uk-grid-small" data-uk-grid>
                            <div class="uk-width-auto uk-text-secondary uk-text-zero">
                                <img src="<?php echo JURI::base().'images/sprite.svg#user-circle'; ?>" width="48" height="48" data-uk-svg>
                            </div>
                            <div class="uk-width-expand">
                                <div class="uk-margin-small-bottom">
                                    <div class="uk-grid-small uk-text-tiny font" data-uk-grid>
                                        <div class="uk-width-expand">
                                            <div>
                                                <div class="uk-child-width-auto uk-grid-divider uk-grid-small" data-uk-grid>
                                                    <div <?php echo $td_elements; ?> class="hika_comment_listing_name">
                                                        <?php if ($elt->vote_pseudo == '0') { ?>
                                                            <span <?php echo $span_elements; ?> class="hika_vote_listing_username uk-text-muted commentMeta"><?php echo $elt->vote_pseudo; ?> </span>
                                                        <?php } else { ?>
                                                            <span <?php echo $span_elements; ?> class="hika_vote_listing_username uk-text-muted commentMeta" ><?php echo $elt->name; ?></span>
                                                        <?php } ?>
                                                    </div>
                                                    <?php if($row->show_comment_date) { ?>
                                                        <div>
                                                            <span class="uk-text-muted commentMeta">
                                                                <?php
                                                                $voteClass = hikashop_get('class.vote');
                                                                $vote = $voteClass->get($elt->vote_id);
                                                                echo hikashop_getDate($vote->vote_date);
                                                                ?>
                                                            </span>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if(!empty ($elt->purchased)) { ?>
                                                        <div class="hika_comment_listing_bottom">
                                                            <span class="hikashop_vote_listing_useful_bought uk-text-success commentMeta"><?php echo JText::_('HIKASHOP_VOTE_BOUGHT_COMMENT'); ?></span>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uk-width-auto"></div>
                                    </div>
                                </div>
                                <div>
                                    <div id="<?php echo $i++; ?>"<?php echo $div_elements; ?> class="hika_comment_listing_content uk-text-small uk-text-secondary font commentText"><?php echo nl2br($this->escape($elt->vote_comment)); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="uk-hidden">
                        <div class="hika_comment_listing_stars hk-rating">
                            <?php
                            $nb_star_vote = $elt->vote_rating;
                            hikaInput::get()->set("nb_star", $nb_star_vote);
                            $nb_star_config = $row->vote_star_number;
                            hikaInput::get()->set("nb_max_star", $nb_star_config);
                            if($nb_star_vote != 0) {
                                for($k = 0; $k < $nb_star_vote; $k++) {
                                    ?>
                                    <span class="hika_comment_listing_full_stars hk-rate-star state-full"></span>
                                    <?php
                                }
                                $nb_star_empty = $nb_star_config - $nb_star_vote;
                                for($k = 0; $k < $nb_star_empty; $k++) {
                                    ?>
                                    <span class="hika_comment_listing_empty_stars hk-rate-star state-empty"></span>
                                    <?php
                                }
                                if($this->microData == true) {
                                    ?>
                                    <span style="display:none;" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                                        <span itemprop="bestRating"><?php echo $nb_star_config; ?></span>
                                        <span itemprop="worstRating">1</span>
                                        <span itemprop="ratingValue"><?php echo $nb_star_vote; ?></span>
                                    </span>
                                <?php
                                }
                            }
                            ?>
                        </div>
                        <div>
                            <div class="hika_comment_listing_notification" id="<?php echo $elt->vote_id; ?>">
                                <?php
                                if($row->useful_rating == 1){
                                    if($elt->total_vote_useful != 0){
                                        if($elt->vote_useful == 0) {
                                            $hika_useful = $elt->total_vote_useful / 2;
                                        } else if($elt->total_vote_useful == $elt->vote_useful) {
                                            $hika_useful = $elt->vote_useful;
                                        } else if($elt->total_vote_useful == -$elt->vote_useful) {
                                            $hika_useful = 0;
                                        } else {
                                            $hika_useful = ($elt->total_vote_useful + $elt->vote_useful) /2;
                                        }
                                        $hika_useless = $elt->total_vote_useful - $hika_useful;
                                        if($row->useful_style == 'helpful'){
                                            echo JText::sprintf('HIKA_FIND_IT_HELPFUL', $hika_useful, $elt->total_vote_useful);
                                        }
                                    } else {
                                        $hika_useless = 0;
                                        $hika_useful  = 0;
                                        if($row->useful_style == 'helpful' && $elt->vote_user_id != hikashop_loadUser() && $elt->vote_user_id != hikashop_getIP()) {
                                            echo JText::_('HIKASHOP_NO_USEFUL');
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        if($row->useful_rating == 1) {
                            if($row->hide == 0 && $elt->already_vote == 0 && $elt->vote_user_id != hikashop_loadUser() && $elt->vote_user_id != hikashop_getIP()){
                                if($row->useful_style == 'thumbs') {
                                    ?>
                                    <div vvvvvvv class="hika_comment_listing_useful_p"><?php echo $hika_useful; ?></div>
                                    <?php } ?>
                                <td class="hika_comment_listing_useful" title="<?php echo JText::_('HIKA_USEFUL'); ?>" onclick="if(!window.Oby.hasClass(this, 'next_button_disabled')) hikashop_vote_useful(<?php echo $elt->vote_id;?>,1); window.Oby.addClass(this, 'next_button_disabled');"></td>
                                <?php if($row->useful_style == 'thumbs'){?>
                                    <td class="hika_comment_listing_useful_p">
                                        <?php echo $hika_useless; ?>
                                    </td>
                                <?php } ?>
                                <td class="hika_comment_listing_useless" title="<?php echo JText::_('HIKA_USELESS'); ?>" onclick="if(!window.Oby.hasClass(this, 'next_button_disabled')) hikashop_vote_useful(<?php echo $elt->vote_id;?>,2); window.Oby.addClass(this, 'next_button_disabled');"></td>
                                <?php } else if($row->useful_style == "thumbs") { ?>
                                <td class="hika_comment_listing_useful_p"><?php echo $hika_useful; ?></td>
                                <td class="hika_comment_listing_useful locked"></td>
                                <td class="hika_comment_listing_useless_p"><?php echo $hika_useless; ?></td>
                                <td class="hika_comment_listing_useless locked"></td>
                            <?php } else { ?>
                                <td class="hika_comment_listing_useful_p hide"></td>
                                <td class="hika_comment_listing_useful locked hide"></td>
                                <td class="hika_comment_listing_useless_p hide"></td>
                                <td class="hika_comment_listing_useless locked hide"></td>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if(!count($this->elts)) { ?>
            <div class="uk-text-center">
                <div class="uk-margin-bottom"><img src="<?php echo JURI::base().'images/sprite.svg#comments-alt'; ?>" width="128" height="128" alt="<?php echo $sitename; ?>" data-uk-svg></div>
                <p class="uk-margin-remove uk-text-danger uk-text-small uk-text-bold font"><?php echo JTEXT::_('HIKASHOP_NO_COMMENT_YET'); ?></p>
            </div>
        <?php } else { $this->pagination->form = '_hikashop_comment_form'; ?>
            <div class="pagination uk-hidden">
                <?php
                echo $this->pagination->getListFooter();
                echo $this->pagination->getResultsCounter();
                ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php
$this->params->set('comments_count', $comments_count);
if($row->vote_comment_sort_frontend) {
	$jconfig = JFactory::getConfig();
	$sef = (HIKASHOP_J30 ? $jconfig->get('sef') : $jconfig->getValue('config.sef'));
	$sortUrl = $sef ? '/sort_comment-' : '&sort_comment=';
?>
<script type="text/javascript">
function refreshCommentSort(value){
	var url = window.location.href;
	if(url.match(/sort_comment/g)){
		url = url.replace(/\/sort_comment.?[a-z]*/g,'').replace(/&sort_comment.?[a-z]*/g,'');
	}
	url = url+'<?php echo $sortUrl; ?>'+value;
	document.location.href = url;
}
</script>
<?php } ?>