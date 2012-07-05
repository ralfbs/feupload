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
     * @var Tx_Feupload_Domain_Repository_FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository;

    /**
     *
     * @var array
     */
    protected $userTS;

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
     * Initializer
     *
     * @return void
     */
    protected function initializeAction ()
    {
        $ts = $GLOBALS["TSFE"]->fe_user->getUserTSconf();
        $this->userTS = $ts['feupload.'];
        $this->extConf = unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
        
        $this->config = $this->configurationManager->getConfiguration(
                Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        
        // Storage PID from group select. Take first one and skip after.
        foreach ($GLOBALS['TSFE']->fe_user->groupData['uid'] as $uid) {
            $group = $this->frontendUserGroupRepository->FindByUid($uid);
            if ($group->getFeuploadStoragePid() > 0) {
                $this->config['persistence']['storagePid'] = $group->getFeuploadStoragePid();
                break;
            }
        }
        
        // Storage PID from user/group ts has precedence - may be deprecated in
        // a newer version
        if ((int) $this->userTS['overrideStoragePid'] > 0)
            $this->config['persistence']['storagePid'] = $this->userTS['overrideStoragePid'];
        $this->configurationManager->setConfiguration($this->config);
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
        
        $folder = $sessionHandler->getCurrentFolder();
        
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
        $this->view->assign('mayDeleteFolder', $folder->isDeletable());
        $this->view->assign('folders', $folders);
        $this->view->assign('parent', $parent);
        $this->view->assign('current_user', $GLOBALS['TSFE']->fe_user->user);
        $this->view->assign('currentFolder', 
                $sessionHandler->getCurrentFolder());
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
     * Display form to create a new folder
     *
     * @param Tx_Feupload_Domain_Model_Folder $folder            
     * @return void
     */
    public function newAction (Tx_Feupload_Domain_Model_Folder $folder = null)
    {
        $this->view->assign('folder', $folder);
        
        if ((bool) $this->userTS['allowAllGroups']) {
            $groups = $this->frontendUserGroupRepository->findAll();
        } else {
            $group_ids = explode(',', 
                    $GLOBALS['TSFE']->fe_user->user['usergroup']);
            
            $groups = array();
            foreach ($group_ids as $uid) {
                $group = $this->frontendUserGroupRepository->FindByUid($uid);
                if ($group) {
                    $groups[] = $group;
                }
            }
        }
        $extConf = unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
        $defaultVisibility = $extConf['defaultFileVisibility'];
        $this->view->assign('allowVisibiltySelection', 
                empty($defaultVisibility));
        $this->view->assign('groups', $groups);
    }

    /**
     * Create new folder in DB
     *
     * @param Tx_Feupload_Domain_Model_Folder $folder            
     * @return void
     */
    public function createAction (Tx_Feupload_Domain_Model_Folder $folder)
    {
        if ($GLOBALS['TSFE']->fe_user->user) {
            // This is because $GLOBALS['TSFE']->fe_user is of type
            // tslib_feUserAuth
            // and $GLOBALS['TSFE']->fe_user->user is an array.
            $owner = $this->frontendUserRepository->findByUid(
                    $GLOBALS["TSFE"]->fe_user->user['uid']);
            $folder->setOwner($owner);
        }
        
        $visibility = $_POST['tx_feupload_upload']['visibility'];
        
        $extConf = unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
        $defaultVisibility = $extConf['defaultFileVisibility'];
        // globalVisibilty set? ignore user preference
        if (! empty($defaultVisibility) and in_array($defaultVisibility, 
                array(
                        'public',
                        'login',
                        'groups'
                ))) {
            $visibility = $defaultVisibility;
        }
        
        switch ($visibility) {
            case 'public':
                $folder->setVisibility(0);
                break;
            
            case 'login':
                $folder->setVisibility(- 2);
                break;
            
            case 'groups':
                $folder->setVisibility(1);
                
                foreach ($_POST['tx_feupload_upload']['groups'] as $groupId) {
                    $group = $this->frontendUserGroupRepository->findByUid(
                            $groupId);
                    if ($group) {
                        $folder->addFrontendUserGroup($group);
                    }
                }
                break;
        }
        
        if ((boolean) $this->userTS['appendGroups']) {
            $groupIds = explode(',', $this->userTS['appendGroups']);
            foreach ($groupIds as $groupId) {
                $group = $this->frontendUserGroupRepository->findByUid($groupId);
                if ($group)
                    $folder->addFrontendUserGroup($group);
            }
        }
        
        /* @var $sessionHandler Tx_Feupload_Session_Folder */
        $sessionHandler = t3lib_div::makeInstance('Tx_Feupload_Session_Folder');
        $folder->setParent($sessionHandler->getCurrentFolder());
        
        $this->folderRepository->add($folder);
        
        $this->flashMessageContainer->add('alles ok', t3lib_FlashMessage::OK);
        $this->redirect('index');
    }

    /**
     * Deletes a folder
     *
     * @param Tx_Feupload_Domain_Model_Folder $folder
     * @return void
     */
    public function deleteAction (Tx_Feupload_Domain_Model_Folder $folder)
    {
        $parentId = $folder->getParent();
        
        if ($folder->isDeletable()) {
            $this->folderRepository->remove($folder);
            /* @var $sessionHandler Tx_Feupload_Session_Folder */
            $sessionHandler = t3lib_div::makeInstance('Tx_Feupload_Session_Folder');
            $sessionHandler->writeToSession($parentId);
            $this->flashMessageContainer->add(
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.folder.deleted.title'), 
                    t3lib_FlashMessage::OK);
        } else {
            $this->flashMessageContainer->add(
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.error.folder.not_deleted.title', 
                            array(
                                    $folder->getTitle()
                            )), t3lib_FlashMessage::ERROR);
        }

        $this->redirect('index');
    }
}
