<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Ralf Schneider <ralf@hr-interactive.de>
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

if (! defined('TYPO3_MODE'))
    die('Access denied.');

$extConf = unserialize(
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);

$TCA['tx_feupload_domain_model_folder'] = array(
        'ctrl' => $TCA['tx_feupload_domain_model_folder']['ctrl'],
        'interface' => array(
                'showRecordFieldList' => 'title, crdate'
        ),
        'columns' => array(
                'title' => array(
                        'exclude' => 0,
                        'label' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_folder.title',
                        'config' => array(
                                'type' => 'input',
                                'max' => 255,
                                'eval' => 'trim,required'
                        )
                ),
                
                'visibility' => array(
                        'exclude' => 0,
                        'label' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.visibility',
                        'config' => array(
                                'type' => 'select',
                                'size' => 1,
                                'minitems' => 1,
                                'maxitems' => 1,
                                'default' => 1,
                                'items' => array(
                                        array(
                                                'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.visibility.only_groups',
                                                1
                                        ),
                                        array(
                                                'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.visibility.public',
                                                0
                                        ),
                                        array(
                                                'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.visibility.not_logged_in',
                                                - 1
                                        ),
                                        array(
                                                'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.visibility.logged_in',
                                                - 2
                                        )
                                )
                        )
                ),
                'fe_user' => array(
                        'exclude' => 1,
                        'label' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.fe_user',
                        'config' => array(
                                'type' => 'select',
                                'size' => 1,
                                'maxitems' => 1,
                                'minitems' => 0,
                                'items' => array(
                                        array(
                                                'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.fe_user.empty',
                                                ''
                                        ),
                                        array(
                                                'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_file.fe_user.div',
                                                '--div--'
                                        )
                                ),
                                'foreign_table' => 'fe_users',
                                'foreign_table_where' => 'ORDER BY fe_users.username'
                        )
                ),
                'fe_groups' => array(
                        'exclude' => 0,
                        'displayCond' => 'FIELD:visibility:=:1',
                        'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_groups',
                        'config' => array(
                                'type' => 'group',
                                'internal_type' => 'db',
                                'allowed' => 'fe_groups',
                                'size' => 7,
                                'maxitems' => 20,
                                'foreign_table' => 'fe_groups',
                                'MM' => 'tx_feupload_folder_fegroup_mm'
                        )
                ),
                'parent' => array(
                        'exclude' => 0,
                        'label' => 'LLL:EXT:feupload/Resources/Private/Language/tca.xml:tx_feupload_domain_model_folder.parent',
                        'config' => array(
                                'type' => 'select',
                                'size' => 1,
                                'maxitems' => 1,
                                'minitems' => 0,
                                'renderMode' => 'tree',
                                'treeConfig' => array(
                                        'parentField' => 'pid',
                                        'rootUid' => 1,
                                        'appearance' => array(
                                                'expandAll' => TRUE,
                                                'showHeader' => TRUE,
                                        ),
                                ),
                                'foreign_table' => 'pages'
                        )
                ),
                'crdate' => array(
                        'exclude' => 1,
                        'label' => '[crdate]',
                        'config' => array(
                                'type' => '',
                                'eval' => 'datetime'
                        )
                )
        ),
        'types' => array(
                '0' => array(
                        'showitem' => 'fe_user, title, visibility, parent, fe_groups'
                )
        ),
        'palettes' => array(
                '1' => array(
                        'showitem' => ''
                )
        )
);

?>
