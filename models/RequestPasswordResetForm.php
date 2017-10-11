<?php

namespace mrstroz\wavecms\models;

use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class RequestPasswordResetForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\mrstroz\wavecms\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => Yii::t('wavecms/user/login', 'There is no user with this email address.')
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
            'is_admin' => 1
        ]);

        if (!$user) {
            return false;
        }

        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        Yii::$app->mailer->viewPath = '@wavecms/mail/';

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => 'Wavecms'])
            ->setTo($this->email)
            ->setSubject('Password reset for Wavecms')
            ->send();
    }
}
