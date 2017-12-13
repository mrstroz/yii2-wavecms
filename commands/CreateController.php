<?php

namespace mrstroz\wavecms\commands;

use mrstroz\wavecms\controllers\RoleController;
use mrstroz\wavecms\models\AuthItem;
use mrstroz\wavecms\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class CreateController extends Controller
{

    public function actionIndex($email, $password = null)
    {
        $model = new User();
        $model->email = $email;
        if ($password) {
            $model->setPassword($password);
        } else {
            $model->setPassword(Yii::$app->security->generateRandomString());
        }
        $model->generateAuthKey();
        $model->is_admin = 1;


        if ($model->save()) {

            $auth = Yii::$app->authManager;
            $role = $auth->getRole(AuthItem::SUPER_ADMIN);
            if (!$role) {
                $role = $auth->createRole(AuthItem::SUPER_ADMIN);
                $auth->add($role);
            }
            $auth->assign($role, $model->id);

            $this->stdout(Yii::t('wavecms/user', 'User has been created') . "!\n", Console::FG_GREEN);
        } else {
            $this->stdout(Yii::t('wavecms/user', 'Please fix following errors:') . "\n", Console::FG_RED);
            foreach ($model->errors as $errors) {
                foreach ($errors as $error) {
                    $this->stdout(' - ' . $error . "\n", Console::FG_RED);
                }
            }
        }
    }

    public function actionSetSuperAdmin($email)
    {

        $model = User::findOne(['email' => $email]);

        if (!$model)
            return $this->stdout(Yii::t('wavecms/user', 'User not found') . "\n", Console::FG_RED);

        $auth = Yii::$app->authManager;
        $role = $auth->getRole(AuthItem::SUPER_ADMIN);
        if (!$role) {
            $role = $auth->createRole(AuthItem::SUPER_ADMIN);
            $auth->add($role);
        }


        if (!$auth->getAssignment(AuthItem::SUPER_ADMIN, $model->id)) {
            $auth->assign($role, $model->id);
        }

        $model->is_admin = 1;
        if ($model->save()) {

        } else {
            $this->stdout(Yii::t('wavecms/user', 'Please fix following errors:') . "\n", Console::FG_RED);
            foreach ($model->errors as $errors) {
                foreach ($errors as $error) {
                    $this->stdout(' - ' . $error . "\n", Console::FG_RED);
                }
            }
        }


        return $this->stdout(Yii::t('wavecms/user', 'User assigned to "Super admin" role') . "\n", Console::FG_GREEN);
    }
}