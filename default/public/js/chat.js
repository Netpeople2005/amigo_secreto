//Enviar el mensaje al chat
;
$(document).ready(function() {
    $(document).keypress(function(e) {
    
        if(e.which == 13) {
            $.ajax({
                type: "POST",
                url: $("#chatform").attr('action'),
                data: $("#chatform").serialize(),
                dataType: 'json'
            }).done(function(data) {
                $.each(data, function() {
                    $("#chatbox").append('<p><span>'+this.nombre+' dice:</span><br><span>'+this.mensaje+'</span>');
                });
                $("#chatinput").val('');
                $('#chatbox').scrollTop($('#chatbox').height())
            });
        }
    });
});