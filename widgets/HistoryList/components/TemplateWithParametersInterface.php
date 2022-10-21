<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components;

/**
 * Данные определяющие шаблон и его параметры.
 */
interface TemplateWithParametersInterface
{
    /**
     * @return string имя файла шаблона 
     */
    public function getTemplateName(): string;

    /**
     * @return array
     * - ключи: имена параметров шаблона
     * - значения: значения параметров шаблона
     *
     * Значения могут содержать Html и должны формироваться с использованием `Html::encode()`.
     */
    public function getTemplateParameters(): array;
}