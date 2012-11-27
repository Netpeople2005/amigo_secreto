<?php

namespace Index\Model;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\Security\Auth\User\UserInterface;

class Usuarios extends ActiveRecord implements UserInterface
{

    public static function aleatorio()
    {

        self::createQuery()
                ->where('en_uso = 0');

        $numDisponibles = self::count();

        self::createQuery()
                ->where('en_uso = 0')
                ->limit(1)
                ->offset(rand(0, $numDisponibles));

        return self::find();
    }

    public static function crearRegistro(Usuarios $usuario, Request $request)
    {
        $usuario->begin();

        $usuario->en_uso = 1;

        if (!$usuario->save()) {
            $usuario->rollback();
            return false;
        }

        $equipo = new EquiposRegistrados();

        if (!$equipo->guardar($request)) {
            $usuario->rollback();
            return false;
        }

        $usuario->commit();

        return true;
    }

    public function auth(UserInterface $user)
    {
        var_dump($user);
        return true;
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
        return $this->personaje;
    }

    protected function beforeSave()
    {
        //si existe el campo clave actual, es porque estamos actualizando una clave previamente creada
        if (isset($this->clave_actual) &&
                $this->clave !== md5($this->clave_actual)) {
            $this->addError('clave', "La clave Actual es Incorrecta...!!!");
            return false;
        }
        //cuando exista clave2 es porque se está actualizando la contraseña
        if (isset($this->nueva_clave)) {
            $this->clave = md5($this->nueva_clave);
        }
    }

}
