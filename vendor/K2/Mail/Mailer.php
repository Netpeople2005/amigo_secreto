<?php

namespace K2\Mail;

use KumbiaPHP\Kernel\Response;
use \InvalidArgumentException;
use K2\Mail\Exception\MailException;
use KumbiaPHP\Di\Container\ContainerInterface;

require_once __DIR__ . '/phpmailer/class.phpmailer.php';

/**
 * Clase para envios de correo, que usa PHPMailer.
 * @category servicios
 */
class Mailer
{

    /**
     * 
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @var PHPMailer
     */
    protected $mailer;
    protected $enabled;
    protected $bcc;

    /**
     * Constructor de la clase
     * @param ContainerInterface $container espera el servicio @container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->mailer = new \PHPMailer(true);
        $this->loadParameters();
        $this->mailer->CharSet = $container->getParameter('config.charset') ? : 'UTF-8';
        $this->mailer->SMTPDebug = $container->getParameter('k2.mailer.debug') ? : 0;
        $this->bcc = $container->getParameter('k2.mailer.bcc') ? : array();
        if ($container->get("app.context")->inProduction()) {
            $this->enabled = false;
        } else {
            $this->enabled = $container->getParameter('k2.mailer.enable') ? true : false;
        }
    }

    /**
     * Establece el Asunto del Mensaje
     * @param string $subject
     * @return Mailer
     */
    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;
        return $this;
    }

    /**
     * Devuelve el Asunto del mensaje
     * @return string 
     */
    public function getSubject()
    {
        return $this->mailer->Subject;
    }

    /**
     * Agrega un destinatario de correo.
     * @param string $recipient
     * @param string $name
     * @return Mailer
     */
    public function addRecipient($recipient, $name = NULL)
    {
        $this->mailer->AddAddress($recipient, $name);
        return $this;
    }

    /**
     * Establece el mensaje del correo.
     * @param string|Response $body
     * @param boolean $isHtml = true
     * @return Mailer
     */
    public function setBody($body, $isHtml = true)
    {
        if ($body instanceof Response) {
            $isHtml = 0 === strpos($body->headers->get('Content-Type'), 'text/html');
            $body = $body->getContent();
        }
        $this->mailer->Body = $body;
        $this->mailer->AltBody = stripslashes($body);
        $this->mailer->isHTML($isHtml);
        return $this;
    }

    /**
     * Devuelve el cuerpo del mensaje.
     * @return string 
     */
    public function getBody()
    {
        return $this->mailer->Body;
    }

    /**
     * Realiza el envío del correo.
     * @return boolean true en caso de existo.
     * @throws MailException excepciones de la libreria PHPMailer
     */
    public function send()
    {
        try {
            if ($this->enabled) {
                $this->addBCC();
                $result = $this->mailer->send();
            } elseif (count($this->bcc)) {//si hay correos ocultos del sistema, igual lo enviamos pero solo a ellos.
                $this->mailer->ClearAllRecipients(); //eliminamos todos los correos asociados.
                $this->addBCC();
                $result = $this->mailer->send();
            } else {
                $result = true; //simulamos que se envió correctamente                
            }
            $this->mailer->clearAllRecipients();
            $this->mailer->Body = NULL;
            $this->mailer->AltBody = NULL;
            $this->mailer->Subject = NULL;
            return $result;
        } catch (\Exception $e) {
            throw new MailException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Devuelve el error de la lib PHPMailer
     * @return string
     */
    public function getError()
    {
        return $this->mailer->ErrorInfo;
    }

    /**
     * Carga los parametros establecidos en el app/config/config.ini
     * en la lib PHPMailer
     * @throws InvalidArgumentException lanzada si falta algun parametro en el config
     */
    protected function loadParameters()
    {
        switch (strtolower($this->container->getParameter('k2.mailer.transport'))) {
            case 'smtp':
                $this->mailer->isSMTP();
                $this->mailer->SMTPAuth = true;
                $this->mailer->SMTPSecure = 'ssl';
                if (null == $this->mailer->Port = $this->container
                        ->getParameter('k2.mailer.port')) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.port</b> en el archivo app/config/config.ini</b>");
                }
                if (null == $this->mailer->Host = $this->container
                        ->getParameter('k2.mailer.host')) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.host</b> en el archivo app/config/config.ini</b>");
                }
                if (null == $this->mailer->Username = $this->container
                        ->getParameter('k2.mailer.username')) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.username</b> en el archivo app/config/config.ini</b>");
                }
                if (null == $this->mailer->Password = $this->container
                        ->getParameter('k2.mailer.password')) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.password</b> en el archivo app/config/config.ini</b>");
                }
                break;
            case 'mail':
                $this->mailer->IsMail();
                break;
            case 'qmail':
                $this->mailer->IsQMail();
                break;
            case 'sendmail':
                $this->mailer->IsSendMail();
                break;
            default:
                if ($this->container->hasParameter('k2.mailer.transport')) {
                    throw new InvalidArgumentException("No se reconoce el valor para el transport en k2.mailer.transport</b> en el archivo app/config/config.ini</b>");
                } else {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.transport</b> en el archivo app/config/config.ini</b>");
                }
        }

        if (null == $fromname = $this->container
                ->getParameter('k2.mailer.fromname')) {
            throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.fromname</b> en el archivo app/config/config.ini</b>");
        }
        if (null == $fromemail = $this->container
                ->getParameter('k2.mailer.fromemail')) {
            throw new InvalidArgumentException("Debe especificar un valor para el parametro k2.mailer.fromemail</b> en el archivo app/config/config.ini</b>");
        }
        $this->mailer->SetFrom($fromemail, $fromname);
    }

    protected function addBCC()
    {
        foreach ($this->bcc as $email) {
            $this->mailer->AddBCC($email);
        }
    }

}