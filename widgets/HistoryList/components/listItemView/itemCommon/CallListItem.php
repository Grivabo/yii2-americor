<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommon;

use app\models\Call;
use app\models\History;
use app\widgets\HistoryList\components\listItemView\itemCommon\base\ListItemCommonViewBaseModel;
use yii\helpers\Html;

/**
 * Класс для шаблона отображения событий звонков.
 * @see Call
 */
class CallListItem extends ListItemCommonViewBaseModel
{
    /**
     * @var Call|null
     */
    protected $call;

    /**
     * @inheritDoc
     */
    public function __construct(History $history)
    {
        parent::__construct($history);
        $this->call = $this->history->call;
    }

    /**
     * @inheritDoc
     */
    protected function getContent(): ?string
    {
        return Html::encode($this->call->comment ?? '');
    }

    /**
     * @inheritDoc
     */
    protected function getBody(): string
    {
        return $this->call ?
            $this->call->totalStatusText .
            (
            $this->call->getTotalDisposition(false) ?
                " <span class='text-grey'>" .
                Html::encode($this->call->getTotalDisposition(false)) .
                "</span>"
                :
                ""
            )
            :
            '<i>Deleted</i>';
    }

    /**
     * @inheritDoc
     */
    protected function getFooter(): ?string
    {
        return isset($this->call->applicant) ? "Called <span>" . Html::encode($this->call->applicant->name) . "</span>" : null;
    }

    /**
     * @inheritDoc
     */
    protected function getIconClass(): string
    {
        return $this->call && $this->call->isAnswered() ? 'md-phone bg-green' : 'md-phone-missed bg-red';
    }

}