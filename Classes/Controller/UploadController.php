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
 * Upload plugin
 *
 * @package Feupload
 * @subpackage Controller
 * @author Pascal Dürsteler
 */
class Tx_Feupload_Controller_UploadController extends Tx_Extbase_MVC_Controller_ActionController
{

    /**
     *
     * @var Tx_Feupload_Domain_Repository_FileRepository
     */
    protected $fileRepository;

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
    public function injectFileRepository(Tx_Feupload_Domain_Repository_FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }



    /**
     *
     * @return void
     */
    public function injectFrontendUserRepository(Tx_Feupload_Domain_Repository_FrontendUserRepository $frontendUserRepository)
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }



    /**
     *
     * @return void
     */
    public function injectFrontendUserGroupRepository(
            Tx_Feupload_Domain_Repository_FrontendUserGroupRepository $frontendUserGroupRepository)
    {
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
    }



    /**
     * Initializer
     *
     * @return void
     */
    protected function initializeAction()
    {
        $ts = $GLOBALS["TSFE"]->fe_user->getUserTSconf();
        $this->userTS = $ts['feupload.'];
        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
        
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
        if ((int) $this->userTS['overrideStoragePid'] > 0) $this->config['persistence']['storagePid'] = $this->userTS['overrideStoragePid'];
        $this->configurationManager->setConfiguration($this->config);
    }



    /**
     * Shows the upload form
     *
     * @param Tx_Feupload_Domain_Model_File $file            
     * @return void
     */
    public function newAction(Tx_Feupload_Domain_Model_File $file = null)
    {
        $this->view->assign('file', $file);
        
        if ((bool) $this->userTS['allowAllGroups']) {
            $groups = $this->frontendUserGroupRepository->findAll();
        } else {
            $group_ids = explode(',', $GLOBALS['TSFE']->fe_user->user['usergroup']);
            
            $groups = array();
            foreach ($group_ids as $uid) {
                $group = $this->frontendUserGroupRepository->FindByUid($uid);
                if ($group) $groups[] = $group;
            }
        } 
        
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
        $defaultVisibility = $extConf['defaultFileVisibility'];
        $this->view->assign('allowVisibiltySelection', empty($defaultVisibility));
        $this->view->assign('groups', $groups);
        $this->view->assign('allowedFileExtensions', $this->extConf['allowedFileExtensions']);
    }



    /**
     * Processes uploads
     *
     * @param mixed $file            
     * @return void
     */
    public function createAction(Tx_Feupload_Domain_Model_File $file)
    {
        $ffunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
        
        $path = $ffunc->getUniqueName($_FILES['tx_feupload_upload']['name']['file']['file'], 
                t3lib_div::getFileAbsFileName('uploads/feupload/'));
        t3lib_div::upload_copy_move($_FILES['tx_feupload_upload']['tmp_name']['file']['file'], $path);
        $file->setFile(basename($path));
        

        if ($GLOBALS['TSFE']->fe_user->user) {
            // This is because $GLOBALS['TSFE']->fe_user is of type
            // tslib_feUserAuth
            // and $GLOBALS['TSFE']->fe_user->user is an array.
            $owner = $this->frontendUserRepository->findByUid($GLOBALS["TSFE"]->fe_user->user['uid']);
            $file->setOwner($owner);
        }
        
        $visibility = $_POST['tx_feupload_upload']['visibility'];
        
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['feupload']);
        $defaultVisibility = $extConf['defaultFileVisibility'];
        // globalVisibilty set? ignore user preference
        if (! empty($defaultVisibility) and in_array($defaultVisibility, array('public' , 'login' , 'groups'))) {
            $visibility = $defaultVisibility;
        }
        
        switch ($visibility) {
            case 'public':
                $file->setVisibility(0);
                break;
            
            case 'login':
                $file->setVisibility(- 2);
                break;
            
            case 'groups':
                $file->setVisibility(1);
                
                foreach ($_POST['tx_feupload_upload']['groups'] as $groupId) {
                    $group = $this->frontendUserGroupRepository->findByUid($groupId);
                    if ($group) $file->addFrontendUserGroup($group);
                }
                break;
        }
        

        if ((boolean) $this->userTS['appendGroups']) {
            $groupIds = explode(',', $this->userTS['appendGroups']);
            foreach ($groupIds as $groupId) {
                $group = $this->frontendUserGroupRepository->findByUid($groupId);
                if ($group) $file->addFrontendUserGroup($group);
            }
        }
        
        /* @var $sessionHandler Tx_Feupload_Session_Folder */
        $sessionHandler = t3lib_div::makeInstance('Tx_Feupload_Session_Folder');
        $file->setFolder($sessionHandler->getCurrentFolder());
        
        $this->fileRepository->add($file);
        

        $this->flashMessageContainer->add(
                Tx_Extbase_Utility_Localization::translate(
                        'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.file.uploaded.content'), 
                Tx_Extbase_Utility_Localization::translate(
                        'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.file.uploaded.title'), 
                t3lib_FlashMessage::OK);
        $this->redirect('new');
    }
}
