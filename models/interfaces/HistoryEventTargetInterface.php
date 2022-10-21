<?php
declare(strict_types=1);

namespace app\models\interfaces;

use app\components\historyEvents\HistoryEventInterface;
use app\models\exceptions\HistoryEventNotFoundException;
use app\models\History;

/**
 * Интерфейс классов к которым относятся события в журнале событий.
 */
interface HistoryEventTargetInterface
{
    /**
     * Простая фабрика.
     * Создает нужные объекты и задает свойства на основании данных в `$history`.
     * т.к. общее количество типов событий планируется 150+, то их описание сгруппировано по классам к которым
     * они относятся.
     * @param History $history
     * @return HistoryEventInterface
     * @throws HistoryEventNotFoundException
     */
    public static function createEventHistory(History $history): HistoryEventInterface;
}