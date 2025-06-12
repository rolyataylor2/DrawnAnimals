<script>
    String.prototype.toProperCase = function () {
    return this.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    };
    function ClassAnimal() {
        var source = {
            Name:'',Species:'',HP:{min:0,max:0},
            Moves:[], ItemHeld:'',
            ImageSmall:0, ImageMedium:0, ImageLarge:0,
            Init:function() {},
            SetImage:function(image,elementname) {
                $(source[image]).clone().appendTo($('#'+elementname));
            }
        };
        return source;
    }
    function ClassUser(username,userid,location,level,gender,avatar,accounttype) {
        var source = { 
            Username:username, Userid:userid, 
            Location:location, Level:level, 
            Gender:gender, Team:[], 
            Online:0, Avatar:avatar, 
            AccountType:accounttype
        };
        return source;
    };
    function ClassItem(type, ids, catagory) {
        var source = { Type:type, Ids:ids, Catagory:catagory, Use:function() {}, Discard:function() {} };
        return source;
    };
    function ClassMessage(subject, content, sender, attachment, read, starred, paper, date) {
        var source = { Subject:subject, Content:content, Sender:sender, Attachment:attachment, Read:read, Starred:starred, Paper:paper, Date:date };
        return source;
    };
    function ClassChat(sender,message) {
        var source = {Sender:sender, Message:message};
        return source;
    }
    function ClassFriend(username,userid,confirmed,tag) {
        var source = {Username:username,Userid:userid,Confirmed:confirmed,Tag:tag};
        return source;
    }
    function ClassServer() {
        var source = {
            Username:'', Userid:0, Coins:0, Cash:0, Location:0,
            NetworkKey:'',
            Sketchbook:[],SketchbookDiv:0,
            Inventory:[], InventoryDiv:0,
            ProfileDiv:0,
            Messages:[], MessagesDiv:0,
            Friends:[], FriendsDiv:0,
            OptionDiv:0,
            Users:[],
            Team:[], TeamDiv:0, TeamToolbar:0,
            ChatLog:[],
            Init:function() {
                source.SketchbookDiv = $('<div class="PA-C" id="PA-C-SKETCHBOOK"></div>').appendTo($('#PA-C-R'));
                source.InventoryDiv = $('<div class="PA-C" id="PA-C-ITEMS"></div>').appendTo($('#PA-C-R'));
                source.ProfileDiv = $('<div class="PA-C" id="PA-C-PROFILE"></div>').appendTo($('#PA-C-R'));
                source.MessagesDiv = $('<div class="PA-C" id="PA-C-MESSAGES"></div>').appendTo($('#PA-C-R'));
                source.FriendsDiv = $('<div class="PA-C" id="PA-C-FRIENDS"></div>').appendTo($('#PA-C-R'));
                source.OptionDiv = $('<div class="PA-C" id="PA-C-OPTION"></div>').appendTo($('#PA-C-R'));
                source.TeamDiv = $('<div class="PA-C" id="PA-C-TEAM"></div>').appendTo($('#PA-C-R'));
            },
            showSketchbook:function() {
                source.SketchbookDiv.removeClass('hidden');
                source.InventoryDiv.addClass('hidden');
                source.ProfileDiv.addClass('hidden');
                source.MessagesDiv.addClass('hidden');
                source.FriendsDiv.addClass('hidden');
                source.OptionDiv.addClass('hidden');
                source.TeamDiv.addClass('hidden');
            },
            showInventory:function() {
                source.SketchbookDiv.addClass('hidden');
                source.InventoryDiv.removeClass('hidden');
                source.ProfileDiv.addClass('hidden');
                source.MessagesDiv.addClass('hidden');
                source.FriendsDiv.addClass('hidden');
                source.OptionDiv.addClass('hidden');
                source.TeamDiv.addClass('hidden');
            },
            showProfile:function() {
                source.SketchbookDiv.addClass('hidden');
                source.InventoryDiv.addClass('hidden');
                source.ProfileDiv.removeClass('hidden');
                source.MessagesDiv.addClass('hidden');
                source.FriendsDiv.addClass('hidden');
                source.OptionDiv.addClass('hidden');
                source.TeamDiv.addClass('hidden');
            },
            showMessages:function() {
                source.SketchbookDiv.addClass('hidden');
                source.InventoryDiv.addClass('hidden');
                source.ProfileDiv.addClass('hidden');
                source.MessagesDiv.removeClass('hidden');
                source.FriendsDiv.addClass('hidden');
                source.OptionDiv.addClass('hidden');
                source.TeamDiv.addClass('hidden');
            },
            showFriends:function() {
                source.SketchbookDiv.addClass('hidden');
                source.InventoryDiv.addClass('hidden');
                source.ProfileDiv.addClass('hidden');
                source.MessagesDiv.addClass('hidden');
                source.FriendsDiv.removeClass('hidden');
                source.OptionDiv.addClass('hidden');
                source.TeamDiv.addClass('hidden');
            },
            showOptions:function() {
                source.SketchbookDiv.addClass('hidden');
                source.InventoryDiv.addClass('hidden');
                source.ProfileDiv.addClass('hidden');
                source.MessagesDiv.addClass('hidden');
                source.FriendsDiv.addClass('hidden');
                source.OptionDiv.removeClass('hidden');
                source.TeamDiv.addClass('hidden');
            },
            showTeam:function() {
                source.SketchbookDiv.addClass('hidden');
                source.InventoryDiv.addClass('hidden');
                source.ProfileDiv.addClass('hidden');
                source.MessagesDiv.addClass('hidden');
                source.FriendsDiv.addClass('hidden');
                source.OptionDiv.removeClass('hidden');
                source.TeamDiv.removeClass('hidden');
            },
        
            divInventory:function(sort) {
                var div = source.InventoryDiv;
                div.html('');
                var content = '<div class="title">';
                if (sort !== void 0) content += sort+' ';
                content+= 'ITEMS</div><div class="description">Your Inventory.</div>'+
                        '<div class="tabs">'+
                        '<a href="javascript:server.divInventory()">ALL</a><br/>'+
                        '<a href="javascript:server.divInventory(\'Healing\')">HEAL</a><br/>'+
                        '<a href="javascript:server.divInventory(\'Clothing\')">CLOTHS</a><br/>'+
                        '<a href="javascript:server.divInventory(\'Orb\')">ORBS</a><br/>'+
                        '<a href="javascript:server.divInventory(\'Key\')">KEY</a>'+
                        '</div><div class="list">';
                var i = source.Inventory.length;
                while(i--) {
                    if (sort !== void 0)
                        if (source.Inventory[i].Catagory !== sort) continue;
                
                    content += '<div class="item" onclick="$(this).addClass(\'active\').siblings().removeClass(\'active\');">'+
                                '<img src="'+sys.imgdir+'itm/'+source.Inventory[i].Type+'.png" />'+
                                source.Inventory[i].Type.toProperCase()+' x'+source.Inventory[i].Ids.length+
                                '<div class="controls">'+
                                '<a href="javascript:server.Inventory['+i+'].Use();">Use</a><br/>'+
                                '<a href="javascript:server.Inventory['+i+'].Discard();">Discard</a>'+
                                '</div></div>';
                }
                content+='</div>';
                div.append(content);
                return div;
            },
            divMessages:function(sort) {
                var div = source.MessagesDiv;
                div.html('');
                var content = '<div class="title">';
                if (sort !== void 0) content += sort+' ';
                content+= 'Messages</div><div class="description">Your Messages.</div>'+
                        '<div class="tabs">'+
                        '<a href="javascript:server.divMesssages()">Unread</a><br/>'+
                        '<a href="javascript:server.divMessages(\'Read\')">Read</a><br/>'+
                        '</div><div class="list">';
                var i = source.Messages.length;
                while(i--) {
                    content += '<div class="item" onclick="$(this).addClass(\'active\').siblings().removeClass(\'active\');">'+
                                '<img src="'+sys.imgdir+'itm/'+source.Messages[i].Paper+'.png" />'+
                                source.Messages[i].Sender+':'+source.Messages[i].Subject+
                                '<div class="controls">'+
                                '<a href="javascript:server.Messages['+i+'].Use();">Open</a><br/>'+
                                '<a href="javascript:server.Messages['+i+'].Discard();">Discard</a>'+
                                '</div></div>';
                }
                content+='</div>';
                div.append(content);
                return div;
            }
        };
        return source;
    }
    var server = new ClassServer();
    <?php include 'JS-Var.php';?>
    
</script>