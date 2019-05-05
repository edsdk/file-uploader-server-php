<?php

namespace EdSDK\FileUploaderServer\lib;

use Exception;

class MessageException extends Exception {

    protected $m_message;

    public function __construct($message) {
        parent::__construct();
        $this->m_message = (array)$message;
    }

    public function getFailMessage() {
        return $this->m_message;
    }

}
