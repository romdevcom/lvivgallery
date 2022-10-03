<?php
##############################################################################
# Copyright (C) 2005  Adam Smith  asmith@agile-software.com
#
# Ported from Python to PHP by Wes Wright
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
##############################################################################

namespace App\Http\Controllers;

class ZoomifyFileProcessor
{
    public $_v_imageFilename;
    public $originalWidth;
    public $originalHeight;
    public $_v_scaleInfo = array();
    public $numberOfTiles;
    public $_v_tileGroupMappings = array();
    public $qualitySetting;
    public $tileSize;
    public $_debug;
    public $_filemode;
    public $_dirmode;
    public $_filegroup;

    public function __construct()
    {
        $this->_v_imageFilename = '';
        $this->format = '';
        $this->originalWidth = 0;
        $this->originalHeight = 0;
        $this->numberOfTiles = 0;
        $this->qualitySetting = 80;
        $this->tileSize = 256;
        $this->_debug = 0;
        $this->_filemode = octdec('664');
        $this->_dirmode = octdec('2775');
        $this->_filegroup = "user";
    }

    public function imageCrop($image, $left, $upper, $right, $lower)
    {
        $x = imagesx($image);
        $y = imagesy($image);

        $this->debugMessage("imageCrop x=$x y=$y left=$left upper=$upper right=$right lower=$lower");

        $w = abs($right-$left);
        $h = abs($lower-$upper);
        $crop = imagecreatetruecolor($w, $h);
        imagecopy($crop, $image, 0, 0, $left, $upper, $w, $h);

        return $crop;
    }

    public function rm($fileglob)
    {
        if (is_string($fileglob)) {
            if (is_file($fileglob)) {
                return unlink($fileglob);
            } elseif (is_dir($fileglob)) {
                $ok = $this->rm("$fileglob/*");
                if (! $ok) {
                    return false;
                }
                return rmdir($fileglob);
            } else {
                $matching = glob($fileglob);
                if ($matching === false) {
                    trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
                    return false;
                }
                $rcs = array_map(array($this, 'rm'), $matching);
                if (in_array(false, $rcs)) {
                    return false;
                }
            }
        } elseif (is_array($fileglob)) {
            $rcs = array_map(array($this, 'rm'), $fileglob);
            if (in_array(false, $rcs)) {
                return false;
            }
        } else {
            trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
            return false;
        }

        return true;
    }


    public function openImage($file)
    {
        $ret = false;

        # """ load the image data """
        $this->debugMessage("openImage ". $file);

        switch (exif_imagetype($file)) {
            case IMAGETYPE_JPEG:
                $ret = imagecreatefromjpeg($file);
                break;

            case IMAGETYPE_PNG:
                $ret = imagecreatefrompng($file);
                break;

            case IMAGETYPE_GIF:
                $ret = imagecreatefromgif($file);
                break;

            default:
                trigger_error(sprintf('Unsupported image format %s', $file), E_USER_WARNING);
                break;
        }

        return $ret;
    }

    public function writeImage($image, $file, $quality=100, $format="jpg")
    {
        $ret = false;

        # """ load the image data """
        $this->debugMessage("writeImage ". $file);

        switch($format) {
            case "png":
                $ret = imagepng($image, $file, 0);
                break;

            case "jpg":
            default;
                $ret = imagejpeg($image, $file, $quality);
                break;
        }


        return $ret;
    }

    public function isSupportImageType($file)
    {
        $ret = false;

        switch (exif_imagetype($file)) {
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
            case IMAGETYPE_GIF:
                $ret = true;
                break;

            default:
                $ret = false;
                break;
        }

        $this->debugMessage("isSupportImageType ". $ret);

        return($ret);
    }

    public function getTileFileName($scaleNumber, $columnNumber, $rowNumber)
    {
        # """ get the name of the file the tile will be saved as """
        # return '%s-%s-%s.jpg' % (str(scaleNumber), str(columnNumber), str(rowNumber))
        return "$scaleNumber-$columnNumber-$rowNumber.jpg";
    }

