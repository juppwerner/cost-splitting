<?php 
// events.php file

use Da\User\Controller\AdminController;
use Da\User\Event\UserEvent;
use app\models\User;
use yii\base\Event;

// This will happen at the model's level
Event::on(User::class, UserEvent::EVENT_AFTER_CONFIRMATION, function (UserEvent $event) {
    // Assign author role to user
    $user = $event->getUser();
    $auth = Yii::$app->authManager;
    $author = $auth->getRole('author');
    $auth->assign($author, $user->id);
});