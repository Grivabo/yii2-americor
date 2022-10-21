<?php
declare(strict_types=1);

namespace app\models\interfaces;

/**
 * Интерфейс для трейта классов имеющих текстовое представление типа образованного значением свойства `type`.
 */
interface TypeTextInterface
{
    /**
     * @return string|null
     */
    public function getTypeText(): ?string;

    /**
     * @return string[]
     */
    public static function getTypeTexts(): array;
}