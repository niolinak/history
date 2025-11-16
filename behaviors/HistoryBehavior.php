<?php

namespace backend\modules\history\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use backend\modules\history\controllers\HistoryController;

class HistoryBehavior extends Behavior
{
    /**
     * @var array Атрибути, які ігноруються при збереженні історії
     */
    public $ignoreAttributes = [];

    /**
     * @var string Назва первинного ключа
     */
    public $primaryKey = 'id';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    /**
     * Отримати історію для моделі
     */
    public function getHistory()
    {
        if (Yii::$app instanceof \yii\console\Application) {
            return null;
        }
        $id = $this->owner->{$this->primaryKey} ?: Yii::$app->request->get($this->primaryKey);
        return HistoryController::List($this->owner->tableName(), $id);
    }


    /**
     * Обробник збереження моделі
     */
    public function afterSave($event)
    {
        $insert = $event->name === ActiveRecord::EVENT_AFTER_INSERT;

        if (!$insert && !empty($event->changedAttributes)) {
            // Фільтруємо ігноровані атрибути
            $changedAttributes = array_diff_key(
                $event->changedAttributes,
                array_flip($this->ignoreAttributes)
            );

            if (!empty($changedAttributes)) {
                HistoryController::Save(
                    $this->owner->tableName(),
                    $changedAttributes,
                    $this->owner->getAttributes(),
                    $this->primaryKey,
                    $this->ignoreAttributes
                );
            }
        }
    }

    /**
     * Знайти одну модель з підтримкою історії
     */
    public function findOne($condition)
    {
        if (!Yii::$app instanceof \yii\console\Application && $history = Yii::$app->request->get('history')) {
            $class = static::class;
            $model = new $class();
            $model->load(HistoryController::View($condition, $history), '');
            return $model;
        }
        return parent::findOne($condition);
    }

    /**
     * Обробник видалення моделі
     */
    public function beforeDelete($event)
    {
        HistoryController::Delete(
            $this->owner->tableName(),
            $this->owner->{$this->primaryKey}
        );
        return true;
    }

}
