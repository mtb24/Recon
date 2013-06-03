<!DOCTYPE HTML>
<html>
<head>
<title>MB Nightly Recon</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
<link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.4.custom.css" />
<link type="text/css" rel="Stylesheet" href="css/jquery.validity.css" />
<script src="js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="js/jquery.tools.min.js"></script>
<script type="text/javascript" src="js/jquery.validity.js"></script>
<script type="text/javascript"> 

	$(document).ready(function() {

                var d = new Date(),
                    month = d.getMonth() + 1,
                    day = d.getDate(),
                    year = d.getFullYear(),
		    theDate = month + "-" + day + "-" + year,
		    errorFound = false;
                $("#report_date, #date").val(theDate);
                
                $(document).on("change", "#store_id", function(){
			var theStore = $('#store_id option:selected').attr('value');
		});

		$(document).on("change", "input[type='text']", function() {
			var theClass = $(this).attr('class'),
			    theItem  = $(this).attr('rel'),
			    errorFound = false,
			    curValue = parseFloat( $(this).val() );
			curValue = Math.round(curValue*100)/100;
			
			$("."+theClass).each(function() {
				if($(this).attr('rel') === theItem) {
					/* input is not a valid number */
					if (isNaN(curValue)) {
						//alert ('not a valid number (use the 1.23 format)');
						$(this).css('background-color','#ffa1a1');
						//$(this).val('').focus();
						errorFound = true;
					} else { /* input is valid */
						$(this).css('background-color',''); /* reset color, if previous error */
						$("input[class='col4'][rel='"+theItem+"']").val( $(this).val() ); /* populate disabled fields with data */
					}			
				}
			});
			
			if (!errorFound) {
				if(theClass === 'gc_matrix' || theClass === 'gc_rpro') {
					calculateGCVariance(theItem);
				} else {
					calculateVariance(theItem);
					calculateTotal();
				}
			}
		});

		function calculateVariance(theItem) {
			totalValue		=	parseFloat($("#"+theItem).val());	
			totalValue		=	Math.round(totalValue*100)/100;	
			theRPRO			=	parseFloat($("#rpro"+theItem).val());
			theRPRO			=	Math.round(theRPRO*100)/100;
			variance		=	totalValue - theRPRO;
			if (variance < 0) {
				$("#variance"+theItem).html('<font color="red"><b>$ '+Math.round(variance*100)/100+'</b></font>');
			} else if (isNaN(variance)) {
				$("#variance"+theItem).html('<font color="black">$ 0.00</font>');
			} else {
				$("#variance"+theItem).html('<font color="black"><b>$ '+Math.round(variance*100)/100+'</b></font>');
			}
		}
		function calculateTotal() {
			totalactual	=	0;
			totalrPRO	=	0;
			totalvariance	=	0;

			$('.col1, .col4').each(function() {
				v1 = parseFloat($(this).val());
				totalactual	+=	(isNaN(v1)) ? 0 : v1;
			});
			$('.col2').each(function() {
				v2 = parseFloat($(this).val());
				totalrPRO	+=	(isNaN(v2)) ? 0 : v2;
			});
			$('.col3').each(function() {
				v3 = parseFloat($(this).text().substring(2));
				totalvariance	+=	(isNaN(v3)) ? 0 : v3;
			});
			totalactual 	=       Math.round(totalactual*100)/100;
			totalrPRO	=	Math.round(totalrPRO*100)/100;
			totalvariance	=	Math.round(totalvariance*100)/100;

			$('#total_actual').text('$ '+totalactual);
			$('#total_rpro').text('$ '+totalrPRO);
			if (totalvariance < 0) {
				$("#total_variance").html('<font color="red"><b>$ '+totalvariance+'</b></font>');
			} else {
				$("#total_variance").html('<font color="black"><b>$ '+totalvariance+'</b></font>');
			}
			
		}
		
		function calculateGCVariance(gc_Item) {
			actualValue		=	$("#gc_"+gc_Item).val();
			actualValue		=	Math.round(parseFloat(actualValue)*100)/100;
			rproValue		=	$("#gc_rpro_"+gc_Item).val();
			rproValue		=	Math.round(parseFloat(rproValue)*100)/100;
			gc_variance		=	actualValue - rproValue;

			if (gc_variance < 0) {
				$("#gc_variance_"+gc_Item).html('<font color="red"><b>$ '+gc_variance+'</b></font>');
			} else if (isNaN(gc_variance)) {
				$("#gc_variance_"+gc_Item).html('<font color="black">$ 0.00</font>');
			} else {
				$("#gc_variance_"+gc_Item).html('<font color="black"><b>$ '+gc_variance+'</b></font>');
			}
		}
		

		// Labor Goal per person (in hundreds) indexed by store ID
		var labor_goal = [];
		labor_goal["1"]  = 225;
		labor_goal["2"]  = 225;
		labor_goal["3"]  = 225;
		labor_goal["4"]  = 250;
		labor_goal["5"]  = 225;
		labor_goal["6"]  = 225;
		labor_goal["7"]  = 200;
		labor_goal["8"]  = 225;
		labor_goal["9"]  = 200;
		labor_goal["10"] = 200;
		labor_goal["17"] = 200;
		labor_goal["19"] = 200;
		labor_goal["20"] = 0;


		$(document).on("blur", "#service_head_count", function () {
			var wholeNumber = false;
			var v = $("#service_head_count").val();
			/* force whole or half numbers */
			wholeNumber = ( ( parseInt( v * 2 ) / 2 ) == v );
			if ( wholeNumber )
			{
				$("#service_head_count").css({"background-color":""}); /* reset color if previous error */
				$("#service_labor_goal").val( CurrencyFormatted (v * labor_goal[ $('#store_id option:selected').attr('value') ]) );
				$("#shc_formError").text(""); /* reset error message */
			} else {
				$("#shc_formError").text("Head Count must be whole number").css("color", "red");
				$("#service_head_count").css({"background-color":"red"});
			}
		});
		
		$(document).on("blur", "#service_labor_completed", function() {
			setServiceVariance();
		});
		
		function  setServiceVariance() {
			var serviceGoal = $("#service_labor_goal").val();
			var serviceCompleted = $("#service_labor_completed").val();
			var serviceVariance = serviceCompleted - serviceGoal;
			if (serviceVariance > 0) {
				$("#service_labor_variance").text('+'+serviceVariance).css({"color":"green"});
			} else {
				$("#service_labor_variance").text(serviceVariance).css({"color":"red"});
			}
		}
		
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

		function validInputs() {
			$.validity.start();
			$("#employee_name").require("Your name is required!").match(/[0-9a-fA-F ]/);
			$("#store_id").require("What Store?");
			$("#service_head_count").require("Whole number only").match("integer");
			var result = $.validity.end();
			return result.valid;
		};
		
		$(document).on("click", ".submit", function() {
			/* Make sure required form data is present */
			if (validInputs()) { 
				/* Make sure form hasn't already been submitted today */
				$.ajax({url: "ajaxDaily.php",
				        type: "POST",
					async: false,
					data:{date: theDate, store: $('#store_id option:selected').attr('value')},
				       	success: function(data) {
						/* If no prior submission for today */
						if (data == 'new') {
							/* Submit the form */
							var dataForm = $("#reconform").serialize();
							$.ajax({
								type: "POST",
								url: "processDaily.php",
								data: dataForm,
								success: function(response) {
									$("p#statusText").html(response);
									$("#popUpModal").css({border:'10px solid #33AD33',display:'block'});
									$("#popUpModal").overlay({
										top: 260,
										mask: {
											color: '#99FF99',
											loadSpeed: 100,
											opacity: 0.95
										},
										// load it immediately after the construction
										load: true
									});
									$('.submit').attr("disabled","true").css("background-color","#33AD33").val("Done!");
									$(document).on("click", "button.close", function(){
										$("#popUpModal").css({display:'none'});
									});
								}
							});
							//return false;
						} else {
							/* flag user to show form has already been submitted for today */
							$("p#statusText").html("Recon already submitted for "+$('#store_id option:selected').text()+" today!");
							$("#popUpModal").css({border:'10px solid #CD5555',display:'block'});
							$("#popUpModal").overlay({
								top: 260,
								mask: {
									color: '#99FF99',
									loadSpeed: 100,
									opacity: 0.95
								},
								// disable this for modal dialog-type of overlays
								//closeOnClick: true,
								// load it immediately after the construction
								load: true
							});
							$('.submit').attr("disabled","true").css("background-color","#CD5555").val("Already submitted for today");
							$(document).on("click", "button.close", function(){
								$("#popUpModal").css({display:'none'});
							});
						}
					}
			        });
			} else {
				// Some input is not valid
				$('.submit').css("background-color","#CD5555").val("Please fix errors!");
			}
		});
		
		/* flag user to show form has already been submitted for today */
		$("p#statusText").html(
				       "<p class='firstNotice'>NEW FORM! Some things you should know:</p><ul class='firstNoticeList'><li>You cannot change the date</li><li>Store Name is mandatory</li><li>Employee Name is mandatory</li><li>You can tab through the fields for speed!</li></ul>"
				       );
		$("#popUpModal").css({border:'10px solid red'});

		//$("#popUpModal").overlay({
		//	top: 260,
		//	mask: {
		//		color: '#99FF99',
		//		loadSpeed: 100,
		//		opacity: 0.95
		//	},
			// disable this for modal dialog-type of overlays
		//	closeOnClick: true,
			// load it immediately after the construction
		//	load: true
		//});
        });
	</script>
