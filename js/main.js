$(document).ready(function() {

        var errorFound = false;
        
        /* When store selected, query for existing data */
        $(document).on("change", "#store_id", function(){
            $(".status").empty();
            $('.submit').prop("disabled",false);
            $.getJSON('update.php', {store:$('#store_id option:selected').val()}, function(data){
                if (data.action === "new") {
                    /* show blank form */
                    $("#action").val("new");
                    document.getElementById("reconform").reset();
                    $(".variance, #total_actual, #total_rpro").empty();
                    $(".submit").html("Submit Report");
                } else {
                    /* it's an update, so populate the form with the data */
                   populate(data);
                    $("#action").val("update");
                    $("#store").val(data.header.store_id);
                    $("#header_id").val(data.header.id);
                    $(".submit").html("Update Report");
                }
                setLaborGoal($('#store_id option:selected').val());
                $("#store").val($('#store_id option:selected').val());
                $("div#form").css({"display":"block"});
            });
        });

        $(document).on("change", "input[type='text']", function() {
                var theClass = $(this).attr('class'),
                    theItem  = $(this).attr('rel'),
                    curValue = Math.round(parseFloat( $(this).val() )*100)/100;
                    errorFound = false;
                    $(this).removeClass('input-error');
                
                $("."+theClass).each(function() {
                    if($(this).attr('rel') === theItem) {
                        /* value is invalid */
                        if (isNaN(curValue)) {
                            //alert ('not a valid number (use the 1.23 format)');
                            $(this).addClass("input-error");
                            //$(this).val('').focus();
                            errorFound = true;
                        } else { /* input is valid */
                            /* reset color, if previous error */
                            /* populate disabled fields with data */
                            $("input[class='col4'][rel='"+theItem+"']").val( $(this).val() );
                        }
                    }
                });
                
                /* Make sure name is filled in with valid characters */
                if( $("input[name='username']").val() == '' || /[^a-zA-Z\s]/.test( $("input[name='username']").val() ) ){
                    errorFound = true;
                    $("input[name='username']").addClass("input-error");
                }
                
                /* if no errors, calculate totals */
                if (!errorFound) {
                    if(theClass === 'gc_matrix' || theClass === 'gc_rpro') {
                        calculateGCVariance(theItem);
                    } else {
                        calculateVariance(theItem);
                        calculateTotals();
                    }
                }
        });

    /**
     * Service Labor
     * Labor goal is indexed by store ID
     * Show any variances between goal and actual based on labor head count
     */
        var labor_goal = {"1":225,
                          "2":225,
                          "3":225,
                          "4":250,
                          "5":225,
                          "6":225,
                          "7":200,
                          "8":225,
                          "9":200,
                          "10":200,
                          "17":200,
                          "19":200,
                          "20":0};

        function setLaborGoal(store_id) {
            var headcount = $("#service_head_count").val(),
                laborgoal = ( labor_goal[store_id] * headcount );
            $("#service_labor_goal").val(laborgoal);
        }
        
        $(document).on("change", "#service_head_count", function () {
            var wholeNumber = false;
            var v = $("#service_head_count").val();
            wholeNumber = ( ( parseInt( v * 2 ) / 2 ) == v ); /* force whole or half numbers */
            if ( wholeNumber )
            {
                    $("#service_head_count").removeClass("input-error"); /* reset color if previous error */
                    $("#service_labor_goal").val( CurrencyFormatted (v * labor_goal[ $('#store_id option:selected').val() ]));
            } else { /* show error */
                    $("#service_head_count").addClass("input-error");
            }
        });
        
        $(document).on("change", "#service_labor_completed", function() {
            var serviceGoal = $("#service_labor_goal").val();
            var serviceCompleted = $("#service_labor_completed").val();
            var serviceVariance = (serviceCompleted - serviceGoal);
            if (serviceVariance > 0) {
                    $("#service_labor_completed").addClass("input-error");
            }
        });
        
        $(document).on("blur", "#service_head_count, #service_labor_completed", function(){
            setLaborGoal($('#store_id option:selected').val());
        });

        function CurrencyFormatted(amount) {
            var i = parseFloat(amount);
            if(isNaN(i)) { i = 0.00; }
            var minus = '';
            if(i < 0) { minus = '-'; }
            i = Math.abs(i);
            i = parseInt((i + .005) * 100);
            i = i / 100;
            s = new String(i);
            if(s.indexOf('.') < 0) { s += '.00'; }
            if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
            s = minus + s;
            return s;
        }
        
        function calculateTotals() {
            var totalactual = 0;
            var totalrPRO = 0;
            var totalvariance = 0;

            /* add the actuals, even from the disabled fields */
            $('.col1, .col4').each(function() {
                    var v1 = parseFloat($(this).val());
                    totalactual += (isNaN(v1)) ? 0 : v1;
            });
            totalactual = Math.round(totalactual*100)/100;
            $('#total_actual').html('<strong>$ '+totalactual+'</strong>');
            
            /* add the rpro values */
            $('.col2').each(function() {
                    var v2 = parseFloat($(this).val());
                    totalrPRO += (isNaN(v2)) ? 0 : v2;
            });
            totalrPRO = Math.round(totalrPRO*100)/100;
            $('#total_rpro').html('<strong>$ '+totalrPRO+'</strong>');
            
            /* add the variances */
            $('.col3').each(function() {
                v3 = parseFloat($(this).text().substring(2));
                totalvariance	+=	(isNaN(v3)) ? 0 : v3;
                totalvariance	=	Math.round(totalvariance*100)/100;
                if (totalvariance < 0) {
                        $("#total_variance").html('<span class="negative-variance">$ '+totalvariance+'</span>');
                } else {
                        $("#total_variance").html('<span class="positive-variance">$ '+totalvariance+'</span>');
                }
            });
        }

        function calculateVariance(theItem) {
            var totalValue = Math.round(parseFloat($("#"+theItem).val())*100)/100;
            var theRPRO = Math.round(parseFloat($("#rpro"+theItem).val())*100)/100;
            var variance = Math.round((totalValue - theRPRO)*100)/100;
                if (variance < 0) {
                        $("#variance"+theItem).html('<span class="negative-variance">$ '+variance+'</span>');
                } else if (isNaN(variance)) {
                        $("#variance"+theItem).html('<span class="">$ 0.00</span>');
                } else {
                        $("#variance"+theItem).html('<span class="positive-variance">$ '+variance+'</span>');
                }
        }
        
        function calculateGCVariance(gc_Item) {
            var actualValue = Math.round(parseFloat( $("#gc_"+gc_Item).val() )*100)/100;
            var rproValue = Math.round(parseFloat( $("#gc_rpro_"+gc_Item).val() )*100)/100;
            var gc_variance = Math.round((actualValue - rproValue)*100)/100;
                if (gc_variance < 0) {
                        $("#gc_variance_"+gc_Item).html('<span class="negative-variance">$ '+gc_variance+'</span>');
                } else if (isNaN(gc_variance)) {
                        $("#gc_variance_"+gc_Item).html('<span class="">$ 0.00</span>');
                } else {
                        $("#gc_variance_"+gc_Item).html('<span class="positive-variance">$ '+gc_variance+'</span>');
                }
        }

    function populate(array) {
        document.getElementById("employee_name").value = array.header['employee_name'];
        document.getElementById("cash").value = array.items.Cash[1];
        document.getElementById("rprocash").value = array.items.Cash[0];
        document.getElementById("visa").value = array.items.VISA[0];
        document.getElementById("rprovisa").value = array.items.VISA[0];
        document.getElementById("mc").value = array.items.MC[0];
        document.getElementById("rpromc").value = array.items.MC[0];
        document.getElementById("discover").value = array.items.DISCOVER[0];
        document.getElementById("rprodiscover").value = array.items.DISCOVER[0];
        document.getElementById("amex").value = array.items.AMEX[0];
        document.getElementById("rproamex").value = array.items.AMEX[0];
        document.getElementById("debit").value = array.items.DEBIT[0];
        document.getElementById("rprodebit").value = array.items.DEBIT[0];
        document.getElementById("ge-money").value = array.items.GE_Capital[1];
        document.getElementById("rproge-money").value = array.items.GE_Capital[0];
        document.getElementById("amazon").value = array.items.Amazon[0];
        document.getElementById("rproamazon").value = array.items.Amazon[0];
        document.getElementById("paypal").value = array.items.Paypal[1];
        document.getElementById("rpropaypal").value = array.items.Paypal[0];
        document.getElementById("wire").value = array.items.Wire[1];
        document.getElementById("rprowire").value = array.items.Wire[0];
        document.getElementById("misc").value = array.items.Misc[1];
        document.getElementById("rpromisc").value = array.items.Misc[0];
        document.getElementById("gc_loaded").value = array.items.Gift_Cards_Loaded[0];
        document.getElementById("gc_rpro_loaded").value = array.items.Gift_Cards_Loaded[0];
        document.getElementById("gc_redeemed").value = array.items.Gift_Cards_Redeemed[0];
        document.getElementById("gc_rpro_redeemed").value = array.items.Gift_Cards_Redeemed[0];
        document.getElementById("service_head_count").value = array.checklist['service_head_count'];
        document.getElementById("service_labor_completed").value = array.checklist['service_labor_completed'];
        document.getElementsByClassName("comments")[0].value = (array.header['note'] !== "undefined") ? array.header['note'] : '';
        document.getElementsByClassName("huddle")[0].value =  (array.checklist.huddle_topic !== "undefined") ? array.checklist.huddle_topic : '';
    }
    
        /* submit the form */
        $(document).on("click", ".submit", function(e) {
            e.preventDefault();
            /* Make sure required form data is present */
            if ( !errorFound ) {
                /* Submit the form */
                $.ajax({
                    type: "POST",
                    url: "process.php",
                    data: $("#reconform").serialize()
                })
                .done(function(response) {
                    $(".status").html(response);
                    $('.submit').prop("disabled",true).html("Done!");
                });
            }
        });
});