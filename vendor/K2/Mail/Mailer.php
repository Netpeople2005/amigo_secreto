<?php

namespace K2\Mail;

use KumbiaPHP\Kernel\Response;
use \InvalidArgumentException;
use K2\Mail\Exception\MailException;
use KumbiaPHP\Di\Container\ContainerInterface;
use K2\EmailTemplate\Template\TemplateInterface;

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
     * @var array
     */
    protected $config;

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
        $this->config = $container->getParameter('k2_mailer');
        $this->mailer = new \PHPMailer(true);
        $this->loadParameters();
        $this->mailer->CharSet = isset($this->config['charset']) ? $this->config['charset'] : 'UTF-8';
        $this->mailer->SMTPDebug = isset($this->config['debug']) ? $this->config['debug'] : 0;
        $this->bcc = isset($this->config['bcc']) ? $this->config['bcc'] : array();
        if ($container->get("app.context")->inProduction()) {
            $this->enabled = false;
        } else {
            $this->enabled = isset($this->config['enable']) ? $this->config['enable'] : true;
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
     * Establece los atributos de la clase K2\EmailTemplate\Template\TemplateInterface
     * cuando se usa dicho módulo.
     * @param TemplateInterface $template
     * @return \K2\Mail\Mailer 
     */
    public function setTemplate(TemplateInterface $template)
    {
        $this->setSubject($template->getSubject())
                ->setBody($template->getContent());
        return $this;
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
        switch (strtolower($this->config['transport'])) {
            case 'smtp':
                $this->mailer->isSMTP();
                $this->mailer->SMTPAuth = true;
                $this->mailer->SMTPSecure = 'ssl';
                if (!isset($this->config['port'])) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro port en el archivo app/config/config.ini en la sección [k2_mailer]");
                }
                $this->mailer->Port = $this->config['port'];
                if (!isset($this->config['host'])) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro host en el archivo app/config/config.ini en la sección [k2_mailer]");
                }
                $this->mailer->Host = $this->config['host'];
                if (!isset($this->config['username'])) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro username en el archivo app/config/config.ini en la sección [k2_mailer]");
                }
                $this->mailer->Username = $this->config['username'];
                if (!isset($this->config['password'])) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro password en el archivo app/config/config.ini en la sección [k2_mailer]");
                }
                $this->mailer->Password = $this->config['password'];
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
                if (!isset($this->config['transport'])) {
                    throw new InvalidArgumentException("Debe especificar un valor para el parametro transport en el archivo app/config/config.ini en la sección [k2_mailer]");
                } else {
                    throw new InvalidArgumentException("No se reconoce el valor para el transport en transport en el archivo app/config/config.ini en la sección [k2_mailer]");
                }
        }

        if (!isset($this->config['fromname'])) {
            throw new InvalidArgumentException("Debe especificar un valor para el parametro fromname en el archivo app/config/config.ini en la sección [k2_mailer]");
        }
        if (!isset($this->config['fromemail'])) {
            throw new InvalidArgumentException("Debe especificar un valor para el parametro fromemail en el archivo app/config/config.ini en la sección [k2_mailer]");
        }
        $this->mailer->SetFrom($this->config['fromemail'], $this->config['fromname']);
    }

    protected function addBCC()
    {
        foreach ($this->bcc as $email) {
            $this->mailer->AddBCC($email);
        }
    }

}