<?php

namespace app\controllers;

use app\components\fileExport\HistoryEventsCsvExportMySql;
use app\models\search\HistorySearch;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Экспорт истории событий в csv без создания объектов ActiveRecord.
     * @return void
     */
    public function actionExportArray(): void
    {
        $this->createHistoryEventsCsvExport(false)->send(20000);
    }

    /**
     * Экспорт истории событий в csv c созданием объектов ActiveRecord.
     * @return void
     */
    public function actionExportActiveRecord(): void
    {
        $this->createHistoryEventsCsvExport(true)->send(2000);
    }

    /**
     * Создание HistoryEventsCsvExport и обработка ошибок параметров запроса.
     * @param bool $useActiveRecord
     * @return HistoryEventsCsvExportMySql
     */
    protected function createHistoryEventsCsvExport(bool $useActiveRecord): HistoryEventsCsvExportMySql
    {
        $historySearch = new HistorySearch();
        $activeDataProvider = $historySearch->search(Yii::$app->request->queryParams);
        if ($historySearch->hasErrors()) {
            throw new InvalidArgumentException('Ошибка параметров фильтра.');
        }
        return new HistoryEventsCsvExportMySql($activeDataProvider, $useActiveRecord);
    }
}
