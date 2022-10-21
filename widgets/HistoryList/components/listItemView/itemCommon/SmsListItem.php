<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommon;

use app\models\History;
use app\models\Sms;
use app\widgets\HistoryList\components\listItemView\itemCommon\base\ListItemCommonViewBaseModel;
use Yii;
use yii\helpers\Html;

/**
 * Класс для шаблона отображения событий смс.
 * @see Sms
 */
class SmsListItem extends ListItemCommonViewBaseModel
{
    /**
     * @var Sms
     */
    protected $sms;

    /**
     * @inheritDoc
     */
    public function __construct(History $history)
    {
        parent::__construct($history);
        $this->sms = $this->history->sms;
    }

    /**
     * @inheritDoc
     */
    protected function getBody(): string
    {
        return Html::encode($this->sms->message ?? '');
    }

    /**
     * @inheritDoc
     */
    protected function getFooter(): string
    {
        return $this->sms->direction === Sms::DIRECTION_INCOMING ?
            Yii::t('app', 'Incoming message from {number}', [
                'number' => Html::encode($this->sms->phone_from ?? '')
            ]) : Yii::t('app', 'Sent message to {number}', [
                'number' => Html::encode($this->sms->phone_to ?? '')
            ]);
    }

    /**
     * @inheritDoc
     */
    protected function getIconClass(): string
    {
        return 'icon-sms bg-dark-blue';
    }

}