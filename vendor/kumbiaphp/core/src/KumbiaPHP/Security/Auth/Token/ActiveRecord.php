<?php

namespace KumbiaPHP\Security\Auth\Token;

use KumbiaPHP\Security\Auth\User\UserInterface;
use KumbiaPHP\Security\Auth\Token\TokenInterface;
use KumbiaPHP\Security\Auth\Token\AbstractToken;

class ActiveRecord extends AbstractToken implements TokenInterface
{

    public function serialize()
    {
        $userClass = get_class($this->user);

        //nos quedamos solo con los indices que son parte de la metadata del modelo
        $data = array_intersect_key(get_object_vars($this->user), $this->user
                        ->metadata()->getAttributes());

        return serialize(array(new $userClass($data), $this->valid));
    }

}