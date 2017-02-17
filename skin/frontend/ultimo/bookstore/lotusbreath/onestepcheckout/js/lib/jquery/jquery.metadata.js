/*
 * Metadata - jQuery plugin for parsing metadata from elements
 *
 * Copyright (c) 2006 John Resig, Yehuda Katz, Jï¿½Ã¶rn Zaefferer, Paul McLanahan
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Revision: $Id: jquery.metadata.js 3640 2007-10-11 18:34:38Z pmclanahan $
 *
 */


(function($) {

    $.extend({
        metadata : {
            defaults : {
                type: 'class',
                name: 'metadata',
                cre: /({.*})/,
                single: 'metadata',
                meta:'validate'
            },
            setType: function( type, name ){
                this.defaults.type = type;
                this.defaults.name = name;
            },
            get: function( elem, opts ){
                var settings = $.extend({},this.defaults,opts);
                // check for empty string in single property
                if (!settings.single.length) {
                    settings.single = 'metadata';
                }
                if (!settings.meta.length) {
                    settings.meta = 'validate';
                }

                var data = $.data(elem, settings.single);
                // returned cached data if it already exists
                if ( data ) return data;

                data = "{}";

                var getData = function(data) {
                    if(typeof data != "string") return data;

                    if( data.indexOf('{') < 0 ) {
                        data = eval("(" + data + ")");
                    }
                }

                var getObject = function(data) {
                    if(typeof data != "string") return data;

                    data = eval("(" + data + ")");
                    return data;
                }

                if ( settings.type == "html5" ) {
                    var object = {};
                    $( elem.attributes ).each(function() {
                        var name = this.nodeName;
                        if (name.indexOf('data-' + settings.meta) === 0) {
                            name = name.replace(/^data-/, '');
                        }
                        else {
                            return true;
                        }
                        //object[name] = getObject(this.nodeValue);
                        object[name] = getObject(this.value);
                    });
                } else {
                    if ( settings.type == "class" ) {
                        var m = settings.cre.exec( elem.className );
                        if ( m )
                            data = m[1];
                    } else if ( settings.type == "elem" ) {
                        if( !elem.getElementsByTagName ) return;
                        var e = elem.getElementsByTagName(settings.name);
                        if ( e.length )
                            data = $.trim(e[0].innerHTML);
                    } else if ( elem.getAttribute != undefined ) {
                        var attr = elem.getAttribute( settings.name );
                        if ( attr )
                            data = attr;
                    }
                    object = getObject(data.indexOf("{") < 0 ? "{" + data + "}" : data);
                }

                $.data( elem, settings.single, object );
                return object;
            }
        }
    });

    $.fn.metadata = function( opts ){
        return $.metadata.get( this[0], opts );
    };

})(jQuery);