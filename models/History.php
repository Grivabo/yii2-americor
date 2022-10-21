<?php

namespace app\models;

use app\components\historyEvents\ChangeAttributeValueEventHistory;
use app\components\historyEvents\EventHistoryBase;
use app\components\historyEvents\HistoryEventInterface;
use app\models\exceptions\HistoryEventNotFoundException;
use app\models\interfaces\HistoryEventTargetInterface;
use app\models\traits\ObjectNameTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%history}}".
 *
 * @property integer $id
 * @property string $ins_ts
 * @property integer $customer_id
 * @property string $event
 * @property string $object
 * @property integer $object_id
 * @property string $message
 * @property string $detail
 * @property integer $user_id
 *
 * @property Customer $customer
 * @property User $user
 *
 * @property Task|null $task
 * @property Sms|null $sms
 * @property Call|null $call
 * @property Fax|null $fax
 */
class History extends ActiveRecord
{
    use ObjectNameTrait;

    /**
     * @var HistoryEventInterface
     */
    private $historyEvent;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%history}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ins_ts'], 'safe'],
            [['customer_id', 'object_id', 'user_id'], 'integer'],
            [['event'], 'required'],
            [['message', 'detail'], 'string'],
            [['event', 'object'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ins_ts' => Yii::t('app', 'Ins Ts'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'event' => Yii::t('app', 'Event'),
            'object' => Yii::t('app', 'Object'),
            'object_id' => Yii::t('app', 'Object ID'),
            'message' => Yii::t('app', 'Message'),
            'detail' => Yii::t('app', 'Detail'),
            'user_id' => Yii::t('app', 'User ID'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQueryInterface|ActiveQuery|null
     */
    public function getHistoryEventTargetRelation()
    {
        $relation = $this->getRelation($this->object, false);

        // Для некоторых событий $this->object содержит не имя таблицы. Но по значению можно получить имя таблицы.
        if (!$relation) {
            $relationName = null;

            $map = [
                'customer' => Customer::getTypeTexts(),
                'call' => array_flip(['call_ytel']),
            ];

            $relationTableName = $this->object;
            foreach ($map as $new => $check) {
                if (array_key_exists($relationTableName, $check)) {
                    $relationName = $new;
                    break;
                }
            }
            $relation = $relationName ? $this->getRelation($relationName, false) : null;
        }
        return $relation;
    }

    /**
     * @return string|null
     */
    public function getHistoryEventTargetClass(): ?string
    {
        $relation = $this->getHistoryEventTargetRelation();
        return $relation->modelClass ?? null;
    }

    /**
     * @return HistoryEventTargetInterface|null
     */
    public function getHistoryEventTarget(): ?HistoryEventTargetInterface
    {
        $relation = $this->getHistoryEventTargetRelation();
        return $relation ? $relation->one() : null;
    }

    /**
     * Кэширует результат.
     * @return HistoryEventInterface
     * @throws HistoryEventNotFoundException
     */
    public function getHistoryEvent(): HistoryEventInterface
    {
        if ($this->historyEvent) {
            return $this->historyEvent;
        }

        /** @var HistoryEventTargetInterface $historyEventTargetClass */
        $historyEventTargetClass = $this->getHistoryEventTargetClass();
        return $this->historyEvent = $historyEventTargetClass::createEventHistory($this);
    }

    /**
     * @param $attribute
     * @return null
     */
    public function getDetailChangedAttribute($attribute)
    {
        $detail = json_decode($this->detail);
        return isset($detail->changedAttributes->{$attribute}) ? $detail->changedAttributes->{$attribute} : null;
    }

    /**
     * @param $attribute
     * @return null
     */
    public function getDetailOldValue($attribute)
    {
        $detail = $this->getDetailChangedAttribute($attribute);
        return isset($detail->old) ? $detail->old : null;
    }

    /**
     * @param $attribute
     * @return null
     */
    public function getDetailNewValue($attribute)
    {
        $detail = $this->getDetailChangedAttribute($attribute);
        return isset($detail->new) ? $detail->new : null;
    }

    /**
     * @param $attribute
     * @return null
     */
    public function getDetailData($attribute)
    {
        $detail = json_decode($this->detail);
        return isset($detail->data->{$attribute}) ? $detail->data->{$attribute} : null;
    }

    /**
     * Создающий метод.
     * @param string $eventText
     * @return EventHistoryBase
     */
    public function createEventHistoryBase(string $eventText): EventHistoryBase
    {
        return new EventHistoryBase(
            $this,
            $eventText
        );
    }

    /**
     * Создающий метод.
     * @param string $eventText
     * @param string $attribute
     * @return EventHistoryBase
     */
    public function createChangeAttributeValueEventHistory(
        string $eventText,
        string $attribute
    ): EventHistoryBase
    {
        return new ChangeAttributeValueEventHistory(
            $this,
            $eventText,
            $attribute
        );
    }
}
