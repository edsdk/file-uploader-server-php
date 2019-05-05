<?php

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