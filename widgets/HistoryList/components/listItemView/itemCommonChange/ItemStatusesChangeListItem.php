<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommonChange;

use app\components\historyEvents\ChangeAttributeValueEventHistory;
use app\models\exceptions\HistoryEventNotFoundException;
use app\models\History;
use app\widgets\HistoryList\components\listItemView\itemCommonChange\base\ListItemStatusesChangeViewBaseModel;
use Yii;
use yii\helpers\Html;

/**
 * Реализация абстрактного класса.
 * @see ListItemStatusesChangeViewBaseModel
 */
class ItemStatusesChangeListItem extends ListItemStatusesChangeViewBaseModel
{
    /**
     * @var ChangeAttributeValueEventHistory
     */
    protected $attributeValueEvent;

    /**
     * @var callable|null функция форматирующая или иным способом преобразовывающая измененное значение для
     * отображения.
     */
    protected $valueMapper;

    /**
     * @inheritDoc
     * @param callable|null $valueMapper функция форматирующая или иным способом преобразовывающая измененное значение
     * для отображения.
     * @throws HistoryEventNotFoundException
     */
    public function __construct(
        History  $history,
        callable $valueMapper = null
    )
    {
        parent::__construct($history);

        $this->attributeValueEvent = $history->getHistoryEvent();
        assert($this->attributeValueEvent instanceof ChangeAttributeValueEventHistory);

        $this->valueMapper = $valueMapper;
    }

    /**
     * @inheritDoc
     */
    public function getHistory(): History
    {
        return $this->attributeValueEvent->getHistory();
    }

    /**
     * @param $value
     * @return string
     */
    protected function formatValue($value): string
    {
        return Html::encode($this->valueMapper ? ($this->valueMapper)($value) : $value);
    }

    /**
     * @inheritDoc
     */
    public function getNewValue(): string
    {
        return $this->formatValue($this->attributeValueEvent->getDetailNewValue()) ?? self::getNotSetValueHtml();
    }

    /**
     * @inheritDoc
     */
    public function getOldValue(): string
    {
        return $this->formatValue($this->attributeValueEvent->getDetailOldValue()) ?? self::getNotSetValueHtml();
    }

    /**
     * @return string
     */
    public static function getNotSetValueHtml(): string
    {
        return '<i>' . Yii::t('app', 'not set') . '</i>';
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     * @throws HistoryEventNotFoundException
     */
    public function getExportDataColumnValue(): string
    {
        return $this->history->getHistoryEvent()->getEventText() . ' ' .
            ($this->formatValue($this->attributeValueEvent->getDetailOldValue()) ?? "not set") . ' to ' .
            ($this->formatValue($this->attributeValueEvent->getDetailNewValue()) ?? "not set");
    }
}