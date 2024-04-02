<?php
if (false == defined('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT')) define('XAJAX_HTML_CONTROL_DOCTYPE_FORMAT', 'XHTML');
if (false == defined('XAJAX_HTML_CONTROL_DOCTYPE_VERSION')) define('XAJAX_HTML_CONTROL_DOCTYPE_VERSION', '1.0');
if (false == defined('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION')) define('XAJAX_HTML_CONTROL_DOCTYPE_VALIDATION', 'TRANSITIONAL');

class xajaxControl
{
	var $sTag;
	var $sEndTag;
	var $aAttributes;
	var $aEvents;
	var $sClass;
	function xajaxControl($sTag, $aConfiguration=array())
	{
		$this->sTag = $sTag;

		$this->clearAttributes();
				
		if (isset($aConfiguration['attributes']))
			if (is_array($aConfiguration['attributes']))
				foreach ($aConfiguration['attributes'] as $sKey => $sValue)
					$this->setAttribute($sKey, $sValue);

		$this->clearEvents();
		
		if (isset($aConfiguration['event']))
			call_user_func_array(array(&$this, 'setEvent'), $aConfiguration['event']);
		
		else if (isset($aConfiguration['events']))
			if (is_array($aConfiguration['events']))
				foreach ($aConfiguration['events'] as $aEvent)
					call_user_func_array(array(&$this, 'setEvent'), $aEvent);
		
		$this->sClass = '%block';
		$this->sEndTag = 'forbidden';
	}
	function getClass()
	{
		return $this->sClass;
	}
	function clearAttributes()
	{
		$this->aAttributes = array();
	}
	function setAttribute($sName, $sValue)
	{
//SkipDebug
		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			if (false == $objValidator->attributeValid($this->sTag, $sName)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:IAERR:01') 
					. $sName 
					. $objLanguageManager->getText('XJXCTL:IAERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:IAERR:03')
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$this->aAttributes[$sName] = $sValue;
	}
	
	function getAttribute($sName)
	{
		if (false == isset($this->aAttributes[$sName]))
			return null;
		
		return $this->aAttributes[$sName];
	}
	
	/*
		Function: clearEvents
		
		Clear the events that have been associated with this object.
	*/
	function clearEvents()
	{
		$this->aEvents = array();
	}
	function setEvent($sEvent, &$objRequest, $aParameters=array(), $sBeforeRequest='', $sAfterRequest='; return false;')
	{
//SkipDebug
		if (false == is_a($objRequest, 'xajaxRequest')) {
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:IRERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}

		if (class_exists('clsValidator')) {
			$objValidator =& clsValidator::getInstance();
			if (false == $objValidator->attributeValid($this->sTag, $sEvent)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:IEERR:01') 
					. $sEvent 
					. $objLanguageManager->getText('XJXCTL:IEERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:IEERR:03')
					, E_USER_ERROR
					);
			}
		}

		$this->aEvents[$sEvent] = array(
			&$objRequest, 
			$aParameters, 
			$sBeforeRequest, 
			$sAfterRequest
			);
	}

	function getHTML($bFormat=false)
	{
		ob_start();
		if ($bFormat)
			$this->printHTML();
		else
			$this->printHTML(false);
		return ob_get_clean();
	}
	function printHTML($sIndent='')
	{
//SkipDebug
		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			$sMissing = '';
			if (false == $objValidator->checkRequiredAttributes($this->sTag, $this->aAttributes, $sMissing)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:MAERR:01') 
					. $sMissing
					. $objLanguageManager->getText('XJXCTL:MAERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:MAERR:03')
					, E_USER_ERROR
					);
			}
		}

		$sClass = $this->getClass();
		
		if ('%inline' != $sClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo $sIndent;
			
		echo '<';
		echo $this->sTag;
		echo ' ';
		$this->_printAttributes();
		$this->_printEvents();
		
		if ('forbidden' == $this->sEndTag)
		{
			if ('HTML' == XAJAX_HTML_CONTROL_DOCTYPE_FORMAT)
				echo '>';
			else if ('XHTML' == XAJAX_HTML_CONTROL_DOCTYPE_FORMAT)
				echo '/>';
			
			if ('%inline' != $sClass)
				// this odd syntax is necessary to detect request for no formatting
				if (false === (false === $sIndent))
					echo "\n";
				
			return;
		}
		else if ('optional' == $this->sEndTag)
		{
			echo '/>';
			
			if ('%inline' == $sClass)
				// this odd syntax is necessary to detect request for no formatting
				if (false === (false === $sIndent))
					echo "\n";
				
			return;
		}
//SkipDebug
		else
		{
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:IETERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}
//EndSkipDebug
	}

	function _printAttributes()
	{
		// NOTE: Special case here: disabled='false' does not work in HTML; does work in javascript
		foreach ($this->aAttributes as $sKey => $sValue)
			if ('disabled' != $sKey || 'false' != $sValue)
				echo "{$sKey}='{$sValue}' ";
	}

	function _printEvents()
	{
		foreach (array_keys($this->aEvents) as $sKey)
		{
			$aEvent =& $this->aEvents[$sKey];
			$objRequest =& $aEvent[0];
			$aParameters = $aEvent[1];
			$sBeforeRequest = $aEvent[2];
			$sAfterRequest = $aEvent[3];

			foreach ($aParameters as $aParameter)
			{
				$nParameter = $aParameter[0];
				$sType = $aParameter[1];
				$sValue = $aParameter[2];
				$objRequest->setParameter($nParameter, $sType, $sValue);
			}

			$objRequest->useDoubleQuote();

			echo "{$sKey}='{$sBeforeRequest}";

			$objRequest->printScript();

			echo "{$sAfterRequest}' ";
		}
	}

	function backtrace()
	{
		// debug_backtrace was added to php in version 4.3.0
		// version_compare was added to php in version 4.0.7
		if (0 <= version_compare(PHP_VERSION, '4.3.0'))
			return '<div><div>Backtrace:</div><pre>' 
				. print_r(debug_backtrace(), true) 
				. '</pre></div>';
		return '';
	}
}

