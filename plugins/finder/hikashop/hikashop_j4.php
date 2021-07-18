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
class plgFinderHikashop extends plgFinderHikashopBridge {

	protected function index(Joomla\Component\Finder\Administrator\Indexer\Result $item)
	{
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params = JComponentHelper::getParams('com_hikashop', true);
		$item->params->merge($registry);

		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry;

		$item->summary = FinderIndexerHelper::prepareContent($item->summary, $item->params);
		$item->body    = FinderIndexerHelper::prepareContent($item->body, $item->params);

		$menusClass = hikashop_get('class.menus');
		$itemid = $menusClass->getPublicMenuItemId();
		$this->addAlias($item);
		$extra = '';
		if(!empty($itemid))
			$extra = '&Itemid='.$itemid;

		$item->url   = "index.php?option=com_hikashop&ctrl=product&task=show&cid=" . $item->id."&name=".$item->alias."&category_pathway=" . $item->catid.$extra;
		$item->route = "index.php?option=com_hikashop&ctrl=product&task=show&cid=" . $item->id."&name=".$item->alias."&category_pathway=" . $item->catid.$extra;

		$title = $this->getItemMenuTitle($item->url);

		if (!empty($title) && $this->params->get('use_menu_title', true))
		{
			$item->title = $title;
		}

		$item->metaauthor = $item->metadata->get('author');

		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'created_by_alias');

		if(!empty($item->cat_state))
			$item->state = $this->translateState($item->state, $item->cat_state);

		$item->addTaxonomy('Type', 'Product');



		$item->addTaxonomy('Language', 		$item->language);

		FinderIndexerHelper::getContentExtras($item);

		$this->indexer->index($item);
	}
}
