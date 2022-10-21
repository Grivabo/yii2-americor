<?php
declare(strict_types=1);

namespace app\widgets\HistoryList\components\listItemView;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Чтобы не заполнять сервис локатор или контейнер приложения объектами из представлений создание `ItemViewModelFactory`
 * выполнено трэйтом.
 * После переноса функционала в модуль от этого трэйта можно избавиться.
 */
trait ItemViewModelFactoryAwareTrait
{
    /**
     * @var ItemViewModelFactory
     */
    protected static $itemViewFactory;

    /**
     * @throws InvalidConfigException
     */
    protected function getItemViewModelFactory(): ItemViewModelFactory
    {
        return self::$itemViewFactory = self::$itemViewFactory ?: Yii::createObject(ItemViewModelFactory::class);
    }
}