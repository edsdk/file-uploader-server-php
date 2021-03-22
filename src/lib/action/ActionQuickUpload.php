<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\action;

use EdSDK\FlmngrServer\fs\FMDiskFileSystem;
use EdSDK\FileUploaderServer\lib\file\FileUploadedQuick;
use EdSDK\FileUploaderServer\lib\action\resp\RespUploadAddFile;
use EdSDK\FileUploaderServer\lib\action\resp\Message;
use EdSDK\FileUploaderServer\lib\MessageException;

class ActionQuickUpload extends AActionUploadId
{
    public function getName()
    {
        return 'upload';
    }
    public function run($req)
    {
        $fileSystem = new FMDiskFileSystem([
            'dirFiles' => $this->m_config->getBaseDir(),
            'dirCache' => '',
        ]);


        if ($req->m_file) {
            if (
                array_key_exists('dir', $_POST) &&
                $_POST['dir'] &&
                $_POST['dir'] != '/' &&
                $_POST['dir'] != '' &&
                $_POST['dir'] != '.'
            ) {
                $target_dir = basename($_POST['dir']);
                $path =
                    dirname($_POST['dir']) == '.' ||
                    dirname($_POST['dir']) == '/'
                        ? ''
                        : '/' . dirname($_POST['dir']);

                $fullPath = basename($this->m_config->getBaseDir()) . $path;
                $fileSystem->createDir($fullPath, $target_dir);
                $uploadDir =
                    $fileSystem->getAbsolutePath($fullPath) . '/' . $target_dir;
                $req->m_relativePath = $_POST['dir'];
            } else {
                $target_dir = '';
                $fullPath = basename($this->m_config->getBaseDir());
                $uploadDir =
                    $fileSystem->getAbsolutePath($fullPath) .
                    '/' .
                    $target_dir .
                    '/';
                $req->m_relativePath = '/';
            }

            $file = new FileUploadedQuick(
                $this->m_config,
                $uploadDir,
                $req->m_fileName,
                $req->m_fileName,
                $req->m_relativePath
            );
            $file->upload($req->m_file);

            $resp = new RespUploadAddFile();
            $resp->file = $file->getData();

            return $resp;
        } else {
          throw new MessageException(Message::createMessage(Message::FILES_NOT_SET));
        }
    }
}
