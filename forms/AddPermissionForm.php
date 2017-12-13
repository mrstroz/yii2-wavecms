<?php

namespace mrstroz\wavecms\forms;

use mrstroz\wavecms\models\AuthItemChild;
use Yii;
use yii\base\Model;

/**
 * Assign role form
 */
class AddPermissionForm extends Model
{
    public $name;
    public $role;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'name'], 'required'],
            [['name'], 'permission_assigned']
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['name'] = Yii::t('wavecms/user','Name');
        $labels['role'] = Yii::t('wavecms/user','Role');

        return $labels;
    }

    public function permission_assigned($attribute, $params)
    {
        if (AuthItemChild::find()->where(['parent' => $this->role, 'child' => $this->name])->one()) {
            $this->addError($attribute, Yii::t('wavecms/user', 'This permission is already assigned'));
            return true;
        }
        return false;

    }
}
