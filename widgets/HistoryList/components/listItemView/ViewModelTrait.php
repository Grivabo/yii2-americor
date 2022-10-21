<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView;

use app\models\exceptions\HistoryEventNotFoundException;
use app\models\History;
use app\models\User;
use yii\helpers\Html;

/**
 * Реализация ListItemViewModelInterface с логикой по умолчанию.
 * @see ListItemViewModelInterface
 */
trait ViewModelTrait
{
    /**
     * @var History
     */
    protected $history;

    /**
     * @param History $history
     */
    public function __construct(History $history)
    {
        $this->history = $history;
    }

    /**
     * @return User|null
     */
    protected function getUser(): ?User
    {
        return $this->history->user ?? null;
    }

    /**
     * @return string
     * @throws HistoryEventNotFoundException
     */
    protected function getBody(): string
    {
        $historyEvent = $this->history->getHistoryEvent();
        return Html::encode($historyEvent->getEventText());
    }

    /**
     * @return History
     */
    public function getHistory(): History
    {
        return $this->history;
    }
}