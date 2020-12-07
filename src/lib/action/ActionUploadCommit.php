<?php

/**
 * File Uploader Server package
 * Developer: N1ED
 * Website: https://n1ed.com/
 * License: GNU General Public License Version 3 or later
 **/

namespace EdSDK\FileUploaderServer\lib\action;

use EdSDK\FileUploaderServer\lib\file\FileUploaded;
use EdSDK\FileUploaderServer\lib\file\UtilsPHP;
use EdSDK\FileUploaderServer\lib\action\resp\Message;
use EdSDK\FileUploaderServer\lib\action\resp\RespOk;
use EdSDK\FileUploaderServer\lib\action\resp\RespUploadCommit;
use EdSDK\FileUploaderServer\lib\MessageException;
use Exception;

class ActionUploadCommit extends AActionUploadId {

  public function getName() {
    return "uploadCommit";
  }

  protected function validateSize($size, $sizeName) {
    $size->enlarge = $this->validateBoolean($size->enlarge, $sizeName === "preview");
    $size->width = $this->validateInteger($size->width, 0);
    $size->height = $this->validateInteger($size->height, 0);
  }

  protected function validateSizes($req) {
    if (!isset($req->sizes) || $req->sizes === NULL) {
      $req->sizes = [];
    }
    else {
      $sizesNames = ["full", "preview"];
      //theoretical workaround for 7.4
      $req->sizes = (array) $req->sizes;
      for ($i = 0; $i < count($sizesNames); $i++) {
        $sizeName = $sizesNames[$i];
        if ($req->sizes[$sizeName]) {
          $this->validateSize($req->sizes[$sizeName], $sizeName);
        }
      }
    }
  }

  public function run($req) {
    $this->validateUploadId($req);

    $this->validateSizes($req);

    $req->doCommit = $this->validateBoolean($req->doCommit, TRUE);
    $req->autoRename = $this->validateBoolean($req->autoRename, FALSE);
    $req->dir = $this->validateString($req->dir, "");

    if (strpos($req->dir, "/") !== 0) {
      $req->dir = "/" . $req->dir;
    }

    if (UtilsPHP::normalizeNoEndSeparator($req->dir) === NULL) {
      throw new MessageException(Message::createMessage(Message::DIR_DOES_NOT_EXIST, $req->dir));
    }

    $req->dir = UtilsPHP::normalizeNoEndSeparator($req->dir) . "/";

    $dir = $this->m_config->getBaseDir() . $req->dir;
    if (!file_exists($dir) && !mkdir($dir)) {
      throw new MessageException(Message::createMessage(Message::DIR_DOES_NOT_EXIST, $req->dir));
    }

    if ($req->files === NULL || count($req->files) == 0) {
      throw new MessageException(Message::createMessage(Message::FILES_NOT_SET));
    }

    $filesToCommit = [];
    for ($i = 0; $i < count($req->files); $i++) {
      $fileDef = $req->files[$i];

      if ($fileDef->name === NULL) {
        throw new MessageException(Message::createMessage(Message::MALFORMED_REQUEST));
      }

      if (!isset($fileDef->newName) || $fileDef->newName === NULL) {
        $fileDef->newName = $fileDef->name;
      }

      $file = new FileUploaded($this->m_config, $req->uploadId, $fileDef->name, $fileDef->newName);
      $filesToCommit[] = $file;

      if (!$file->isImage() && count($req->sizes) > 0) {
        $file->addCustomError(Message::createMessage(Message::FILE_IS_NOT_IMAGE));
      }
    }

    // Check there are no equal names
    for ($i = 0; $i < count($filesToCommit); $i++) {
      $name = $filesToCommit[$i]->getNewName();
      for ($j = 0; $j < count($filesToCommit); $j++) {
        $name2 = $filesToCommit[$j]->getNewName();
        if ($i != $j && $name === $name2) {
          $filesToCommit[$i]->addCustomError(Message::createMessage(Message::DUPLICATE_NAME));
          break;
        }
      }
    }

    // Check files for errors
    for ($i = 0; $i < count($filesToCommit); $i++) {
      $file = $filesToCommit[$i];
      $file->checkForErrors(TRUE);
      if (!$req->autoRename) {
        $file->checkForConflicts($req->dir);
      }
    }

    $filesToCommitWithErrors = [];
    for ($i = 0; $i < count($filesToCommit); $i++) {
      if (count($filesToCommit[$i]->getErrors()) > 0) {
        $filesToCommitWithErrors[] = $filesToCommit[$i]->getData();
      }
    }

    if (count($filesToCommitWithErrors) > 0) {
      throw new MessageException(Message::createMessageByFiles(Message::FILES_ERRORS, $filesToCommitWithErrors));
    }

    // Validation ended
    if (!$req->doCommit) {
      return new RespOk();
    }

    // 1. Commit
    $filesCommited = [];
    for ($i = 0; $i < count($filesToCommit); $i++) {
      $fileToCommit = $filesToCommit[$i];
      $fileCommited = $fileToCommit->commit($req->dir, $req->autoRename);
      $filesCommited[] = $fileCommited;
      try {
        $fileCommited->applySizes($req->sizes);
      } catch (MessageException $e) {
        for ($j = 0; $j < count($filesCommited); $j++) {
          $filesCommited[$j]->delete();
        }
        throw $e;
      }
    }

    // 2. Remove uploadAndCommit directory
    if (!$this->m_config->doKeepUploads()) {
      try {
        UtilsPHP::delete($this->m_config->getTmpDir() . "/" . $req->uploadId);
      } catch (Exception $e) {
        error_log($e);
        // Error, but we do not throw anything - we've commited files and need to return them
      }
    }

    // 3. Send response with the list of files copied
    $files = [];
    for ($i = 0; $i < count($filesCommited); $i++) {
      $files[] = $filesCommited[$i]->getData();
    }

    $resp = new RespUploadCommit();
    $resp->files = $files;
    return $resp;
  }

}
