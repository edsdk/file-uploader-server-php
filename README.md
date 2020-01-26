# File Uploader

> Module for PHP for handling file uploads

Use uploader in your PHP website or application to let ImgPen or other compatible frontend components to store files on the server.

Choose the URL for handling uploads and configure ImgPen to use it, that's all.

Being tiny File Uploader has many useful features like transaction upload with no DB, supports changing URL and destination directory for files.

Currently this package has no documented API due to it supposed to use together with [ImgPen](https://imgpen.com) image editor only, but in future we will provide fine API reference for building your own applications with this uploader.


## Install

With [Composer](https://getcomposer.org/) installed, run

```
$ composer require edsdk/file-uploader-server-php
```


## Usage

To handle some URL you want in your web application, create a file which will be entry point for all requests, e. g. `uploader.php`: 

```php
<?php

    require __DIR__ . '/../vendor/autoload.php';
    
    use EdSDK\FileUploaderServer\FileUploader;
    
    FileUploader::fileUploadRequest(
        array(
            'dirFiles' => 'data',
            'dirTmp'   => 'data'
        )
    );
```

If you want to allow access to uploaded files (usually you do) please do not forget to open access to files directory.

Please also see [example of usage](https://packagist.org/packages/edsdk/imgpen-example-php) File Uploader with ImgPen for editing and uploading images.


## Server languages support

Current package is targeted to serve uploads inside PHP environment.

Another backends are also available for [ImgPen](https://imgpen.com) users:

- Node (TypeScript/JavaScript)
- PHP
- Java
- ASP.NET


## See Also

- [N1ED](https://n1ed.com) - Flmngr server perfectly works with #1 free HTML WYSIWYG Editor which can be installed on your website (any CMS).


## License

GNU General Public License version 3 or later; see LICENSE.txt