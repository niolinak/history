Model.php:

```php

    public function behaviors()
    {
        return [
            'history' => [
                'class' => \HistoryBehavior::class,
                'ignoreAttributes' => ['created_at', 'updated_at'],
                // 'primaryKey' => 'user_id', // якщо первинний ключ має іншу назву
            ],
        ];
    }
    
    // або
    public function behaviors()
    {
        return [
            'history' => \HistoryBehavior::class,
        ];
    }
    
    // +
    public static function findOne($condition)
    {
        if (!Yii::$app instanceof \yii\console\Application && $history = Yii::$app->request->get('history')) {
            $class = static::class;
            $model = new $class();
            $model->load(HistoryController::View($condition, $history), '');
            return $model;
        }
        return parent::findOne($condition);
    }


```

view.php:
```php
            [
                'attribute' => 'history',
                'format' => 'html',
                'label' => 'Історія змін'
            ]
```

config/main.php
```
'modules'=>[
    ...
        'history' => [
            'class' => 'backend\modules\history\Module',
        ],
    ...
]
```