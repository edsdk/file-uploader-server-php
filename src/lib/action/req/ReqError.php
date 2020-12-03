<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\action\req;

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