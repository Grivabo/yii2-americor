<?php
declare(strict_types=1);

namespace app\models\traits;

use app\models\interfaces\TypeTextInterface;

/**
 * @mixin TypeTextInterface
 * @see TypeTextInterface
 */
trait TypeTextTrait
{
    /**
     * @return string
     */
    public function getTypeText(): string
    {
        return self::getTypeTexts()[$this->type] ?? $this->type ?? '';
    }
}