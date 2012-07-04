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

/**
 * browse, create, delete folders
 *
 * @package Feupload
 * @subpackage Controller
 * @author Ralf Schneider
 */
class Tx_Feupload_Controller_FolderController extends Tx_Extbase_MVC_Controller_ActionController
{

    /**
     *
     * @var Tx_Feupload_Domain_Repository_FolderRepository
     */
    protected $folderRepositry;

    /**
     *
     * @var Tx_Feupload_Domain_Repository_FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     *
     * @return void
     */
    public function injectFolderRepository (
            Tx_Feupload_Domain_Repository_FolderRepository $folderRepository)
    {
        $this->folderRepository = $folderRepository;
    }

    /**
     *
     * @return void
     */
    public function injectFrontendUserRepository (
            Tx_Feupload_Domain_Repository_FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     *
     * @return void
     */
    public function injectFrontendUserGroupRepository (
            Tx_Feupload_Domain_Repository_FrontendUserGroupRepository $frontendUserGroupRepository)
    {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }

    /**
     * Displays folder list
     *
     * @return void
     */
    public function indexAction ()
    {
        /* @var $sessionHandler Tx_Feupload_Session_Folder */
        $sessionHandler = t3lib_div::makeInstance('Tx_Feupload_Session_Folder');
        $parent = (int) $sessionHandler->restoreFromSession();
        
        $this->folderRepository->setParent($parent); // root = 0
        $folders = array();
        
        if ($GLOBALS['TSFE']->fe_user->user) {
            $groupIds = explode(',', 
                    $GLOBALS['TSFE']->fe_user->user['usergroup']);
            foreach ($groupIds as $groupId) {
                $group = $this->frontendUserGroupRepository->findByUid($groupId);
                $folders = array_merge($folders, 
                        $this->folderRepository->findByGroup($group)->toArray());
            }
            
            $folders = array_merge($folders, 
                    $this->folderRepository->findByVisibility(- 2)->toArray());
            $folders = array_merge($folders, 
                    $this->folderRepository->findByVisibility(0)->toArray());
        } else {
            $folders = array_merge($folders, 
                    $this->folderRepository->findByVisibility(- 1)->toArray());
            $folders = array_merge($folders, 
                    $this->folderRepository->findByVisibility(- 0)->toArray());
        }
        
        uasort($folders, 
                array(
                        $this,
                        'sortfolders'
                ));
        $this->view->assign('folders', $folders);
        $this->view->assign('parent', $parent);
        $this->view->assign('current_user', $GLOBALS['TSFE']->fe_user->user);
    }

    /**
     * Change to a given Folder (Change Directory)
     * 
     * @return void
     */
    public function cdAction ()
    {
        $folder = (int) $this->request->getArgument('folder');
        /* @var $sessionHandler Tx_Feupload_Session_Folder */
        $sessionHandler = t3lib_div::makeInstance('Tx_Feupload_Session_Folder');
        $sessionHandler->writeToSession($folder);
        $this->redirect('index');
    }

    /**
     * Deletes a folder
     *
     * @param Tx_Feupload_Domain_Model_Folder $folder
     *            $folder
     * @return void
     */
    public function deleteAction (Tx_Feupload_Domain_Model_Folder $folder)
    {
        if ($folder->getDeletable()) {
            $this->folderRepository->remove($folder);
            $this->flashMessageContainer->add(
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.folder.deleted.content'), 
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.folder.deleted.title'), 
                    t3lib_FlashMessage::OK);
        } else {
            $this->flashMessageContainer->add(
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.error.folder.not_deleted.content'), 
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.error.folder.not_deleted.title', 
                            array(
                                    $folder->getTitle()
                            )), t3lib_FlashMessage::ERROR);
        }
        
        $this->redirect('index');
    }

    /**
     * Callback for userfunc-sorting
     *
     * @param mixed $a
     *            to compare
     * @param mixed $b
     *            to compare
     * @return integer position (-1, 0, 1)
     */
    protected function sortFolders ($a, $b)
    {
        if (! $this->settings['sorting']['field']) {
            $this->settings['sorting']['field'] = 'getTitle';
        }
        $a_val = strtoupper($a->{$this->settings['sorting']['field']}());
        $b_val = strtoupper($b->{$this->settings['sorting']['field']}());
        
        if ($a_val == $b_val) {
            return 0;
        }
        
        switch ($this->settings['sorting']['mode']) {
            case 'ASC':
                return $a_val < $b_val ? - 1 : 1;
                break;
            
            case 'DESC':
                return $a_val < $b_val ? 1 : - 1;
                break;
            
            default:
                return 0;
                break;
        }
    }
}
