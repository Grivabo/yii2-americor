<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommonChange\base;

use app\widgets\HistoryList\components\listItemView\ListItemViewModelInterface;
use app\widgets\HistoryList\components\listItemView\ViewModelTrait;

/**
 * Данные для шаблона отображающего событие связанное с изменением значения поля целевого объекта.
 */
abstract class ListItemStatusesChangeViewBaseModel implements ListItemViewModelInterface
{
    use ViewModelTrait;

    /**
     * @return string
     */
    abstract public function getOldValue(): string;

    /**
     * @return string
     */
    abstract public function getNewValue(): string;

    /**
     * @return string
     * TODO Описать что должно относиться к контенту
     */
    abstract public function getContent(): string;

    /**
     * @inheritDoc
     */
    public function getTemplateName(): string
    {
        return '_item_statuses_change';
    }

    /**
     * @inheritDoc
     */
    public function getTemplateParameters(): array
    {
        return [
            'model' => $this->getHistory(),
            'oldValue' => $this->getOldValue(),
            'newValue' => $this->getNewValue(),
            'content' => $this->getContent(),
        ];
    }
}