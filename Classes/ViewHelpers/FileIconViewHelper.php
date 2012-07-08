<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2012 Ralf Schneider <ralf@hr-interactive.com>
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
 *
 * @package Feupload
 * @subpackage ViewHelpers
 * @author Ralf Schneider
 */
class Tx_Feupload_ViewHelpers_FileIconViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper
{

    /**
     * Resizes a given image (if required) and renders the respective img tag
     *
     * @see http://typo3.org/documentation/document-library/references/doc_core_tsref/4.2.0/view/1/5/#id4164427
     *
     *@param Tx_Feupload_Domain_Model_File $file
     * @return string rendered tag.
     */
    public function render ($file)
    {
        $type = $this->_getFileType($file);
        $path = t3lib_extMgm::extRelPath('feupload');
        $src = "{$path}/Resources/Public/Css/images/{$type}.png";
        $tag = "<img src='{$src}' alt='{$type}' />";
        return $tag;
    }

    /**
     *
     * @param Tx_Feupload_Domain_Model_File $file            
     */
    protected function _getFileType ($file)
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
