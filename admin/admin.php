<?php

include('../configuration.php');
include('../functions.php');
// Connect to the DB
$con = mysql_connect($configuration['host'],$configuration['user'],$configuration['pass']) or die (mysql_error());
$db  = mysql_select_db($configuration['db'],$con) or die(mysql_error());
if (empty($_GET['store_id']) || !is_numeric($_GET['store_id'])) {
	$defaultStore	=	"1";		// default store ID
} else {
	$defaultStore	=	$_GET['store_id'];
}

if (!empty($_GET['view_id'])) { 
	// This is a view/update action so we are grabbing data
	
	// create the information array - will be filled up with the amounts etc
	// format: $items['item type']['term number']	=   amount of $
	$items	=	array();
	$id	=	$_GET['view_id'];
	$query	=	"Select * from headers where id = $id";
	$info	=	mysql_query($query) or die(mysql_error().' query: '.$query);
	$header	=	mysql_fetch_assoc($info);
	
        $items['user']	 =   $header['employee_name'];

	
	// Prepare form vars
	$items['comment']   =	$header['note'];
	$defaultStore	    =	$header['store_id'];
	$showdate	    =	explode('-', $header['date']);
	$items['date']	    =	$showdate[1].'/'.$showdate[2].'/'.$showdate[0];
	
	// Get more info now on all cash items
	$query		=	"select * from items where header_id = $id";
	$headerData	=	mysql_query($query) or die(mysql_error());
	while	($r	=	mysql_fetch_assoc($headerData)) { 
		$query	=	"select name from itemtypes where id = ".$r['itemtype_id'];
		$name	=	mysql_query($query) or die(mysql_error() . $query);
		$result	=	mysql_fetch_assoc($name);
		$name	=	$result['name'];
		$items[$name][$r['term_num']] = $r['amount'];
	}
	foreach($items as $itemname => $array) {
		$count	=	0;
		// If there are no amounts in here, it means there were no 
		// transactions saved for this one:
		if (!is_array($array)) { 
		$items[$itemname]['total'] = 0;
		continue;
		}
		
		foreach($array as $term => $amount) {
			if ($term == 0) continue;
			$count += $amount;
		}
		$items[$itemname]['total'] = $count;
	}

	// grab the checklist items
	$date = $header['date'];
	$query	        =	"select * from checklists where store_id = $defaultStore and date = '$date'";
	$checklist	=	mysql_query($query) or die(mysql_error().$query);
	$checklist	=	mysql_fetch_assoc($checklist);
	
	$populate	=	1;
	
} else {
	
	$populate	=	0;
}
// grab selected store
$query	        =	"Select * from stores where id = '".$defaultStore."'";
$selectedStore	=	mysql_query($query);
$selectedStore	=	mysql_fetch_assoc($selectedStore);

// grab active stores
$query	        =	"Select * from stores where active = 1";
$allStores	=	mysql_query($query);

// Grab all item types except giftcards
$query	        =	"Select * from itemtypes where active = 1 and id not in (2,3,4,5,6)";
$allTypes	=	mysql_query($query);

// grab gift card item types
$query          =       "Select * from itemtypes where id in (2,3)";
$giftcards      =       mysql_query($query);

// Labor Goal per person (in hundreds) indexed by store ID
$labor_goal = array(
		    "1"  => 225,
		    "2"  => 225,
		    "3"  => 225,
		    "4"  => 250,
		    "5"  => 225,
		    "6"  => 225,
		    "7"  => 200,
		    "8"  => 225,
		    "9"  => 200,
		    "10" => 200,
		    "11" => 200,
		    "17" => 200,
		    "19" => 200,
		    "20" => 0
		    );

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8" />
  <title> Recon Reloaded </title>
  <link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
  <link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.10.3.custom.min.css" />
  <script type="text/javascript" src="../js/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.10.3.custom.min.js"></script>
