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
 * File repository
 *
 * @package Feupload
 * @subpackage Domain\Repository
 * @author Pascal Dürsteler
 */
class Tx_Feupload_Domain_Repository_FileRepository extends Tx_Extbase_Persistence_Repository
{

    /**
     * ID of the current folder | 0 if we are at root level
     *
     * @var integer
     */
    protected $_folder = 0;

    /**
     * Set the parent level for all calls
     *
     * @param ingeger $parent            
     */
    public function setFolderId ($folderId = 0)
    {
        $this->_folder = $folderId;
    }

    /**
     * return the parent level
     *
     * @return number
     */
    public function getFolderId ()
    {
        return $this->_folder;
    }

    /**
     * Find files by a group
     *
     * @param Tx_Feupload_Domain_Model_Group $group            
     * @return array
     */
    public function findByGroup ($group)
    {
        $query = $this->createQuery();
        $this->setQuerySettings($query);
        
        $constraint = $query->logicalAnd(
                $query->equals('folder', $this->_folder), 
                $query->contains('feGroups', $group), 
                $query->equals('visibility', 
                        Tx_Feupload_Domain_Model_File::VISIBILITY_GROUPS));
        
        return $query->matching($constraint)->execute();
    }

    /**
     * Find by visibility setting
     *
     * @param integer $visibility
     *            for everyone, -1 for "Only guests", -2 for "Only logged-in"
     * @return array
     */
    public function findByVisibility ($visibility)
    {
        $query = $this->createQuery();
        $this->setQuerySettings($query);
        
        $query->matching(
                $query->logicalAnd($query->equals('visibility', $visibility), 
                        $query->equals('folder', $this->_folder)));
        
        $ret = $query->execute();
        
        return $ret;
    }

    /**
     * all files within one folder 
     *
     * @param Tx_Feupload_Domain_Model_Folder $folder            
     * @return integer
     */
    public function numFilesInFolder (Tx_Feupload_Domain_Model_Folder $folder)
    {
        $query = $this->createQuery();
        $this->setQuerySettings($query);
        $query->matching($query->equals('folder', $folder->getUid()));
        return $query->count();
    }

    /**
     * Sets query settings
     *
     * @param
     *            Tx_Extbase_Persistence_Query		&$query
     */
    protected function setQuerySettings (Tx_Extbase_Persistence_Query &$query)
    {
        if ((int) $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['feupload']['enableStoragePage'] ==
                 0)
            $query->getQuerySettings()->setRespectStoragePage(false);
    }
}
?>
