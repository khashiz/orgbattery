<?php 
/**
 * @package Module EB Ajax Search for Joomla!
 * @version 1.5: mod_ebajaxsearch.php Sep 2020
 * @author url: https://www/extnbakers.com
 * @copyright Copyright (C) 2020 extnbakers.com. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
**/
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
JHtml::_('jquery.framework');
jimport('joomla.application.component.helper');

class modEbajaxsearchHelper
{
    public static function getAjax()
    {
        include_once JPATH_ROOT . '/components/com_content/helpers/route.php';

        $input = JFactory::getApplication()->input;
        $ajax_data = $input->get('data', array(), 'ARRAY');
        $searchword = $ajax_data['keyword'];

        $catids = $ajax_data['catids'];
        $search_in_article = isset($ajax_data['search_in_article'])?$ajax_data['search_in_article']:'';

        $k2catid = isset($ajax_data['k2catid'])?$ajax_data['k2catid']:'';
        $search_in_k2 = isset($ajax_data['search_in_k2'])?$ajax_data['search_in_k2']:'';

        $hikashopcatid = isset($ajax_data['hikashopcatid'])?$ajax_data['hikashopcatid']:'';
        $search_in_hikashop = isset($ajax_data['search_in_hikashop'])?$ajax_data['search_in_hikashop']:'';

        $spcatid = isset($ajax_data['spcatid'])?$ajax_data['spcatid']:'';
        $search_in_sppage = isset($ajax_data['search_in_sppage'])?$ajax_data['search_in_sppage']:'';

        $by_ordering = $ajax_data['order'];
        $show_title = $ajax_data['title'];
        $show_description = $ajax_data['description'];
        $show_image = $ajax_data['image'];
        
        $db = JFactory::getDbo();
        $results1=array();
        $results3=array();
        $results5=array();
        $results7=array();
        if($search_in_article==1)
        {
            $cat_query = '';
            if($catids != ''){
                $cat_query = 'AND ' . $db->quoteName('catid') . ' IN('.$catids.')';
            }
            $query1 = $db->getQuery(true);
            $query1
            ->select($db->quoteName(array('id','title','catid','images','introtext','fulltext','hits','created')))
            ->from($db->quoteName('#__content'))
            ->where('('.$db->quoteName('title') . ' LIKE '. $db->quote('%' . $searchword . '%'). ' OR ' . $db->quoteName('introtext') . ' LIKE '. $db->quote('%' . $searchword . '%'). ' OR ' . $db->quoteName('fulltext') . ' LIKE '. $db->quote('%' . $searchword . '%'). ' ) '.$cat_query. ' AND ' . $db->quoteName('state') . ' = 1 ')
            ->order('ordering ASC');
            
            $db->setQuery($query1);
            $db->execute();
            $num_rows = $db->getNumRows();
            $results1 = $db->loadObjectList();

            if(!empty($results1)){
                foreach($results1 as $key => &$val){
                    $val->ajaxsearchtype = 'joomla_article';
                }
            }
        }

        if($search_in_k2==1)
        {   
            $db->getQuery(true);
            $query = "SHOW CREATE TABLE #__k2_categories";
            $db->setQuery($query);
                try                
                {
                    // If it fails, it will throw a RuntimeException
                    $result = $db->loadResult(); 
                    $db1 = JFactory::getDbo();
                    $k2cat_query = '';
                    if($k2catid != ''){
                        $k2cat_query = 'AND ' . $db1->quoteName('catid') . ' IN('.$k2catid.')';
                    }
                    $query3 = $db1->getQuery(true);
                    $query3
                    ->select('`id`,`title`,`alias`,`catid`,`introtext`,`fulltext`,`hits`,title as type,`created`')
                    ->from($db1->quoteName('#__k2_items'))
                    ->where('('.$db1->quoteName('title') . ' LIKE '. $db1->quote('%' . $searchword . '%'). ' OR ' . $db1->quoteName('introtext') . ' LIKE '. $db1->quote('%' . $searchword . '%'). ' OR ' . $db1->quoteName('fulltext') . ' LIKE '. $db1->quote('%' . $searchword . '%'). ' ) '.$k2cat_query. ' AND ' . $db1->quoteName('published') . ' = 1 ')
                    ->order('ordering ASC');


                    $db1->setQuery($query3);            
                    $db1->execute();
                    $num_rows = $db1->getNumRows();
                    $results3 = $db1->loadObjectList();  


                    if(!empty($results3)){
                        foreach($results3 as $key => &$val){
                            $val->ajaxsearchtype = 'k2_items';
                        }
                    }
                }
                catch (RuntimeException $e){

                }
        }

        if($search_in_hikashop == 1){
            $db->getQuery(true);
            $query = "SHOW CREATE TABLE #__hikashop_product";
            $db->setQuery($query);
                try                
                {
                    // If it fails, it will throw a RuntimeException
                    $result = $db->loadResult(); 
                    $db2 = JFactory::getDbo();
                    $hika_query = '';
                    if($hikashopcatid != ''){
//                        $hika_query = 'AND hkc.category_id IN('.$hikashopcatid.') AND hkcm.category_type = "product"';
                    }
                    $query5 = $db2->getQuery(true);
                    $query5
                      ->select('hk.product_id as id, hk.product_name as title, hk.product_description as introtext, hk.product_hit as hits, hk.product_created as created, hk.product_alias, hkc.category_id as catid, hkf.file_path as images, hkf.file_ordering, hkcm.category_name')
                      ->from($db2->quoteName('#__hikashop_product').'AS hk')
                      ->innerJoin($db2->quoteName('#__hikashop_product_category').'AS hkc ON hkc.product_id = hk.product_id')
                      ->leftJoin($db2->quoteName('#__hikashop_file').'AS hkf ON hkf.file_ref_id = hk.product_id AND hkf.file_ordering = 0')
                      ->leftJoin($db2->quoteName('#__hikashop_category').'AS hkcm ON hkcm.category_id = hkc.category_id')
                        ->where( '( hk.product_name LIKE "%' . $searchword . '%" OR hk.product_description LIKE "%'. $searchword . '%" )  '.$hika_query.' AND hk.product_published = 1 ')
//                        ->where( '( hk.product_name LIKE  "%' . $searchword . '%" ) AND hk.product_published = 1 ')
//                        ->where( '( REPLACE(REPLACE(REPLACE(REPLACE(product_name, ")",""), "(",""), " ",""), "-","") LIKE  "%' . $searchword . '%" ) AND hk.product_published = 1 ')
                      ->group('hk.product_id')
                      ->order('ordering ASC');

                    $db2->setQuery($query5);
                    $db2->execute();
                    $num_rows = $db2->getNumRows();
                    $results5 = $db2->loadObjectList();

                    if(!empty($results5)){
                        foreach($results5 as $key => &$val){
                            $dateInLocal = gmdate('Y-m-d H:i:s', $val->created);
                            $val->created = $dateInLocal;
                            $val->ajaxsearchtype = 'hikashop_product';
                        }
                    }
                }
                catch (RuntimeException $e){

                }
        }

        if($search_in_sppage==1)
        {   
            $db->getQuery(true);
            $query = "SHOW CREATE TABLE #__sppagebuilder";
            $db->setQuery($query);
                try                
                {
                    // If it fails, it will throw a RuntimeException
                    $result = $db->loadResult(); 
                    $db7 = JFactory::getDbo();
                    $spcat_query = '';
                    if($spcatid != ''){
                        $spcat_query = 'AND ' . $db7->quoteName('catid') . ' IN('.$spcatid.')';
                    }
                    $query7 = $db7->getQuery(true);
                    $query7
                    ->select('`id`,`title`,`catid`, text as introtext,`hits`, created_on as created,`published`,`extension`, `language`')
                    ->from($db7->quoteName('#__sppagebuilder'))
                    ->where('('.$db7->quoteName('title') . ' LIKE '. $db7->quote('%' . $searchword . '%'). ' OR ' . $db7->quoteName('text') . ' LIKE '. $db7->quote('%' . $searchword . '%'). ' ) '.$spcat_query. ' AND ' .'published = 1 AND extension = "com_sppagebuilder"')
                    ->order('ordering ASC');


                    $db7->setQuery($query7);            
                    $db7->execute();
                    $num_rows = $db7->getNumRows();
                    $results7 = $db7->loadObjectList();  


                    if(!empty($results7)){
                        foreach($results7 as $key => &$val){
                            $val->ajaxsearchtype = 'sppage';
                        }
                    }
                }
                catch (RuntimeException $e){

                }
        }

    $merged_results = array_merge($results1, $results3, $results5, $results7);   
    $unique_array = modEbajaxsearchHelper::array_multi_unique($merged_results);
    $unique_array = json_decode( json_encode($unique_array), true);

    if($by_ordering == 'newest'){
        array_multisort(array_map(function($element) {
            return $element['created'];
        }, $unique_array), SORT_DESC, $unique_array);
    } else if($by_ordering == 'oldest'){
        array_multisort(array_map(function($element) {
            return $element['created'];
        }, $unique_array), SORT_ASC, $unique_array);
    } else if($by_ordering == 'popular'){
        array_multisort(array_map(function($element) {
            return $element['hits'];
        }, $unique_array), SORT_DESC, $unique_array);
    } else if($by_ordering == 'alpha'){
        array_multisort(array_map(function($element) {
            return $element['title'];
        }, $unique_array), SORT_ASC, $unique_array);
    } else {
        array_multisort(array_map(function($element) {
            return $element['created'];
        }, $unique_array), SORT_DESC, $unique_array);
    }
        // echo "<pre>"; print_r($unique_array);echo "</pre>";

        // Get output
    $output = '';
    if($searchword != ''){
        if($unique_array){
            $count = 0;
            $output .= '<div class="result_wrap uk-card uk-card-default uk-box-shadow-small uk-overflow-hidden uk-border-rounded uk-overflow-auto uk-margin-small-top">';
            foreach($unique_array as $result){
                $itemurl = '';
                $cattitle = '';
                $image_intro_img = '';
                $result_type_img = '';
                $image_pro_img = '';
                $article_class = '';
                if($result['ajaxsearchtype'] == 'joomla_article'){
                    $article_text = $result['introtext'];
                    $art_img = isset($result['images'])?$result['images']:'';
                    $allvalue = array();
                    if($art_img!=''){
                        $allvalue = json_decode($result['images']);
                    }
                    $image_intro_img = isset($allvalue->image_intro)?$allvalue->image_intro:'';
                    $image_intro_img_url = '';
                    if($image_intro_img!=''){
                        $arrParsedUrl = parse_url($image_intro_img);
                        if (!empty($arrParsedUrl['scheme']))
                        {
                            // Contains http:// schema
                            if ($arrParsedUrl['scheme'] === "http")
                            {
                                $image_intro_img_url = $image_intro_img;
                            }
                            // Contains https:// schema
                            else if ($arrParsedUrl['scheme'] === "https")
                            {
                                $image_intro_img_url = $image_intro_img;
                            }
                        }
                        // Don't contains http:// or https://
                        else
                        {   
                             $image_intro_img_url = JUri::base().$allvalue->image_intro;
                        }
                    }
                    $itemurl = ContentHelperRoute::getArticleRoute($result['id'],  $result['catid']);
                    $cattitle = modEbajaxsearchHelper::getCategoryName($result['id']);
                    if($image_intro_img != '' && $show_image != 0){
                        $article_class = 'result-products';
                    } else {
                        $article_class = 'desc_fullwidth';
                    }
                } else if($result['ajaxsearchtype'] == 'k2_items'){
                    $article_text = $result['introtext'];
                    $result_type_img = isset($result['type'])?$result['type']:'';
                    $k2itempath=JUri::base().'media/k2/items/cache/'.md5("Image".$result['id'])."_M.jpg";

                    require_once JPATH_SITE.'/components/com_k2/helpers/route.php';
                    $itemurl  = K2HelperRoute::getItemRoute($result['id'].':'.$result['alias'], $result['catid']);
                    $cattitle = modEbajaxsearchHelper::getK2CategoryName($result['catid']);
                    if($result_type_img!='' && $show_image != 0 && @getimagesize($k2itempath) != ''){
                        $article_class = 'result-products';
                    } else {
                        $article_class = 'desc_fullwidth';
                    }
                } else if($result['ajaxsearchtype'] == 'hikashop_product'){
                    $article_text = $result['introtext'];
                    $product_image = '';
                    $image_pro_img = isset($result['images'])?$result['images']:'';
                    $image_pro_img_url = '';
                    if($image_pro_img!=''){
                        $arrParsedUrl = parse_url($image_pro_img);
                        if (!empty($arrParsedUrl['scheme']))
                        {
                            // Contains http:// schema
                            if ($arrParsedUrl['scheme'] === "http")
                            {
                                $image_pro_img_url = $image_pro_img;
                            }
                            // Contains https:// schema
                            else if ($arrParsedUrl['scheme'] === "https")
                            {
                                $image_pro_img_url = $image_pro_img;
                            }
                        }
                        // Don't contains http:// or https://
                        else
                        {   
                            $image_pro_img_url = JUri::base().'images/com_hikashop/upload/'.$result['images'];
                        }
                    }
                    require_once JPATH_SITE.'/components/com_hikashop/helpers/route.php';
                    $itemurl = hikashopTagRouteHelper::getProductRoute($result['id'].':'.$result['product_alias'],$result['catid'], '');
                    $cattitle_pro = isset($result['category_name'])?$result['category_name']:'';
                    $cattitle = ucfirst($cattitle_pro);
                    if($image_pro_img != '' && $show_image != 0){
                        $article_class = 'result-products';
                    } else {
                        $article_class = 'desc_fullwidth';
                    }
                } else if($result['ajaxsearchtype'] == 'sppage'){
                    require_once (JPATH_ADMINISTRATOR.'/components/com_sppagebuilder/builder/classes/addon.php');
                    require_once JPATH_SITE.'/components/com_sppagebuilder/helpers/route.php';

                    $page_text = SpPageBuilderAddonHelper::__($result['introtext']);
                    $content = json_decode($page_text);
                    $pageName = 'page-'.$result['id']; 
                    $article_text_tag = AddonParser::viewAddons( $content, 0, $pageName );
                    $article_text = strip_tags($article_text_tag);

                    $itemurl = SppagebuilderHelperRoute::getPageRoute($result['id'], $result['language']);

                    $spcattitle = modEbajaxsearchHelper::getSPCategoryName($result['catid']);
                    $cattitle = '';
                    if(!empty($spcattitle)){
                        $cattitle = $spcattitle->title;
                    }

                    preg_match( '@src="([^"]+)"@' , $article_text_tag, $matches );
                    $image_intro_img = isset($matches[1])?$matches[1]:'';
                    if($image_intro_img!=''){
                        $image_intro_img_url = $matches[1];
                    }

                    if($image_intro_img != '' && $show_image != 0){
                        $article_class = 'result-products';
                    } else {
                        $article_class = 'desc_fullwidth';
                    }

                }


                $article_title = $result['title'];
                $highlighted_text = "<strong style='font-weight:bold;background-color:#ff0'>$searchword</strong>";
                $article_title1 = str_ireplace($searchword, $highlighted_text, $article_title);

                $cattitle1 = str_ireplace($searchword, $highlighted_text, $cattitle);

                $limit = 100;
                if (strlen($article_text) > $limit) {
                    $article_content =  (substr($article_text, 0, $limit)).' ...';
                } else {
                    $article_content =  $article_text;
                }
                $article_content = strip_tags($article_content);
                $article_content1 = str_ireplace($searchword, $highlighted_text, $article_content);

                $output .= '<div class="result_box">';
                $output .= '<a class="uk-display-block uk-text-dark hoverAccent uk-padding-small result-element '.$article_class.'" href="'. $itemurl.'" target=""><div class="uk-grid-medium" data-uk-grid>';

                if($show_image == 1){
                    if($result['ajaxsearchtype'] == 'joomla_article'){
                        if($image_intro_img != ''){
                            $output .= '<div class="uk-width-1-4 result_img"><img class="uk-box-shadow-small uk-border-rounded" alt="'.$article_title.'" src="'.$image_intro_img_url.'"></div>';
                        }
                    } else if($result['ajaxsearchtype'] == 'k2_items'){
                        if($result_type_img!=''){
                            if(@getimagesize($k2itempath) != ''){
                                $output .= '<div class="uk-width-1-4 result_img"><img class="uk-box-shadow-small uk-border-rounded" alt="'.$result['title'].'" src="'.$k2itempath.'"></div>';
                            }                                                                                       

                        }
                    } else if($result['ajaxsearchtype'] == 'hikashop_product'){
                        if($image_pro_img != ''){
                            $output .= '<div class="uk-width-1-4 result_img"><img class="uk-box-shadow-small uk-border-rounded" alt="'.$article_title.'" src="'.$image_pro_img_url.'"></div>';
                        }
                    } else if($result['ajaxsearchtype'] == 'sppage'){
                        if($image_intro_img != ''){
                            $output .= '<div class="uk-width-1-4 result_img"><img class="uk-box-shadow-small uk-border-rounded" alt="'.$article_title.'" src="'.$image_intro_img_url.'"></div>';
                        }
                    }
                }
                $output .= '<div class="uk-width-expand uk-flex uk-flex-middle result_content">';
                if($show_title == 1){
                    $output .= '<span class="uk-text-small font f600  small-title">'.$article_title.'</span><span class="small-cat">'.$cattitle.'</span></div>';
                } if($show_description == 1){
                    $output .= '<span class="small-desc">'.$article_content.'</span>';
                }
                $output .= '</div></a><hr class="uk-margin-remove"></div>';
                $count++; }
                $output .= '</div>';
            } else {
                $output .= '<div class="uk-border-rounded uk-box-shadow-small uk-card uk-card-default uk-margin-small-top uk-overflow-auto uk-overflow-hidden result_wrap"><div class="is_noresult"><div class="uk-padding uk-text-center uk-width-large"><div class="uk-margin-bottom"><img class="uk-text-muted" src="'.JUri::base().'/images/sprite.svg#search" width="48" height="48" data-uk-svg></div><p class="uk-text-small uk-margin-remove font f500 uk-text-dark">'.JText::sprintf('SEARCHNORESULTS').'</p></div></div></div>';
            }
        } 
        return $output;
    }

    function getCategoryName($articleId)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('c.title');
        $query->from('#__categories AS c');
        $query->join("INNER","#__content AS a ON c.id = a.catid");
        $query->where("a.id = '$articleId'");
        $db->setQuery($query);
        $row = $db->loadObject();
        return $row->title;
    }
    function getK2CategoryName($articleId)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('name');
        $query->from('#__k2_categories');        
        $query->where("id = '$articleId'");
        $db->setQuery($query);
        $row = $db->loadObject();
        return $row->name;
    }
    function getSPCategoryName($catid)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('title');
        $query->from('#__categories');
        $query->where("id = '$catid'");
        $db->setQuery($query);
        $row = $db->loadObject();
        return $row;
    }
    function array_multi_unique($multiArray){
        $uniqueArray = array();
        foreach($multiArray as $subArray){
            if(!in_array($subArray, $uniqueArray)){
                $uniqueArray[] = $subArray;
            }
        }
        return $uniqueArray;
    }

}