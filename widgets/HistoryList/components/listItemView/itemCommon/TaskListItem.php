<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommon;

use app\models\History;
use app\models\Task;
use app\widgets\HistoryList\components\listItemView\itemCommon\base\ListItemCommonViewBaseModel;
use yii\helpers\Html;

/**
 * Класс для шаблона отображения событий задач.
 * @see Task
 */
class TaskListItem extends ListItemCommonViewBaseModel
{
    /**
     * @var Task|null
     */
    private $task;

    /**
     * @inheritDoc
     */
    public function __construct(History $history)
    {
        parent::__construct($history);
        $this->task = $this->history->task;
    }

    /**
     * @inheritDoc
     */
    protected function getFooter(): string
    {
        return isset($this->task->customerCreditor->name) ? "Creditor: " . Html::encode($this->task->customerCreditor->name) : '';
    }

    /**
     * @inheritDoc
     */
    protected function getBody(): string
    {
        return Html::encode($this->history->getHistoryEvent()->getEventText()) .
            ':' .
            (($this->task->title ?? '') ? ' ' . $this->task->title : '');
    }

    /**
     * @inheritDoc
     */
    protected function getIconClass(): string
    {
        return 'fa-check-square bg-yellow';
    }

}