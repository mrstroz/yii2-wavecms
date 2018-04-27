<?php

namespace mrstroz\wavecms\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class FileBehavior
 * @package mrstroz\wavecms\components\behaviors
 * Behavior used for save files in WaveCMS
 */
class FileBehavior extends Behavior
{

    /**
     * @var string Attribute used in behavior
     */
    public $attribute;

    /**
     * @var string Folder name used for save files
     */
    public $folder = 'files';

    /**
     * Events
     * @inheritdoc
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'uploadFile',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'uploadFile',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteFile'
        ];
    }

    /**
     * Init function
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {

        if (!$this->attribute) {
            throw new InvalidConfigException(Yii::t('wavecms/main', 'Property "attribute" is not defined in FileBehavior'));
        }

        parent::init();
    }

    /**
     * Upload files on save and update event
     * @param $event
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function uploadFile($event)
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

            $event->sender->{$this->attribute} = $fileName;
        } else {
            if (Yii::$app->request->post($this->attribute . '_file_delete')) {
                $this->unlinkFiles($oldFile);
                $event->sender->{$this->attribute} = null;
            } else {
                $event->sender->{$this->attribute} = $oldFile;
            }
        }
    }

    /**
     * Delete file on delete event
     * @param $event
     */
    public function deleteFile($event)
    {
        $this->unlinkFiles($event->sender->{$this->attribute});
    }

    /**
     * Helper function
     * @return string
     */
    public function getWebFolder()
    {
        return Yii::getAlias('@frontWeb') . '/' . $this->folder;
    }

    /**
     * Helper function
     * @return string
     */
    public function getWebrootFolder()
    {
        return Yii::getAlias('@frontWebroot') . '/' . $this->folder;
    }

    /**
     * Helper function used for unlink files
     * @return string
     */
    public function unlinkFiles($fileName)
    {
        $folder = $this->getWebrootFolder();

        if ($fileName) {
            if (@file_exists($folder . '/' . $fileName)) {
                unlink($folder . '/' . $fileName);
            }
        }
    }
}