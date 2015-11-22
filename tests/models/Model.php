<?php
/**
 * Created by PhpStorm.
 * User: ihor
 * Date: 11/22/15
 * Time: 11:30
 */

namespace tests\models;

/**
 * Model
 */
class Model extends \yii\base\Model
{
    public $image;

    public function rules()
    {
        return [
            ['image', 'igogo5yo\uploadfromurl\FileFromUrlValidator', 'extensions' => 'csv', 'mimeTypes' => 'text/plain'],
            ['image', 'safe']
        ];
    }
}