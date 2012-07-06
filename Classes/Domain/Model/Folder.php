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
 * Folder model
 *
 * @package Feupload
 * @subpackage Domain\Model
 * @author Ralf Schneider
 */
class Tx_Feupload_Domain_Model_Folder extends Tx_Extbase_DomainObject_AbstractEntity
{

    /**
     * For assigned groups only
     *
     * @var interer
     */
    CONST VISIBILITY_GROUPS = 1;

    /**
     * Public
     *
     * @var integer
     */
    CONST VISIBILITY_PUBLIC = 0;

    /**
     * For not-logged in users
     *
     * @var integer
     */
    CONST VISIBILITY_NOTLOGGEDIN = - 1;

    /**
     * For all logged-in users
     *
     * @var integer
     */
    CONST VISIBILITY_LOGGEDIN = - 2;

    /**
     *
     * @var string @validate NotEmpty
     */
    protected $title;

    /**
     *
     * @var Tx_Feupload_Domain_Model_FrontendUser @dontvalidate
     */
    protected $feUser;

    /**
     *
     * @var Tx_Extbase_Persistence_ObjectStorage<Tx_Feupload_Domain_Model_FrontendUserGroup>
     *      @lazy
     */
    protected $feGroups;

    /**
     * parent folder
     *
     * @var Tx_Feupload_Domain_Model_Folder
     */
    protected $parent;

    /**
     * 1 = For assigned groups only
     * 0 = Public
     * -1 = For not-logged in users
     * -2 = For all logged-in users
     *
     * @var integer
     */
    protected $visibility;

    /**
     *
     * @var integer
     */
    protected $crdate;

    /**
     *
     * @return void
     */
    public function __construct ()
    {
        if (! $this->crdate)
            $this->setCrdate();
        $this->setFrontendUserGroups(new Tx_Extbase_Persistence_ObjectStorage());
    }

    /**
     *
     * @param string $title            
     * @return void
     */
    public function setTitle ($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getTitle ()
    {
        return $this->title;
    }

    /**
     *
     * @param înteger $visibility            
     * @return void
     */
    public function setVisibility ($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     *
     * @return integer
     */
    public function getVisibility ()
    {
        return $this->visibility;
    }

    /**
     *
     * @return Tx_Extbase_Persistence_ObjectStorage
     */
    public function getFrontendUserGroups ()
    {
        return $this->feGroups;
    }

    /**
     *
     * @param Tx_Extbase_Persistence_ObjectStorage $frontendUserGroups            
     * @return void
     */
    public function setFrontendUserGroups (
            Tx_Extbase_Persistence_ObjectStorage $frontendUserGroups)
    {
        $this->feGroups = $frontendUserGroups;
    }

    /**
     *
     * @param Tx_Feupload_Domain_Model_FrontendUserGroup $frontendUserGroup            
     * @return void
     */
    public function addFrontendUserGroup (
            Tx_Feupload_Domain_Model_FrontendUserGroup $frontendUserGroup)
    {
        $this->feGroups->attach($frontendUserGroup);
    }

    /**
     *
     * @param Tx_Feupload_Domain_Model_FrontendUser $feUser            
     * @return void
     */
    public function setOwner (Tx_Feupload_Domain_Model_FrontendUser $feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     *
     * @return Tx_Feupload_Domain_Model_FrontendUser
     */
    public function getOwner ()
    {
        return $this->feUser;
    }

    /**
     *
     * @param Tx_Feupload_Domain_Model_Folder $parent            
     */
    public function setParent (Tx_Feupload_Domain_Model_Folder $parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @return Tx_Feupload_Domain_Model_Folder
     */
    public function getParent ()
    {
        return $this->parent;
    }

    /**
     *
     * @param integer $time            
     * @return void
     */
    public function setCrdate ($time = null)
    {
        if ($time == null)
            $time = time();
        $this->crdate = $time;
    }

    /**
     *
     * @return integer
     */
    public function getCrdate ()
    {
        return (int) $this->crdate;
    }

    /**
     *
     * @return integer in bytes
     */
    public function getSize ()
    {
        return filesize('uploads/feupload/' . $this->file);
    }

    /**
     *
     * @return boolean
     */
    public function isDeletable ()
    {
        // must be owned by user anyway
        if (! $GLOBALS['TSFE']->fe_user->user && (int) $GLOBALS['TSFE']->fe_user->user['uid'] ==
                 (int) $this->feUser->uid) {
            return false;
        }
        
        if (! $this->isEmpty()) {
            return false;
        }
        return true;
    }

    /**
     * do we have any subfolders or any files? => may not delete folder
     */
    public function isEmpty ()
    {
        /*
         * @var $folderRepositry Tx_Feupload_Domain_Repository_FolderRepository
         */
        $folderRepositry = t3lib_div::makeInstance(
                'Tx_Feupload_Domain_Repository_FolderRepository');
        if ($folderRepositry->numChildren($this) > 0) {
            return false;
        }
        
        /*
         * @var $folderRepositry Tx_Feupload_Domain_Repository_FileRepository
         */
        $fileRepositry = t3lib_div::makeInstance(
                'Tx_Feupload_Domain_Repository_FileRepository');
        
        if ($fileRepositry->numFilesInFolder($this) > 0) {
            return false;
        }
        return true;
    }

    /**
     * find all folders from this folder up in the rootline
     *
     * @param array $folders
     *            as we use recursion, already found folders are returned here
     * @return array of folders
     */
    public function getRootlineFolders (array $folders = null)
    {
        // add current element
        $folders[] = array('uid' => $this->uid, 'title'=>$this->getTitle());
        
        /*@var $folderRepositry Tx_Feupload_Domain_Repository_FolderRepository */
        $folderRepositry = t3lib_div::makeInstance(
                'Tx_Feupload_Domain_Repository_FolderRepository');

        // any parents?
        if ($this->parent > 0) {
            /* @var $parentFolder Tx_Feupload_Domain_Model_Folder */
            $parentFolder = $folderRepositry->findByUid((int)$this->parent);
            if ($parentFolder instanceof Tx_Feupload_Domain_Model_Folder) {
                $folders = $parentFolder->getRootlineFolders($folders);
            }
        } else {
            // no - add root element
            $folders[] = array('uid'=>0, 'title'=>'/');
        }
        
        return $folders;
    }

    /**
     * we have to create folders with uid=0 for the root directory
     *
     * @param integer $uid            
     */
    public function setUid ($uid)
    {
        $this->uid = $uid;
    }
}
