<?php

namespace app\widgets\HistoryList;

use app\models\exceptions\HistoryEventNotFoundException;
use app\models\History;
use app\models\search\HistorySearch;
use app\widgets\HistoryList\components\listItemView\ItemViewModelFactoryAwareTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Url;

class HistoryList extends Widget
{
    use ItemViewModelFactoryAwareTrait;

    /**
     * @return string
     */
    public function run(): string
    {
        $model = new HistorySearch();

        return $this->render('main', [
            'linkExportArray' => $this->getLinkExport(false),
            'linkExportActiveRecord' => $this->getLinkExport(true),
            'dataProvider' => $model->search(Yii::$app->request->queryParams)
        ]);
    }

    /**
     * @param bool $useActiveRecord
     * @return string
     * @see CsvExportMySqlBase::$useActiveRecord
     */
    private function getLinkExport(bool $useActiveRecord): string
    {
        $params = Yii::$app->getRequest()->getQueryParams();
        $params[0] = $useActiveRecord ? 'site/export-active-record' : 'site/export-array';
        return Url::to($params);
    }

    /**
     * @param History $history
     * @return string
     * @throws InvalidConfigException
     * @throws HistoryEventNotFoundException
     * @see ListView::$itemView
     * @noinspection PhpUnused используется в представлении
     */
    public function renderItem(History $history): string
    {
        $listItemViewModel = $this->getItemViewModelFactory()->create($history);
        return $this->render(
            $listItemViewModel->getTemplateName(),
            $listItemViewModel->getTemplateParameters()
        );
    }
}
