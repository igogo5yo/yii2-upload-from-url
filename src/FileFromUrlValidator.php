<?php
 /**
 * This file is part of the igogo5yo/yii2-upload-from-url project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright (c) igogo5yo
 * @link http://github.com/igogo5yo/yii2-upload-from-url
 */
namespace igogo5yo\uploadfromurl;
use Yii;
use igogo5yo\uploadfromurl\UploadFromUrl;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;

/**
 * FileFromUrlValidator verifies if an attribute is receiving a valid uploaded file from url.
 *
 * @property integer $sizeLimit The size limit for uploaded files. This property is read-only.
 *
 * @author Skliar Ihor <skliar.ihor@gmail.com>
 * @since 1.0
 */
class FileFromUrlValidator extends FileValidator
{  
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->uploadRequired === null) {
            $this->uploadRequired = Yii::t('yii', 'Please select a correct url.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($this->maxFiles > 1) {
            $files = $model->$attribute;
            if (!is_array($files)) {
                $this->addError($model, $attribute, $this->uploadRequired);
                return;
            }
            foreach ($files as $i => $file) {
                if (!$file instanceof UploadFromUrl || $file->error == UPLOAD_ERR_NO_FILE) {
                    unset($files[$i]);
                }
            }
            $model->$attribute = array_values($files);
            if (empty($files)) {
                $this->addError($model, $attribute, $this->uploadRequired);
            }
            if (count($files) > $this->maxFiles) {
                $this->addError($model, $attribute, $this->tooMany, ['limit' => $this->maxFiles]);
            } else {
                foreach ($files as $file) {
                    $result = $this->validateValue($file);
                    if (!empty($result)) {
                        $this->addError($model, $attribute, $result[0], $result[1]);
                    }
                }
            }
        } else {
            $result = $this->validateValue($model->$attribute);
            if (!empty($result)) {
                $this->addError($model, $attribute, $result[0], $result[1]);
            }
        }
    }
    /**
     * @inheritdoc
     */
    protected function validateValue($file)
    {
        if (!$file instanceof UploadFromUrl || $file->error == UPLOAD_ERR_NO_FILE) {
            return [$this->uploadRequired, []];
        }
        switch ($file->error) {
            case UPLOAD_ERR_OK:
                if ($this->maxSize !== null && $file->size > $this->maxSize) {
                    return [$this->tooBig, ['file' => $file->name, 'limit' => $this->getSizeLimit()]];
                } elseif ($this->minSize !== null && $file->size < $this->minSize) {
                    return [$this->tooSmall, ['file' => $file->name, 'limit' => $this->minSize]];
                } elseif (!empty($this->extensions) && !$this->validateExtension($file)) {
                    return [$this->wrongExtension, ['file' => $file->name, 'extensions' => implode(', ', $this->extensions)]];
                } elseif (!empty($this->mimeTypes) &&  !in_array(FileHelper::getMimeType($file->tempName), $this->mimeTypes, false)) {
                    return [$this->wrongMimeType, ['file' => $file->name, 'mimeTypes' => implode(', ', $this->mimeTypes)]];
                } else {
                    return null;
                }
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return [$this->tooBig, ['file' => $file->name, 'limit' => $this->getSizeLimit()]];
            case UPLOAD_ERR_PARTIAL:
                Yii::warning('File was only partially uploaded: ' . $file->name, __METHOD__);
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                Yii::warning('Missing the temporary folder to store the uploaded file: ' . $file->name, __METHOD__);
                break;
            case UPLOAD_ERR_CANT_WRITE:
                Yii::warning('Failed to write the uploaded file to disk: ' . $file->name, __METHOD__);
                break;
            case UPLOAD_ERR_EXTENSION:
                Yii::warning('File upload was stopped by some PHP extension: ' . $file->name, __METHOD__);
                break;
            default:
                break;
        }
        return [$this->message, []];
    }
    /**
     * @inheritdoc
     */
    public function isEmpty($value, $trim = false)
    {
        $value = is_array($value) ? reset($value) : $value;
        return !($value instanceof UploadFromUrl) || $value->error == UPLOAD_ERR_NO_FILE;
    }
    /**
     * Checks if given uploaded from url file have correct type (extension) according current validator settings.
     * @param BaseUploadFileValidable $file
     * @return boolean
     */
    protected function validateExtension($file)
    {
        $extension = mb_strtolower($file->extension, 'utf-8');
        if (!in_array($extension, $this->extensions, true)) {
            return false;
        }
        return true;
    }
}