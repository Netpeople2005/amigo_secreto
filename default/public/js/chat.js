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
        data: {
            'ultimo_id' : $("#ultimo_id").val()
        },
        dataType: 'json'
    }).done(function(data) {
                
        if(data.length > 0) {
                
            $.each(data, function() {
                $("#chatbox").append('<p>\n\
<table>\n\
<tr>\n\
<td rowspan="2">\n\
<img width="40px" height="48px" alt="Foto de Aquaman" src="/amigo_secreto/img/'+this.imagen+'">\n\
</td>\n\
<td style=" padding-left: 7px"><b>'
                    +this.nombre+' dice:</b>\n\
</td></tr><tr><td style=" padding-left: 7px">'
                    +this.mensaje+'\
</td>\n\
</tr>\n\
</table>\n\
</p>');
                $("#ultimo_id").val(this.mensaje_id)
                
                var song = $('audio');
                if(song.paused){
                    song.play();
                } else {
                    song.pause();
                }
                
                
            });
            $("#chatbox").scrollTop($("#chatbox")[0].scrollHeight);
        }
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