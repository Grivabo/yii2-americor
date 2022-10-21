<?php
declare(strict_types=1);

use app\models\User;
use yii\helpers\Html;

/* @var $user User|null */
/* @var $body string */
/* @var $content string|null */
/* @var $footer string|null */
/* @var $footerDatetime string */
/* @var $iconClass string */
?>
<?php echo Html::tag('i', '', ['class' => "icon icon-circle icon-main white $iconClass"]); ?>

    <div class="bg-success ">
        <?php echo $body ?>

        <?php if (isset($bodyDatetime)): ?>
            <span>
       <?= \app\widgets\DateTime\DateTime::widget(['dateTime' => $bodyDatetime]) ?>
    </span>
        <?php endif; ?>
    </div>

<?php if (isset($user)): ?>
    <div class="bg-info"><?= $user->username; ?></div>
<?php endif; ?>

<?php if (isset($content) && $content): ?>
    <div class="bg-info">
        <?php echo $content ?>
    </div>
<?php endif; ?>

<?php if (isset($footer) || isset($footerDatetime)): ?>
    <div class="bg-warning">
        <?php echo isset($footer) ? $footer : '' ?>
        <?php if (isset($footerDatetime)): ?>
            <span><?= \app\widgets\DateTime\DateTime::widget(['dateTime' => $footerDatetime]) ?></span>
        <?php endif; ?>
    </div>
<?php endif; ?>