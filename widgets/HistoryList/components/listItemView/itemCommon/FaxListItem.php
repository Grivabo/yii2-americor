<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommon;

use app\models\exceptions\HistoryEventNotFoundException;
use app\models\Fax;
use app\models\History;
use app\widgets\HistoryList\components\listItemView\itemCommon\base\ListItemCommonViewBaseModel;
use Yii;
use yii\helpers\Html;

/**
 * Класс для шаблона отображения событий факсов.
 * @see Fax
 */
class FaxListItem extends ListItemCommonViewBaseModel
{
    /**
     * @var Fax
     */
    protected $fax;

    /**
     * @param History $history
     */
    public function __construct(History $history)
    {
        parent::__construct($history);
        $this->fax = $this->history->fax;
    }

    /**
     * @return string
     * @throws HistoryEventNotFoundException
     */
    protected function getBody(): string
    {
        return $this->history->getHistoryEvent()->getEventText() .
            ' - ' .
            (isset($this->fax->document) ? Html::a(
                Yii::t('app', 'view document'),
                $this->fax->document->getViewUrl(),
                [
                    'target' => '_blank',
                    'data-pjax' => 0
                ]
            ) : '');
    }

    /**
     * @inheritDoc
     */
    protected function getFooter(): string
    {
        return Yii::t('app', '{type} was sent to {group}', [
            'type' => $this->fax ? Html::encode($this->fax->getTypeText()) : 'Fax',
            'group' => isset($this->fax->creditorGroup) ? Html::a(Html::encode($this->fax->creditorGroup->name), ['creditors/groups'], ['data-pjax' => 0]) : ''
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getIconClass(): string
    {
        return 'fa-fax bg-green';
    }
}