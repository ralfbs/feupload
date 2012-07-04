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


if (! defined('TYPO3_MODE')) die('Access denied.');

$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);

/**
 * Register uploads plugin
 */
Tx_Extbase_Utility_Extension::registerPlugin($_EXTKEY, 'Upload', 'File-Uploading for frontend users');

$pluginSignature = strtolower($extensionName) . '_upload';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';


Tx_Extbase_Utility_Extension::registerPlugin($_EXTKEY, 'Download', 'Downloading of uploaded files from frontend users');

Tx_Extbase_Utility_Extension::registerPlugin($_EXTKEY, 'Folder', 'Folder structure for frontend users');


$pluginSignature = strtolower($extensionName) . '_download';
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/download.xml');


t3lib_div::loadTCA('fe_users');
t3lib_extMgm::allowTableOnStandardPages('tx_feupload_domain_model_file');
t3lib_extMgm::allowTableOnStandardPages('tx_feupload_domain_model_folder');


$TCA['tx_feupload_domain_model_file'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file' , 
        'label' => 'title' , 
        'crdate' => 'crdate' , 
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/File.php' , 
        'dividers2tabs' => true , 
        'searchFields' => 'title,file' , 
        'requestUpdate' => 'visibility' , 
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Private/Icons/default.png'));


$TCA['tx_feupload_domain_model_folder'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_folder' , 
        'label' => 'title' , 
        'crdate' => 'crdate' , 
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/Folder.php' , 
        'dividers2tabs' => true , 
        'searchFields' => 'title' , 
        'requestUpdate' => 'visibility' , 
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Private/Icons/folder.png'));


t3lib_div::loadTCA('fe_groups');
t3lib_extMgm::addToAllTCAtypes('fe_groups', 'feupload_storage_pid;;;;1-1-1');
t3lib_extMgm::addToAllTCAtypes('fe_groups', 'feupload_append_group_ids;;;;1-1-1');
$TCA['fe_groups']['columns'] += array(
    'feupload_storage_pid' => array(
        'exclude' => 1 , 
        'label' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:fe_groups.storage_pid' , 
        'config' => array(
            'type' => 'group' , 
            'internal_type' => 'db' , 
            'allowed' => 'pages' , 
            'size' => 1 , 
            'minitems' => 0 , 
            'maxitems' => 1 , 
            'wizards' => array('suggest' => array('type' => 'suggest')))));

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/default/', 'FE Upload default settings');
?>
