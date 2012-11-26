<?php

namespace Index\Model;

use KumbiaPHP\Kernel\Request;
use KumbiaPHP\ActiveRecord\ActiveRecord;

class EquiposRegistrados extends ActiveRecord
{

    public static function existe(Request $request)
    {
        self::createQuery()
                ->where('descripcion = :pc')
                ->bindValue('pc', md5($request->getClientIp()));

        return 0 < self::count();
    }

    public function guardar(Request $request)
    {
        $this->descripcion = md5($request->getClientIp());
        return $this->save();
    }

}
