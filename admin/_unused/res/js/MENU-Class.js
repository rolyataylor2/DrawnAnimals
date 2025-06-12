
function MENUCLASS() {
    var source = {
        _popup: function(html) {
            if ($('#MainWindowPopups').html() === '');
                $('#MainWindowPopups').html('<div class="BG" onclick="MENU._popupHide();"></div>');
            $("#MainWindowPopups").css("opacity", "1");
            if (GAME !== void 0)
                GAME.System.Pause('MainWindowPopups');
            try {
                $('#MainWindowPopups .popup').addClass('closed');
                source._popupDiv = $('<div class="popup hidden"></div>')
                        .html(html)
                        .appendTo($('#MainWindowPopups'));
                setTimeout(function() {
                    source._popupDiv.removeClass('hidden');
                    $('#MainWindowPopups .closed').remove();
                    
                }, 1000);
            } catch (e) {
                console.log(html);
                console.log(e);
            }
        },
        _popupLoad: function(url) {
            $("#MainWindowPopups").css("display", "block");
            if (GAME !== void 0)
                GAME.System.Pause('MainWindowPopups');
            source._popupLastUrl = url;
            $.ajax({
                context: $('#MainWindowPopups'),
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source._popup('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source._popup(data);
                },
                timeout: 5000,
                type: "GET",
                dataType: "html",
                url: 'http://192.168.0.18/' + url
            });
        },
        _popupReload: function(data) {
            $("#MainWindowPopups").css("display", "block");
            if (GAME !== void 0)
                GAME.System.Pause('MainWindowPopups');
            url = source._popupLastUrl;
            $.ajax({
                context: $('#MainWindowPopups'),
                data:data,
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source._popup('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source._popup(data);
                },
                timeout: 5000,
                type: "POST",
                dataType: "html",
                url: 'http://192.168.0.18/' + url
            });
            return false;
        },
        _popupHide: function() {
            $("#MainWindowPopups").css("opacity", "0");
            $('#MainWindowPopups .popup').addClass('closed');
            if (GAME !== void 0)
                GAME.System.UnPause('MainWindowPopups');
            setTimeout(function() {
                $('#MainWindowPopups').css("display", "none").css("opacity", "0");
            }, 500);
        },
        
        _content: function(html) {
            $("#MainWindowContents").css("opacity", "1");
            if (GAME !== void 0)
                GAME.System.Pause('MainWindowContents');
            try {
                $('#MainWindowContents .content').addClass('closed');
                source._contentDiv = $('<div class="content hidden"></div>')
                        .html(html)
                        .appendTo($('#MainWindowContents'));
                setTimeout(function() {
                    source._contentDiv.removeClass('hidden');
                    $('#MainWindowContents .closed').remove();
                }, 1000);
            } catch (e) {
                console.log(html);
                console.log(e);
            }
        },
        _contentLoad: function(url) {
            $("#MainWindowContents").css("display", "block");
            if (GAME !== void 0)
                GAME.System.Pause('MainWindowContents');
            source._contentLastUrl = url;
            $.ajax({
                context: $('#MainWindowContents'),
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source._content('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source._content(data);
                },
                timeout: 5000,
                type: "GET",
                dataType: "html",
                url: 'http://192.168.0.18/' + url
            });
        },
        _contentReload: function(additions) {
            $("#MainWindowContents").css("display", "block");
            if (GAME !== void 0)
                GAME.System.Pause('MainWindowContents');
            var url = source._contentLastUrl + additions;
            $.ajax({
                context: $('#MainWindowContents'),
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source._content('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source._content(data);
                },
                timeout: 5000,
                type: "GET",
                dataType: "html",
                url: 'http://192.168.0.18/' + url
            });
        },
        
        System: {
            Start:function() {
                source._contentLoad('ctrl/content/start/index.php');
            },
            Login: function() {
                source._contentLoad('ctrl/content/login/index.php');
            },
            LoginForm: function() {
                source._popupLoad('ctrl/popup/login/index.php');
            },
            RegisterForm: function() {
                source._popupLoad('ctrl/popup/register/index.php');
            },
            Logout: function() {
                source._popupLoad('ctrl/popup/logout/index.php');
            },
            GamePlay:function() {
                source._contentLoad('ctrl/content/gameplay/index.php');
            }
        },
        Drawnimal: {
            PlayWith: function(id) {
            },
            ListenTo: function(id) {
            },
            QuickStats: function(id) {
            },
            HeldItem: function(id, item) {
            },
            HelpfulMoves: function(id) {
            },
            HatchEgg: function(id) {
            }
        },
        Message: {
            View: function(id) {
            },
            Delete: function(idArray) {
            },
            Compose: function(tooUser) {
            },
            Reply: function(messageId) {
            },
            Save: function(messageId) {
            }
        },
        Item: {
            Store: {
                ViewAll: function() {
                },
                View: function() {
                },
                Buy: function() {
                }
            },
            User: {
                ViewAll: function() {
                },
                View: function() {
                },
                Buy: function() {
                }
            },
            View: function() {
            },
            Sell: function() {
            }
        },
        Trade: {
            Add: function(petId) {
            },
            Remove: function(petId) {
            },
            Offer: function(petId, offerPetId) {
            },
            Accept: function(petId, offerPetId) {
            }
        },
        Battle: {
            Select: {
                Move: function() {
                },
                Item: function() {
                },
                Switch: function() {
                }
            },
            Submit: {
                Move: function() {
                },
                Item: function() {
                },
                Switch: function() {
                },
                Run: function() {
                }
            },
            Request: function(uid) {
            },
            Wager: {
                Item: function(battleid, winner, item) {
                },
                Money: function(battleid, winner, money) {
                }
            }
        }
    };
    return source;
}
MENU = new MENUCLASS();