<script> 
sfHover = function() {
	var sfEls = document.getElementById("navbar").getElementsByTagName("li");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onmouseover=function() {
			this.className+=" hover";
		}
		sfEls[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" hover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfHover);
</script>
<script> 
	$(document).ready(function() {
	
			
                $("#datepicker").datepicker();
		populate  =  <?php echo $populate; ?> +"";
		
		$(document).on("change", '.matrix', function() {
			theItem		=	$(this).attr('rel');
			curValue	=	parseFloat($(this).val());
			curValue	=	Math.round(curValue*100)/100;
			totalID		=	$("#total"+theItem);
			totalValue	=	0;
			errorFound	=	false;
			$('.matrix').each(function() {
				if($(this).attr('rel') == theItem) {
					if (isNaN($(this).val())) {
						alert ('not a valid number (use the 1.23 format)');
						$(this).css('background-color','#ffa1a1');
						$(this).focus();
						errorFound	=	true;
					} else {
						thisValue = parseFloat($(this).val());	
						totalValue += Math.round(thisValue*100)/100;	
						$(this).css('background-color','white');
					}			
				}
			});
			
			if (!errorFound) {
				totalID.html(totalValue);
				calculateVariance(theItem);
				lastClass = $(this).attr('class').split(' ').slice(-1);
				calculateTotal(lastClass);
			}
		});
		
		$(document).on("change", '.gc_matrix', function() {
			theItem		=	$(this).attr('rel');
			curValue	=	parseFloat($(this).val());
			curValue	=	Math.round(curValue*100)/100;
			totalID		=	$("#gc_total"+theItem);
			totalValue	=	0;
			errorFound	=	false;
			$('.gc_matrix').each(function() {
				if($(this).attr('rel') == theItem) {
					if (isNaN($(this).val())) {
						alert ('not a valid number (use the 1.23 format)');
						$(this).css('background-color','#ffa1a1');
						$(this).focus();
						
						errorFound	=	true;
					} else {
						thisValue = parseFloat($(this).val());	
						totalValue += Math.round(thisValue*100)/100;	
						$(this).css('background-color','white');
					}			
				}
			});
			
			if (!errorFound) {
				totalID.html(totalValue);
				calculateGCVariance(theItem);
				lastClass = $(this).attr('class').split(' ').slice(-1);
				calculateGCTotal(lastClass);
			}
		});

		function calculateGCVariance(theItem) {
		totalValue		=	parseFloat($("#gc_total"+theItem).text());	
		totalValue		=	Math.round(totalValue*100)/100;	
		theRPRO			=	parseFloat($("#gc_rpro"+theItem).val());
		theRPRO			=	Math.round(theRPRO*100)/100;
		variance		=	totalValue - theRPRO;
			if (variance < 0) {
				$("#gc_variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
			} else {
				$("#gc_variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
			}
		}
		
		function calculateGCTotal(col) {
			totalColValue	        =	0;
			grandTotal		=	0;
			rPRO			=	0;
			variance		=	0;
			$('.'+col).each(function() {
				totalColValue   +=      parseFloat($(this).val());
			});
			$('.gc_totalcol').each(function() {
				grandTotal	+=	parseFloat($(this).text());
			});
			$('.gc_rpro').each(function() {
				rPRO		+=	parseFloat($(this).val());
			});
			$('.gc_variance').each(function() {
				variance	+=	parseFloat($(this).text().substring(2));
			});
			totalColValue 	        =       Math.round(totalColValue*100)/100;;
			grandTotal		=	Math.round(grandTotal*100)/100;
			rPRO			=	Math.round(rPRO*100)/100;
			variance		=	Math.round(variance*100)/100;

			if (variance < 0) {
				$("#gc_variancethetotal").html('<font color="red"><b>$ '+variance+'</b></font>');
			} else {
				$("#gc_variancethetotal").html('<font color="black"><b>$ '+variance+'</b></font>');
			}
		}

		$(document).on("change",'.gc_rpro', function() {
			if (isNaN($(this).val())) {
				alert ('not a valid number (use the 1.23 format)');
				$(this).css('background-color','#ffa1a1');
				$(this).focus();
			} else {
				theItem		=	$(this).attr('rel');
				totalValue	=	parseFloat($("#gc_total"+theItem).text());
				theRPRO		=	parseFloat($(this).val());
				variance	=	totalValue - theRPRO;
				if (variance < 0) {
					$("#gc_variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
				} else {
					$("#gc_variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
				}
			}
		});

		function calculateVariance(theItem) {
		totalValue		=	parseFloat($("#total"+theItem).text());	
		totalValue		=	Math.round(totalValue*100)/100;	
		theRPRO			=	parseFloat($("#rpro"+theItem).val());
		theRPRO			=	Math.round(theRPRO*100)/100;
		variance		=	totalValue - theRPRO;
			if (variance < 0) {
				$("#variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
			} else {
				$("#variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
			}
		}
		function calculateTotal(col) {
			totalColValue	        =	0;
			grandTotal		=	0;
			rPRO			=	0;
			variance		=	0;
			$('.'+col).each(function() {
				totalColValue   +=      parseFloat($(this).val());
			});
			$('.totalcol').each(function() {
				grandTotal	+=	parseFloat($(this).text());
			});
			$('.rpro').each(function() {
				rPRO		+=	parseFloat($(this).val());
			});
			$('.variance').each(function() {
				variance	+=	parseFloat($(this).text().substring(2));
			});
			totalColValue 	        =       Math.round(totalColValue*100)/100;;
			grandTotal		=	Math.round(grandTotal*100)/100;
			rPRO			=	Math.round(rPRO*100)/100;
			variance		=	Math.round(variance*100)/100;

			$('#total'+col).text('$ '+totalColValue);
			$('#totalthetotal').text(grandTotal);
			$('#rprototal').text('$ '+rPRO);
			//$('#variancethetotal').text('$ '+variance);
			if (variance < 0) {
				$("#variancethetotal").html('<font color="red"><b>$ '+variance+'</b></font>');
			} else {
				$("#variancethetotal").html('<font color="black"><b>$ '+variance+'</b></font>');
			}
		}
		
		
		function calculateAll() {
			$('.matrix').each(function() {
				if	($(this).val()	== '') {
					$(this).val('0.00');
				}
			});
			calculateTotal(1);
			calculateTotal(2);
			calculateTotal(3);
			calculateTotal(4);
			
			$('.rpro').each(function() {
			       theItem		=	$(this).attr('rel');
			       totalValue	=	parseFloat($("#total"+theItem).text());
			       theRPRO		=	parseFloat($(this).val());
			       variance	        =	totalValue - theRPRO;
			       variance	        =	Math.round(variance*100)/100;
			       if (variance < 0) {
				       $("#variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
			       } else {
				       $("#variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
			       }
			       calculateTotal(1);
		       }); 
		}
		
		$(document).on("change", '.rpro', function() {
			if (isNaN($(this).val())) {
				alert ('not a valid number (use the 1.23 format)');
				$(this).css('background-color','#ffa1a1');
				$(this).focus();
			} else {
				theItem		=	$(this).attr('rel');
				totalValue	=	parseFloat($("#total"+theItem).text());
				theRPRO		=	parseFloat($(this).val());
				variance	=	totalValue - theRPRO;
				if (variance < 0) {
					$("#variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
				} else {
					$("#variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
				}
				calculateTotal(1);
			}
		});

		$(document).on("change", '.report_date', function() {
			theDate		=	$(this).val();
			theStore	=	$('#curstore').val();
			$.ajax({url: "update.php", type: "POST", async: false, data:{date: theDate, store: theStore}})
			.done(function(data) {
				if (data === 'new form') {
					if (shown == 0 && populate == 0) {
						$("#tablediv").css('display','block');
						$("#tooltip").fadeIn(750).delay(7000).fadeOut(750);
						shown = 1;
					}
				}
				else {
					 location="admin.php?view_id="+data;
				}
			});
		});
		var shown = 0;
		<?php if (!empty($items)) { echo 'calculateAll();'; } ?>
		
		$(document).on("blur", "#service_head_count", function () {
			var rc = false;
			var v = $("#service_head_count").val();
			/* force whole or half numbers */
			rc = ( ( parseInt( v * 2 ) / 2 ) == v );
			if ( rc )
			{
				$("#service_head_count").css({"background-color":"green"});
				$("#service_labor_goal").val( CurrencyFormatted (v * <?php echo $labor_goal[$selectedStore['id']]; ?>) );
				$("#shc_formError").text("");
			} else {
				$("#shc_formError").text("must be whole or half").css("color", "red");
				$("#service_head_count").css({"background-color":"red"});
			}
		});
		
		$(document).on("blur", "#service_labor_completed", function()
		{
			setSLCColor();
		});
		
		function  setSLCColor()
		{
			var slg = $("#service_labor_goal").val();
			var slc = $("#service_labor_completed").val();
			if ( slg > slc )
			{
				$("#service_labor_goal").css({"background-color":"red"});
			} else {
				$("#service_labor_goal").css({"background-color":"green"});
			}
		}
		
		function CurrencyFormatted(amount)
		{
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


		$(document).on("click", ".submit", function()
		{
			var dataForm = $("#reconform").serialize();
			$.ajax({
				type: "POST",
				url: "process.php",
				data: dataForm,
				success: function(response) {
                                    $("p#statusText").html(response);
                                    $("#submitStatus").css({border:'10px solid #33AD33'});
                                    $("#submitStatus").overlay({
                                     
                                            // custom top position
                                            top: 260,
                                     
                                            // some mask tweaks suitable for facebox-looking dialogs
                                            mask: {
                                     
                                                    // you might also consider a "transparent" color for the mask
                                                    color: '#99FF99',
                                     
                                                    // load mask a little faster
                                                    loadSpeed: 100,
                                     
                                                    // very transparent
                                                    opacity: 0.95
                                            },
                                     
                                            // disable this for modal dialog-type of overlays
                                            closeOnClick: true,
                                     
                                            // load it immediately after the construction
                                            load: true
                                     
                                    });
				    $('.submit').css("display","none");
				}
			});
			return false;
		});
});
	
</script>
</head>
<body>
    <div id="container">
            <form id="reconform">
                <input type="hidden" name="store_id" id="curstore" value="<?php echo $selectedStore['id'];?>" />
                <input type="hidden" name="action" value="<?php if (!empty($items)) { echo 'edit'; }?>" />
                <input type="hidden" name="headers_id" value="<?php if (!empty($items)) { echo $id; }?>" />
		<div class="status"></div>
		<div id="i">
			<ul id="navbar">
				<li class="size-border"><a href="#"><span><?php echo $selectedStore['name'];?><br /> </span></a>
					<ul>
						<?php
						// Generate the store popup
						while($r  =  mysql_fetch_assoc($allStores)) { ?>
							<li>
								<a href="admin.php?store_id=<?php echo $r['id'];?>"> <?php echo $r['name'];?> </a>
							</li>					
						<?php	} ?>
					</ul>
				</li>
			</ul>
                        <br />
			<p><b>Date: </b> <input id="datepicker" type="text" class="report_date" name="report_date" value="<?php if (!empty($items)) { echo $items['date'];}?>" /></p>
			<br /><br />
		</div>
		<div style="clear:both;">&nbsp;</div>
		<div id="tablediv" style="<?php if (!empty($items)) { echo 'display:block'; }?>">
			<div id="tablecontent">
				<table id="table" style="width:90%" style="float:left;margin-left:50px;">
					<thead>
						<tr>
						<td width="auto">&nbsp;</td>
						<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { ?>
							<td width="auto" style="<? if ($i==1) { echo 'margin-left:auto;';}?>">Actuals</td>
						<?php } ?>
						<td width="auto" >RPro</td>
						<td width="auto" >Variance</td>
						</tr> 
					</thead>
					<tbody> 
					<?php while ($r	= mysql_fetch_assoc($allTypes) ) { ?>
					<?php $totals = 0; ?>
						<tr>
							<td width="auto"><strong><?php echo $r['name']; ?></strong></td>
							<?php $r['newname'] = friendly_string($r['name']); ?>
						<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { 
							if(($r['newname'] == 'paper-checks' || $r['newname'] == 'cash') && $i > 1) { ?>
								<td width="auto"></td>
							<?php continue; } else { ?> 
							<td width="auto" style="<? if ($i==1) { echo 'margin-left:auto;';}?>">$<input class="<?php echo "matrix ".$i;?>" rel="<?php echo $r['newname']; ?>" name="item[<?php echo $r['name'];?>][]" type="text" size="8"
							value="<?php if (!empty($items)) {echo $items[$r['name']][$i]; } else { echo '0.00';}?>" /></td>
							<?php continue; }	?>
							<td width="auto" style="<? if ($i==1) { echo 'margin-left:auto;';}?>">$<input class="<?php echo "matrix ".$i;?>" rel="<?php echo $r['newname']; ?>" name="item[<?php echo $r['name'];?>][]" type="text" size="8" 
							value="<?php if (!empty($items)) { if (!empty($items[$r['name']][$i])) {echo $items[$r['name']][$i]; } else { echo '0.00';}} else { echo '0.00';}?>" /></td>
						<?php } ?>
							<td width="auto" >$<input id="rpro<?php echo $r['newname'];?>" name="rpro[<?php echo $r['name'];?>]" class="rpro" rel="<?php echo $r['newname'];?>" type="text" size="8"
							value="<?php if (!empty($items)) { if($items[$r['name']][0] != '') { echo $items[$r['name']][0]; } else { echo '0.00';}}else{echo '0.00';}?>" /></td>
							<td width="auto" id="variance<?php echo $r['newname'];?>" class="variance">$ 0.00</td>						
						</tr>
					<?php  } ?>
						<tr><td colspan="5"><hr style="width:95%;" /></td></tr>
						<tr class="totals">
							<td width="auto"><strong>TOTALS</strong></td>
						<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { ?>
							<td id="total<?php echo $i;?>" width="auto" style="<? if ($i==1) { echo 'margin-left:auto;';}?>">$ 0.00<input type="hidden" class="matrix" rel="thetotal" name="total[term<?php echo $i;?>]" /></td>
						<?php } ?>
							<td width="auto" id="rprototal">$ 0.00</td>
							<td width="auto" id="variancethetotal">$ 0.00</td>						
						</tr>
						<tr><td colspan=5>&nbsp;</td></tr>

					<?php while ($gc = mysql_fetch_assoc($giftcards) ) { ?>
					        <tr>
							<td width="auto"><strong><?php echo $gc['name']; ?></strong></td>
							<?php $gc['newname'] = friendly_string($gc['name']); ?>
							<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { ?>
							    <td width="auto" style="<? if ($i==1) { echo 'margin-left:auto;';}?>">$<input class="<?php echo "gc_matrix ".$i;?>" rel="<?php echo $gc['newname']; ?>" name="item[<?php echo $gc['name'];?>][]" type="text" size="8" value="<?php if (!empty($items)) { if (!empty($items[$gc['name']][$i])) {echo $items[$gc['name']][$i]; } else { echo '0.00';}} else { echo '0.00';}?>" /></td>
						        <?php } ?>
							<td width="auto" >$<input id="gc_rpro<?php echo $gc['newname'];?>" name="gc_rpro[<?php echo $gc['name'];?>]" class="gc_rpro" rel="<?php echo $gc['newname'];?>" type="text" size="8" value="<?php if (!empty($items)) { if($items[$gc['name']][0] != '') { echo $items[$gc['name']][0]; } else { echo '0.00';}}else{echo '0.00';}?>" /></td>
							<td width="auto" id="gc_variance<?php echo $gc['newname'];?>" class="gc_variance">$ 0.00</td>						
						</tr>
					
					<?php } ?>
					</tbody>
				</table>
				<div style="clear:both;"></div>
				<div id="textboxes">
					<div class="float_left">
						<h2>Comments:</h2>
						<textarea rows="8" cols="45" name="comment"><?php if (!empty($items)) { echo $header['note']; }  ?></textarea>
					</div>
					<div class="float_right">
						<h2>Huddle Topic:</h2>
						<textarea rows="8" cols="45" name="huddle"><?php if (!empty($items)) { echo $checklist['huddle_topic']; } ?></textarea>
						<br />
					</div>
				</div>
				<div style="clear:both;"></div>
				<div id="restoptions">
					<table style="text-align:center;margin-left:80px;">
						<tr>
							<td align="center" style="width:250px;">
								<label>Service Head count </label><br /><span id="shc_formError"></span>
								<input type="text" size="15" name="headcount" id="service_head_count" value="<?php if (!empty($items)) { echo $checklist['service_head_count']; } ?>" />
							</td>
							<td align="center" style="width:250px;">
								<label>Service Labor completed:</label><br />
								<input type="text" size="15" name="labor_completed" id="service_labor_completed" value="<?php if (!empty($items)) { echo $checklist['service_labor_completed']; } ?>" />
							</td>
							<td align="center" style="width:250px;">
								<label>Service Labor goal</label><br />
								<input readonly type="text" size="15" name="laborgoal" id="service_labor_goal" value="<?php if (!empty($items)) { echo $checklist['service_head_count'] * 200; } ?>" />
							</td>
						</tr>
					</table>
				</div>
				<div id="checkboxes" style="text-align:center;">
					<table cellspacing="10" width="100%">
						<tr align="center">
							<td><label>A/C Off</label><br /><input type="checkbox" name="close_ac" <?php if (!empty($items)) { if ($checklist['ac_off'] == 1) { echo 'checked="checked"';} }?> /></td>
							<td><label>A/V Off</label><br /><input type="checkbox" name="close_av"  <?php if (!empty($items)) { if ($checklist['av_off'] == 1) { echo 'checked="checked"';} }?>/></td>
							<td><label>Close RPro</label><br /><input type="checkbox" name="close_rpro"  <?php if (!empty($items)) { if ($checklist['close_rpro'] == 1) { echo 'checked="checked"';} }?>/></td>
							<td width="180"><input type="checkbox" name="bike_sales_reviewed"  <?php if (!empty($items)) { if ($checklist['bike_sales_reviewed'] == 1) { echo 'checked="checked"';} }?>/><label> Today's bike sales reviewed?</label></td>
							<td width="180"><input type="checkbox" name="bike_receipts_accurate"  <?php if (!empty($items)) { if ($checklist['bike_receipts_accurate'] == 1) { echo 'checked="checked"';} }?>/><label> Reviewed all bike receipts for accuracy?</label></td>
						</tr>
					</table>
				</div>
				<p id="reportedBy"><b>Reported By:&nbsp;&nbsp;&nbsp;</b> <input type="text" size="20" name="username" id="employee_name" class="validate[required,custom[onlyLetterNumber]]" data-prompt-position="centerRight" value="<?php if (!empty($items)) { echo $header['employee_name']; }?>" /></p>
				<div class="status"></div>
				<input id="submit_recon" class="submit" type="button" value="<? if (!empty($items)) { echo 'Update report';} else { echo 'Submit report'; }?>" />
		</div>
            </form>
    </div>
<!-- status dialog --> 
<div id="submitStatus"> 
	<div> 
		<h2>Recon</h2> 
		<p id="statusText"></p> 
		<!-- yes/no buttons --> 
		<p> 
			<button class="close"> Close </button> 
		</p> 
	</div> 
</div>
</body>
</html>