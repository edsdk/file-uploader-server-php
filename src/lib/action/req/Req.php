<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\action\req;

class Req {

    public $action;

    public $test_clearAllFiles;
    public $test_serverConfig;

    public $m_fileName;
    public $m_fileSize;
    public $m_file;

}

class ReqError extends Req {

    public $message;

    public static function createReqError($msg) {

        ob_start();
        debug_print_backtrace();
        error_log(ob_get_clean());

        $req = new ReqError();
        $req->message = $msg;
        $req->action = "error";
        return $req;
    }

}

class ReqUploadId extends Req {

    public $uploadId;

}

class ReqUploadAddFile extends ReqUploadId {

    public $url;

}

class ReqUploadRemoveFile extends ReqUploadId {

    public $name;

}

class ReqUploadCommit extends ReqUploadId {

    public $sizes; // of [enlarge: boolean, width: number, height: number]
    public $doCommit;
    public $autoRename;
    public $dir;
    public $files; // of [name: string, newName: string]

}