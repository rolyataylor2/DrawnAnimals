Menu.A_InventoryAddItem = function(item,token) {
    var url = 'a_inventoryadditem.php?';
    if (token !== void 0)
        url += '&confirm='+token;
    if (token !== void 0)
        url += '&item='+item;
    Menu.showMessageURL(url);
};
