var oscDeliverydate = Class
        .create({
            initialize : function(target, config) {
                this.config = config;
                var target = $(target);
                if (target) {
                    var sMethods = $$('#order-shipping_method div.fieldset')
                            .first();
                    if (sMethods) {
                        sMethods.insert({
                            bottom : target
                        });
                        target.toggle();
                    }

                    var dateField = $('deliverydate:date');
                    if (dateField) {

                        var now = new Date();
                        var end = new Date(now);
                        end.setDate(now.getDate()+parseInt(config.end));

                        var weekdays =  config.weekdays;
                        var tempweek = Array.apply(null, Array(7)).map(Number.prototype.valueOf,7);
                        if(weekdays.length > 0){
                            weekdays.each(function(e){
                                tempweek[e] = e;
                            });
                        }

                        var calendarSetup = {
                            inputField : "deliverydate:date",
                            ifFormat : "%d/%m/%Y",
                            daFormat : "%d/%m/%Y",
                            showsTime : false,
                            weekNumbers : false,
                            bottomBar : false,
                            fdow : 1,
                            fixed : true,
                            button : "datepicker:date",
                            align : "Bl",
                            singleClick : true,
                            disableFunc : function(date) {

                                //disable last year
                                if(date.getFullYear() < now.getFullYear()) {
                                    return true;
                                }

                                //disable previous months
                                if(date.getFullYear() == now.getFullYear()) {
                                    if(date.getMonth() < now.getMonth()) {
                                        return true;
                                    }
                                }

                                //disable previous days
                                if(date.getMonth() == now.getMonth()) {
                                    if(date.getDate() < now.getDate()) {
                                        return true;
                                    }
                                }

                                //disalbe weekends
                                //console.log(config.weekdays.length);
                                if(tempweek.length > 0){
                                    if(date.getDay() != tempweek[date.getDay()]){
                                        return true;
                                    }
                                }

                                //disable future after
                                if(config.end > 0){
                                     if(date > end){
                                         console.log(date.getDate());
                                         console.log(end.getDate());
                                         return true;
                                     }
                                }

                                //disable all other disabled dates
                                date = date.print("%d/%m/%Y");
                                return date in config.excluded;
                            },
                            range: [(now.getFullYear()), (now.getFullYear()+1)]
                        }

                        Calendar.setup(calendarSetup);
                    }
                }
            }
        });