    public function getNewTileContainerName($tileGroupNumber=0)
    {
        # """ return the name of the next tile group container """
        return "TileGroup" . $tileGroupNumber;
    }


    public function preProcess()
    {
        # """ plan for the arrangement of the tile groups """
        $tier = 0;
        $tileGroupNumber = 0;
        $numberOfTiles = 0;

        foreach ($this->_v_scaleInfo as $width_height) {
            list($width, $height) = $width_height;

            # cycle through columns, then rows
            $row = 0;
            $column = 0;
            $ul_x = 0;
            $ul_y = 0;
            $lr_x = 0;
            $lr_y = 0;

            while (! (($lr_x == $width) && ($lr_y == $height))) {
                $tileFileName = $this->getTileFileName($tier, $column, $row);
                $tileContainerName = $this->getNewTileContainerName($tileGroupNumber);

                if ($numberOfTiles == 0) {
                    $this->createTileContainer($tileContainerName);
                } elseif ($numberOfTiles % $this->tileSize == 0) {
                    $tileGroupNumber++;
                    $tileContainerName = $this->getNewTileContainerName($tileGroupNumber);
                    $this->createTileContainer($tileContainerName);

                    $this->debugMessage("new tile group " .$tileGroupNumber ." tileContainerName=" . $tileContainerName);
                }
                $this->_v_tileGroupMappings[$tileFileName] = $tileContainerName;
                $numberOfTiles++;

                # for the next tile, set lower right cropping point
                if ($ul_x + $this->tileSize < $width) {
                    $lr_x = $ul_x + $this->tileSize;
                } else {
                    $lr_x = $width;
                }

                if ($ul_y + $this->tileSize < $height) {
                    $lr_y = $ul_y + $this->tileSize;
                } else {
                    $lr_y = $height;
                }

                # for the next tile, set upper left cropping point
                if ($lr_x == $width) {
                    $ul_x = 0;
                    $ul_y = $lr_y;
                    $column = 0;
                    $row++;
                } else {
                    $ul_x = $lr_x;
                    $column++;
                }
            }
            $tier++;
        }
    }

