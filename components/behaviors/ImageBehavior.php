<?php

namespace mrstroz\wavecms\components\behaviors;


use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

class ImageBehavior extends Behavior
{

    public $attribute;
    public $folder = 'images';
    public $sizes = [];

    public $thumbFolder = 'thumbs';
    public $thumbWidth = 320;
    public $thumbHeight;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'uploadImage',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'uploadImage',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteImage'
        ];
    }

    public function init()
    {

        if (!$this->attribute) {
            throw new InvalidConfigException(Yii::t('wavecms/main','Property "attribute" is not defined in ImageBehavior'));
        }

        if (!is_array($this->sizes)) {
            throw new InvalidConfigException(Yii::t('wavecms/main','Property "sizes" is not defined in ImageBehavior'));
        }

        parent::init();
    }

    public function uploadImage($event)
    {
        if (!array_key_exists($this->attribute, $event->sender->attributes)) {
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Attribute {attribute} not found in model {model}', ['attribute' => $this->attribute, 'model' => $event->sender->className()]));
        }

        $oldFile = false;
        if (isset($event->sender->oldAttributes[$this->attribute])) {
            $oldFile = $event->sender->oldAttributes[$this->attribute];
        }

        $uploadedFile = UploadedFile::getInstance($event->sender, $this->attribute);

        if (null !== $uploadedFile && $uploadedFile->size !== 0) {

            $folder = $this->getWebrootFolder();

            if ($oldFile) {
                $this->unlinkFiles($oldFile);
            }

            $baseName = $uploadedFile->baseName;
            $fileName = $baseName . '.' . $uploadedFile->extension;

            while (@file_exists($folder . '/' . $fileName)) {
                $baseName .= '_';
                $fileName = $baseName . '.' . $uploadedFile->extension;
            }

            FileHelper::createDirectory($folder, 0777);
            $uploadedFile->saveAs($folder . '/' . $fileName);

            FileHelper::createDirectory($folder . '/' . $this->thumbFolder, 0777);
            Image::thumbnail($folder . '/' . $fileName, $this->thumbWidth, $this->thumbHeight)
                ->save($folder . '/' . $this->thumbFolder . '/' . $fileName);

            if (is_array($this->sizes)) {
                $i = 0;
                foreach ($this->sizes as $size) {
                    FileHelper::createDirectory($folder . '/' . $i, 0777);
                    Image::thumbnail($folder . '/' . $fileName, $size[0], $size[1])
                        ->save($folder . '/' . $i . '/' . $fileName);
                    $i++;
                }
            }

            $event->sender->{$this->attribute} = $fileName;
        } else {
            if (Yii::$app->request->post($this->attribute . '_image_delete')) {
                $this->unlinkFiles($oldFile);
                $event->sender->{$this->attribute} = null;
            } else {
                $event->sender->{$this->attribute} = $oldFile;
            }
        }
    }

    public function deleteImage($event)
    {
        $this->unlinkFiles($event->sender->{$this->attribute});
    }

    public function getWebFolder()
    {
        return Yii::getAlias('@frontWeb').'/'.$this->folder;
    }

    public function getWebrootFolder()
    {
        return Yii::getAlias('@frontWebroot').'/'.$this->folder;
    }

    public function unlinkFiles($fileName)
    {
        $folder = $this->getWebrootFolder();

        if ($fileName) {
            if (@file_exists($folder . '/' . $fileName)) {
                unlink($folder . '/' . $fileName);
            }
            if (@file_exists($folder . '/' . $this->thumbFolder . '/' . $fileName)) {
                unlink($folder . '/' . $this->thumbFolder . '/' . $fileName);
            }
            if (is_array($this->sizes)) {
                $i = 0;
                foreach ($this->sizes as $size) {
                    if (@file_exists($folder . '/' . $i . '/' . $fileName)) {
                        unlink($folder . '/' . $i . '/' . $fileName);
                    }
                    $i++;
                }
            }
        }
    }
}