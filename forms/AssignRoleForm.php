<?php

namespace mrstroz\wavecms\forms;

use mrstroz\wavecms\models\AuthAssignment;
use Yii;
use yii\base\Model;

/**
 * Assign role form
 */
class AssignRoleForm extends Model
{
    public $user_id;
    public $role;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'user_id'], 'required'],
            [['role'], 'role_assigned']
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        $labels['role'] = Yii::t('wavecms/user','Role');

        return $labels;
    }

    public function role_assigned($attribute, $params)
    {
        if (AuthAssignment::find()->where(['item_name' => $this->role, 'user_id' => $this->user_id])->one()) {
            $this->addError($attribute, Yii::t('wavecms/user', 'This role is already assigned'));
            return true;
        }
        return false;

    }
}
