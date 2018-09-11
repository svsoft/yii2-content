<?php

namespace svsoft\yii\content\admin\controllers;

use svsoft\yii\content\forms\import\TypeImportJson;
use svsoft\yii\content\forms\TypeExportJson;
use Yii;
use svsoft\yii\content\models\Type;
use svsoft\yii\content\models\TypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TypeController implements the CRUD actions for Type model.
 */
class TypeController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Type models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TypeSearch();
        $searchModel->simple = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Type model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Type model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Type();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->type_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Type model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->type_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Type model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Type::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionExportJson($id)
    {
        $model = $this->findModel($id);
        $export = new TypeExportJson($model);

        $filename = 'type_' . $model->name . '.json';

        return Yii::$app->response->sendContentAsFile($export->toJson(), $filename);
    }

    /**
     * Импорт типов
     *
     * @return string
     */
    public function actionImport()
    {
        $model = new TypeImportJson();

        if ($model->load(Yii::$app->request->post()))
        {
            $model->read();

            if (Yii::$app->request->post('read'))
                if (!$model->validate())
                    Yii::$app->session->setFlash('error','Ошибки валидации данных!');

            if (Yii::$app->request->post('save'))
            {
                if ($model->save())
                {
                    Yii::$app->session->setFlash('success','Типы успешно добавлены!');
                    return $this->redirect('import');
                }

                Yii::$app->session->setFlash('error','Ошибка сохранения');
            }
        }

        return $this->render('import',['model'=>$model]);
    }
}
