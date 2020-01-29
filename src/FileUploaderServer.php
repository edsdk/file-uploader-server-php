<?php


/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer;

use EdSDK\FileUploaderServer\servlet\UploaderServlet;
use Exception;

class FileUploaderServer {

    static function fileUploadRequest($config) {

        try {
            $servlet = new UploaderServlet();
            $servlet->init($config);
            $servlet->doPost($_POST, $_FILES);
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }

    }

}