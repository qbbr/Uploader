Q_Uploader
==========

```php
require_once 'Uploader/Autoloader.php';
Q_Uploader_Autoloader::register();

$uploader = new Q_Uploader('file');
$uploader->setAllowedExtensions(array('gif', 'jpeg', 'jpg', 'png'));
$uploader->saveTo('/path/to/writable/dir/');
```