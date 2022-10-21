<?php
declare(strict_types=1);

namespace app\components\historyEvents;

use app\models\History;
use app\models\interfaces\HistoryEventTargetInterface;

/**
 * Интерфейс описывающий комбинацию записи о событии, объекте к которому событие относится и типа события.
 * Наследники содержат специфичную для комбинации работу с данными и свойства.
 */
interface HistoryEventInterface
{
    /**
     * Объект к которому относится событие. Null т.к. запись в базе о целевого объекта может быть удалена.
     * @return HistoryEventTargetInterface|null
     */
    public function getHistoryEventTarget(): ?HistoryEventTargetInterface;

    /**
     * @return History запись о событии.
     */
    public function getHistory(): History;

    /**
     * @return string описание типа события.
     */
    public function getEventText(): string;
}