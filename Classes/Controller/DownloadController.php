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
class Tx_Feupload_Controller_DownloadController extends Tx_Extbase_MVC_Controller_ActionController
{

    /**
     *
     * @var Tx_Feupload_Domain_Repository_FileRepository
     */
    protected $fileRepository;

    /**
     *
     * @var Tx_Feupload_Domain_Repository_FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     *
     * @return void
     */
    public function injectFileRepository (
            Tx_Feupload_Domain_Repository_FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
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
     * Displays file list
     *
     * @return void
     */
    public function indexAction ()
    {
        /* @var $sessionHandler Tx_Feupload_Session_Folder */
        $sessionHandler = t3lib_div::makeInstance('Tx_Feupload_Session_Folder');
        $folderId = (int) $sessionHandler->restoreFromSession();
        $this->fileRepository->setFolderId($folderId);
        
        $files = array();
        
        if ($GLOBALS['TSFE']->fe_user->user) {
            $groupIds = explode(',', 
                    $GLOBALS['TSFE']->fe_user->user['usergroup']);
            foreach ($groupIds as $groupId) {
                $group = $this->frontendUserGroupRepository->findByUid($groupId);
                $files = array_merge($files, 
                        $this->fileRepository->findByGroup($group)->toArray());
            }
            
            $files = array_merge($files, 
                    $this->fileRepository->findByVisibility(- 2)->toArray());
            $files = array_merge($files, 
                    $this->fileRepository->findByVisibility(0)->toArray());
        } else {
            $files = array_merge($files, 
                    $this->fileRepository->findByVisibility(- 1)->toArray());
            
            $files = array_merge($files, 
                    $this->fileRepository->findByVisibility(0)->toArray());
        }
        
        uasort($files, 
                array(
                        $this,
                        'sortFiles'
                ));
        $this->view->assign('files', $files);
        $this->view->assign('current_user', $GLOBALS['TSFE']->fe_user->user);
    }

    /**
     * Deletes a file
     *
     * @param Tx_Feupload_Domain_Model_File $file
     *            $file
     * @return void
     */
    public function deleteAction (Tx_Feupload_Domain_Model_File $file)
    {
        if ($file->getDeletable()) {
            $this->fileRepository->remove($file);
            $this->flashMessageContainer->add(
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.file.deleted.content'), 
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.ok.file.deleted.title'), 
                    t3lib_FlashMessage::OK);
        } else {
            $this->flashMessageContainer->add(
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.error.file.not_deleted.content'), 
                    Tx_Extbase_Utility_Localization::translate(
                            'LLL:EXT:feupload/Resources/Private/Language/locallang.xml:flash.error.file.not_deleted.title', 
                            array(
                                    $file->getTitle()
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
    protected function sortFiles ($a, $b)
    {
        if (! $this->settings['sorting']['field'])
            $this->settings['sorting']['field'] = 'getTitle';
        
        $a_val = strtoupper($a->{$this->settings['sorting']['field']}());
        $b_val = strtoupper($b->{$this->settings['sorting']['field']}());
        
        if ($a_val == $b_val)
            return 0;
        
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
