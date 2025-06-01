<?php

namespace backend\modules\history\controllers;

use backend\modules\history\models\History;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\web\Controller;
use Yii;

/**
 * Default controller for the `history` module
 */
class HistoryController extends Controller
{
    public static function Save(string $name,array $old, array $new, array $exclude=[]){
        $old_data=$new;
        foreach ($old as $k => $v){
            $old_data[$k]=$v;
        }
        $identical=true;
        foreach ($new as $k => $v){
            if(!in_array($k, $exclude, false)){
                if($old_data[$k] != $v){
                    $identical=false;
                }
            }
        }
        if($identical){
            return true;
        }else{
            $model=new History();
            $model->table_name = $name;
            $model->record_id = $old_data['id'];
            $model->change_date = date('Y-m-d H:i:s');
            $model->user_id = Yii::$app->user->id;
            $model->changes = Json::encode($old_data);

            return $model->save();
        }
    }
    public static function List(string $name,int $record_id){
        $resp='<ul>';
        foreach (History::find()->where(['table_name'=>$name,'record_id'=>$record_id])->all() as $el){

            $resp.='<li>данні до: '.Html::a($el->change_date, ['view','id'=>$record_id,'history'=>$el->id],
                [
                    'data-pjax'=>0,
                    'role'=>'modal-remote',
                ]).'</li>';
        }
        $resp.='</ul>';
        if($resp==='<ul></ul>'){
            return '<span class="text-muted">Зміни відсутні</span>';
        }
        return $resp;
    }
    public static function View(int $record_id, int $id){
        $record=History::find()->where(['id'=>$id,'record_id'=>$record_id])->one();
        return Json::decode($record->changes,true);
    }

    public static function Delete(string $name,int $record_id){
        $ok=true;
        foreach (History::find()->where(['table_name'=>$name,'record_id'=>$record_id])->select(['id'])->all() as $el) {
            if(!History::find($el->id)->one()->delete()){
                $ok=false;
            }
        }
        return $ok;
    }

    public static function historyFind($model,$condition,$history,$id_attr){
        $model_data=HistoryController::View($condition,$history);
        $model->load($model_data,'');
        $model->$id_attr=$model_data[$id_attr];
        return $model;
    }

}
