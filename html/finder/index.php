<?php
if (isset($_POST['address'])) {
    die(file_get_contents($_POST['address']));
}
?>
<html>
    <head>
        <script src="jquery.min.js"></script>
        <script>
            NamesToCheck = [];
            function PullAnotherName() {
                if ($('#paused').prop('checked') === true) return;
                if (NamesToCheck.length === 0) {
                    NamesToCheck = $('#nameList').val().split("\n");
                    if ($('#nameList').val() === '') {
                        $('#results').append('No More Names Left<br/>');
                        return;
                    }
                    $('#nameList').val("");
                }
                var name = NamesToCheck.pop();
                $('#currentName').html('Current Name:'+name)
                
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: {
                        address: 'http://www.neopets.com/petlookup.phtml?pet='+name
                    },
                    success: function(response) {
                        if (response.indexOf("/pound/adopt.phtml?") !== -1) {
                            $('#results').append('<b style="color:green;">'+name+' is In the pound.</b><br/>');
                        } else if (response.indexOf("there is no Neopet by that name in Neopia") !== -1) {
                            $('#results').append('<b style="color:green;">'+name+' doesn\'t exist.</b><br/>');
                        } else {
                            $('#results').append('<b style="color:red; font-size:6px;">'+name+' is not availiable.</b><br/>');
                        };
                    }
                });
                setTimeout(PullAnotherName,500);
            }
        </script>
         
    </head>
    <body>
        <textarea id="nameList"></textarea>
        <div id="currentName"></div>
        <label>Pause <input id='paused' type="checkbox"/></label>
        <div id="results"></div>
        <a href="#" onclick="PullAnotherName()">Click Here to continue</a>
    </body>
</html>