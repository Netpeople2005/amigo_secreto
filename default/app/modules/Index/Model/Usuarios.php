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

}
