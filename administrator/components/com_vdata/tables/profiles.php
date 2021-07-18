<?php
/*------------------------------------------------------------------------
# com_vdata - vData
# ------------------------------------------------------------------------
# author    Team WDMtech
# copyright Copyright (C) 2014 www.wdmtech.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.wdmtech.com
# Technical Support:  Forum - http://www.wdmtech.com/support-forum
-----------------------------------------------------------------------*/
 // No direct access
defined('_JEXEC') or die('Restricted access');


class TableProfiles extends JTable
{
    /**
     * Primary Key
     *
     * @var int
     */
    var $id = null;
 
    /**
     * @var string
     */
    
    var $title = null;
    var $pluginid = null;
    var $iotype = null;
    var $source_enc = null;
    var $quick = null;
    var $params = null;
    
    function TableProfiles( &$db ) {
        parent::__construct('#__vd_profiles', 'id', $db);
    }
	
	function bind($array, $ignore = '')
	{
		
		return parent::bind($array, $ignore);
		
	}
	
	function check()
	{
		$db = $this->getDbc();
		$this->id = intval($this->id);
		$this->pluginid = intval($this->pluginid);
		
		if(array_key_exists('joins', $this->params)){
			if(!array_key_exists('columns', $this->params['joins'])){
				$this->params['joins']['columns'] = array();
			}
			foreach($this->params['joins']['table1'] as $key=>$val){
				if(!array_key_exists($key, $this->params['joins']['columns'])){
					$this->params['joins']['columns'][$key] = array();
				}
			}
			ksort($this->params['joins']['columns']);
		}
		
		$query_params = (object)$this->params;
		
		// replace single quote
		if( property_exists($query_params, 'filters') ){
			if(array_key_exists('value', $query_params->filters)){
				foreach($query_params->filters["value"] as $key=>$value){
					$temp= str_replace('\'', '"', $value);
					$this->params["filters"]["value"][$key] = $temp;
				}
			}
		}

		if(property_exists($query_params, 'fields')){
			foreach($query_params->fields as $key=>$value){
				if($value["data"]=='defined'){
					$temp= str_replace('\'', '"', $value["default"]);
					$this->params["fields"][$key]["default"] = $temp;
				}
			}
		}
		
		if(empty($this->title))	{
			$this->setError( JText::_('PLZ_ENTER_TITLE') );
			return false;
		}
		
		if(empty($this->pluginid))	{
			$this->setError( JText::_('PLZ_SELECT_PLUGIN') );
			return false;
		}
		if(isset($this->params["operation"]) && ($this->params["operation"]!=1) && !isset($this->params["unqkey"])){
			$msg = (isset($this->id) && !empty($this->id))?JText::_('VDATA_PROFILE_UPDATE_SELECT_PRIMARY_KEY'):JText::_('VDATA_PROFILE_INSERT_SELECT_PRIMARY_KEY');
			$this->setError( $msg );
			return false;
		}
       if(!isset($this->params["unqkey"]))
		{
			$this->params["unqkey"]=array();
		}		
		$this->params = json_encode($this->params);
		
		//export query validation
		if($this->iotype==1 && $this->quick!=1){
			$query = "SELECT ".$db->quoteName($query_params->table)."."."* FROM ".$db->quoteName($query_params->table);
			$query_params->filters = (object) $query_params->filters;
			
			//apply filters
			if(!empty($query_params->filters) && property_exists($query_params->filters, 'column')){
				//filter tracker
				$applyFilter = array();
				$filterTables = array();
				
				foreach($query_params->filters->column as $j=>$column){
					$filterField = explode('.', $column);
					$applyFilter[$j] = false;
					
					//base table reference column filter
					if(count($filterField)==2){
						if(isset($query_params->fields) && !empty($query_params->fields)){
							foreach($query_params->fields as $iofield=>$ioval){
								if( ($ioval["data"]=='reference') && ($ioval["table"]==$filterField[0]) && !in_array($ioval["table"], $filterTables) ){
									$query .= " JOIN ".$db->quoteName($filterField[0]).' ON '.$db->quoteName($query_params->table).".".$db->quoteName($iofield)."=".$db->quoteName($ioval["table"]).".".$db->quoteName($ioval["on"]);
									$applyFilter[$j] = true;
									array_push($filterTables, $ioval["table"]);
									break;
								}
							}
						}
					}
					//child table column filter
					elseif(count($filterField)==3){
						
						if( isset($query_params->joins) && isset($query_params->joins["table2"]) ){
							foreach($query_params->joins["table2"] as $ioidx=>$iotable){
								if( ($iotable==$filterField[1]) ){
									if( ($query_params->joins["table1"][$ioidx]==$query_params->table) && !in_array($iotable, $filterTables) ){
										//
										$query .= ' JOIN '.$db->quoteName($filterField[1])." ON ".$db->quoteName($query_params->table).".".$db->quoteName($query_params->joins["column1"][$ioidx])."=".$db->quoteName($filterField[1]).".".$db->quoteName($query_params->joins["column2"][$ioidx]);
										$applyFilter[$j] = true;
										array_push($filterTables, $iotable);
									}
									else{
										$tempIdx = $ioidx;
										$left = $query_params->joins["table1"][$ioidx];
										$joinPath = array($ioidx);
										
										while($tempIdx>=0){
											$tempIdx--;
											if($left==$query_params->joins["table2"][$tempIdx]){
												array_unshift($joinPath, $tempIdx);
												if($query_params->joins["table1"][$tempIdx]==$query_params->table){
													break;
												}
											}
										}
										
										foreach($joinPath as $joinIdx){
											if(!in_array($query_params->joins["table2"][$joinIdx], $filterTables)){
												$query .= ' JOIN '.$db->quoteName($query_params->joins["table2"][$joinIdx]).' ON '.$db->quoteName($query_params->joins["table1"][$joinIdx]).'.'.$db->quoteName($query_params->joins["column1"][$joinIdx]).'='.$db->quoteName($query_params->joins["table2"][$joinIdx]).'.'.$db->quoteName($query_params->joins["column2"][$joinIdx]);
												array_push($filterTables, $query_params->joins["table2"][$joinIdx]);
												$applyFilter[$j] = true;
											}
										}
									}
									break;
								}
							}
						}
						
					}
					//child table reference column filter
					elseif(count($filterField)==4){
						if( isset($query_params->joins) && isset($query_params->joins["table2"]) ){
							if(!in_array($filterField[1], $filterTables)){
								foreach($query_params->joins["table2"] as $ioidx=>$iotable){
									if($iotable==$filterField[1]){
										if($query_params->joins["table1"][$ioidx]==$query_params->table){
											$query .= ' JOIN '.$db->quoteName($filterField[1])." ON ".$db->quoteName($query_params->table).".".$db->quoteName($query_params->joins["column1"][$ioidx])."=".$db->quoteName($filterField[1]).".".$db->quoteName($query_params->joins["column2"][$ioidx]);
											$applyFilter[$j] = true;
											array_push($filterTables, $iotable);
										}
										else{
											$tempIdx = $ioidx;
											$left = $query_params->joins["table1"][$ioidx];
											$joinPath = array($ioidx);
											while($tempIdx>=0){
												$tempIdx--;
												if($left==$query_params->joins["table2"][$tempIdx]){
													array_unshift($joinPath, $tempIdx);
													if($query_params->joins["table1"][$tempIdx]==$query_params->table){
														break;
													}
												}
											}
											foreach($joinPath as $joinIdx){
												if(!in_array($query_params->joins["table2"][$joinIdx], $filterTables)){
													$query .= ' JOIN '.$db->quoteName($query_params->joins["table2"][$joinIdx]).' ON '.$db->quoteName($query_params->joins["table1"][$joinIdx]).'.'.$db->quoteName($query_params->joins["column1"][$joinIdx]).'='.$db->quoteName($query_params->joins["table2"][$joinIdx]).'.'.$db->quoteName($query_params->joins["column2"][$joinIdx]);
													array_push($filterTables, $query_params->joins["table2"][$joinIdx]);
													$applyFilter[$j] = true;
												}
											}
										}
									}
									
								}
							}
							if(!in_array($filterField[2], $filterTables)){
								$tableIndex = array_search($filterField[1], $query_params->joins["table2"]);
								foreach($query_params->joins["columns"][$tableIndex] as $ccol=>$cval){
									if($cval['data']=='reference' && $cval['table']==$filterField[2]){
										$query .= ' JOIN '.$db->quoteName($filterField[2]).' ON '.$db->quoteName($filterField[1]).'.'.$db->quoteName($ccol).'='.$db->quoteName($filterField[2]).'.'.$db->quoteName($cval['on']);
										
										array_push($filterTables, $filterField[2]);
										$applyFilter[$j] = true;
										break;
									}
								}
								
							}
							if( in_array($filterField[1], $filterTables) && in_array($filterField[2], $filterTables) ){
								$applyFilter[$j] = true;
							}
							
						}
					}
				}
				
				$query .= " WHERE ";
				$m = $n = -1;
				foreach($query_params->filters->column as $j=>$column){
					$filterField = explode('.', $column);
					$filterFieldPath = count($filterField);
					//base table reference filter
					if( ($filterFieldPath==2) && ($applyFilter[$j]) ){
						$query .= $db->quoteName($filterField[0]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
					}
					//child table filter
					elseif( ($filterFieldPath==3) && ($applyFilter[$j]) ){
						$query .= $db->quoteName($filterField[1]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
					}
					//child table reference filter
					elseif( ($filterFieldPath==4) && ($applyFilter[$j]) ){
						$query .= $db->quoteName($filterField[2]).'.'.$db->quoteName($filterField[$filterFieldPath-1])." ";
					}
					//base table filters
					else{
						$query .= $db->quoteName($query_params->table).'.'.$db->quoteName($column)." ";
					}
					
					if($query_params->filters->cond[$j]=='between' || $query_params->filters->cond[$j]=='notbetween'){
						$n = ($n<0)?0:$n+1;
					}
					else{
						$m = ($m<0)?0:$m+1;
						$value = $this->getQueryFilteredValue($query_params->filters->value[$m]);
					}
					
					if($query_params->filters->cond[$j]=='in'){
						$query .= " IN ( ".$db->quote($value)." )";
					}
					elseif($query_params->filters->cond[$j]=='notin'){
						$query .= " NOT IN ( ".$value." )";
					}
					elseif($query_params->filters->cond[$j]=='between'){
						$value1 = $this->getQueryFilteredValue($query_params->filters->value1[$n]);
						$value2 = $this->getQueryFilteredValue($query_params->filters->value2[$n]);
						$query .= " BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
					}
					elseif($query_params->filters->cond[$j]=='notbetween'){
						$value1 = $this->getQueryFilteredValue($query_params->filters->value1[$n]);
						$value2 = $this->getQueryFilteredValue($query_params->filters->value2[$n]);
						$query .= " NOT BETWEEN ".$db->quote($value1)." AND ".$db->quote($value2);
					}
					elseif($query_params->filters->cond[$j]=='like'){
						$query .= " LIKE ".$db->quote($value);
					}
					elseif($query_params->filters->cond[$j]=='notlike'){
						$query .= " NOT LIKE ".$db->quote($value);
					}
					elseif($query_params->filters->cond[$j]=='regexp'){
						$query .= " REGEXP ".$db->quote($query_params->filters->value[$j]);
					}
					else{
						$query .= $query_params->filters->cond[$j]." ".$db->quote($value);
					}

					//sql operator
					if($j < (count($query_params->filters->column)-1)){
						$query .= " ".$query_params->filters->op." ";
					}
				}
			}
			if(!empty($query_params->groupby)){
				$query .= " GROUP BY ".$db->quoteName($query_params->table).".".$db->quoteName($query_params->groupby);
			}
			if(!empty($query_params->orderby)){
				$query .= " ORDER BY ".$db->quoteName($query_params->table).".".$db->quoteName($query_params->orderby)." ".$query_params->orderdir;
			}
			
			try{
				$db->setQuery($query,0, 1);
				$db->execute();
				// $data = $db->loadAssocList();
			}
			catch(Exception $e){
				$this->setError( $e->getMessage() );
				return false;
			}
		}

		if($db->getErrorNum())	{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
		
		return parent::check();
		
	}
	
	function getQueryFilteredValue($value){
		
		$val = $value;
		$hd_sql_pattern = '/^@vdSql:(.*?)$/';
		$hd_php_pattern = '/^@vdPhp:(.*?)$/';
		if(preg_match($hd_sql_pattern, $value, $sqlmatches)){
			$val = $sqlmatches[1];
		}
		elseif(preg_match($hd_php_pattern, $value, $phpmatches)){
			$func = $phpmatches[1];
			$val = (eval("return $func;"));
		}
		
		return $val;
	}
	
	function store($updateNulls = false)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		if($this->id){
			$this->modified =  $date->toSql();//$date->toMySQL();
			$this->modified_by = $user->id;
		}
		else{
			$this->created = $date->toSql();//$date->toMySQL();
			$this->created_by = $user->id;
		}
		if(!parent::store($updateNulls)){
			return false;
		}
		
		$profile = $this->getProfile();
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('onSaveProfile', array($this->id));
			return true;
		}catch(Exception $e){
			$this->setError($e->getMessage());
			return false;
		}
		
		return true;
	
	}
	
	function delete($oid=null)
	{
		
		$this->id = $oid;
		
		$profile = $this->getProfile();
		
		if(empty($profile))	{
			$this->setError(JText::_('PROFILE_NOT_FOUND'));
			return false;
		}
		
		if(!parent::delete($oid))	{
			return false;
		}
		
		JPluginHelper::importPlugin('vdata', $profile->plugin);
		$dispatcher = JDispatcher::getInstance();
		
		try{
			$dispatcher->trigger('onDeleteProfile', array($oid));
			return true;
		}catch(Exception $e){
			$this->setError($e->getMessage());
			return false;
		}
		
	}
	
	//get the profile object with the associated plugin info
	function getProfile()
	{
				
		$query = 'select i.*, e.element as plugin from #__vd_profiles as i join #__extensions as e on (i.pluginid=e.extension_id and e.enabled=1) where i.id = '.(int)$this->id;
		$this->_db->setQuery( $query );
		$item = $this->_db->loadObject();
		
		return $item;
		
	}
	
	function getDbc()
	{
		$config = JFactory::getConfig();
		$hd_config = $this->getConfig();
		$dbconfig = json_decode($hd_config->dbconfig);
		if($dbconfig->local_db==1){
			$dbc = JFactory::getDbo();
		}
		else{
			$option = array();
			if( property_exists($dbconfig, 'driver') && property_exists($dbconfig, 'host') && property_exists($dbconfig, 'user') && property_exists($dbconfig, 'password') && property_exists($dbconfig, 'database') && property_exists($dbconfig, 'dbprefix') ){
				$option['driver'] = $dbconfig->driver;
				$option['host'] = $dbconfig->host;
				$option['user'] = $dbconfig->user;
				$option['password'] = $dbconfig->password;
				$option['database'] = $dbconfig->database;
				if(!empty($dbconfig->dbprefix))
					$option['prefix'] = $dbconfig->dbprefix;
				
			}
			try{
				$dbc = JDatabaseDriver::getInstance($option);
				$dbc->connect();
			}
			catch(Exception $e){
				throw new Exception($e->getMessage());
				return false;
			}
		}
		
		return $dbc;
	}
	
	//method to get complete configuration
	function getConfig()
	{
		$db = JFactory::getDbo();
		$query = "select * from #__vd_config where id =1";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
}
