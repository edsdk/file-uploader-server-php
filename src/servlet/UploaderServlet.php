<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\servlet;

use EdSDK\FileUploaderServer\lib\file\UtilsPHP;
use EdSDK\FileUploaderServer\lib\action\req\ReqError;
use EdSDK\FileUploaderServer\lib\action\resp\Message;
use EdSDK\FileUploaderServer\lib\action\resp\RespFail;
use EdSDK\FileUploaderServer\lib\Actions;
use EdSDK\FileUploaderServer\lib\JsonCodec;
use EdSDK\FileUploaderServer\lib\Uploader;
use EdSDK\FileUploaderServer\lib\file\FileUploadedQuick;
use Exception;

class UploaderServlet {

    protected $m_actions;
    protected $m_json;
    protected $m_uploader;
    protected $m_config;

    public function init($config) {
        $this->m_actions = new Actions();
        $this->m_json = new JsonCodec($this->m_actions);
        $this->m_config = new ServletConfig($config);
        $this->m_uploader = new Uploader($this->m_config, $this->m_actions);
    }

    private function getFileInfo($vector) {
        $result = array();
        foreach($vector as $key1 => $value1)
            foreach($value1 as $key2 => $value2)
                $result[$key2][$key1] = $value2;
        return $result;
    }

    protected function getReq($post, $files, $quick = false) {
        $req = null;
        try {
            if ($quick) {
                $req = new \StdClass();
                $req->action = $post['action'];
            } else {
                $req = $this->m_json->fromJson($post['data']);
                if ($this->m_config->isTestAllowed()) {
                    if (array_key_exists('test_serverConfig', $req)) {
                        $this->m_config->setTestConfig($req->test_serverConfig);
                    }
                    if (array_key_exists('test_clearAllFiles', $req)) {
                        $this->clearAllFiles();
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e);
            return null;
        }

        if (array_key_exists('file', $files)) {
            $req->m_file = $files['file'];//$this->getFileInfo($files['file']);
            $req->m_fileName = $req->m_file['name'];
            $req->m_fileSize = $req->m_file['size'];
        }

        return $req;
    }

    protected function clearAllFiles() {
        UtilsPHP::cleanDirectory($this->m_config->getTmpDir());
        UtilsPHP::cleanDirectory($this->m_config->getBaseDir());
    }

    protected function addHeaders() {
        if ($this->m_config->getCrossDomainUrl() != null && strlen($this->m_config->getCrossDomainUrl()) > 0) {
            header("Access-Control-Allow-Origin: " . $this->m_config->getCrossDomainUrl());
            header("Access-Control-Allow-Methods: POST");
            header("Access-Control-Allow-Headers: accept, content-type");
            header("Access-Control-Max-Age: 1728000");
        }
    }

    public function doOptions() {
        $this->addHeaders();
    }

    public function doPost($post, $files, $quick = false) {
        $this->addHeaders();
        $resp = null;
        $strResp = null;
        try {
            $req = null;

            try {
                $req = $this->getReq($post, $files, $quick);
            } catch (Exception $e) {
                error_log($e);
            }

            if ($req === null)
                $req = new ReqError(Message::createMessage(Message::MALFORMED_REQUEST));

            $resp = $this->m_uploader->run($req);
            if ($resp === null)
                throw new Exception("Null response as result");

            $strResp = $this->m_json->toJson($resp);
        } catch (Exception $e) {
            error_log($e);
            $resp = new RespFail(Message::createMessage(Message::INTERNAL_ERROR));
            $strResp = $this->m_json->toJson($resp);
        }

        if ($quick) {
            return $strResp;
        } else {
            try {
                http_response_code(200);
                header('Content-Type: application/json; charset=UTF-8');
                print $strResp;
            } catch (Exception $e) {
                error_log($e);
            }
        }
    }

   

}