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

    static function fileUploadRequest($config, $quick = false) {

        try {
            $servlet = new UploaderServlet();
            $servlet->init($config);
            $resp = $servlet->doPost($_POST, $_FILES, $quick);
            if ($quick) {
                return $resp;
            }
        } catch (Exception $e) {
            error_log($e);
            throw $e;
        }
    }
}