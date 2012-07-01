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
	 * Find files by a group
	 *
	 * @param		Tx_Feupload_Domain_Model_Group 		$group
	 * @return	array
	 */
	public function findByGroup($group)
	{
		$query = $this->createQuery();
		$this->setQuerySettings($query);
		
		return $query->matching($query->contains('feGroups', $group), $query->equals('visibility', 1))->execute();
	}
	
	/**
	 * Find by visibility setting
	 *
	 * @param		integer 	$visibility			0 for everyone, -1 for "Only guests", -2 for "Only logged-in"
	 * @return	array
	 */
	public function findByVisibility($visibility)
	{
		$query = $this->createQuery();
		$this->setQuerySettings($query);
		
		return $query->matching($query->equals('visibility', $visibility))->execute();
	}
	
	
	
	/**
	 * Sets query settings
	 *
	 * @param		Tx_Extbase_Persistence_Query		&$query
	 */
	protected function setQuerySettings(Tx_Extbase_Persistence_Query &$query)
	{
		if( (int)$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['feupload']['enableStoragePage'] == 0 )  $query->getQuerySettings()->setRespectStoragePage(false);
	}
	
}
?>
