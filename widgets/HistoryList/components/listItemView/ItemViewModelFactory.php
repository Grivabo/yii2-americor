<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView;

use app\components\historyEvents\ChangeAttributeValueEventHistory;
use app\models\Call;
use app\models\Customer;
use app\models\enums\HistoryEventsEnum;
use app\models\exceptions\HistoryEventNotFoundException;
use app\models\Fax;
use app\models\History;
use app\models\Sms;
use app\models\Task;
use app\widgets\HistoryList\components\listItemView\itemCommon\base\ListItemCommonViewBaseModel;
use app\widgets\HistoryList\components\listItemView\itemCommon\CallListItem;
use app\widgets\HistoryList\components\listItemView\itemCommon\FaxListItem;
use app\widgets\HistoryList\components\listItemView\itemCommon\SmsListItem;
use app\widgets\HistoryList\components\listItemView\itemCommon\TaskListItem;
use app\widgets\HistoryList\components\listItemView\itemCommonChange\ItemStatusesChangeListItem;
use Yii;
use yii\base\InvalidConfigException;

/**
 * @see ItemViewModelFactory::create()
 */
class ItemViewModelFactory
{
    /**
     * Простая фабрика.
     * Создание объектов определяющих шаблон и его параметры на основании данных в параметре `$history`.
     *
     * @param History $history
     * @return ListItemViewModelInterface
     * @throws InvalidConfigException
     * @throws HistoryEventNotFoundException
     */
    public function create(History $history): ListItemViewModelInterface
    {
        $historyEvent = $history->getHistoryEvent();

        //---------- События изменения значения параметра ---------------------
        if ($historyEvent instanceof ChangeAttributeValueEventHistory) {
            /** @see  ItemStatusesChangeListItem::$valueMapper */
            $valueMapper = [
                HistoryEventsEnum::EVENT_CUSTOMER_CHANGE_TYPE =>
                    [Customer::class, 'getTypeTextByType'],
                HistoryEventsEnum::EVENT_CUSTOMER_CHANGE_QUALITY =>
                    [Customer::class, 'getQualityTextByQuality']
            ][$historyEvent->getHistory()->event] ?? null;

            return new ItemStatusesChangeListItem(
                $history,
                $valueMapper
            );
        }

        //---------- События специфичные для определенных классов -------------
        $historyEventTargetClass = $history->getHistoryEventTargetClass();
        $classParams = [
            Task::class => TaskListItem::class,
            Sms::class => SmsListItem::class,
            Fax::class => FaxListItem::class,
            Call::class => CallListItem::class,
        ][$historyEventTargetClass] ?? ListItemCommonViewBaseModel::class;

        $classParams = is_array($classParams) ? $classParams : ['class' => $classParams];
        $classParams['__construct()']['history'] = $history;
        /** @var ListItemViewModelInterface $result */
        $result = Yii::createObject($classParams);
        return $result;
    }
}