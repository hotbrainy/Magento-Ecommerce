function initAwall(){
    if(_section == 'awall'){
        $('awall_extensions').update($('awall_extensions_table').innerHTML)
    }
    if(_section == 'awstore'){
       $('awstore_extensions').update($('awall_store_response').innerHTML)
    }
}
Event.observe(window, 'load', function() {
   initAwall();
});
