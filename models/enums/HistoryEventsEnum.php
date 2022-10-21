<?php
declare(strict_types=1);

namespace app\models\enums;

/**
 * Перечень всех доступных событий.
 * Создан для получения полного списка событий вне группировок по целевым объектам событий.
 */
abstract class HistoryEventsEnum
{
    public const EVENT_CREATED_TASK = 'created_task';
    public const EVENT_UPDATED_TASK = 'updated_task';
    public const EVENT_COMPLETED_TASK = 'completed_task';

    public const EVENT_INCOMING_SMS = 'incoming_sms';
    public const EVENT_OUTGOING_SMS = 'outgoing_sms';

    public const EVENT_INCOMING_CALL = 'incoming_call';
    public const EVENT_OUTGOING_CALL = 'outgoing_call';

    public const EVENT_INCOMING_FAX = 'incoming_fax';
    public const EVENT_OUTGOING_FAX = 'outgoing_fax';

    public const EVENT_CUSTOMER_CHANGE_TYPE = 'customer_change_type';
    public const EVENT_CUSTOMER_CHANGE_QUALITY = 'customer_change_quality';
}