    public function processRowImage($tier=0, $row=0)
    {
        # """ for an image, create and save tiles """
        list($tierWidth, $tierHeight) = $this->_v_scaleInfo[$tier];

        $this->debugMessage("tier $tier width $tierWidth  height $tierHeight");

        $rowsForTier = floor($tierHeight/$this->tileSize);

        if ($tierHeight % $this->tileSize > 0) {
            $rowsForTier++;
        }

        list($root, $ext) = explode(".", $this->_v_imageFilename);

        if (!$root) {
            $root = $this->_v_imageFilename;
        }

        $ext = ".jpg";

        # $imageRow = None
        if ($tier == count($this->_v_scaleInfo) -1) {
            $firstTierRowFile = $root . $tier. "-" . $row . $ext;

            $this->debugMessage("firstTierRowFile=$firstTierRowFile");

            if (is_file($firstTierRowFile)) {
                $imageRow = $this->openImage($firstTierRowFile);

                $this->debugMessage("firstTierRowFile exists");
            }
        } else {
            # create this row from previous tier's rows
            $imageRow = imagecreatetruecolor($tierWidth, $this->tileSize);

            $t = $tier+1;
            $r = $row+$row;
            $firstRowFile = $root . $t . "-" . $r . $ext;

            $this->debugMessage("create this row from previous tier's rows tier=$tier row=$row firstRowFile=$firstRowFile");

            $this->debugMessage("imageRow tierWidth=$tierWidth tierHeight= $this->tileSize");

            $firstRowWidth = 0;
            $firstRowHeight = 0;
            $secondRowWidth = 0;
            $secondRowHeight = 0;
            if (is_file($firstRowFile)) {
                #        print firstRowFile + ' exists, try to open...'
                $firstRowImage = $this->openImage($firstRowFile);
                $firstRowWidth = imagesx($firstRowImage);
                $firstRowHeight = imagesy($firstRowImage);
                $imageRowHalfHeight = floor($this->tileSize/2);

                $this->debugMessage("imageRow imagecopyresized tierWidth=$tierWidth imageRowHalfHeight= $imageRowHalfHeight firstRowWidth=$firstRowWidth firstRowHeight=$firstRowHeight");

                imagecopyresized($imageRow, $firstRowImage, 0, 0, 0, 0, $tierWidth, $firstRowHeight, $firstRowWidth, $firstRowHeight);
                unlink($firstRowFile);
            }

            $r=$r+1;
            $secondRowFile =  $root . $t . "-" . $r . $ext;

            $this->debugMessage("create this row from previous tier's rows tier=$tier row=$row secondRowFile=$secondRowFile");

            # there may not be a second row at the bottom of the image...
            if (is_file($secondRowFile)) {
                $this->debugMessage($secondRowFile . " exists, try to open...");

                $secondRowImage = $this->openImage($secondRowFile);
                $secondRowWidth = imagesx($secondRowImage);
                $secondRowHeight = imagesy($secondRowImage);

                $this->debugMessage("imageRow imagecopyresized tierWidth=$tierWidth imageRowHalfHeight= $imageRowHalfHeight firstRowWidth=$firstRowWidth firstRowHeight=$firstRowHeight");

                imagecopyresampled($imageRow, $secondRowImage, 0, $imageRowHalfHeight, 0, 0, $tierWidth, $secondRowHeight, $secondRowWidth, $secondRowHeight);

                unlink($secondRowFile);
            }

            # the last row may be less than $this->tileSize...
            $rowHeight = $firstRowHeight+$secondRowHeight;
            $tileHeight = $this->tileSize;
            if ($rowHeight < $tileHeight) {
                $this->debugMessage("calling crop rowHeight=$rowHeight tileHeight=$tileHeight");
                $imageRow = $this->imageCrop($imageRow, 0, 0, $tierWidth, $rowHeight);
            }
        }

        if ($imageRow) {
            # cycle through columns, then rows
            $column = 0;
            $imageWidth=imagesx($imageRow);
            $imageHeight = imagesy($imageRow);
            $ul_x = 0;
            $ul_y = 0;
            $lr_x = 0;
            $lr_y = 0;

            while (!(($lr_x == $imageWidth) && ($lr_y == $imageHeight))) {
                $this->debugMessage("ul_x=$ul_x lr_x=$lr_x ul_y=$ul_y lr_y=$lr_y imageWidth=$imageWidth imageHeight=$imageHeight");

                # set lower right cropping point
                if (($ul_x + $this->tileSize) < $imageWidth) {
                    $lr_x = $ul_x + $this->tileSize;
                } else {
                    $lr_x = $imageWidth;
                }

                if (($ul_y + $this->tileSize) < $imageHeight) {
                    $lr_y = $ul_y + $this->tileSize;
                } else {
                    $lr_y = $imageHeight;
                }

                #tierLabel = len($this->_v_scaleInfo) - tier
                $this->debugMessage("calling crop");

                $this->saveTile($this->imageCrop($imageRow, $ul_x, $ul_y, $lr_x, $lr_y), $tier, $column, $row);
                $this->numberOfTiles++;

                $this->debugMessage("created tile: numberOfTiles= $this->numberOfTiles tier column row =($tier,$column,$row)");

                # set upper left cropping point
                if ($lr_x == $imageWidth) {
                    $ul_x = 0;
                    $ul_y = $lr_y;
                    $column = 0;
                    #row += 1
                } else {
                    $ul_x = $lr_x;
                    $column++;
                }
            }

            if ($tier > 0) {
                $halfWidth = max(1, floor($imageWidth/2));
                $halfHeight = max(1, floor($imageHeight/2));

                $tempImage = imagecreatetruecolor($halfWidth, $halfHeight);

                imagecopyresampled($tempImage, $imageRow, 0, 0, 0, 0, $halfWidth, $halfHeight, $imageWidth, $imageHeight);

                $rowFileName = $root.$tier."-".$row.$ext;

                $this->touch("file", $rowFileName);
                $this->writeImage($tempImage, $rowFileName);
                imagedestroy($tempImage);
            }

            if ($tier > 0) {
                $this->debugMessage("processRowImage final checks for tier $tier row=$row rowsForTier=$rowsForTier");

                if ($row % 2 != 0) {
                    $this->debugMessage("processRowImage final checks tier=$tier row=$row mod 2 check before");

                    $this->processRowImage($tier-1, floor(($row-1)/2));

                    $this->debugMessage("processRowImage final checks tier=$tier row=$row mod 2 check after");
                } elseif ($row == $rowsForTier-1) {
                    $this->debugMessage("processRowImage final checks tier=$tier row=$row rowsForTier=$rowsForTier check before");

                    $this->processRowImage($tier-1, floor($row/2));
                    $this->debugMessage("processRowImage final checks tier=$tier row=$row rowsForTier=$rowsForTier check after");
                }
            }
        }
    }

