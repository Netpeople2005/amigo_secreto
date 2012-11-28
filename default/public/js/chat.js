//Enviar el mensaje al chat

$(document).keypress(function(e) {
    
    console.log(e);
    
    if(e.which == 13) {
        $.ajax({
            type: "POST",
            url: $("#chatform").attr('action'),
            data: $("#chatform").serialize(),
            dataType: 'json'
        }).done(function(data) {
            alert(data);
        });
    }
});

