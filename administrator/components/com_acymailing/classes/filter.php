<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

class filterClass extends acymailingClass{

	var $tables = array('filter');
	var $pkey = 'filid';
	var $report = array();
	var $subid;
	var $onlynew = false;
	var $didAnAction = false;

	function trigger($triggerName){
		if(!acymailing_level(3)) return;

		$config = acymailing_config();
		if($triggerName != 'daycron' && !$config->get('triggerfilter_'.$triggerName)) return;

		$this->database->setQuery("SELECT * FROM `#__acymailing_filter` WHERE `trigger` LIKE '%".acymailing_getEscaped($triggerName, true)."%' ORDER BY `filid` ASC");
		$filters = $this->database->loadObjectList();

		if(empty($filters) && $triggerName != 'daycron'){
			$newconfig = new stdClass();
			$name = 'triggerfilter_'.$triggerName;
			$newconfig->$name = 0;
			$config->save($newconfig);
			return;
		}
		foreach($filters as $oneFilter){
			if(empty($oneFilter->published)) continue;
			if($triggerName == 'daycron' && $oneFilter->daycron > time()) continue;
			if(!empty($oneFilter->filter)) $oneFilter->filter = unserialize($oneFilter->filter);
			if(!empty($oneFilter->action)) $oneFilter->action = unserialize($oneFilter->action);
			$this->execute($oneFilter->filter, $oneFilter->action);
			if($triggerName == 'daycron'){
				$newDaycron = $oneFilter->daycron+86400;
				while($newDaycron < time())	$newDaycron += 86400;
				$this->database->setQuery('UPDATE #__acymailing_filter SET `daycron` = '.intval($newDaycron).' WHERE `filid` = '.intval($oneFilter->filid));
				$this->database->query();
			}
		}
	}


	function displayFilters($filters){
		$resultFilters = array();
		if(empty($filters['type'])) return $resultFilters;
		acymailing_importPlugin('acymailing');
		foreach($filters['type'] as $num => $oneType){
			if(empty($oneType)) continue;
			$resultFilters = array_merge($resultFilters, acymailing_trigger('onAcyDisplayFilter_'.$oneType, array($filters[$num][$oneType])));
		}
		return $resultFilters;
	}

