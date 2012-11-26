FormBuilder
=========

Lib para crear, filtrar y validar formularios de manera facil y segura,
tanto desde el servidor como con HTML5 para los navegadores que lo soporten.

Ejemplo de Uso
==============

::

    //en el controlador
    $form = new FormBuilder();

    //agrego un campo de texto, con name = nombre, label = Nombre
    //ademas le agrego dos validaciones, required y un maxLength
    $form->text('nombre')->required()->maxLength(15)->setLabel('Nombre :');

    //agrego un campo de texto con name = apellido y label = Apellido
    $form->text('apellido')->setLabel('Apellido :');

    //agrega un campo number con name = edad y label = Edad :
    //ademas se le da un rango de numeros permitidos, entre 5 y 100
    $form->number('edad')->setLabel('Edad :')->range(5, 100);

    $form['apellido']['maxlength'] = 20;//asi tambien asignamos atributos.
    $form['apellido'] = array('maxlength' => 20);//esta es otra forma de hacer lo mismo.
    $form->getField('apellido')->attr(array('maxlength' => 20));
    //produce el mismo resultado que los métodos anteriores.


    //pasamos el form a la vista
    $this->form = $form;

    //en la vista

    <?php echo $form; //genera el formulario con los elementos que se crearon ?>

    <!-- Tambien se pueden imprimir los elementos individualmente -->

    <?php echo $form['nombre'];//genera <input type="text" required="required" maxlength="15" /> ?>
    <?php echo $form['apellido'];//genera <input type="text" maxlength="20" /> ?>
    <?php echo $form['edad'];//genera <input type="number" max="100" min="5" /> ?>


