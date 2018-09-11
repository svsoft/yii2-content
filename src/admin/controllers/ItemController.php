<?php

namespace svsoft\yii\content\admin\controllers;

use svsoft\yii\content\forms\import\ItemImport;
use svsoft\yii\content\forms\import\ReaderJson;
use svsoft\yii\content\models\ItemObject;
use svsoft\yii\content\models\Type;
use Yii;
use svsoft\yii\content\models\ItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @param $type_id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($type_id)
    {
        $type = Type::findOne($type_id);
        if (!$type)
            throw new NotFoundHttpException('The requested page does not exist.');

        $searchModel = new ItemSearch(['type_id'=>$type_id]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->view->title = $type->label. '. Список';

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'type'=>$type
        ]);
    }

    /**
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    /**
     * @param $type_id
     * @param null $parent_item_id - Ид родительского элемента для которого, текущий элемент является значением
     * @param null $parent_property_id - Ид свойства родительского элемента, к оторому прекрепляется текущий эдемент
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionCreate($type_id, $parent_item_id = null, $parent_property_id = null)
    {
        $sessionVar = $this->id . '.' . $this->action->id;

        // Получаем родительский элемент
        $parentItemModel = null;
        if ($parent_item_id)
        {
            $parentModel = $this->findModel($parent_item_id);

            // Получаем свойство родительского элемент
            if (!$parentProperty = $parentModel->getItemProperty($parent_property_id))
                throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model = new ItemObject();
        $model->type_id = $type_id;
        $model->parentItemId = $parent_item_id;
        $model->parentPropertyId = $parent_property_id;

        if ($model->load(Yii::$app->request->post()))
        {
            foreach($model->getItemProperties() as $itemProperty)
            {
                $itemProperty->loadValues(UploadedFile::getInstances($itemProperty, 'value'));
            }

            if ($model->save())
            {
                return $this->redirect(Yii::$app->session->get($sessionVar));
            }
        }
        else
        {
            $backUrl = Yii::$app->request->referrer;
            if (!$backUrl)
                $backUrl = ['index','type_id'=>$type_id];

            Yii::$app->session->set($sessionVar, $backUrl);
        }

        $this->view->title = $model->type->label . '. Добавление';

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            foreach($model->getItemProperties() as $itemProperty)
            {
                $itemProperty->loadValues(UploadedFile::getInstances($itemProperty, 'value'));
            }

            if ($model->save())
            {
                return $this->refresh();
            }
        }

        $this->view->title = $model->type->label.'. Редактирование';

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$model->delete())
        {
            Yii::$app->session->setFlash('error', 'Ошибка удаления');
            return $this->redirect(['index', 'type_id'=>$model->type_id]);
        }

        Yii::$app->session->setFlash('success', 'Элемент удален');
        return $this->redirect(['index', 'type_id'=>$model->type_id]);
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ItemObject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemObject::findOne($id)) !== null) {
            return $model;
        } else {
            if ($model = ItemObject::findOne(['slug'=>$id]))
                return $model;

            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Импорт типов
     *
     * @return string
     */
    public function actionImport()
    {
        $reader = new ReaderJson();
        $model = new ItemImport($reader);

        if ($model->load(Yii::$app->request->post()))
        {
            // Загружаем фалайлы
            $model->files = UploadedFile::getInstances($model,'files');

            if ($model->read())
            {
                $model->scenario = ItemImport::SCENARIO_SAVE;
                if (Yii::$app->request->post('read'))
                {
                    if (!$model->validate())
                        Yii::$app->session->setFlash('error','Ошибки валидации данных!');
                }
                else
                {
                    if ($model->save())
                    {
                        Yii::$app->session->setFlash('success','Типы успешно добавлены!');
                        return $this->redirect('import');
                    }

                    Yii::$app->session->setFlash('error','Ошибка сохранения');
                }
            }
        }

        return $this->render('import',['model'=>$model]);
    }
}
