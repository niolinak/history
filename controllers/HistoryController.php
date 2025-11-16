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
    public static function Save(string $name,array $old, array $new,string $primaryKey, array $exclude=[]){
        $old_data = $new;
        foreach ($old as $k => $v) {
            $old_data[$k] = $v;
        }

        $changed = [];

        foreach ($new as $k => $v) {
            if (in_array($k, $exclude, true)) {
                continue;
            }

            $oldValue = $old_data[$k] ?? null;

            if ($oldValue != $v) {
                $changed[] = $k;
            }
        }

        if (empty($changed)) {
            return true;
        }
        
        
        $model = new History();
        $model->table_name = $name;
        $model->record_id = $old_data[$primaryKey];
        $model->change_date = date('Y-m-d H:i:s');
        $model->user_id = (Yii::$app instanceof \yii\console\Application) ? 0 : Yii::$app->user->id;
        $model->changes = Json::encode($old_data);
        $model->changed_attributes = Json::encode($changed);
        return $model->save();
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
    public static function View(int $id){
        $record=History::findOne($id);
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

    public static function historyFind($model,$history,$id_attr){
        $model_data=HistoryController::View($history);
        $model->load($model_data,'');
        $model->$id_attr=$model_data[$id_attr];
        return $model;
    }

    public static function searchByChanged($model, $filters = [], $atts = [], $idAttr = 'id')
    {
        $query = History::find()->andFilterWhere($filters);
        foreach ($atts as $attr) {
            $query->andFilterWhere(['like', 'changed_attributes', '"'.$attr.'"']);
        }
        $histories = $query->all();
        $models = [];

        foreach ($histories as $history) {
            $m = new $model();
            $data = Json::decode($history->changes, true);
            $m->load($data, '');
            $m->$idAttr = $data[$idAttr] ?? null;
            $models[] = $m;
        }

        return $models;
    }

}