	function execute($filters, $actions){
		acymailing_importPlugin('acymailing');
		$query = new acyQuery();

		if(!empty($this->subid)){
			$subArray = explode(',', trim($this->subid, ','));
			acymailing_arrayToInteger($subArray);
			$query->where[] = 'sub.subid IN ('.implode(',', $subArray).')';
		}

		if(!empty($filters['type'])){
			foreach($filters['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				$oldObject = (count($query->where) + count($query->leftjoin) + count($query->join)).'_'.$query->limit.$query->orderBy;
				$res = acymailing_trigger('onAcyProcessFilter_'.$oneType, array(&$query, $filters[$num][$oneType], $num));
				$newObject = (count($query->where) + count($query->leftjoin) + count($query->join)).'_'.$query->limit.$query->orderBy;
				if(count($res) == 0 && $newObject == $oldObject){
					$query->where[] = '0 = 1';
					$this->report[] = 'Function onAcyProcessFilter_'.$oneType.' did not add a condition, filter blocked. Maybe a plugin is missing ?';
				}
			}
		}

		if(!empty($actions['type'])){
			$this->didAnAction = $this->didAnAction || $query->count() > 0;
			foreach($actions['type'] as $num => $oneType){
				if(empty($oneType) || !isset($actions[$num][$oneType])) continue;
				$this->report = array_merge($this->report, acymailing_trigger('onAcyProcessAction_'.$oneType, array(&$query, $actions[$num][$oneType], $num)));
			}
		}
	}


	function saveForm(){
		$filter = new stdClass();
		$filter->filid = acymailing_getCID('filid');

		$formData = acymailing_getVar('array', 'data', array(), '');

		foreach($formData['filter'] as $column => $value){
			acymailing_secureField($column);
			$filter->$column = strip_tags($value);
		}
		$config = acymailing_config();
		$alltriggers = array_keys((array)acymailing_getVar('none', 'trigger'));
		$filter->trigger = implode(',', $alltriggers);
		$newConfig = new stdClass();
		foreach($alltriggers as $oneTrigger){
			$name = 'triggerfilter_'.$oneTrigger;
			if($config->get($name)) continue;
			$newConfig->$name = 1;
		}

		if(in_array('daycron', $alltriggers)){
			$newHours = acymailing_getVar('none', 'triggerhours');
			$newMinutes = acymailing_getVar('none', 'triggerminutes');
			$newTime = acymailing_getTime(date('Y').'-'.date('m').'-'.date('d').' '.$newHours.':'.$newMinutes);
			if($newTime < time()) $newTime += 86400;
			$filter->daycron = $newTime;
		}

		if(!empty($newConfig)) $config->save($newConfig);

		$data = array('action', 'filter');
		foreach($data as $oneData){
			$filter->$oneData = array();
			$formData = acymailing_getVar('none', $oneData);
			if(!empty($formData['type'])){
				foreach($formData['type'] as $num => $oneType){
					if(empty($oneType)) continue;
					$filter->{$oneData}['type'][$num] = $oneType;
					$filter->{$oneData}[$num][$oneType] = $formData[$num][$oneType];
				}
			}
			$filter->$oneData = serialize($filter->$oneData);
		}

		$filid = $this->save($filter);
		if(!$filid) return false;

		acymailing_setVar('filid', $filid);
		return true;
	}

	function get($filid, $default = null){
		$query = 'SELECT a.* FROM #__acymailing_filter as a WHERE a.`filid` = '.intval($filid).' LIMIT 1';
		$this->database->setQuery($query);

		$filter = $this->database->loadObject();
		if(!empty($filter->filter)){
			$filter->filter = unserialize($filter->filter);
		}

		if(!empty($filter->action)){
			$filter->action = unserialize($filter->action);
		}

		if(!empty($filter->trigger)){
			$filter->trigger = array_flip(explode(',', $filter->trigger));
		}

		return $filter;
	}

	function countReceivers($listids, $filters, $mailid = 0){

		if(empty($listids)) return 0;

		acymailing_importPlugin('acymailing');
		$query = new acyQuery();

		acymailing_arrayToInteger($listids);
		$query->from = '#__acymailing_listsub as listsub';
		$query->join[] = '#__acymailing_subscriber as sub ON sub.subid = listsub.subid';
		$query->where[] = 'listsub.listid IN ('.implode(',', $listids).') AND listsub.status=1';
		$config = acymailing_config();
		if($config->get('require_confirmation')){
			$query->where[] = 'sub.confirmed = 1';
		}
		$query->where[] = 'sub.enabled = 1 AND sub.accept = 1';

		if($this->onlynew && !empty($mailid)){
			$query->leftjoin[] = '#__acymailing_userstats as userstats ON sub.subid = userstats.subid AND userstats.mailid = '.intval($mailid);
			$query->where[] = 'userstats.subid IS NULL';
		}

		if(!empty($filters['type'])){
			foreach($filters['type'] as $num => $oneType){
				if(empty($oneType)) continue;
				acymailing_trigger('onAcyProcessFilter_'.$oneType, array(&$query, $filters[$num][$oneType], $num));
			}
		}

		return $query->count();
	}

	function addJSFilterFunctions(){
		$js = " var numFilters = 0;
				function addAcyFilter(){
					var newdiv = document.createElement('div');
					newdiv.id = 'filter'+numFilters;
					newdiv.className = 'plugarea';
					newdiv.innerHTML = '';
					if(numFilters > 0) newdiv.innerHTML += '".acymailing_translation('FILTER_AND')."';
					newdiv.innerHTML += document.getElementById('filters_original').innerHTML.replace(/__num__/g, numFilters);
					if(document.getElementById('allfilters')){
						document.getElementById('allfilters').appendChild(newdiv); updateFilter(numFilters); numFilters++;
					}
				}
				function countresults(num){ ";
		if(!acymailing_isAdmin()) $js .= " return; ";
		$js .= "
					if(document.getElementById('filtertype'+num).value == ''){
						document.getElementById('countresult_'+num).innerHTML = '';
						return;
					}
					document.getElementById('countresult_'+num).innerHTML = '<span class=\"onload\"></span>';

					var dataform = new FormData(document.getElementById('adminForm'));
					dataform.append('task', 'countresults');
					dataform.append('ctrl', 'filter');
					dataform.append('option', 'com_acymailing');
					dataform.append('tmpl', 'component');
					dataform.append('num', num);

					var xhr = new XMLHttpRequest();
					xhr.open('POST', 'index.php?option=com_acymailing&tmpl=component&ctrl=filter&task=countresults&num='+num);
					xhr.onload = function(){
						document.getElementById('countresult_'+num).innerHTML = xhr.responseText;
					};
					xhr.send(dataform);
				}

				function updateFilter(filterNum){
					currentFilterType =window.document.getElementById('filtertype'+filterNum).value;
					if(!currentFilterType){
						window.document.getElementById('filterarea_'+filterNum).innerHTML = '';
						document.getElementById('countresult_'+filterNum).innerHTML = '';
						return;
					}
					filterArea = 'filter__num__'+currentFilterType;
					window.document.getElementById('filterarea_'+filterNum).innerHTML = window.document.getElementById(filterArea).innerHTML.replace(/__num__/g,filterNum);
					if(typeof(window['onAcyDisplayFilter_'+currentFilterType]) == 'function') {
						try{ window['onAcyDisplayFilter_'+currentFilterType](filterNum); }catch(e){alert('Error in the onAcyDisplayFilter_'+currentFilterType+' function : '+e); }
					}
				}

				function displayCondFilter(fct, element, num, extra){";
		$ctrl = 'filter';
		if(!acymailing_isAdmin()) $ctrl = 'frontfilter';
		$js .= "
					var xhr = new XMLHttpRequest();
					xhr.open('GET', 'index.php?option=com_acymailing&tmpl=component&ctrl=".$ctrl."&task=displayCondFilter&fct='+fct+'&num='+num+'&'+extra);
					xhr.onload = function(){
						document.getElementById(element).innerHTML = xhr.responseText;
						countresults(num);
					};
					xhr.send();
				}";
		acymailing_addScript(true, $js);

		$this->addDateDetailHandling();

		$eltsToClean = array('acybase_filters', 'allfilters', 'allactions');
		acymailing_removeChzn($eltsToClean);
	}

	protected function addDateDetailHandling(){
		$js = "var dateFieldSelected = null;
				function updateDateDetail(element){
					if(element.value=='relativedate'){
						document.getElementById('specificDate').style.display = 'none';
						document.getElementById('relativeDate').style.display = 'inline';
					} else if(element.value=='specificdate'){
						document.getElementById('specificDate').style.display = 'inline';
						document.getElementById('relativeDate').style.display = 'none';
					} else{
						document.getElementById('specificDate').style.display = 'none';
						document.getElementById('relativeDate').style.display = 'none';
					}
				}

				function hideDateDetail(){
					document.getElementById('dateDetails').style.display = 'none';
				}

				function validateDateField(){
					if(document.getElementById('dateDetail_typerelativedate').checked == true){
						dateVal = '{time}';
						if(document.getElementById('dateDetail_delay').value != 0){
							if(document.getElementById('dateDetail_operator').value == 'before'){
								dateVal += '-';
							} else{
								dateVal += '+';
							}
							if(document.getElementById('dateDetail_length').value == 'minutes'){
								dateVal += document.getElementById('dateDetail_delay').value * 60;
							} else if(document.getElementById('dateDetail_length').value == 'hours'){
								dateVal += document.getElementById('dateDetail_delay').value * 3600;
							} else{
								dateVal += document.getElementById('dateDetail_delay').value * 24 * 3600;
							}
						}
						dateFieldSelected.value = dateVal;
					} else{
						year = document.getElementById('dateDetail_year').value;
						month = document.getElementById('dateDetail_month').value;
						day = document.getElementById('dateDetail_day').value;
						dateFieldSelected.value = year+'-'+month+'-'+day;
					}
					hideDateDetail();
					if(dateFieldSelected.name.substr(0,6) == 'filter'){ dateFieldSelected.onchange(); }
				}

				function displayDatePicker(element,e){
					dateFieldSelected = element;
					try{
						currentVal = element.value;
						if(currentVal.substr(0,6) == '{time}'){
							toggleDateBtn('relative');
							if(currentVal == '{time}'){
								document.getElementById('dateDetail_delay').value = 0;
								document.getElementById('dateDetail_operator').value = 'before';
								document.getElementById('dateDetail_length').value = 'minutes';
							} else{
								currentOperator = currentVal.substr(6,1);
								currentNumber = currentVal.substr(7);
								if(currentNumber/86400 === parseInt(currentNumber/86400)){
									document.getElementById('dateDetail_delay').value = parseInt(currentNumber/86400);
									document.getElementById('dateDetail_length').value = 'days';
								} else if(currentNumber/3600 === parseInt(currentNumber/3600) ){
									document.getElementById('dateDetail_delay').value = parseInt(currentNumber/3600);
									document.getElementById('dateDetail_length').value = 'hours';
								} else{
									document.getElementById('dateDetail_delay').value = parseInt(currentNumber/60);
									document.getElementById('dateDetail_length').value = 'minutes';
								}
								if(currentOperator == '-'){
									document.getElementById('dateDetail_operator').value = 'before';
								} else{
									document.getElementById('dateDetail_operator').value = 'after'
								}
							}
							dateTmp = new Date();
							document.getElementById('dateDetail_year').value = dateTmp.getFullYear();
							month = dateTmp.getMonth() + 1;
							if(month < 10){ month = '0'+ month; }
							document.getElementById('dateDetail_month').value = month;
							if(dateTmp.getDate() < 10){ day = '0'+ dateTmp.getDate(); }
							else{ day = dateTmp.getDate();}
							document.getElementById('dateDetail_day').value = day;
						} else{
							toggleDateBtn('specific');
							if(currentVal == '' || currentVal == parseInt(currentVal)){
								if(currentVal == ''){ dateTmp = new Date();}
								else{ dateTmp = new Date(1000*currentVal); }
								document.getElementById('dateDetail_year').value = dateTmp.getFullYear();
								month = dateTmp.getMonth() + 1;
								if(month < 10){ month = '0'+ month; }
								document.getElementById('dateDetail_month').value = month;
								if(dateTmp.getDate() < 10){ day = '0'+ dateTmp.getDate(); }
								else{ day = dateTmp.getDate();}
								document.getElementById('dateDetail_day').value = day;
							} else{
								document.getElementById('dateDetail_year').value = currentVal.substr(0,4);
								document.getElementById('dateDetail_month').value = currentVal.substr(5,2);
								document.getElementById('dateDetail_day').value = currentVal.substr(8,2);
							}
						}

						document.getElementById('dateDetails').style.left = e.clientX + 'px';
						document.getElementById('dateDetails').style.top = e.clientY + 20 + 'px';
					}catch(err){
						document.getElementById('dateDetails').style.left = e.x+'px';
						document.getElementById('dateDetails').style.top = e.y+20+'px';
					}

					document.getElementById('dateDetails').style.display = 'block';
				}

				function toggleDateBtn(btnToActive){
					if(btnToActive == 'specific'){
						if(typeof jQuery != 'undefined'){
							jQuery('#dateDetail_typefieldset label[for=dateDetail_typespecificdate]').click();
							jQuery('#dateDetail_typespecificdate').click();
						}else{
							document.getElementById('dateDetail_typerelativedate').checked='';
							document.getElementById('dateDetail_typespecificdate').checked='checked';
						}
						document.getElementById('specificDate').style.display = 'inline';
						document.getElementById('relativeDate').style.display = 'none';
					} else{
						if(typeof jQuery != 'undefined'){
							jQuery('#dateDetail_type label[for=dateDetail_typerelativedate]').click();
							jQuery('#dateDetail_typerelativedate').click();
						}else{
							document.getElementById('dateDetail_typerelativedate').checked='checked';
							document.getElementById('dateDetail_typespecificdate').checked='';
						}
						document.getElementById('specificDate').style.display = 'none';
						document.getElementById('relativeDate').style.display = 'inline';
					}
				}";

		acymailing_addScript(true, $js);

		$dateDetails = '<div id="dateDetails" style="display:none;z-index: 60;">';
		$dateTypeData = array();
		$dateTypeData[] = acymailing_selectOption('relativedate', acymailing_translation('ACY_RELATIVE_DATE'));
		$dateTypeData[] = acymailing_selectOption('specificdate', acymailing_translation('ACY_SPECIFIC_DATE'));
		$dateDetails .= '<div class="dateDetailType">'.acymailing_radio($dateTypeData, 'dateDetail_type', 'onchange="updateDateDetail(this);"', 'value', 'text', 'relativedate', 'dateDetail_type').'</div>';
		$dateDetails .= '<div id="relativeDate">';
		$dateDetails .= '<input type="text" name="dateDetail_delay" id="dateDetail_delay" size="5" style="width:30px" value="0" pattern="[0-9]*"> ';
		$tempData = array();
		$tempData[] = acymailing_selectOption('minutes', acymailing_translation('ACY_MINUTES'));
		$tempData[] = acymailing_selectOption('hours', acymailing_translation('HOURS'));
		$tempData[] = acymailing_selectOption('days', acymailing_translation('DAYS'));
		$dateDetails .= acymailing_select($tempData, 'dateDetail_length', 'style="width:100px"', 'value', 'text');
		$tempData = array();
		$tempData[] = acymailing_selectOption('before', acymailing_translation('ACY_BEFORE'));
		$tempData[] = acymailing_selectOption('after', acymailing_translation('ACY_AFTER'));
		$dateDetails .= acymailing_select($tempData, 'dateDetail_operator', 'style="width:100px"', 'value', 'text');
		$dateDetails .= ' '.acymailing_translation('ACY_EXECUTION_TIME');
		$dateDetails .= '</div>';
		$dateDetails .= '<div id="specificDate" style="display:none;">';
		$tempData = array();
		$currentYear = (int)date('Y');
		for($i = 1970; $i <= $currentYear + 5; $i++){
			$tempData[] = acymailing_selectOption($i, $i);
		}
		$dateDetails .= acymailing_select($tempData, 'dateDetail_year', 'style="width:80px"', 'value', 'text');
		$tempData = array();
		for($i = 1; $i < 13; $i++){
			$monthVal = ($i < 10 ? '0'.$i : $i);
			$tempData[] = acymailing_selectOption($monthVal, $monthVal);
		}
		$dateDetails .= acymailing_select($tempData, 'dateDetail_month', 'style="width:60px"', 'value', 'text');
		$tempData = array();
		for($i = 1; $i < 32; $i++){
			$dayVal = ($i < 10 ? '0'.$i : $i);
			$tempData[] = acymailing_selectOption($dayVal, $dayVal);
		}
		$dateDetails .= acymailing_select($tempData, 'dateDetail_day', 'style="width:60px"', 'value', 'text');
		$dateDetails .= '</div>';
		$dateDetails .= '<div class="dateBtn"><input type="button" onClick="hideDateDetail();" class="btn btn-danger" value="Cancel"> <input type="button" onClick="validateDateField();" class="btn btn-success" value="OK"></div>';
		$dateDetails .= '</div>';
		echo($dateDetails);
	}
}

class acyQuery{
	var $leftjoin = array();
	var $join = array();
	var $where = array();
	var $from = '#__acymailing_subscriber as sub';
	var $limit = '';
	var $orderBy = '';