</head>
<body>
        <div id="container">
                <form id="reconform">
			<div class="status"></div>
			<div id="i">
			    <span id="pageHeading">Nightly Recon for</span>
				<select name="store_id" id="store_id">
						    <option value="">Select Store...</option>
						    <option value="1">San Rafael</option>
						    <option value="2">Sausalito</option>
						    <option value="3">Berkeley</option>
						    <option value="4">Palo Alto</option>
						    <option value="5">San Francisco</option>
						    <option value="6">Sacramento</option>
						    <option value="7">Los Gatos</option>
						    <option value="8">Petaluma</option>
						    <option value="9">Walnut Creek</option>
						    <option value="10">San Jose</option>
						    <option value="11">Pleasanton</option>
						    <option value="17">mikesbikes.com</option>
						    <option value="19">BikeSmart Wholesale</option>
						    <option value="20">Field Marketing</option>
				</select><br />
				<input id="date" type="text" name="date" value="" readonly="true" tabindex="-1" /><br />
				<input type="text" size="20" name="username" id="employee_name" value="" placeholder="Reported By:" />
			</div>
			<div id="tablediv">
				<table>
					<thead>
						<tr>
						<td width="150">&nbsp;</td>
						<td class="table_column" >Actual</td>
						<td class="table_column" >RPro</td>
						<td class="table_column" >Variance</td>
						</tr> 
					</thead>
					<tbody> 
																
						<tr>
							<td width="150"><strong>Cash</strong></td>
													 
							<td class="table_column actual">
								$<input id="cash" class="col1" rel="cash" name="item[Cash]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">
								$<input id="rprocash" class="col2" rel="cash" name="rpro[Cash]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancecash" class="variance table_column col3">$ 0.00</td>						
						</tr>
																
						<tr>
							<td width="150"><strong>VISA</strong></td>
													 
							<td class="table_column actual">
								$<input id="visa" class="col4" rel="visa" name="item[VISA]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">
								$<input id="rprovisa" class="col2" rel="visa" name="rpro[VISA]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>MC</strong></td>
													 
							<td class="table_column actual">$<input id="mc" class="col4" rel="mc" name="item[MC]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$<input id="rpromc" name="rpro[MC]" class="col2" rel="mc" type="text" size="8"	value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>DISCOVER</strong></td>
													 
							<td class="table_column actual">$<input id="discover" class="col4" rel="discover" name="item[DISCOVER]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$<input id="rprodiscover" name="rpro[DISCOVER]" class="col2" rel="discover" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>AMEX</strong></td>
													 
							<td class="table_column actual">$<input id="amex" class="col4" rel="amex" name="item[AMEX]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$<input id="rproamex" name="rpro[AMEX]" class="col2" rel="amex" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>DEBIT</strong></td>
													 
							<td class="table_column actual">$<input id="debit" class="col4" rel="debit" name="item[DEBIT]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$<input id="rprodebit" name="rpro[DEBIT]" class="col2" rel="debit" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>GE Capital</strong></td>
													 
							<td class="table_column actual">$<input id="ge-money" class="col1" rel="ge-money" name="item[GE Capital]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">$<input id="rproge-money" name="rpro[GE Capital]" class="col2" rel="ge-money" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancege-money" class="variance table_column col3">$ 0.00</td>
						</tr>
																
						<tr>
							<td width="150"><strong>Amazon</strong></td>
													 
							<td class="table_column actual">$<input id="amazon" class="col4" rel="amazon" name="item[Amazon]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$<input id="rproamazon" name="rpro[Amazon]" class="col2" rel="amazon" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>Paypal</strong></td>
													 
							<td class="table_column actual">$<input id="paypal" class="col1" rel="paypal" name="item[Paypal]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">$<input id="rpropaypal" name="rpro[Paypal]" class="col2" rel="paypal" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancepaypal" class="variance table_column col3">$ 0.00</td>						
						</tr>
																
						<tr>
							<td width="150"><strong>Wire</strong></td>
													 
							<td class="table_column actual">$<input id="wire" class="col1" rel="wire" name="item[Wire]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">$<input id="rprowire" name="rpro[Wire]" class="col2" rel="wire" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancewire" class="variance table_column col3">$ 0.00</td>						
						</tr>
																
						<tr>
							<td width="150"><strong>Misc</strong></td>
													 
							<td class="table_column actual">$<input id="misc" class="col1" rel="misc" name="item[Misc]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">$<input id="rpromisc" name="rpro[Misc]" class="col2" rel="misc" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancemisc" class="variance table_column col3">$ 0.00</td>						
						</tr>
						<tr><td colspan="4"><hr style="width:95%;" /></td></tr>
						<tr class="totals">
							<td width="150"><strong>TOTALS</strong></td>
							<td class="table_column actual" id="total_actual">$ 0.00</td>
							<td class="table_column rpro" id="total_rpro">$ 0.00</td>
							<td class="table_column variance" id="total_variance">$ 0.00</td>						
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td width="150"><strong>Gift Cards Loaded</strong></td>
							<td class="table_column actual">
								$<input id="gc_loaded" class="gc_matrix" rel="loaded" name="item[Gift Cards Loaded]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">
								$<input id="gc_rpro_loaded" class="gc_rpro" rel="loaded" name="gc_rpro[Gift Cards Loaded]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td id="gc_variance_loaded" class="table_column variance">$ 0.00</td>						
						</tr>
					
						<tr>
							<td width="150"><strong>Gift Cards Redeemed</strong></td>
							<td class="table_column actual">
								$<input id="gc_redeemed" class="gc_matrix" rel="redeemed" name="item[Gift Cards Redeemed]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td class="table_column rpro">
								$<input id="gc_rpro_redeemed" class="gc_rpro" rel="redeemed" name="gc_rpro[Gift Cards Redeemed]" type="text" size="8" value="0" placeholder="0.00" /></td>
							<td id="gc_variance_redeemed" class="table_column variance">$ 0.00</td>						
						</tr>
					
					</tbody>
				</table>
			</div>
			<div id="service">
				<span id="serviceHeading"><strong>Service</strong></span>  <span id="shc_formError"></span><br />
				<label for="headcount">Head count </label><input type="text" size="7" name="headcount" id="service_head_count" value="" />
				<label for="labor_completed">Labor completed</label><input type="text" size="7" name="labor_completed" id="service_labor_completed" value="" />
				<label for="laborgoal">Labor goal</label><input readonly type="text" size="7" name="laborgoal" id="service_labor_goal" value="0" tabindex="-1" />
				<label for="service_labor_variance">Under/Over</label><span id="service_labor_variance"></span>
			</div>
			<div id="textboxes">
				<textarea class="comments" rows="8" cols="45" name="comment" placeholder="Comments"></textarea>
				<textarea class="huddle" rows="8" cols="45" name="huddle" placeholder="This morning's huddle topic"></textarea>
			</div>
			<div class="status"></div>
			<input id="submit_recon" class="submit" type="button" value="Submit Report" />
                </form>
        </div>
	<!-- status dialog --> 
	<div id="popUpModal"> 
		<div> 
			<h2>Recon</h2> 
			<p id="statusText"></p> 
			<!-- yes/no buttons --> 
			<p><button class="close"> Close </button></p> 
		</div>
	</div>
</body>
</html>