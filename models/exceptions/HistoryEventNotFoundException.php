<?php
declare(strict_types=1);

namespace app\models\exceptions;

use app\models\enums\HistoryEventsEnum;
use Exception;

/**
 * В коде не определен тип события.
 * @see HistoryEventsEnum
 * @see HistoryEventTargetInterface::createEventHistory()
  */
class HistoryEventNotFoundException extends Exception
{

}