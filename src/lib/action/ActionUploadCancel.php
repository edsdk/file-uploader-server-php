<?php

namespace EdSDK\FileUploaderServer\lib\action;

use EdSDK\FileUploaderServer\lib\file\UtilsPHP;
use EdSDK\FileUploaderServer\lib\action\resp\Message;
use EdSDK\FileUploaderServer\lib\action\resp\RespOk;
use EdSDK\FileUploaderServer\lib\MessageException;
use Exception;

class ActionUploadCancel extends AActionUploadId {

	public function getName() {
		return "uploadCancel";
	}

	public function run($req) {
		$this->validateUploadId($req);
		if (!$this->m_config->doKeepUploads()) {
            try {
                UtilsPHP::delete($this->m_config->getTmpDir() . "/" . $req->uploadId);
            } catch (Exception $e) {
                error_log($e);
                throw new MessageException(Message::createMessage(Message::UNABLE_TO_DELETE_UPLOAD_DIR));
            }
		}
		return new RespOk();
	}

}