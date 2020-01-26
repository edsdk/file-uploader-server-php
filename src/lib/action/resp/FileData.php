<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\action\resp;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class FileData {

    public $isCommited;
    public $name;
    public $dir;
    public $bytes;
    public $isImage;
    public $width;
    public $height;
    public $errors;
    public $sizes;

}
