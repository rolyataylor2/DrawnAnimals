function ShortCutMenu() {
    var source = {
        windowlist:[],
        addWindow:function(html) {
            $("#GUI-Popups").css("opacity", "1");
            GUIEditChanged(true);
            if (source.windowlist.length > 0)
                source.windowlist[source.windowlist.length - 1].addClass('hidden');

            var popup = $('<div class="popup closed"></div>')
                        .html( html )
                        .appendTo($('#GUI-Popups'));

            source.windowlist.push( popup );

            setTimeout(function() {
                source.windowlist[source.windowlist.length - 1].removeClass('closed');
            }, 100);
        },
        toolbarlist:[],
        addToolbar:function(html) {
            $("#GUI-Toolbar").css("opacity", "1");
            GUIEditChanged(true);
            if (source.toolbarlist.length > 0)
                source.toolbarlist[source.toolbarlist.length - 1].addClass('hidden');

            var toolbar = $('<div class="popup closed"></div>')
                        .html( html ).appendTo($('#GUI-Toolbar'));

            source.toolbarlist.push( toolbar );

            setTimeout(function() {
                source.toolbarlist[source.toolbarlist.length - 1].removeClass('closed');
            }, 100);
        },
        
        messageList:0,
        showMessageURL:function(url) {
            $("#GUI-Message").css("display", "block");
            Game.pausedMessage = true;
            
            $.ajax({
                context: $('#GUI-Message'),
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source.addMessage('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source.addMessage(data);
                },
                timeout: 5000,
                type: "GET",
                url: 'http://www.drawnimals.com/JRT2/pop/' + url
            });
        },
        addMessage:function(html) {
            $("#GUI-Message").css("opacity", "1");
            Game.pausedMessage = true;
            try {
            $('#GUI-Message .popup').addClass('closed');
            source.messageList = $('<div class="popup closed"></div>')
                        .html( html ).appendTo($('#GUI-Message'));
            setTimeout(function() {
                source.messageList.removeClass('closed');
            }, 100);
            } catch (e) {
                console.log(html);
            }
        },
        hideMessage:function() {
            $("#GUI-Message").css("opacity", "0");
            $('#GUI-Message .popup').addClass('closed');
            setTimeout(function() {
                $('#GUI-Message').html('<div class="BG" onclick="Menu.hideMessage();"></div>').css("display", "none").css("opacity", "0");
            }, 500);
        },
        

        showURL:function(url) {
            $("#GUI-Popups").css("display", "block");
            $("#GUI-Toolbar").css("display", "block");
            Game.pausedMenu = true;
            
            $.ajax({
                context: $('#GUI-Popups'),
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source.addWindow('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                    source.addToolbar('<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source.addWindow(data);
                },
                timeout: 5000,
                type: "GET",
                url: 'http://www.drawnimals.com/JRT2/pop/' + url
            });
        },
        replaceURL:function(url) {
            $("#GUI-Popups").css("display", "block");
            $("#GUI-Toolbar").css("display", "block");
            Game.pausedMenu = true;
            
            source.windowlist.pop().addClass('closed');
            source.toolbarlist.pop().addClass('closed');
            if (source.windowlist.length !== 0)
                source.windowlist[source.windowlist.length - 1].removeClass('hidden');
            $.ajax({
                context: $('#GUI-Popups'),
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    source.addWindow('<center></br>Error Occured</br>URL: "' + url + '"</br>Reason: ' + errorThrown + '</center>');
                    source.addToolbar('<a href="javascript:Menu.hide();"><img src="img/sit/toolbar/back.png"/>Close</a>');
                },
                success: function(data, textStatus, XMLHttpRequest) {
                    source.addWindow(data);
                },
                timeout: 5000,
                type: "GET",
                url: 'http://www.drawnimals.com/JRT2/pop/' + url
            });
        },
        
        
        hide:function() {
            GUIEditChanged(true);
            if (source.windowlist.length < 1) {
                $("#GUI-Popups").html('').css("display", "none").css("opacity", "0");
                $("#GUI-Toolbar").html('').css("display", "none").css("opacity", "0");
                Game.pausedMenu = false;
                GUIEditChanged();
            } else {
                source.windowlist.pop().addClass('closed');
                if (source.toolbarlist.length > 0) {
                    source.toolbarlist.pop().addClass('closed');
                    $('#GUI-Toolbar .popup .menu').removeClass('open');
                    
                }
                if (source.windowlist.length === 0) {
                    $("#GUI-Popups").css("opacity", "0");
                    $("#GUI-Toolbar").css("opacity", "0");
                    setTimeout(function() {
                        Menu.hide();
                    }, 1000);
                }
                else {
                    source.windowlist[source.windowlist.length - 1].removeClass('hidden');
                    if (source.toolbarlist.length > 0)
                        source.toolbarlist[source.toolbarlist.length - 1].removeClass('hidden');
                }
            }
        },
        hideAll:function() {
            source.windowlist = [];
            source.toolbarlist = [];
            $("#GUI-Popups").html('').css("display", "none").css("opacity", "0");
            $("#GUI-Toolbar").html('').css("display", "none").css("opacity", "0");
            Game.pausedMenu = false;
            GUIEditChanged();
        },
        
        MainMenu:function() {source.showURL('menu.php');},
        
        Settings:function() {source.showURL('menu/settings.php');},
        
        Friends:function() {source.showURL('menu/friends.php');},
        FriendAdd:function(userid) {},
        FriendRemove:function(userid) {},
        FriendSendMessage:function(userid) {},
        FriendRequestTrade:function(userid) {},
        FriendRequestBattle:function(userid) {},
        
        Profile:function() {source.showURL('menu/profile.php');},
        
        TrainerCard:function(userid) {source.showURL('menu/trainercard.php');},
        TrainerAddFreind:function(userid) {},
        TrainerRemoveFriend:function(userid) {},
        TrainerSendMessage:function(userid) {},
        TrainerRequestTrade:function(userid) {},
        TrianerRequestBattle:function(userid) {},
        
        Bag:function() {source.showURL('menu/bag.php');},
        
        Items:function() {source.showURL('menu/items.php');},
        ItemView:function(id, token, args) {
            var url = 'itemview.php?id='+id;
            if (token !== void 0)
                url += '&confirm='+token;
            if (token !== void 0)
                url += '&'+args;
            source.showMessageURL(url);
        },
        ItemDiscard:function(id) {},
        ItemGive:function(id) {},
        
        Party:function(id) {source.showURL('menu/party.php');},
        
        PokemonView:function(id) {source.showURL('pokemonview.php?id='+id);},
        PokemonComment:function(id) {},
        PokemonLike:function(id) {},
        PokemonEvolve:function(id,token) {source.showPopup('pokemonevolve.php?id='+id+'&token='+token);},
        
        Pokedex:function() {source.showURL('menu/pokedex.php');},
        PokedexEntry:function(species) {},
        
        Messages:function() {source.showURL('menu/messages.php');},
        MessageView:function(id) {},
        MessageDiscard:function(id) {},
        MessageSave:function(id) {},
        MessageCompose:function() {},
        
        SettingsChangeTheme:function(token, theme) {
            var url = 'settingschangetheme.php?';
            if (token !== void 0)
                url += '&confirm='+token;
            if (token !== void 0)
                url += '&theme='+theme;
            source.showMessageURL(url);
        },
        
        Logout:function() {source.showURL('menu/logout.php');},
        LogoutConfirmed:function() {}
       
        
    };
    return source;
}
var Menu = new ShortCutMenu();