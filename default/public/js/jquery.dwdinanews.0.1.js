/*
 PLUGIN JQUERY DWDINANEWS
 ======================
 By DesarrolloWeb.com
 Miguel Angel Alvarez S�nchez
 
 DESCRIPCI�N: Sencillo plugin para hacer una presentaci�n de novedades din�mica en un espacio reducido,
 lo que se conoce como ticker de noticias.
 
 LICENCIA: Open source under the BSD License.
 Puedes utilizar este script para los usos que desees, incluso comercialmente.
 Se agradece el reconocimiento con un enlace a www.desarrolloweb.com
 
 USO: Para usar el plugin debes crear un contenedor y dentro una lista <UL> con tantos <LI> como desees
 Debes llamar al plugin con el contenedor donde hayas colocado la lista
 $("#contenedor").dwdinanews();
 
 OPCIONES PERMITIDAS: Al invocar al plugin puedes pasar un objeto de opciones
 retardo: el tiempo que pasa entre visualizaci�n de una noticia y otra
 tiempoAnimacion: el tiempo que se ocupa en la animaci�n al pasar de una noticia a otra
 funcionAnimacion: la funci�n a utilizar para la animaci�n entre noticias
 
 DEPENDENCIAS: El plugin hace uso de otro plugin jQuery llamado "jQuery Timer"
 P�gina del plugin: http://plugins.jquery.com/project/Timer
 Explicaci�n del plugin timer: http://www.desarrolloweb.com/articulos/plugin-jquery-timer.html
 
 DEPENDENCIA OPCIONAL: Si lo deseas puedes usar el plugin "jQuery Easing" para especificar cualquier funci�n de animaci�n de las que implementa
 P�gina del plugin: http://gsgd.co.uk/sandbox/jquery/easing/
 Explicaci�n del plugin Easing: http://www.desarrolloweb.com/articulos/plugin-efectos-jquery-easing.html
 
 Espero que lo puedas aprovechar, para aprender o para tu web.
 
 Miguel Angel Alvarez
 DesarrolloWeb.com
*/

(function($) {
	$.fn.dwdinanews = function(opt) {
		var opciones = {
			retardo: 4000,
			tiempoAnimacion: 500,
			funcionAnimacion: ''
		}
		jQuery.extend(opciones, opt);
		
		this.each(function(){
			var listaNovedades = $(this).children("ul");
			var elementosLista = listaNovedades.children("li");
			var elementoActual = 0;
			$.timer(opciones.retardo, function(timer){
				elementoActual =  (elementoActual + 1) % elementosLista.length;
				listaNovedades.animate({
					top: "-" + $(elementosLista[elementoActual]).position().top + "px"
				}, opciones.tiempoAnimacion, opciones.funcionAnimacion)
				
			});
		});
		return this;
	};
})(jQuery);