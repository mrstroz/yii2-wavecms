<?php

namespace mrstroz\wavecms\models;

use mrstroz\wavecms\models\query\SourceMessageQuery;
use Yii;

/**
 * This is the model class for table "source_message".
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 * @property string $translation
 *
 * @property Message[] $messages
 */
class SourceMessage extends \yii\db\ActiveRecord
{
    public $translation;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'source_message';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message'], 'string'],
            [['translation'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('wavecms/main', 'ID'),
            'category' => Yii::t('wavecms/main', 'Category'),
            'message' => Yii::t('wavecms/main', 'Message'),
            'translation' => Yii::t('wavecms/main', 'Translation'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Message::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageMessage()
    {
        return $this->hasMany(Message::className(), ['id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return SourceMessageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SourceMessageQuery(get_called_class());
    }
}
