K2_Mail
=======
Módulo para el envio de correos en K2, ofrece una serie de métodos para configurar y enviar emails con el uso de la lib PHPMailer.

Instalacion
-----------

Solo debemos descargar e instalar la lib en **vendor/K2/Mail** y registrarla en el [AppKernel](https://github.com/manuelj555/k2/blob/master/doc/app_kernel.rst):

```php

//archivo app/AppKernel.php

protected function registerModules()
{
    $modules = array(
        'KumbiaPHP'   => __DIR__ . '/../../vendor/kumbiaphp/kumbiaphp/src/',
        'Index'       => __DIR__ . '/modules/',
        ...
        'K2/Mail'   => __DIR__ . '/../../vendor/',
    );
    ...
}
```

Con esto ya hemos registrado el módulo en nuestra aplicación.

Configuracion
-------------

En el archivo **app/config/config.ini** debemos crear la configuración de conexion a la cuenta de correo, estos son los parametros disponibles:

```php

;archivo app/config/config.ini

[parameters]
k2.mailer.debug = On|Off ;opcional, habilita el módo debug para ve mensajes de error en desarrollo.
k2.mailer.transport = smtp|sendmail|mail|qmail ;parametro obligarotio, debe tener alguna de esas opciones.
k2.mailer.host = ;servidor de correo al que nos vamos a conectar ;opcional, solo si es smtp
k2.mailer.port = ;puerto de la conexion al servidor de correo. ;opcional, solo si es smtp
k2.mailer.fromname = Nombre del Remitente ;Obligatorio
k2.mailer.fromemail = correo@dominio.com ;correo del remitente, Obligatorio
k2.mailer.username = nombre de usuario ;opcional, solo si es smtp
k2.mailer.password = clave de usuario ;opcional, solo si es smtp
k2.mailer.enable = On ;indica si se envia ó no el correo, ideal para pruebas sin envio de correo. Opcional, On por defecto
k2.mailer.bcc[] = correo_oculto@dominio.com ;dir de correo a la que le llegan todos los correos enviados. Opcional
```

Con esto ya podremos usar el servicio de envio de correos.

Ejemplo de Uso:
---------------
```php

<?php

namespace Registro\Controller;

use KumbiaPHP\Kernel\Controller\Controller;
use K2\Mail\Exception\MailException;

class RegistroController extends Controller
{

    public function correoBasico()
    {

        $mailer = $this->get("k2_mailer")
                            ->setSubject("Este es el asunto del correo...!!!")
                            ->setBody("<h2>Título mensaje</h2><p>Contenido del mensaje...</p>")
                            ->addRecipient('correo@gmail.com');
        try{
            if ( $mailer->send() )
            {
                $this->get("flash")->success("Se envió el correo exitosamente...!!!");
            }else{
                $this->get("flash")->warning("Nó se pudo enviar el correo");
            }

        }catch(MailException $e){
            $this->get("flash")->error("Error al enviar el correo: " . $e->getMessage());
        }
    }

    public function enviarCorreo2($usuarioId)
    {
        //verificamos la existencia del usuario en la BD
        if (!$usuario = Usuarios::findByPK((int) $usuarioId)){
            $this->renderNotFound("No existe el usuario con id $usuarioId");
        }

        //obtenemos el contenido de la url email_templates/usuarios/registro/{id}
        //el cual es el html que se enviará por correo.

        $response = $this->getRouter()->forward("email_templates/usuarios/registro/$usuarioId");

        if ( 200 === $response->getStatus() ){ //si la respuesta es exitosa.
            $email = $this->get("k2_mailer")
                                ->setSubject("Registro Exitoso")
                                ->setBody($response); //tambien puede recibir un objeto Response

            $email->addRecipient($usuario->email, $usuario->nombres);

            try{
                if ( $email->send() ){
                    $this->get("flash")->success("El correo fué enviado con éxito...!!!");
                }else{ //si hubo un error.
                    $this->get("flash")->error("No se Pudo enviar el Correo...!!!");
                }
            }catch(MailException $e){
                $this->get("flash")->error("Error al enviar el correo: " . $e->getMessage());
            }
        }else{ //si hubo un error.
            $this->get("flash")->error("No se Pudo enviar el Correo...!!!");
        }
    }
}
```