    public function processImage()
    {
        # """ starting with the original image, start processing each row """
        $tier = (count($this->_v_scaleInfo) -1);
        $row = 0;

        list($ul_y, $lr_y) = array(0,0);
        list($root, $ext) = explode(".", $this->_v_imageFilename);

        if (! $this->isSupportImageType($this->_v_imageFilename)) {
            $this->debugMessage("invalid image type");

            return(false);
        }

        if (!$root) {
            $root = $this->_v_imageFilename;
        }

        $ext = ".jpg";
        $this->debugMessage("processImage root=$root ext=$ext");

        $image = $this->openImage($this->_v_imageFilename);
        while ($row * $this->tileSize < $this->originalHeight) {
            $ul_y = $row * $this->tileSize;

            if ($ul_y + $this->tileSize < $this->originalHeight) {
                $lr_y = $ul_y + $this->tileSize;
            } else {
                $lr_y = $this->originalHeight;
            }

            $imageRow = $this->imageCrop($image, 0, $ul_y, $this->originalWidth, $lr_y);

            $saveFilename = $root . $tier . "-" . $row .  $ext;
            $this->debugMessage("processImage root=$root tier=$tier row=$row saveFilename=$saveFilename");

            $this->touch("file", $saveFilename);

            $this->writeImage($imageRow, $saveFilename, 100, "png");
            imagedestroy($imageRow);
            $this->processRowImage($tier, $row);
            $row++;
        }
        imagedestroy($image);
    }


    public function getXMLOutput()
    {
        # """ create xml metadata about the tiles """
        $numberOfTiles = $this->getNumberOfTiles();
        $xmlOutput = "<IMAGE_PROPERTIES WIDTH=\"".$this->originalWidth."\" HEIGHT=\"".$this->originalHeight."\" NUMTILES=\"".$numberOfTiles."\" NUMIMAGES=\"1\" VERSION=\"1.8\" TILESIZE=\"".$this->tileSize."\" />";

        return $xmlOutput;
    }


    public function getAssignedTileContainerName($tileFileName)
    {
        # """ return the name of the tile group for the indicated tile """
        if ($tileFileName) {
            if (isset($this->_v_tileGroupMappings) && $this->_v_tileGroupMappings) {
                $containerName = $this->_v_tileGroupMappings[$tileFileName];
                if ($containerName) {
                    return $containerName;
                }
            }
        }

        $containerName = $this->getNewTileContainerName();

        $this->debugMessage("getAssignedTileContainerName returning getNewTileContainerName " .$containerName);

        return $containerName;
    }

