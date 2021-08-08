<?php 
/**
 * @package Module EB Ajax Search for Joomla!
 * @version 1.5: mod_ebajaxsearch.php Sep 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
class JFormFieldSPcategories extends JFormField {
	protected $type = 'SPcategories';
	
	public function getInput() { // added class hidden in fieldset
		require_once dirname(__FILE__) . './../helper.php';
	    $this->app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$db->getQuery(true);
		$query = "SHOW CREATE TABLE #__sppagebuilder";
		$db->setQuery($query);
		    try
		    {
		        // If it fails, it will throw a RuntimeException
		        $result = $db->loadResult(); 
		        $db1 = JFactory::getDbo();
				$query6 = $db1->getQuery(true);
				$query6
	            ->select('id as value, title as title')
	            ->from('#__categories')
	            ->where('published=1 AND extension="com_sppagebuilder"');
	            $db1->setQuery($query6);
	            $db1->execute();
	            $num_rows = $db1->getNumRows();
	            $results6 = $db1->loadObjectList();

	            $options = array();
		        if($results6!=''){
		            foreach ($results6 as $list) {
		                $options[] = array(
		                    'id'   => $list->value,
		                    'id'   => $list->value,
		                    'name' => $list->title
		                );
		            }
		        }
		        $attribs = 'multiple="multiple" class="multipleCategories"';
	        	return JHtml::_('select.genericlist', $options, 'jform[params][spcatid][]', $attribs, 'id', 'name', $this->value, $this->id);
		    }
		    catch (RuntimeException $e)
		    {
		    	$search_in_sppage = $this->form->getValue('search_in_sppage', 'params');
		    	if($search_in_sppage){
		    		JFactory::getApplication()->enqueueMessage(JText::_('MOD_SEARCHAJAX_FIELD_SP_NOT_EXITS'), 'error');
		    	}
		        
		        $options = array();
		        $attribs = 'multiple="multiple" class="multipleCategories"';
			    return JHtml::_('select.genericlist', $options, 'jform[params][spcatid][]', $attribs, 'id', 'name', $this->value, $this->id);
		    }
		
	}
}