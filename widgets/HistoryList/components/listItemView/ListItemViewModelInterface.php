<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView;

use app\components\fileExport\ExportDataColumnInterface;
use app\models\History;
use app\widgets\HistoryList\components\TemplateWithParametersInterface;

/**
 * Интерфейс для всех классов описывающих шаблон, и его параметры для отображения History.
 * @see History
 */
interface ListItemViewModelInterface extends TemplateWithParametersInterface, ExportDataColumnInterface
{
    /**
     * @return History
     */
    public function getHistory(): History;
}