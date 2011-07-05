Q_Uploader
==========

```php
require_once 'Uploader/Autoloader.php';
Q_Uploader_Autoloader::register();

$uploader = new Q_Uploader('file');
$uploader->setAllowedExtensions(array('gif', 'jpeg', 'jpg', 'png'));
$uploader->setSizeLimit(10485760); // default =  1048576 (1M)
//$uploader->originalFileName(true);
print_r($uploader->saveTo('_tmp'));
```

**or**

```php
$uploader->setAllowedExtensions(array('gif', 'jpeg', 'jpg', 'png'))
    ->setSizeLimit(1048576)
    ->originalFileName(true)
    ->saveTo('_tmp');
```
