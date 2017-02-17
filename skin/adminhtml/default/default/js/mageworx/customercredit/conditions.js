function rendererConditions(el,url)
{
   return createRequest('customercredit_rules_edit_tabs_conditions_section_content',el.value,url);
}
function rendererActions(el,url)
{
   return createRequest('customercredit_rules_edit_tabs_actions_section_content',el.value,url);
}

function createRequest(elId,value,url)
{
    
    new Ajax.Updater(
        $(elId), 
        url, 
        { 
            method: "post", 
            evalScripts: true,
            parameters: {'current_rule_type':value}
        }
    );
}

function openInfoGrid(url,current_rule)
{
    new Ajax.Updater(
        'diallog_box_group-select', 
        url, 
        {
            method: "post", 
            evalScripts: true,
            parameters: {'rule':current_rule}
        }
            
    );

    Ajax.Responders.register({
        onCreate: function(){
            $$('.loader').each(function(el) {
              eel = el.up();
              eel.style.zIndex=5500;
              eel.show();
           });
      //    alert('a request has been initialized!');
        }, 
        onComplete: function(transport){
            if(!transport.options.parameters.rule) return true;
            $('diallog_box-wrapper').show();
            $$('.loader').each(function(el) {
                el.up().hide();
             });
            $$('#diallog_box_group-select .footer').each(function(el){
                el.hide();
            })
            $$('#diallog_box_group-select .middle').each(function(el){
                el.style ="background:none";
            })
        }
    });
}
