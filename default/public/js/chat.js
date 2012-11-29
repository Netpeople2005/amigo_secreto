//Enviar el mensaje al chat

$(document).ready(function() {
    
    setInterval(actualizarChat, 1000);
    
    $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
    
    $(document).keypress(function(e) {
    
        if(e.which == 13) {
            enviarMensaje();
        }
    });
});

function actualizarChat() {
        
        $.ajax({
                type: "POST",
                url: '/amigo_secreto/chat/actualizar',
                data: {'ultimo_id' : $("#ultimo_id").val()},
                dataType: 'json'
            }).done(function(data) {
                $.each(data, function() {
                    $("#chatbox").append('<p><span>'+this.nombre+' dice:</span><br><span>'+this.mensaje+'</span>');
                    $("#ultimo_id").val(this.mensaje_id)
                });
               $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
            });
    }
    
    function enviarMensaje() {
        
        $.ajax({
                type: "POST",
                url: $("#chatform").attr('action'),
                data: $("#chatform").serialize(),
                dataType: 'json'
            }).done(function(data) {
                $("#chatinput").val('');
            });
    }