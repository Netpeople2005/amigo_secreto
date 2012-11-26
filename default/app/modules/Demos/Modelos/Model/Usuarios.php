<?php

namespace Demos\Modelos\Model;

use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\ActiveRecord\Validation\ValidationBuilder;

/**
 * Description of Usuarios
 *
 * @author maguirre
 */
class Usuarios extends ActiveRecord implements \KumbiaPHP\Security\Auth\User\UserInterface
{

    protected function validations(ValidationBuilder $builder)
    {
        $builder->notNull('login', array(
            'message' => "Escribe tu login por favor :-)"
        ));
        return $builder;
    }

    public function auth(\KumbiaPHP\Security\Auth\User\UserInterface $user)
    {
        return TRUE;// crypt($user->getPassword()) === $this->getPassword();
    }

    public function getPassword()
    {
        return $this->clave;
    }

    public function getRoles()
    {
        
    }

    public function getUsername()
    {
        return $this->login;
    }

}