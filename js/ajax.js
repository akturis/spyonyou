    $(document).ready(function(){
        $("#ssoSelectButton").click(function(e){
//            var hiddenSection = $('#ssoSelect').parents('section.hidden');
            var scopes = [];
//            $('input[name="scopes"]:checked').each(function(){scopes.push($(this).val())});
            var array={action:"ssoLogin", scopes:scopes};
            $.ajax({
                type: 'POST',
                url: 'ajax.php?t=' + new Date().getTime(),
                data: array,
                async: true,
                success: function (data, textStatus, XHR) {
                    data=$.parseJSON(data);
                    window.location.href=data.url;
                }
            });
        });
    });
