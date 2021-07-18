<?php
/**
* @package RSSeo!
* @copyright (C) 2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class plgRsseoHikashop extends JPlugin
{
	public function onrsseo_structuredTabs($args) {
		JForm::addFormPath(dirname(__FILE__));
		
		$this->loadLanguage();
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$form	= JForm::getInstance('rsseohikashop', 'rsseohikashop', array('control' => 'jform', 'load_data' => true));
		$tabs 	= $args['tabs'];
		$data	= array();
		$html	= '';
		
		$query->select($db->qn('data'))
			->from($db->qn('#__rsseo_data'))
			->where($db->qn('type').' = '.$db->q('hikashop'));
		$db->setQuery($query);
		if ($config = $db->loadResult()) {
			try {
				$registry = new JRegistry;
				$registry->loadString($config);
				$data['hikashop'] = $registry->toArray();
			} catch (Exception $e) {
				$data['hikashop'] = array();
			}
		}
		
		$form->bind($data);
		
		foreach ($form->getFieldset('hikashop') as $field) {
			$html.= $field->renderField();
		}
		
		$tabs->addTitle(JText::_('PLG_RSSEO_HIKASHOP_TAB_NAME'), 'hikashop');
		$tabs->addContent($html);
	}
	
	public function onrsseo_generate_hikashop($args) {
		$data = $args['data'];
		
		if ($this->getValue($data, 'enable')) {
			$input	= JFactory::getApplication()->input;
			$option	= $input->get('option');
			$view	= $input->get('view');
			$id		= $input->getInt('cid');
			$json	= array();
			$array	= array();
			
			if ($option == 'com_hikashop' && $view == 'product' && $id) {
				if ($product = $this->getData($id)) {
					$array['@context'] = 'https://schema.org';
					$array['@type'] = 'Product';
					$array['name'] = $product->product_name;
					$array['description'] = strip_tags($product->product_description);
					
					if ($product->prodimage) {
						$array['image'] = JUri::getInstance()->toString(array('host', 'scheme', 'port')).$product->prodimage;
					}
					
					if (isset($product->product_average_score) && isset($product->product_total_vote)) {
						$array['aggregateRating']['@type'] = 'AggregateRating';
						$array['aggregateRating']['ratingValue'] = $product->product_average_score;
						$array['aggregateRating']['reviewCount'] = $product->product_total_vote;
					}
					
					$array['offers']['@type'] = 'Offer';
					$array['offers']['priceCurrency'] = isset($product->currency) ? $product->currency : '';
					$array['offers']['price'] = number_format($product->price, 2);
					
					$json[] = '<script type="application/ld+json">';
					$json[] = json_encode($array, $this->json_options());
					$json[] = '</script>';
					
					$args['json'][] = implode("\n",$json);
				}
			}
		}
	}
	
	protected function getData($id) {
		if (file_exists(JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php')) {
			require_once JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php';
			
			$imageHelper	= hikashop_get('helper.image');
			$productClass	= hikashop_get('class.product');
			$currencyClass	= hikashop_get('class.currency');
			$product		= $productClass->getProduct($id);
			
			$image_path = ((isset($product->parent) && isset($product->parent->images[0]->file_path)) ? $product->parent->images[0]->file_path : '');
			$image_path = (isset($product->images[0]->file_path) ? $product->images[0]->file_path : $image_path);
			$img = $imageHelper->getThumbnail($image_path, array(50,50), array('default' => true), true);
			
			if ($img->origin_url) {
				$product->prodimage = $img->origin_url;
			}
			
			$null = null;
			$currency_id = hikashop_getCurrency();
			$currencies = $currencyClass->getCurrencies($currency_id, $null);
			if (isset($currencies[$currency_id]->currency_code)) {
				$product->currency = $currencies[$currency_id]->currency_code;
			}
			
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->select($db->qn('price_value'))
				->from($db->qn('#__hikashop_price'))
				->where($db->qn('price_product_id').' = '. $db->q($id));
			$db->setQuery($query);
			$product->price = $db->loadResult();
			
			return $product;
		}
		
		return false;
	}
	
	protected function getValue($data, $name, $default = null) {
		if (isset($data[$name]) && !empty($data[$name])) {
			return $data[$name];
		}
		
		return $default;
	}
	
	protected function json_options() {
		if (version_compare(phpversion(), '5.4.0', '<')) {
			return 0;
		}
		
		return JSON_PRETTY_PRINT;
	}
}