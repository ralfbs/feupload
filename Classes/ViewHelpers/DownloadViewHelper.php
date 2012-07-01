<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2012 Pascal Dürsteler <pascal.duersteler@gmail.com>
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
 * Renders a file download with the FILELINK cObject so it integrates 
 * with e.g. ml_links. 
 *
 * @package Feupload
 * @subpackage ViewHelpers
 * @author Pascal Dürsteler
 */
class Tx_Feupload_ViewHelpers_DownloadViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper
{
	
	/**
	 * @param		Tx_Feupload_Domain_Model_File		$file			File object
	 */
	public function render($file)
	{
		$currentConf['labelStdWrap.']['cObject'] = 'TEXT';
		$currentConf['labelStdWrap.']['cObject.']['value'] = $file->getTitle();
		$conf = array_merge($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_feupload.']['file.'], $currentConf);
		
		$conf['ATagParams'] = 'class="file download '.$file->getFileExt().'"';
		
		return $GLOBALS['TSFE']->cObj->filelink($file->getFile(), $conf);
	}
	
}
