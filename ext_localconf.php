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

if (!defined ('TYPO3_MODE')) die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY] = unserialize($_EXTCONF);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,																	
	'Upload',																		
	array(
		'Upload' => 'new,create'
	),
	array(
		'Upload' => 'new,create'
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,																	
	'Download',																		
	array(
		'Download' => 'index,delete'
	),
	array(
		'Download' => 'index,delete'
	)
);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY, 
	'Folder', 
	array(
		'Folder' => 'index,cd,new,create,delete'), 
    array(
    	'Folder' => 'index,cd,new,create,delete')
);

?>