	function __construct(){
		$this->db = JFactory::getDBO();
	}

	function count(){
		$myquery = $this->getQuery(array('COUNT(DISTINCT sub.subid)'));
		return acymailing_loadResult($myquery);
	}

	function getQuery($select = array()){
		$query = '';
		if(!empty($select)) $query .= ' SELECT DISTINCT '.implode(',', $select);
		if(!empty($this->from)) $query .= ' FROM '.$this->from;
		if(!empty($this->join)) $query .= ' JOIN '.implode(' JOIN ', $this->join);
		if(!empty($this->leftjoin)) $query .= ' LEFT JOIN '.implode(' LEFT JOIN ', $this->leftjoin);
		if(!empty($this->where)) $query .= ' WHERE ('.implode(') AND (', $this->where).')';
		if(!empty($this->orderBy)) $query .= ' ORDER BY '.$this->orderBy;
		if(!empty($this->limit)) $query .= ' LIMIT '.$this->limit;

		return $query;
	}

	function convertQuery($as, $column, $operator, $value, $type = ''){

		$operator = str_replace(array('&lt;', '&gt;'), array('<', '>'), $operator);

		if($operator == 'CONTAINS'){
			$operator = 'LIKE';
			$value = '%'.$value.'%';
		}elseif($operator == 'BEGINS'){
			$operator = 'LIKE';
			$value = $value.'%';
		}elseif($operator == 'END'){
			$operator = 'LIKE';
			$value = '%'.$value;
		}elseif($operator == 'NOTCONTAINS'){
			$operator = 'NOT LIKE';
			$value = '%'.$value.'%';
		}elseif(!in_array($operator, array('REGEXP', 'NOT REGEXP', 'IS NULL', 'IS NOT NULL', 'NOT LIKE', 'LIKE', '=', '!=', '>', '<', '>=', '<='))){
			die('Operator not safe : '.$operator);
		}

		if(strpos($value, '{time}') !== false){
			$value = acymailing_replaceDate($value);
			$value = strftime('%Y-%m-%d %H:%M:%S', $value);
		}

		$replace = array('{year}', '{month}', '{weekday}', '{day}');
		$replaceBy = array(date('Y'), date('m'), date('N'), date('d'));
		$value = str_replace($replace, $replaceBy, $value);

		if(preg_match_all('#{(year|month|weekday|day)\|(add|remove):([^}]*)}#Uis', $value, $results)){

			foreach($results[0] as $i => $oneMatch){
				$format = str_replace(array('year', 'month', 'weekday', 'day'), array('Y', 'm', 'N', 'd'), $results[1][$i]);
				$delay = str_replace(array('add', 'remove'), array('+', '-'), $results[2][$i]).intval($results[3][$i]).' '.str_replace('weekday', 'day', $results[1][$i]);
				$value = str_replace($oneMatch, date($format, strtotime($delay)), $value);
			}
		}

		if(!is_numeric($value) OR in_array($operator, array('REGEXP', 'NOT REGEXP', 'NOT LIKE', 'LIKE', '=', '!='))){
			$value = $this->db->Quote($value);
		}

		if(in_array($operator, array('IS NULL', 'IS NOT NULL'))){
			$value = '';
		}

		if($type == 'datetime' && in_array($operator, array('=', '!='))){
			return 'DATE_FORMAT('.$as.'.`'.acymailing_secureField($column).'`, "%Y-%m-%d") '.$operator.' '.'DATE_FORMAT('.$value.', "%Y-%m-%d")';
		}
		if($type == 'timestamp' && in_array($operator, array('=', '!='))){
			return 'FROM_UNIXTIME('.$as.'.`'.acymailing_secureField($column).'`, "%Y-%m-%d") '.$operator.' '.'FROM_UNIXTIME('.$value.', "%Y-%m-%d")';
		}
		return $as.'.`'.acymailing_secureField($column).'` '.$operator.' '.$value;
	}
}
