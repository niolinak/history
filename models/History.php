<?php

namespace backend\modules\history\models;

use backend\modules\history\controllers\HistoryController;
use Yii;

/**
 * This is the model class for table "history".
 *
 * @property int $id
 * @property string $table_name
 * @property int $record_id
 * @property string $change_date
 * @property int|null $user_id
 * @property string $changes
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table_name', 'record_id', 'change_date', 'changes'], 'required'],
            [['table_name', 'changes'], 'string'],
            [['record_id', 'user_id'], 'integer'],
            [['change_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Назва таблиці',
            'record_id' => 'id Запису в таблиці',
            'change_date' => 'Дата зміни',
            'user_id' => 'Користувач що змінив',
            'changes' => 'Зміни',
        ];
    }
}
