/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function BTTLMOVEMENTCLASS() {
    //To show a object
    $('PokemonAttacker').addClass('show');
    //To hide a object
    $('PokemonAttacker').removeClass('show');
    //To start shaking object
    $('PokemonAttacker').addClass('shake');
    //To stop shaking object
    $('PokemonAttacker').removeClass('shake');
    // or do this http://www.youtube.com/watch?v=T-HN0d9fNzg
}
function BTTLCLASS() {
    var source = {
        Load:{
            Round:function(number) {},
            NextRound:function() {}
        },
        Queue:{
            Clear:function() {}
            
        },
        Set: {
            Div:function(divid) {}
        }
    };
    return source;
}