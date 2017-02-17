/*
 Lotus Breath - One Step Checkout
 Copyright (C) 2014  Lotus Breath
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Developers can add observer to make OSC work together with other plugins
 */


var Lotus_OSC_Observer = Class.create();

window.lotus_osc_observer_debug = false;


Lotus_OSC_Observer.prototype  = {
    initialize : function(){
        /**
         * handlers
         */
        this.handlers = [];  // observers

    },
    /**
     *
     *
     * @param eventName
     * @param fn
     * Example
     *  window.oscObserver.register('afterLoadingNewContent', function(){
                //add observer for afterLoadingNewContent
            });
     *
     */
    register : function(eventName, fn){
        if(!this.handlers[eventName])
            this.handlers[eventName] = [];

        this.handlers[eventName].push(fn);
    },

    fire : function(eventName, params, thisObj){
        var scope = thisObj || window;
        if(this.handlers[eventName]){
            this.handlers[eventName].forEach(function(item) {
                item.call(scope, params);
            });
        }

    },
    afterLoadingNewContent : function(updateItems, previousData){
        if(window.lotus_osc_observer_debug){
            console.log('Before reloading partial contents');
        }

        this.fire('afterLoadingNewContent', null , this);


    },
    beforeUpdateBilling: function(){
        if(window.lotus_osc_observer_debug){
            console.log('Before Billing Updates');
        }
        this.fire('beforeUpdateBilling', null , this);
    },

    afterUpdatingBilling: function(response){
        if(window.lotus_osc_observer_debug){
            console.log('After Billing Updates');
            console.log(response);
        }
        this.fire('afterUpdatingBilling', response , this);
    },

    beforeUpdateShipping: function(){
        if(window.lotus_osc_observer_debug){
            console.log('Before Shipping Updates');
        }
        this.fire('beforeUpdateShipping', null , this);
    },

    afterUpdateShipping: function(response){
        if(window.lotus_osc_observer_debug){
            console.log('After Shipping Updates');
            console.log(response);
        }
        this.fire('afterUpdateShipping', response , this);
    },

    beforeUpdateShippingMethod: function(){
        if(window.lotus_osc_observer_debug){
            console.log('Before Shipping Method Updates');
        }
        this.fire('beforeUpdateShippingMethod', null , this);
    },

    afterUpdateShippingMethod: function(response){
        if(window.lotus_osc_observer_debug){
            console.log('After Shipping Method Updates');
            console.log(response);
        }
        this.fire('beforeUpdateShippingMethod', response , this);
    },

    beforeUpdatePaymentMethod: function(){
        if(window.lotus_osc_observer_debug){
            console.log('Before Payment Updates');

        }
        this.fire('beforeUpdatePaymentMethod', null , this);
    },

    afterUpdatePaymentMethod: function(response){
        if(window.lotus_osc_observer_debug){
            console.log('After Payment Method Updates');
            console.log(response);
        }
        this.fire('afterUpdatePaymentMethod', response , this);

    },
    stopSubmittingOrder: false,

    beforeSubmitOrder : function(){
        if(window.lotus_osc_observer_debug){
            console.log('Before clicking place order');
        }
        this.fire('beforeSubmitOrder', null , this);
    },


    afterSaveOrder : function(response, result){
        if(window.lotus_osc_observer_debug){
            console.log('After placing order');
            console.log(response);
        }
        this.fire('afterSaveOrder', response , this);
        return result;
    }

};

(function (window) {
    'use strict';
    window.oscObserver = new Lotus_OSC_Observer();

})(window);

