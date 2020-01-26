<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\action;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use EdSDK\FileUploaderServer\lib\action\resp\RespFail;

class ActionError extends AAction {

    public function getName() { return "error"; }

	public function run($req) {
		return new RespFail($req->message);
	}

}