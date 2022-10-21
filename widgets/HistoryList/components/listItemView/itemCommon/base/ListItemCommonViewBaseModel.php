<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView\itemCommon\base;

use app\models\exceptions\HistoryEventNotFoundException;
use app\widgets\HistoryList\components\listItemView\ListItemViewModelInterface;
use app\widgets\HistoryList\components\listItemView\ViewModelTrait;

/**
 * @inheritDoc
 * Базовой класс для шаблона _item_common.
 */
class ListItemCommonViewBaseModel implements ListItemViewModelInterface
{
    use ViewModelTrait;

    /**
     * @inheritDoc
     * @throws HistoryEventNotFoundException
     */
    public function getTemplateParameters(): array
    {
        return [
            'user' => $this->getUser(),
            'body' => $this->getBody(),
            'content' => $this->getContent(),
            'footer' => $this->getFooter(),
            'footerDatetime' => $this->getFooterDatetime(),
            'iconClass' => $this->getIconClass(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTemplateName(): string
    {
        return '_item_common';
    }

    /**
     * @return string
     */
    protected function getFooterDatetime(): string
    {
        return $this->history->ins_ts;
    }

    /**
     * @return string|null
     * TODO Описать какие данные относится к контенту, а какие к боди.
     */
    protected function getContent(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    protected function getIconClass(): string
    {
        return 'fa-gear bg-purple-light';
    }

    /**
     * @return string|null
     * TODO Описать какие данные относится подвалу.
     */
    protected function getFooter(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     * @throws HistoryEventNotFoundException
     */
    public function getExportDataColumnValue()
    {
        return $this->getBody();
    }
}