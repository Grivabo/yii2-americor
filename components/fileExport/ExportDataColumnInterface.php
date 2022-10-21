<?php
declare(strict_types=1);

namespace app\components\fileExport;

/**
 * Данные для экспорта.
 */
interface ExportDataColumnInterface
{
    /**
     * @return string|float|integer
     */
    public function getExportDataColumnValue();
}