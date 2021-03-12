<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\file;

use EdSDK\FileUploaderServer\lib\action\resp\Message;
use EdSDK\FileUploaderServer\lib\action\resp\FileData;
use EdSDK\FileUploaderServer\lib\MessageException;

class FileUploadedQuick extends AFile
{
    protected $m_newName;

    protected $m_confilictsErrors = [];
    protected $m_customErrors = [];

    protected $dir;

    protected $name;

    public function __construct($config, $dir, $name, $newName)
    {
        parent::__construct($config, $dir, $name);
        $this->dir = $dir;
        $this->name = $name;
        $this->m_newName = $newName;
        $this->name = $this->checkFileNameExistence();
    }

    private function checkFileNameExistence()
    {
        function file_newname($path, $filename)
        {
            if ($pos = strrpos($filename, '.')) {
                $name = substr($filename, 0, $pos);
                $ext = substr($filename, $pos);
            } else {
                $name = $filename;
            }

            $newpath = $path . '/' . $filename;
            $newname = $filename;
            $counter = 0;
            while (file_exists($newpath)) {
                $newname = $name . '_' . $counter . $ext;
                $newpath = $path . '/' . $newname;
                $counter++;
            }

            return $newname;
        }

        return file_newname($this->dir, $this->name);
    }

    public function getBaseDir()
    {
        return $this->m_config->getTmpDir();
    }

    public function getNewName()
    {
        return $this->m_newName;
    }

    public function checkForErrors($checkForExist)
    {
        if (!parent::checkForErrors($checkForExist)) {
            return false;
        }

        if (
            $this->m_newName !== $this->getName() &&
            !Utils::isFileNameSyntaxOk($this->m_newName)
        ) {
            $this->m_commonErrors[] = Message::createMessage(
                Message::FILE_ERROR_SYNTAX,
                $this->m_newName
            );
        }

        if (Utils::isImage($this->getName())) {
            $ext = $this->getExt();
            $newExt = Utils::getExt($this->m_newName);
            if ($ext !== $newExt) {
                if (
                    !($ext === 'jpg' && $newExt === 'jpeg') &&
                    !($ext === 'jpeg' && $newExt === 'jpg')
                ) {
                    $this->m_commonErrors[] = Message::createMessage(
                        Message::FILE_ERROR_INCORRECT_IMAGE_EXT_CHANGE,
                        $ext,
                        $newExt
                    );
                }
            }
        }
        return true;
    }

    public function addCustomError($message)
    {
        $this->m_customErrors[] = $message;
    }

    public function getErrors()
    {
        $errors = (array) parent::getErrors();
        for ($i = 0; $i < count($this->m_confilictsErrors); $i++) {
            $errors[] = $this->m_confilictsErrors[$i];
        }
        for ($i = 0; $i < count($this->m_customErrors); $i++) {
            $errors[] = $this->m_customErrors[$i];
        }
        return $errors;
    }

    public function checkForConflicts($dir)
    {
        $this->m_confilictsErrors = [];
    }

    public function upload($file)
    {
        $initName = $this->getName();
        $this->setFreeFileName();
        if (!move_uploaded_file($file['tmp_name'], $this->getFullPath())) {
            throw new MessageException(
                Message::createMessage(Message::WRITING_FILE_ERROR, $initName)
            );
        }
    }

    public function isCommited()
    {
        return true;
    }

    public function getFullPath()
    {
        return $this->dir . '/' . $this->name;
    }

    public function getData()
    {
        $data = new FileData();
        $data->isCommited = $this->isCommited();
        $data->name = $this->name;
        $data->dir = $this->getDir();
        $data->bytes = $this->getSize();
        $errors = $this->getErrors();
        $data->errors = [];
        for ($i = 0; $i < count($errors); $i++) {
            $data->errors[] = (array) $errors[$i];
        }

        $data->isImage = $this->isImage();
        $data->sizes = [];
        if ($data->isImage) {
            $data->width = $this->getImageWidth();
            $data->height = $this->getImageHeight();
            if ($data->isCommited) {
                if ($this->m_mainFile === null) {
                    $modifications = $this->getModifications();
                    for ($i = 0; $i < count($modifications); $i++) {
                        $data->sizes[
                            $modifications[$i]->getModificationName()
                        ] = $modifications[$i]->getData();
                    }
                }
            }
        }
        return $data;
    }
}
