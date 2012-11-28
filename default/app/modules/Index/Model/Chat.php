<?php

namespace Index\Model;

use KumbiaPHP\ActiveRecord\ActiveRecord;

class Chat extends ActiveRecord
{
    
    public static function getMensajes($ultimo_id=null) {
        
        if(!is_null($ultimo_id)) {
        
            self::createQuery()
                ->join('usuarios', 'usuarios.id = chat.usuarios_id')
                ->where('chat.id > :ultimo_id')
                ->bindValue('ultimo_id', $ultimo_id)
                ->order('chat.id DESC');
        
        }else {
            
             self::createQuery()
                ->join('usuarios', 'usuarios.id = chat.usuarios_id')
                ->order('chat.id DESC');
        }
        
        return self::findAll('array');
        
    }
    
}
