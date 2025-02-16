<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Управление контентом';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Создать новый блок контента', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Страница</th>
                    <th>Идентификатор блока</th>
                    <th>Содержимое</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($content as $item): ?>
                <tr>
                    <td><?= Html::encode($item->page_url) ?></td>
                    <td><?= Html::encode($item->block_identifier) ?></td>
                    <td><?= Html::encode(substr($item->content, 0, 100)) . (strlen($item->content) > 100 ? '...' : '') ?></td>
                    <td>
                        <?= Html::a('Редактировать', ['update', 'id' => $item->id], ['class' => 'btn btn-primary btn-sm']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
