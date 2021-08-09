<?php
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to pagination rendering.
 *
 * pagination_list_footer
 * 	Input variable $list is an array with poffsets:
 * 		$list[limit]		: int
 * 		$list[limitstart]	: int
 * 		$list[total]		: int
 * 		$list[limitfield]	: string
 * 		$list[pagescounter]	: string
 * 		$list[pageslinks]	: string
 *
 * pagination_list_render
 * 	Input variable $list is an array with poffsets:
 * 		$list[all]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[start]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[previous]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[next]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[end]
 * 			[data]		: string
 * 			[active]	: boolean
 * 		$list[pages]
 * 			[{PAGE}][data]		: string
 * 			[{PAGE}][active]	: boolean
 *
 * pagination_item_active
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * pagination_item_inactive
 * 	Input variable $item is an object with fields:
 * 		$item->base	: integer
 * 		$item->link	: string
 * 		$item->text	: string
 *
 * This gives template designers ultimate control over how pagination is rendered.
 *
 * NOTE: If you override pagination_item_active OR pagination_item_inactive you MUST override them both
 */

function pagination_list_footer($list)
{
	$html = "<div class='container'><div class='pagination uk-grid-small uk-flex-center' data-uk-grid>";

	$html .= "<div class='limit uk-width-expand'>".$list['limitfield']."</div>";
	$html .= $list['pageslinks'];
	$html .= "<div class='uk-hidden limit'>".$list['pagescounter']."</div>";

	$html .= "<input class='uk-hidden' type='hidden' name='limitstart' value='".$list['limitstart']."' />";
	$html .= "</div></div>";

	return $html;
}

function pagination_list_render($list)
{
	$html = '<div class="uk-width-auto"><ul class="uk-padding-remove uk-grid-small uk-flex-center fnum font" data-uk-grid>';

	/*
	if ($list['start']['active'])
		$html .= '<li class="pag_button pag_start">'.$list['start']['data'].'</li>';
	else
		$html .= '<li class="pag_button pag_start poff">'.$list['start']['data'].'</li>';
	if ($list['previous']['active'])
		$html .= '<li class="pag_button pag_previous">'.$list['previous']['data'].'</li>';
	else
		$html .= '<li class="pag_button pag_previous poff">'.$list['previous']['data'].'</li>';
	*/

	foreach($list['pages'] as $page)
		$html .= '<li>'.$page['data'].'</li>';

	/*
	if ($list['next']['active'])
		$html .= '<li class="pag_button pag_next">'.$list['next']['data'].'</li>';
	else
		$html .= '<li class="pag_button pag_next poff">'.$list['next']['data'].'</li>';

	if ($list['end']['active'])
		$html .= '<li class="pag_button pag_end">'.$list['end']['data'].'</li>';
	else
		$html .= '<li class="pag_button pag_end poff">'.$list['end']['data'].'</li>';
	*/

	$html .= '</ul></div>';

	return $html;
}

function pagination_item_active(&$item)
{
	return '<a class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-padding-remove" href="'.$item->link.'">'.$item->text.'</a>';
}

function pagination_item_inactive(&$item)
{
	return '<span class="uk-button uk-button-default uk-border-rounded uk-box-shadow-small uk-padding-remove">'.$item->text.'</span>';
}
?>