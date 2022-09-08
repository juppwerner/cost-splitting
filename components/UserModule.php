<?php
namespace app\components;

use Da\User\Module as BaseModule;

class UserModule extends BaseModule
{
    public function getEnableGeneratingPassword()
    {
        return $this->generatePasswords;
    }
}