/*
	Class: xajaxControlContainer
	
	This class is used as the base class for controls that will contain
	other child controls.
*/
class xajaxControlContainer extends xajaxControl
{

	var $aChildren;
	var $sChildClass;
	function xajaxControlContainer($sTag, $aConfiguration=array())
	{
		xajaxControl::xajaxControl($sTag, $aConfiguration);

		$this->clearChildren();
		
		if (isset($aConfiguration['child']))
			$this->addChild($aConfiguration['child']);

		else if (isset($aConfiguration['children']))
			$this->addChildren($aConfiguration['children']);
		
		$this->sEndTag = 'required';
	}
	
	/*
		Function: getClass
		
		Returns the *adjusted* class of the element
	*/
	function getClass()
	{
		$sClass = xajaxControl::getClass();
		
		if (0 < count($this->aChildren) && '%flow' == $sClass)
			return $this->getContentClass();
		else if (0 == count($this->aChildren) || '%inline' == $sClass || '%block' == $sClass)
			return $sClass;
		
		$objLanguageManager =& xajaxLanguageManager::getInstance();
		trigger_error(
			$objLanguageManager->getText('XJXCTL:ICERR:01')
			. $this->backtrace()
			, E_USER_ERROR
			);
	}
	
	/*
		Function: getContentClass
		
		Returns the *adjusted* class of the content (children) of this element
	*/
	function getContentClass()
	{
		$sClass = '';
		
		foreach (array_keys($this->aChildren) as $sKey)
		{
			if ('' == $sClass)
				$sClass = $this->aChildren[$sKey]->getClass();
			else if ($sClass != $this->aChildren[$sKey]->getClass())
				return '%flow';
		}
		
		if ('' == $sClass)
			return '%inline';
			
		return $sClass;
	}
	
	/*
		Function: clearChildren
		
		Clears the list of child controls associated with this control.
	*/
	function clearChildren()
	{
		$this->sChildClass = '%inline';
		$this->aChildren = array();
	}

	/*
		Function: addChild
		
		Adds a control to the array of child controls.  Child controls
		must be derived from <xajaxControl>.
	*/
	function addChild(&$objControl)
	{
//SkipDebug
		if (false == is_a($objControl, 'xajaxControl')) {
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:ICLERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}

		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			if (false == $objValidator->childValid($this->sTag, $objControl->sTag)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:ICLERR:02') 
					. $objControl->sTag
					. $objLanguageManager->getText('XJXCTL:ICLERR:03') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:ICLERR:04')
					. $this->backtrace()
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$this->aChildren[] =& $objControl;
	}
	
	function addChildren(&$aChildren)
	{
//SkipDebug
		if (false == is_array($aChildren)) {
			$objLanguageManager =& xajaxLanguageManager::getInstance();
			trigger_error(
				$objLanguageManager->getText('XJXCTL:ICHERR:01')
				. $this->backtrace()
				, E_USER_ERROR
				);
		}
//EndSkipDebug
				
		foreach (array_keys($aChildren) as $sKey)
			$this->addChild($aChildren[$sKey]);
	}

	function printHTML($sIndent='')
	{
//SkipDebug
		if (class_exists('clsValidator'))
		{
			$objValidator =& clsValidator::getInstance();
			$sMissing = '';
			if (false == $objValidator->checkRequiredAttributes($this->sTag, $this->aAttributes, $sMissing)) {
				$objLanguageManager =& xajaxLanguageManager::getInstance();
				trigger_error(
					$objLanguageManager->getText('XJXCTL:MRAERR:01') 
					. $sMissing
					. $objLanguageManager->getText('XJXCTL:MRAERR:02') 
					. $this->sTag 
					. $objLanguageManager->getText('XJXCTL:MRAERR:03')
					, E_USER_ERROR
					);
			}
		}
//EndSkipDebug

		$sClass = $this->getClass();
		
		if ('%inline' != $sClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo $sIndent;
			
		echo '<';
		echo $this->sTag;
		echo ' ';
		$this->_printAttributes();
		$this->_printEvents();
		
		if (0 == count($this->aChildren))
		{
			if ('optional' == $this->sEndTag)
			{
				echo '/>';
				
				if ('%inline' != $sClass)
					// this odd syntax is necessary to detect request for no formatting
					if (false === (false === $sIndent))
						echo "\n";
					
				return;
			}
//SkipDebug
			else if ('required' != $this->sEndTag)
				trigger_error("Invalid end tag designation; should be optional or required.\n"
					. $this->backtrace(),
					E_USER_ERROR
					);
//EndSkipDebug
		}
		
		echo '>';
		
		$sContentClass = $this->getContentClass();
		
		if ('%inline' != $sContentClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo "\n";

		$this->_printChildren($sIndent);
		
		if ('%inline' != $sContentClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo $sIndent;
		
		echo '<' . '/';
		echo $this->sTag;
		echo '>';
		
		if ('%inline' != $sClass)
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				echo "\n";
	}

	function _printChildren($sIndent='')
	{
		if (false == is_a($this, 'clsDocument'))
			// this odd syntax is necessary to detect request for no formatting
			if (false === (false === $sIndent))
				$sIndent .= "\t";

		// children
		foreach (array_keys($this->aChildren) as $sKey)
		{
			$objChild =& $this->aChildren[$sKey];
			$objChild->printHTML($sIndent);
		}
	}
}
