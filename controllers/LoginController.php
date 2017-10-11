<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\helpers\NavHelper;
use mrstroz\wavecms\components\web\Controller;
use mrstroz\wavecms\models\LoginForm;
use mrstroz\wavecms\models\RequestPasswordResetForm;
use mrstroz\wavecms\models\ResetPasswordForm;
use mrstroz\wavecms\models\User;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

class LoginController extends Controller
{


    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'logout',
                'my-account'
            ],
            'roles' => [
                '@'
            ],
        ];

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'login',
                'request-password-reset',
                'reset-password'
            ],
            'roles' => [
                '?'
            ],
        ];

        return $behaviors;
    }

    public function init()
    {
        $this->actionsDisabledFromAccessControl = [
            'login',
            'my-account',
            'request-password-reset',
            'reset-password',
            'logout'
        ];

        parent::init();
    }

    public function actionLogin()
    {

        $this->layout = '@wavecms/views/layouts/login.php';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();
        } else {

            return $this->render('login', [
                'model' => $model,
            ]);
        }

    }

    public function actionMyAccount()
    {

        $this->view->params['h1'] = Yii::t('wavecms/user/login', 'My account');
        NavHelper::$active[] = 'my-account';

        $model = User::find()->where(['id' => Yii::$app->user->id])->one();
        $model->scenario = User::SCENARIO_MY_ACCOUNT;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->password) {
                $model->setPassword($model->password);
                $model->generateAuthKey();
                Flash::message('password_changed', 'success', ['message' => Yii::t('wavecms/user/login', 'Password has been changed')]);
            }

            $model->save();
            Flash::message('after_update', 'success', ['message' => Yii::t('wavecms/user/login', 'Data has been changed')]);
            return $this->refresh();
        }

        return $this->render('my-account', [
            'model' => $model
        ]);

    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        Flash::message('logout', 'success', ['message' => Yii::t('wavecms/user/login', 'Correctly logged out')]);


        return $this->redirect(['/login']);
    }

    public function actionRequestPasswordReset()
    {

        $this->layout = '@wavecms/views/layouts/login.php';

        $model = new RequestPasswordResetForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Flash::message('request_password_reset', 'success', ['message' => Yii::t('wavecms/user/login', 'Please check the email you provided for further instructions.')]);

                return $this->redirect(['login']);
            }

            Flash::message('request_password_reset', 'danger', ['message' => Yii::t('wavecms/user/login', 'Sorry, we are unable to reset password for the provided email address.')]);
        }

        return $this->render('request-password-reset', [
            'model' => $model,
        ]);


    }

    public function actionResetPassword($token)
    {
        $this->layout = '@wavecms/views/layouts/login.php';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Flash::message('request_password_reset', 'success', ['message' => Yii::t('wavecms/user/login', 'New password saved.')]);

            return $this->redirect(['login']);
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }
}