<?php
declare(strict_types=1);

namespace app\components\fileExport\base;

use Generator;
use ReflectionClass;
use RuntimeException;
use Throwable;
use Yii;
use yii\base\Event;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Transaction;

/**
 * Экспорт данных в csv.
 *
 * Для использования необходимо наследовать данный класс, реализовать абстрактные методы и один или оба метода
 * `prepareActiveRecordData`, `prepareArrayData`.
 * @see static::prepareActiveRecordData()
 * @see static::$activeDataProvider()
 */
abstract class CsvExportMySqlBase
{
    /**
     * @var ActiveDataProvider
     */
    protected $activeDataProvider;

    /**
     * @var bool использовать для экспорта маппинг объектов `ActiveRecord` или массивы.
     * @see static::prepareActiveRecordData()
     * @see static::$activeDataProvider()
     */
    protected $useActiveRecord;

    /**
     * @param ActiveDataProvider $activeDataProvider
     * @param bool $useActiveRecord
     * @see ActiveDataProvider
     */
    public function __construct(ActiveDataProvider $activeDataProvider, bool $useActiveRecord = true)
    {
        $this->activeDataProvider = $activeDataProvider;
        $this->useActiveRecord = $useActiveRecord;
    }

    /**
     * Отправка результата средствами Yii2.
     * Используется в контроллере для отправки данных в браузер.
     *
     * @param int $batchSize размер обрабатываемых элементов за один раз. Выбирается исходя из размера связей и
     * потребления памяти. Обычно не более 65_000.
     * @return void
     */
    public function send(int $batchSize = 10000): void
    {
        Yii::$app->response->setDownloadHeaders($this->getFileName(), 'application/octet-stream');
        Yii::$app->response->stream =
            function () use ($batchSize) {
                return $this->yieldCsv($batchSize);
            };
        Yii::$app->response->send();
    }

    /**
     * @return string
     */
    protected function getFileName(): string
    {
        return (new ReflectionClass(static::class))->getShortName() .
            '__' .
            date('Y-m-d_H-i-s') .
            '.csv';
    }

    /**
     * Формирование csv в виде строки по частя.
     *
     * @param int $batchSize размер обрабатываемых элементов за один раз. Выбирается исходя из размера связей и
     * потребления памяти.
     * @return Generator
     * @throws Throwable
     */
    public function yieldCsv(int $batchSize = 10000): Generator
    {

        if ([] !== $headers = $this->getHeaders()) {
            yield static::arrayToCsv($headers);
        }

        /** @var Transaction $transaction */
        $transaction = Yii::$app->db->beginTransaction();

        try {

            if ($this->useActiveRecord) {
                Event::offAll();
                Yii::info(
                    'При использовании для экспорта ActiveRecord отключена обработка событий `Event::offAll()`. ' .
                    'Это снижает потребление оперативной памяти и возможные утечки при использовании логирующих и ' .
                    'прочих компонентов.'
                );
            }

            $activeDataProvider = $this->activeDataProvider;
            $query = $activeDataProvider->query;
            if (($sort = $activeDataProvider->getSort()) !== false) {
                $query->addOrderBy($sort->getOrders());
            }

            $page = 0;
            do {
                // TODO Для PostgreSQL вместо запросов с лимитами лучше использовать `batch`.
                /** @var ActiveRecord[]|array[] $items */
                /** @noinspection PhpPossiblePolymorphicInvocationInspection */
                $items = $query->offset($batchSize * $page)->limit($batchSize)->asArray(!$this->useActiveRecord)->all();

                $preparedDate = $this->useActiveRecord ?
                    $this->prepareActiveRecordData($items) :
                    $this->prepareArrayData($items);

                yield static::arrayToCsv($preparedDate);

                $page++;

            } while (count($items) > 0);

            $transaction->commit();

        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());

            /**
             * TODO Пересмотреть операцию экспорта с возможностью корректно отобразить ошибку экспорта.
             * Этот флэш пользователь увидит при открытии очередной страницы, а не сразу после ошибки.
             */
            Yii::$app->session->addFlash(
                'error',
                'При одной из последних выгрузок в csv произошла ошибка и данные могут быть не полные или содержать ошибки.'
            );
        }
    }

    /**
     * @return string[] Массив со значениями заголовков.
     */
    abstract protected function getHeaders(): array;

    /**
     * Можно отдавать стрим, но без расширения Yii2 Response этого корректно не сделать (но это не точно).
     * @param array $items
     * @param string $delimiter
     * @return string
     */
    public static function arrayToCsv(array $items, string $delimiter = ','): string
    {
        if ([] === $items) {
            return '';
        }

        $items = array_values($items);

        $pointer = fopen('php://temp', 'rb+');
        fwrite($pointer, "\xEF\xBB\xBF");
        if (is_array($items[0])) {
            foreach ($items as $arrayItem) {
                fputcsv($pointer, $arrayItem, $delimiter);
            }
        } else {
            fputcsv($pointer, $items, $delimiter);
        }
        rewind($pointer);

        $result = stream_get_contents($pointer);
        fclose($pointer);

        return $result;
    }

    /**
     * Преобразование данных из базы в данные для csv.
     * Данный метод используется при установки параметра `$this->useActiveRecord = true`
     * Преобразование происходит не по одному для организации более эффективного получения дополнительных данных
     * (например связей).
     *
     * Данный способ потребляет больше памяти, чаще вызывает утечки памяти и работает медленнее чем
     * `prepareArrayData`, но более простой в реализации и поддержке.
     * С учетом отключения событий при лимите памяти 128MB позволяет экспортировать несколько сотен тысяч элементов.
     *
     * @param ActiveRecord[] $itemsBatch Массив размером `batchSize` содержащий объект ActiveRecord.
     * @return array[] Массив размером `batchSize` содержащий элементы в виде массивов для формирования csv.
     */
    protected function prepareActiveRecordData(array $itemsBatch): array
    {
        throw new RuntimeException('При использовании ActiveRecord необходимо реализовать метод `prepareActiveRecordData`.');
    }

    /**
     * Преобразование данных из базы в данные для csv.
     * Данный метод используется при установки параметра `$this->useActiveRecord = false`
     * Преобразование происходит не по одному для организации более эффективного получения дополнительных данных
     * (например связей).
     *
     * Данный способ потребляет меньше памяти, реже вызывает утечки памяти и работает быстрее чем
     * `prepareActiveRecordData`, но более сложный в реализации и поддержке.
     * При лимите памяти 128MB позволяет экспортировать несколько миллионов элементов.
     *
     * @param array[] $itemsBatch Массив размером `batchSize` содержащий элементы в виде массивов с полями и значениями.
     * @return array[] Массив размером `batchSize` содержащий элементы в виде массивов для формирования csv.
     */
    protected function prepareArrayData(array $itemsBatch): array
    {
        throw new RuntimeException('При использовании массивов необходимо реализовать метод `prepareArrayData`.');
    }
}
