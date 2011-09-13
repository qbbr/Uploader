Q_Uploader
==========

**uploader.php:**

```php
require_once 'Uploader/Autoloader.php';
Q_Uploader_Autoloader::register();

$uploader = new Q_Uploader('file');
$info = $uploader->setAllowedExtensions(array('gif', 'jpeg', 'jpg', 'png'))
    ->setSizeLimit(1048576) // default =  1048576 (1M)
    ->setUploadDir('_tmp')
    //->originalFileName(true)
    ->upload();

print_r($info);
```

Single file upload
------------------

**html:**

```html
<form action="uploader.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file">
    <input type="submit">
</form>
```

**result:**

*[image] key is optional, only for image files.*

```php
Array
(
    [basename] => 3445499eebc7f397ff67cbf1c218f8d5.jpg
    [filename] => 3445499eebc7f397ff67cbf1c218f8d5
    [extension] => jpg
    [size] => 381137
    [errors] => Array
        (
        )
    [image] => Array
        (
            [width] => 200
            [height] => 253
            [type] => 2
            [mime] => image/jpeg
        )

)
```

Multiple file upload
--------------------

**html:**

```html
<form action="uploader.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file[]"><br>
    <input type="file" name="file[]"><br>
    <input type="file" name="file[]"><br>
    <input type="file" name="file[]"><br>
    <input type="submit">
</form>
```

**result:**

```php
Array
(
    [0] => Array
        (
            [basename] => 8582b302dd9c4812eac51769cdaa5888.jpg
            [filename] => 8582b302dd9c4812eac51769cdaa5888
            [extension] => jpg
            [size] => 86784
            [errors] => Array
                (
                )

        )

    [1] => Array
        (
            [basename] => 7fb2d7a8b2bfe93d9890458e19cfe86e.jpg
            [filename] => 7fb2d7a8b2bfe93d9890458e19cfe86e
            [extension] => jpg
            [size] => 461924
            [errors] => Array
                (
                )

        )

    [2] => Array
        (
            [basename] => 3a1009403036428ab09d8c865a0ccc08.jpg
            [filename] => 3a1009403036428ab09d8c865a0ccc08
            [extension] => jpg
            [size] => 291010
            [errors] => Array
                (
                )

        )

    [3] => Array
        (
            [basename] => af6b7e42fab6d33a90e33b332214f26b.jpg
            [filename] => af6b7e42fab6d33a90e33b332214f26b
            [extension] => jpg
            [size] => 147658
            [errors] => Array
                (
                )

        )

)
```
