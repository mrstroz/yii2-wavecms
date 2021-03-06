<?php

namespace mrstroz\wavecms\controllers;

use mrstroz\wavecms\components\helpers\Flash;
use mrstroz\wavecms\components\web\Controller;
use Yii;
use yii\helpers\FileHelper;

class SettingsController extends Controller
{

    public function init()
    {
        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors(); // TODO: Change the autogenerated stub

        $behaviors['access']['rules'][] = [
            'allow' => true,
            'actions' => [
                'cache'
            ],
            'roles' => [
                '@'
            ],
        ];

        return $behaviors;
    }

    public function actionCache()
    {
        $this->view->params['h1'] = Yii::t('wavecms/main', 'Cache');
        $this->view->title = Yii::t('wavecms/main', 'Cache');

        if (Yii::$app->request->isPost) {
            Yii::$app->cache->flush();

            if (isset(Yii::$app->cacheFrontend)) {
                Yii::$app->cacheFrontend->flush();
            }

            if (is_dir('../assets')) {
                $frontendAsset = scandir('../assets', 0);
                if ($frontendAsset) {
                    foreach ($frontendAsset as $folder) {
                        if ($folder !== '.' && $folder !== '..' && ( (in_array(strlen($folder), [6, 7, 8])) || in_array($folder,['css-compress','js-compress'])) ){
                            FileHelper::removeDirectory('../assets/' . $folder);
                    }
                    }
                }
            }

            if (is_dir('assets')) {
                $backendAsset = scandir('assets', 0);
                if ($backendAsset) {
                    foreach ($backendAsset as $folder) {
                        if ($folder !== '.' && $folder !== '..' && (strlen($folder) === 7 || strlen($folder) === 8)) {
                            FileHelper::removeDirectory('assets/' . $folder);
                        }
                    }
                }
            }

            if (@file_exists('../minify/')) {
                $minifyAsset = FileHelper::findFiles('../minify/');
                if ($minifyAsset) {
                    foreach ($minifyAsset as $file) {
                        unlink('../minify/' . $file);
                    }
                }
            }

            Flash::message(
                'after_clear_cache',
                'success',
                ['message' => Yii::t('wavecms/main', 'Assets folders and cache has been cleared')]
            );

            return $this->refresh();

        }

        return $this->render('cache');
    }
}