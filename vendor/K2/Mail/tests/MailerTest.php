<?php

require_once __DIR__ . '/autoload.php';

require_once __DIR__ . '/../Mailer.php';

use K2\Mail\Mailer;
use KumbiaPHP\Kernel\Request;
use KumbiaPHP\Kernel\AppContext;
use KumbiaPHP\Di\Container\Container;
use KumbiaPHP\Di\DependencyInjection;

/**
 * Description of MailerTest
 *
 * @author maguirre
 */
class MailerTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Container 
     */
    protected $container;
    protected $definitions = array(
        'parameters' => array(
            'k2.mailer.debug' => true, //opcional, habilita el módo debug para ve mensajes de error en desarrollo.
            'k2.mailer.transport' => 'smtp', //smtp|sendmail|mail|qmail parametro obligarotio, debe tener alguna de esas opciones.
            'k2.mailer.host' => 'localhost', //servidor de correo al que nos vamos a conectar ;opcional, solo si es smtp
            'k2.mailer.port' => '123', //puerto de la conexion al servidor de correo. ;opcional, solo si es smtp
            'k2.mailer.fromname' => 'admin', //Nombre del Remitente ;Obligatorio
            'k2.mailer.fromemail' => 'admin@correo.com', //correo@dominio.com ;correo del remitente, Obligatorio
            'k2.mailer.username' => 'admin', //nombre de usuario ;opcional, solo si es smtp
            'k2.mailer.password' => 'admin', //clave de usuario ;opcional, solo si es smtp
            'k2.mailer.enable' => false, //indica si se envia ó no el correo, ideal para pruebas sin envio de correo. Opcional, On por defecto
            'k2.mailer.bcc' => array(),
        ),
    );

    protected function setUp()
    {
        $request = new Request();

        $appContext = new AppContext($request, false, __DIR__, array('Index' => __DIR__), array('/' => 'Index'));

        $container = new Container(new DependencyInjection(), $this->definitions);

        $container->set('request', $request);
        $container->set('app.context', $appContext);

        $this->container = $container;
    }

    public function testMailer()
    {
        $mailer = new Mailer($this->container);

        $this->assertEmpty($mailer->getSubject());
        $this->assertEmpty($mailer->getBody());

        $this->assertInstanceOf(get_class($mailer), $mailer->setSubject('titulo del mensaje'));
        $this->assertInstanceOf(get_class($mailer), $mailer->setBody('cuerpo del mensaje'));

        $this->assertEquals('titulo del mensaje', $mailer->getSubject());
        $this->assertEquals('cuerpo del mensaje', $mailer->getBody());

        //El mailer lanza un MailExceptión si el correo no es un Email Válido
        $this->assertInstanceOf(get_class($mailer), $mailer->addRecipient('correo@dominio.com'));

        $this->assertTrue($mailer->send());
    }

}
