<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Ralf Schneider <ralf@hr-interactive.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 *
 * @see http://www.benny-vs-web.de/typo3/extbase-session-handler-selbstgebastelt/
 *      $sessionHandler =
 *      t3lib_div::makeInstance('Tx_Feupload_Domain_Session_Folder');
 *     
 * @package feupload
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License,
 *          version 3 or later
 */
class Tx_Feupload_Session_Folder implements t3lib_Singleton
{

    /**
     * Returns the object stored in the user´s PHP session
     *
     * @return Object the stored object
     */
    public function restoreFromSession ()
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 
                'tx_feupload_folder');
        return unserialize($sessionData);
    }

    /**
     * return the current folder - retrive an empty one for roots
     *
     * @return Tx_Feupload_Domain_Model_Folder
     */
    public function getCurrentFolder ()
    {
        /* @var $folder Tx_Feupload_Domain_Model_Folder */
        $folder = t3lib_div::makeInstance('Tx_Feupload_Domain_Model_Folder');
        $folder->setUid(0);
        $folderId = (int) $this->restoreFromSession();
        if (0 < $folderId) {
            /*
             * @var $folderRepositry
             * Tx_Feupload_Domain_Repository_FolderRepository
             */
            $folderRepositry = t3lib_div::makeInstance(
                    'Tx_Feupload_Domain_Repository_FolderRepository');
            $folder = $folderRepositry->findByUid($folderId);
        }

        return $folder;
    }

    /**
     * Writes an object into the PHP session
     *
     * @param $object any
     *            object to store into the session
     * @return Tx_MyExt_Domain_Session_Folder this
     */
    public function writeToSession ($object)
    {
        $sessionData = serialize($object);
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'tx_feupload_folder', 
                $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    /**
     * Cleans up the session: removes the stored object from the PHP session
     *
     * @return Tx_MyExt_Domain_Session_Folder this
     */
    public function cleanUpSession ()
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'tx_feupload_folder', NULL);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }
}
?>