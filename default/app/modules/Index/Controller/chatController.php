<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of chatController
 *
 * @author ohernandez
 */

namespace Index\Controller;

use Index\Model\Chat;
use Index\Model\Usuarios;
use KumbiaPHP\Kernel\Controller\Controller;

class chatController extends Controller{
    
    
    public function index_action() {
        
        $this->mensajes = array_reverse(Chat::getMensajes());
        
        $ultimo_mensaje = end($this->mensajes);
        
        $this->ultimo_id = $ultimo_mensaje['mensaje_id'];
        
    }
    
    public function enviar_mensaje_action() {
        
        $nuevo_mensaje = $this->getRequest()->get('mensaje');
        
        $ultimo_id = $this->getRequest()->get('ultimo_id');
        
        $data = array(
            'usuarios_id' => $this->get('security')->getToken('id'),
            'texto' => $this->checkInput($nuevo_mensaje),
            'fecha' => date(DATE_W3C)
        );
        
        $chat = new Chat();
        
        $chat->save($data);
        
        $mensajes = array_reverse(Chat::getMensajes($ultimo_id));
        
        return new \KumbiaPHP\Kernel\JsonResponse($mensajes);
        
    }
    
    public function actualizar_action() {
        
        $ultimo_id = $this->getRequest()->get('ultimo_id');
        
        $mensajes = array_reverse(Chat::getMensajes($ultimo_id));
        
        return new \KumbiaPHP\Kernel\JsonResponse($mensajes);
        
    }
    
    public function checkInput($str) {
        $str = @strip_tags($str);
        $str = @stripslashes($str);
        $str = mysql_real_escape_string($str);
        return $str;
    }
    
}

?>
