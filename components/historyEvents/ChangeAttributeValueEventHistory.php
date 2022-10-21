<?php
declare(strict_types=1);

namespace app\components\historyEvents;

use app\models\History;

/**
 * Функционал получения старого и нового значения измененного поля целевого объекта.
 */
class ChangeAttributeValueEventHistory extends EventHistoryBase
{
    /**
     * @var string
     */
    protected $attribute;

    /**
     * @inheritDoc
     * @param string $attribute имя изменившегося аттрибута
     */
    public function __construct(
        History $history,
        string  $eventText,
        string  $attribute
    )
    {
        parent::__construct($history, $eventText);
        $this->attribute = $attribute;
    }

    /**
     * @return string|bool|int|float|null старое значение
     */
    public function getDetailOldValue()
    {
        return $this->history->getDetailOldValue($this->attribute);
    }

    /**
     * @return string|bool|int|float|null новое значение
     */
    public function getDetailNewValue()
    {
        return $this->history->getDetailNewValue($this->attribute);
    }
}