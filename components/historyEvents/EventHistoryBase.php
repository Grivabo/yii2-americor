<?php
declare(strict_types=1);

namespace app\components\historyEvents;

use app\models\History;
use app\models\interfaces\HistoryEventTargetInterface;
use yii\base\Model;

/**
 * Базовая реализация HistoryEventInterface
 */
class EventHistoryBase implements HistoryEventInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var History
     */
    protected $history;

    /**
     * @var string
     */
    protected $eventText;

    /**
     * @param History $history
     * @param string $eventText
     */
    public function __construct(
        History $history,
        string  $eventText
    )
    {
        $this->history = $history;
        $this->eventText = $eventText;
    }

    /**
     * @inheritDoc
     */
    public function getHistoryEventTarget(): ?HistoryEventTargetInterface
    {
        return $this->history->getHistoryEventTarget();
    }

    /**
     * @inheritDoc
     */
    public function getHistory(): History
    {
        return $this->history;
    }

    /**
     * @inheritDoc
     */
    public function getEventText(): string
    {
        return $this->eventText;
    }
}