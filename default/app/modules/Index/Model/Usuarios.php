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

    protected static function obtenerAQuienRegalar()
    {

        self::createQuery()
                ->where('amigo_asignado = 0');

        $numDisponibles = self::count();

        self::createQuery()
                ->where('amigo_asignado = 0')
                ->limit(1)
                ->offset(rand(0, $numDisponibles));

        return self::find();
    }

    /**
     * Crea el personaje en la bd y el asigna el personaje a quien le va a regalar
     * @param Usuarios $usuario el usuario que se acaba de registrar
     * @param Request $request petición actual
     * @return Usuarios|false el usuario a quien se le va a regalar 
     */
    public static function crearRegistro(Usuarios $usuario, Request $request)
    {
        $usuario->en_uso = 1;

        if (!$usuario->save()) {
            return false;
        }

        $equipo = new EquiposRegistrados();

        if (!$equipo->guardar($request)) {
            return false;
        }

        if (!(($usr2 = self::obtenerAQuienRegalar()) instanceof Usuarios)) {
            $usuario->addError(null, 'No quedan usuarios a quien regalarle');
            return false;
        }

        $usr2->amigo_asignado = 1;

        if (!$usr2->save()) {
            return false;
        }

        return $usr2;
    }

    public function auth(UserInterface $user)
    {
        if ($this->en_uso == 0) {
            return false;
        }

        if (null === $this->clave) {
            return true;
        } else {
            return $this->clave === md5($user->clave);
        }
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
