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
 * Renders a file download with the FILELINK cObject so it integrates
 * with e.g.
 * ml_links.
 *
 * @package Feupload
 * @subpackage ViewHelpers
 * @author Pascal Dürsteler
 */
class Tx_Feupload_ViewHelpers_FileIconViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper
{

    /**
     *
     * @param Tx_Feupload_Domain_Model_File $file            
     */
    public function render ($file)
    {
        switch ($file->getFileExt()) {
            case 'jpg':
            case 'png':
            case 'gif':
            case 'tiff':
                $ret = 'picture';
                break;
            case 'doc':
            case 'docx':
                $ret = 'doc';
                break;
            case 'html':
            case 'htm':
                $ret = 'html';
                break;
            case 'pdf':
                $ret = 'pdf';
                break;
            case 'ppt':
            case 'pptx':
                $ret = 'ppt';
                break;
            case 'rar':
            case 'zip':
            case 'gz':
                return 'zip';
                break;
            default:
                $ret = 'file';
                break;
        }
        return $ret;
    }
}
