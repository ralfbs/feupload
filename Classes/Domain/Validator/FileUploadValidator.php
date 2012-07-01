<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2012 Pascal Dürsteler <pascsal.duersteler@gmail.com>
*  All rights reserved
*
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/



/**
 * File upload validator
 *
 * @package Feupload
 * @subpackage Domain\Validator
 * @author Pascal Dürsteler
 */

class Tx_Feupload_Domain_Validator_FileUploadValidator extends Tx_Extbase_Validation_Validator_AbstractValidator 
{
	
	/**
	 * @param 	mixed 		$dummy		This is empty because the property is not in $_REQUEST but in $_FILES
	 * @return	boolean
	 */
	public function isValid($dummy)
	{
		$property = $this->options['property'];
		
		if( $_FILES['tx_feupload_upload']['tmp_name'][$property][$property] ) 
		{
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
			
			// This is not really a security check, but it does what it should.
			preg_match('/(.*)\.([^\.]*$)/', $_FILES['tx_feupload_upload']['name'][$property][$property], $matches);
			$ext = strtolower($matches[2]);
			
			if( in_array($ext, explode(',', $extConf['allowedFileExtensions'])) ) return true;
			else
			{
				$this->errors[] = new Tx_Extbase_Validation_Error(Tx_Extbase_Utility_Localization::translate('error.' . $property . 'WrongFileType.message', 'feupload'), 1332941667);
				return false;
			}
		}
		else
		{
			$this->errors[] = new Tx_Extbase_Validation_Error(Tx_Extbase_Utility_Localization::translate('error.' . $property . 'Empty.message', 'feupload'), 1332507175);
			return false;
		}
	}
}

?>
