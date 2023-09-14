<?php
/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Админка', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Аккаунты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="users-view">

    <h1><?= yii\helpers\Html::encode($this->title); ?></h1>

    <p>
        <?= yii\helpers\Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php
            if(\Yii::$app->user->can('userManage') && $model->id !== Yii::$app->user->identity->id){
                echo yii\helpers\Html::a(\Yii::$app->authManager->checkAccess($model->id, 'user') ? 'Заблокировать учетную запись' : 'Разблокировать учетную запись', ['block', 'id' => $model->id], [
                    'class' => 'btn btn-danger my-2 mx-1',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите заблокировать данную учетную запись?',
                        'method' => 'post'
                    ]
                ]);

                echo yii\helpers\Html::a('Сбросить пароль', ['reset', 'id' => $model->id], [
                    'class' => 'btn btn-outline-danger my-2 mx-1',
                    'data' => [
                        'confirm' => 'Вы уверены, что хотите сбросить пароль у данной учетной записи?',
                        'method' => 'post'
                    ]
                ]);
            }
        ?>
    </p>

    <?= yii\widgets\DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'tg_member_id',
                'label' => 'Telegram',
                'value' => function(app\models\Users $model){
                    $member = $model->getTgMember()->one();
                    if($member === null){
                        return null;
                    }
                    elseif(isset($member->tg_username)){
                        return yii\helpers\Html::a($member->tg_username, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $model->tg_member_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                    }
                    return yii\helpers\Html::a($member->tg_user_id, \yii\helpers\Url::to(['/admin/tg-member/view', 'id' => $model->tg_member_id]), ['class' => 'link-primary', 'title' => 'Перейти', 'target' => '_self']);
                },
                'format' => 'raw'
            ],
            'username',
            'email:email',
            'phone',
            [
                'attribute' => 'registration_date',
                'label' => 'Дата регистрации',
                'value' => function($model){
                    $dateTime = new DateTime($model->registration_date, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ],
            [
                'attribute' => 'last_activity',
                'label' => 'Дата последней активности',
                'value' => function($model){
                    $dateTime = new DateTime($model->last_activity, null);
                    return Yii::$app->formatter->asDatetime($dateTime, 'php:d.m.Y H:i:s');
                }
            ]
        ]
    ]);
    ?>

</div>