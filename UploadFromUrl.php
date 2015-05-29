<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace igogo5yo\upload;

use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\base\Exception;
use yii\base\Object;
/**
 * UploadFileByURL represents the information for an file by url address.
 *
 * You can call [[initWithModel()]] or  [[initWithUrl()]] or  [[initWithUrlAndModel()]] with to retrieve the instance of file object,
 * and then use [[saveAs()]] to save it on the server.
 * You may also query other information about the file, including [[name]],
 * [[extension]], [[type]], [[size]] etc.
 *
 * @todo COMMENST 
 * @property string $baseName Original file base name. This property is read-only.
 * @property string $extension File extension. This property is read-only.
 * @property boolean $hasError Whether there is an error with the uploaded file. Check [[error]] for detailed
 * error code information. This property is read-only.
 *
 * @author Skliar Ihor <skliar.ihor@gmail.com>
 * @since 2.0
 */

class UploadFromUrl extends Object
{
	/**
     * @var string the original name of the file being uploaded
     */
    public $name;
    /**
     * @var string the MIME-type of the uploaded file (such as "image/gif").
     * Since this MIME type is not checked on the server side, do not take this value for granted.
     * Instead, use [[\yii\helpers\FileHelper::getMimeType()]] to determine the exact MIME type.
     */
    public $type;
    /**
     * @var integer the actual size of the uploaded file in bytes
     */
    public $size;
    /**
     * @var integer an error code describing the status of this file uploading.
     * @see http://www.php.net/manual/en/features.file-upload.errors.php
     */
    public $error = UPLOAD_ERR_OK;
    /**
     * @var integer the actual size of the uploaded file in bytes
     */
    public $extension;

	public $url;
	public $model;
	public $attribute;
	public $isWithModel = false;

    /**
     * String output.
     * This is PHP magic method that returns string representation of an object.
     * The implementation here returns the uploaded file's name.
     * @return string the string representation of the object
     */
    public function __toString()
    {
        return $this->name;
    }

	public static function initWithUrl($url)
    {
        return self::getInstance([
    		'url' => $url
    	]);
    }

	public static function initWithModel(ActiveRecord $model, $attribute) 
	{
        return self::createInstance([
    		'url' => $model->{$attribute},
    		'isWithModel' => true,
    		'model' => $model,
    		'attribute' => $attribute,
    	]);
	}

	public static function initWithUrlAndModel($url, ActiveRecord $model, $attribute)
	{
        return self::createInstance([
    		'url' => $url,
    		'isWithModel' => true,
    		'model' => $model,
    		'attribute' => $attribute,
    	]);
	}

	public function saveAs($file, $saveToModel = false)
	{
		if ($saveToModel && $this->isWithModel) {
			$this->model->{$this->attribute} = $file;
		} else if ($this->isWithModel) {
			$this->model->{$this->attribute} = null;
		}
		return copy($this->url, $file);
	}

	protected static function createInstance($options)
	{
		$options = self::extendOptions($options);
		return new static($options);
	}

	protected static function extendOptions(array $options)
	{
		$parsed_url = parse_url($options['url']);
		$headers = @get_headers($options['url'], 1);

		if (!$parsed_url || !$headers || !preg_match('/^(HTTP)(.*)(200)(.*)/i', $headers[0])) {
			$options['error'] = UPLOAD_ERR_NO_FILE;
		}

		$fname = explode('/', $parsed_url['path']);
		$options['name'] = end($fname);
		$ext = explode('.', $options['name']);
		$options['extension'] = mb_strtolower(end($ext));

		$options['size'] = isset($headers['Content-Length']) ? $headers['Content-Length'] : 0;
		$options['type'] = isset($headers['Content-Type']) ? $headers['Content-Type'] : FileHelper::getMimeTypeByExtension($options['name']);

		return $options;
	}
}