Model.php:
```php

    [[...,'history'], 'safe'],

    public function getHistory(){return HistoryController::List(self::tableName(),($this->id?:Yii::$app->request->get('id')));}

    public static function findOne($condition){
        if($history=Yii::$app->request->get('history')){
            $model = new self;
            $model->load(HistoryController::View($condition,$history),'');
            return $model;
        }
        return parent::findOne($condition);
    }

    public function afterSave( $insert, $changedAttributes ){
        parent::afterSave($insert, $changedAttributes );
        if(!$insert){
            HistoryController::Save(self::tableName(),$changedAttributes,$this->getAttributes(),[...skip...]);
        }
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            HistoryController::Delete(self::tableName(),$this->id);
            return parent::beforeDelete();
        } else {
            return false;
        }
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