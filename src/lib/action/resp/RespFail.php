<?php

namespace EdSDK\FileUploaderServer\lib\action\resp;

class RespFail extends RespOk {

    public $message;

    public function __construct($message) {
        $this->ok = false;
        $this->message = $message;
    }

}
