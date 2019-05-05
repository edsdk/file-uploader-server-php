<?php

namespace EdSDK\FileUploaderServer\lib\action;

use EdSDK\FileUploaderServer\lib\action\resp\RespFail;

class ActionError extends AAction {

    public function getName() { return "error"; }

	public function run($req) {
		return new RespFail($req->message);
	}

}