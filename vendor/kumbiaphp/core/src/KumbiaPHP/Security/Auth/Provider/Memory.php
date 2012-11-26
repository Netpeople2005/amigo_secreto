<?php

namespace KumbiaPHP\Security\Auth\Provider;

use KumbiaPHP\Security\Config\Reader;
use KumbiaPHP\Security\Auth\User\Memory as User;
use KumbiaPHP\Security\Auth\User\UserInterface;
use KumbiaPHP\Security\Auth\Token\User as Token;
use KumbiaPHP\Security\Auth\Token\TokenInterface;
use KumbiaPHP\Security\Auth\Provider\AbstractProvider;
use KumbiaPHP\Security\Exception\UserNotFoundException;

/**
 * Description of Memory
 *
 * @author manuel
 */
class Memory extends AbstractProvider
{

    //put your code here
    public function loadUser(TokenInterface $token)
    {
        $users = (array) Reader::get('users');

        if (!isset($users[$token->getUsername()])) {
            throw new UserNotFoundException("No existe el Usuario {$token->getUsername()} en la Base de Datos");
        }

        $data['username'] = $token->getUsername();
        $data['password'] = key($users[$token->getUsername()]);
        $data['roles'] = explode(',', $users[$token->getUsername()][$data['password']]);

        $userClass = get_class($token->getUser());

        return new $userClass($data);
    }

    public function getToken(array $config = array())
    {
        $this->config = $config;

        isset($config['username']) || $config['username'] = 'username';
        isset($config['password']) || $config['password'] = 'password';

        $request = $this->container->get('request');

        $form = $request->get('form_login', array(
            $config['username'] => $request->server->get('PHP_AUTH_USER'),
            'password' => $request->server->get('PHP_AUTH_PW'),
                ));

        $form['username'] = $form[$config['username']];
        $form['password'] = $form[$config['password']];

//        if ($config && isset($config['class'])) {
//
//            if (!class_exists($config['class'])) {
//                throw new AuthException("No existe la clase {$config['class']}");
//            }
//
//            $user = new $config['class']($form);
//
//            if (!($user instanceof UserInterface)) {
//                throw new AuthException("La clase {$config['class']} debe implementar la interface de UserInterface");
//            }
//        } else {
        $user = new User($form); //por ahora siempre usaran las clase de usuario Memory
//        }

        return new Token($user);
    }

}