    public function getImageMetadata()
    {
        #    """ given an image name, load it and extract metadata """
        list($this->originalWidth, $this->originalHeight, $this->format) = getimagesize($this->_v_imageFilename);

        # get scaling information
        $width = $this->originalWidth;
        $height = $this->originalHeight;

        $this->debugMessage("getImageMetadata for file $this->_v_imageFilename originalWidth=$width originalHeight=$height tilesize=$this->tileSize");

        $width_height = array($width,$height);
        array_unshift($this->_v_scaleInfo, $width_height);
        while (($width > $this->tileSize) || ($height > $this->tileSize)) {
            $width = floor($width / 2);
            $height = floor($height / 2);
            $width_height = array($width,$height);
            array_unshift($this->_v_scaleInfo, $width_height);

            $this->debugMessage("getImageMetadata newWidth=$width newHeight=$height");
        }
        # tile and tile group information
        $this->preProcess();
    }

    public function createTileContainer($tileContainerName="")
    {
        # """ create a container for the next group of tiles within the data container """
        $tileContainerPath = $this->_v_saveToLocation."/".$tileContainerName;

        if (!is_dir($tileContainerPath)) {
            $this->touch("dir", $tileContainerPath);
        }
    }


    public function createDataContainer($imageName)
    {
        # """ create a container for tiles and tile metadata """
        $directory = dirname($imageName);
        $filename = basename($imageName);

        list($root, $ext) = explode(".", basename($filename));
        $root = $root . "_zdata";

        $this->_v_saveToLocation = $directory."/".$root;

        # If the paths already exist, an image is being re-processed, clean up for it.
        if (is_dir($this->_v_saveToLocation)) {
            $rm_err = $this->rm($this->_v_saveToLocation);
        }

        $this->touch("dir", $this->_v_saveToLocation);
    }

    public function getFileReference($scaleNumber, $columnNumber, $rowNumber)
    {
        # """ get the full path of the file the tile will be saved as """
        $tileFileName = $this->getTileFileName($scaleNumber, $columnNumber, $rowNumber);
        $tileContainerName = $this->getAssignedTileContainerName($tileFileName);
        return $this->_v_saveToLocation."/".$tileContainerName."/".$tileFileName;
    }


    public function getNumberOfTiles()
    {
        # """ get the number of tiles generated """

        return $this->numberOfTiles;
    }

    public function saveXMLOutput()
    {
        # """ save xml metadata about the tiles """
        $xml_file = $this->_v_saveToLocation."/ImageProperties.xml";

        $this->touch("file", $xml_file);

        $xmlFile = fopen($xml_file, 'w');
        fwrite($xmlFile, $this->getXMLOutput());
        fclose($xmlFile);
    }

    public function saveTile($image, $scaleNumber, $column, $row)
    {
        #	""" save the cropped region """
        $tile_file = $this->getFileReference($scaleNumber, $column, $row);

        $this->touch("file", $tile_file);

        $this->writeImage($image, $tile_file, $this->qualitySetting);

        $this->debugMessage("Saving to tile_file $tile_file");
    }

    public function ZoomifyProcess($image_name)
    {
        $this->debugMessage("Processing " . $image_name . "...");

        # """ the method the client calls to generate zoomify metadata """
        $this->_v_imageFilename = $image_name;

        $this->createDataContainer($image_name);
        $this->getImageMetadata();
        $this->processImage();
        $this->saveXMLOutput();
    }

    public function touch($type, $path)
    {
        if ($type == "file") {
            @mkdir(dirname($path), $this->_dirmode, true);
            @touch($path);
        } else {
            @mkdir($path, $this->_dirmode, true);
        }

        @chmod($path, $this->_dirmode);
        @chgrp($path, $this->_filegroup);
    }

    public function debugMessage($msg)
    {
        if (! $this->_debug) {
            return;
        }

        $trace = debug_backtrace();

        if (php_sapi_name() == 'cli') {
            printf("Line %04d: %s\n", $trace[0]["line"], $msg);
        } else {
            printf("Line %04d: %s<br>\n", $trace[0]["line"], $msg);
        }
    }
}
