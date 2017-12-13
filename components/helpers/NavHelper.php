<?php

namespace mrstroz\wavecms\components\helpers;

use mrstroz\wavecms\models\AuthItem;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;

class NavHelper extends Component
{

    public static $active = [];


    /**
     * Sort navigation by position attribute
     * @param $Nav
     */
    public static function sortNav(&$Nav)
    {
        usort($Nav, array(self::class, 'sort'));
    }


    /**
     * Sort function user in sortNav
     * @param $a
     * @param $b
     * @return int
     */
    public static function sort($a, $b)
    {
        if (!isset($a['position'])) {
            return 0;
        }

        if (!isset($b['position'])) {
            return 0;
        }

        if ($a['position'] === $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? -1 : 1;
    }


    /**
     * Apply active attriute depends on route
     * @param $Nav
     */
    public static function applyActive(&$Nav)
    {
        if ($Nav) {
            foreach ($Nav as &$one) {
                if (isset($one['items'])) {
                    self::applyActive($one['items']);
                }

                if (!isset($one['active'])) {
                    if (is_array($one['url'])) {

                        $url = $one['url'][0];
                        $route = Yii::$app->requestedRoute;
                        $params = Yii::$app->request->getQueryParams();

                        if ($url === '/' && $route === Yii::$app->controller->module->id . '/dashboard/index') {
                            $one['active'] = true;
                        } else {
                            $url = $one['url'];
                            $urlExploded = explode('/', $url[0]);

                            $one['submenuTemplate'] = '';
                            foreach (self::$active as $activeAction) {
                                $urlExploded[count($urlExploded) - 1] = $activeAction;
                                $url[0] = implode('/', $urlExploded);

                                $one['items'][] =
                                    [
                                        'label' => $one['label'] . ' - ' . $activeAction,
                                        'url' => $url,
                                        'options' => [
                                            'class' => 'hidden'
                                        ]
                                    ];

                                if ('/' . Yii::$app->request->getQueryParam('parentRoute') === trim($url[0])) {
                                    $one['items'][] =
                                        [
                                            'label' => $one['label'] . ' - Sub List',
                                            'url' => array_merge(['/' . $route], $params),
                                            'options' => [
                                                'class' => 'hidden'
                                            ]
                                        ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Apply visible attribute depends on permission or url
     * @param $Nav
     */
    public static function applyVisible(&$Nav)
    {
        if ($Nav) {
            foreach ($Nav as &$one) {
                if (!isset($one['visible'])) {
                    if (isset($one['permission'])) {
                        $authItemModel = Yii::createObject(AuthItem::class);
                        $one['visible'] = Yii::$app->user->can($authItemModel::SUPER_ADMIN) || Yii::$app->user->can($one['permission']);
                    } else {
                        if (is_array($one['url'])) {
                            $url = explode('/', trim($one['url'][0], '/'));
                            if (isset($url['0']) && isset($url['1'])) {
                                $one['visible'] = Yii::$app->user->can('Super admin') || Yii::$app->user->can($url[0] . '/' . $url[1]);
                            }
                        }
                    }
                }

                if (isset($one['items'])) {
                    self::applyVisible($one['items']);
                }
            }
        }

    }

}