<?php

namespace EdSDK\FileUploaderServer\lib\action\resp;

class RespUploadInit extends RespOk {

    public $uploadId;
	public $settings;

	public function __construct($uploadId, $config) {
        $this->uploadId = $uploadId;
        $this->settings = new Settings();
        $this->settings->maxImageResizeWidth = $config->getMaxImageResizeWidth();
        $this->settings->maxImageResizeHeight = $config->getMaxImageResizeHeight();
    }

}
