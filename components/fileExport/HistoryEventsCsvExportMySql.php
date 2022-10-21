<?php
declare(strict_types=1);

namespace app\components\fileExport;

use app\components\fileExport\base\CsvExportMySqlBase;
use app\models\exceptions\HistoryEventNotFoundException;
use app\models\History;
use app\widgets\HistoryList\components\listItemView\ItemViewModelFactoryAwareTrait;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Экспорт истории событий в csv.
 * Для демонстрации идеи реализованы оба способа с массивами и ActiveRecord. На практике достаточно одного.
 */
class HistoryEventsCsvExportMySql extends CsvExportMySqlBase
{
    use ItemViewModelFactoryAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function getHeaders(): array
    {
        return [
            'ins_ts' => Yii::t('app', 'Date'),
            'user' => Yii::t('app', 'User'),
            'type' => Yii::t('app', 'Type'),
            'event' => Yii::t('app', 'Event'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    /**
     * {@inheritdoc}
     * Данная реализация с использованием массивов переведена для демонстрации
     * идеи и результат отличается от реализации на ActiveRecord.
     *
     * При лимите памяти 128MB возможно выгружать несколько миллионов записей.
     *
     * @throws HistoryEventNotFoundException
     */
    protected function prepareArrayData(array $itemsBatch): array
    {
        $history = new History();
        return array_map(
            static function (array $item) use ($history) {
                // Переиспользование единственного объекта. В случае, если объект все же нужен.
                $history->setAttributes($item);
                return [
                    'ins_ts' => Yii::$app->formatter->format($item['ins_ts'], 'datetime'),
                    'user' => isset($item['user']) ? $item['user']['username'] : Yii::t('app', 'System'),
                    'type' => $item['object'],
                    'event' => $history->getHistoryEvent()->getEventText(),
                    'message' => Yii::t('app', 'Message example'),  // FIXME
                ];
            },
            $itemsBatch
        );
    }

    /**
     * {@inheritdoc}
     *
     * При лимите памяти 128MB возможно выгружать несколько сотен тысяч записей.
     *
     * @throws InvalidConfigException
     * @throws HistoryEventNotFoundException
     */
    protected function prepareActiveRecordData(array $itemsBatch): array
    {
        return array_map(
            function (History $item) {
                return [
                    'ins_ts' => Yii::$app->formatter->format($item->ins_ts, 'datetime'),
                    'user' => isset($item->user) ? $item->user->username : Yii::t('app', 'System'),
                    'type' => $item->object,
                    'event' => $item->getHistoryEvent()->getEventText(),
                    'message' => $this->getItemViewModelFactory()->create($item)->getExportDataColumnValue(),
                ];
            },
            $itemsBatch
        );
    }
}