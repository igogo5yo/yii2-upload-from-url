UploadFromUrl extension for Yii 2
=====================================
[![Build Status](https://travis-ci.org/igogo5yo/yii2-upload-from-url.svg?branch=master)](https://travis-ci.org/igogo5yo/yii2-upload-from-url)
[![Latest Stable Version](https://poser.pugx.org/igogo5yo/yii2-upload-from-url/v/stable)](https://packagist.org/packages/igogo5yo/yii2-upload-from-url) [![Total Downloads](https://poser.pugx.org/igogo5yo/yii2-upload-from-url/downloads)](https://packagist.org/packages/igogo5yo/yii2-upload-from-url)  [![License](https://poser.pugx.org/igogo5yo/yii2-upload-from-url/license)](https://packagist.org/packages/igogo5yo/yii2-upload-from-url) [![Dependency Status](https://www.versioneye.com/user/projects/55686ea96365320026021300/badge.svg?style=flat)](https://www.versioneye.com/user/projects/55686ea96365320026021300)

This is the upload file from url address extension for Yii 2. It have class UploadFromUrl for upload files from URL and it have FileFromUlrValidator for validate model attribute with file from url.

Please submit issue reports and pull requests to the main repository.
For license information check the [LICENSE](LICENSE.md)-file.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist igogo5yo/yii2-upload-from-url
```

or add

```
"igogo5yo/yii2-upload-from-url": ">=1.0"
```

to your `composer.json` file


Usage
----

**Example 1**
```php
$model = new Post();
$model->load(Yii::$app->request->post());

$file = UploadFromUrl::getInstance($model, 'image');

//if second parameter is TRUE it writes uploaded file path to this model property
$file->saveAs('uploads/yii.png', true);   

echo $model->image; // uploads/yii.png
```

**Example 2**
```php
$model = new Post();
$model->image = 'http://static.yiiframework.com/files/logo/yii.png';

$file = UploadFromUrl::initWithModel($model, 'image');

//if second parameter is TRUE it writes uploaded file path to this model property
$file->saveAs('uploads/yii.png', true);   

echo $model->image; // uploads/yii.png
```


**Example 3**
```php
$url = 'http://static.yiiframework.com/files/logo/yii.png' ;
$path = 'uploads/yii.png';

$file = UploadFromUrl::initWithUrl($url);
$file->saveAs($path);   

//Set to model
$model = new Post();
$model->image = $path;
```


**Example 4**
```php
$url = 'http://static.yiiframework.com/files/logo/yii.png' ;
$path = 'uploads/yii.png';
$model = new Post();

$file = UploadFromUrl::initWithUrlAndModel($url, $model, 'image');
$file->saveAs($path, true);   

echo $model->image; // uploads/yii.png
```

**Validation Example**
```php
[
...
     [['image'], 'igogo5yo\uploadfromurl\FileFromUrlValidator', 'extensions' => 'csv', 'mimeTypes' => 'text/plain'],
...
]
```


Run Tests
---------
```bash
phpunit --bootstrap tests/bootstrap.php tests
```
