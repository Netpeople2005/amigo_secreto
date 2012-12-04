<?php

namespace Index\Model;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\ActiveRecord\ActiveRecord;
use KumbiaPHP\Security\Auth\User\UserInterface;

class Usuarios extends ActiveRecord implements UserInterface
{

    const ASIGNADO = 2;
    const NO_DISPONIBLE = 1;
    const DISPONIBLE = 0;

    public static function aleatorio()
    {

        self::createQuery()
                ->where('en_uso = :estado')
                ->bindValue('estado', self::DISPONIBLE);

        $numDisponibles = self::count();

        self::createQuery()
                ->where('en_uso = :estado')
                ->bindValue('estado', self::DISPONIBLE)
                ->limit(1)
                ->offset(rand(0, $numDisponibles - 1));

        return self::find();
    }

    protected static function obtenerAQuienRegalar(Usuarios $quienRegala)
    {
        self::createQuery()
                ->where('amigo_asignado = 0')
                ->where('en_uso = :estado')
                ->bindValue('estado', self::DISPONIBLE);

        //si hay personajes que no han sido asignados. usamos estos.
        if (count($numDisponibles = self::count())) {

            self::createQuery()
                    ->where('amigo_asignado = 0')
                    ->where('id != :id')
                    ->limit(1)
                    ->offset(rand(0, $numDisponibles - 1))
                    ->bindValue('id', $quienRegala->id);

            return self::find();
        }

        //si todos los personajes ya fueron asignados
        self::createQuery()
                ->where('amigo_asignado = 0');

        $numDisponibles = self::count();

        self::createQuery()
                ->where('amigo_asignado = 0')
                ->where('id != :id')
                ->limit(1)
                ->offset(rand(0, $numDisponibles - 1))
                ->bindValue('id', $quienRegala->id);

        return self::find();
    }

    /**
     * PreCrea el personaje en la bd y el asigna el personaje a quien le va a regalar
     * @param Usuarios $usuario el usuario que se acaba de registrar
     * @param Request $request petición actual
     * @return Usuarios|false el usuario a quien se le va a regalar 
     */
    public static function preRegistro(Usuarios $usuario, Request $request)
    {
        $usuario->en_uso = self::NO_DISPONIBLE; //lo seteamos primero a no disponible

        if (!$usuario->save()) {
            return false;
        }

        $equipo = new EquiposRegistrados();

        if (!$equipo->guardar($request)) {
            return false;
        }

        if (!(($usr2 = self::obtenerAQuienRegalar($usuario)) instanceof Usuarios)) {
            $usuario->addError(null, 'No quedan usuarios a quien regalarle');
            return false;
        }

        $usr2->amigo_asignado = 1;

        if (!$usr2->save()) {
            return false;
        }

        return $usr2;
    }

    /**
     * Termina el proceso de registro
     * @param Usuarios $usuario el usuario que se acaba de registrar
     * @return boolean el usuario a quien se le va a regalar 
     */
    public static function postRegistro(Usuarios $usuario)
    {
        $usuario = self::findByPK((int) $usuario->id);

        $usuario->en_uso = self::ASIGNADO;

        if (!$usuario->save()) {
            return false;
        }

        return true;
    }

    public static function cancelarRegistro(Usuarios $usuario, Usuarios $aQuienRegala, Request $request)
    {
        $usuario->begin();

        $usuario->en_uso = self::DISPONIBLE;

        if (!$usuario->save()) {
            $usuario->rollback();
            return false;
        }

        $aQuienRegala->amigo_asignado = 0;

        if (!$aQuienRegala->save()) {
            $usuario->rollback();
            return false;
        }

        EquiposRegistrados::eliminar($request);

        $usuario->commit();

        return true;
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
