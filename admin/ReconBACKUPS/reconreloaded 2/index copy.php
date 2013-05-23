<?php

include('functions.php');

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
	
	// Prepare form vars
	$items['comment']   =	$header['note'];
	$items['date']	    =	$header['date'];
	$items['user']	    =	$header['employee_id'];
	$defaultStore	    =	$header['store_id'];
	$header		    =	$header['store_id'];
	$reportDate	    =	$items['date'];
	$showdate	    =	explode('-',$reportDate);
	$showdate	    =	$showdate[1].'/'.$showdate[2].'/'.$showdate[0];
	$items['date']	    =	$showdate;

	// grab the checklist
	$query	        =	"select * from checklists where store_id = $defaultStore and date = '$reportDate'";
	$checklist	=	mysql_query($query) or die(mysql_error().$query);
	$checklist	=	mysql_fetch_assoc($checklist);
	$checklist_id	=	$checklist['id'];
	
	// Now grab the zone checklist
	$query	        =	"select * from checklistszones where checklist_id = '$checklist_id'";
	$zonePeople	=	mysql_query($query) or die(mysql_error().$query);
	$zoneEmps       =       array();
		while($r	=	mysql_fetch_assoc($zonePeople)) {
			$zoneEmps[$r['zonename_id']]	=	$r['employee_id'];
		}
	if (empty($zoneEmps)) {
		unset($zoneEmps);
	}
	
	// Get more info now on all cash items
	$query		=	"select * from items where header_id = $id";
	$headerData	=	mysql_query($query) or die(mysql_error());
	while	($r	=	mysql_fetch_assoc($headerData)) { 
		$query	=	"select name from itemtypes where id = ".$r['itemtype_id'];
		$name	=	mysql_query($query) or die(mysql_error() . $query);
		$result	=	mysql_fetch_assoc($name);
		$name	=	$result['name'];
		$items[$name][$r['term_num']]	=	$r['amount'];
	}
	foreach($items as $itemname => $array) {
		$count	=	0;
		// If there are no amounts in here means there were no 
		// Transactions saved for this one:
		if (!is_array($array)) { 
		$items[$itemname]['total']	=	0;
		continue;
		}
		
		foreach($array as $term => $amount) {
			if ($term == 0) continue;
			$count += $amount;
		}
		$items[$itemname]['total']	=	$count;
	}
	
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

// Grab all item types
$query	        =	"Select * from itemtypes where active = 1";
$allTypes	=	mysql_query($query);

// Find all employees in this store
$empQuery	=	"select * from employees where ( active = true ) && id in (" .
                                                    "select employee_id from storesemployees " .
                                                        "where ( ( store_id = " .$selectedStore['id'] .
                                                            ") && ( active = true ) )) order by firstname, lastname";

// Grab all Zonenames
$query	        =	"Select * from zonenames where active = 1";
$allZones	=	mysql_query($query);

?>
<html>
<head>
<title> Recon Reloaded </title>
<link href="css/stylesheet.css" rel="stylesheet" type="text/css" />
<link href="css/ui-lightness/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
 <!--[if IE]>
<link href="css/stylesheet-IE.css" rel="stylesheet" type="text/css" />
<![endif]-->

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.datepicker.js"></script>
<!-- UI Tools: Tabs, Tooltip, Scrollable and Overlay -->
<script src="http://cdn.jquerytools.org/1.2.5/tiny/jquery.tools.min.js"></script>

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
	
		populate  =  <?php echo $populate;?>;
		$("#datepicker").datepicker();
		$('.matrix').change(function() {
			theItem		=	$(this).attr('rel');
			curValue	=	parseFloat($(this).attr('value'));
			curValue	=	Math.round(curValue*100)/100;
			totalID		=	$("#total"+theItem);
			totalValue	=	0;
			errorFound	=	false;
			$('.matrix').each(function() {
				if($(this).attr('rel') == theItem) {
					if (isNaN($(this).attr('value'))) {
						alert ('not a valid number (use the 1.23 format)');
						$(this).css('background-color','#ffa1a1');
						$(this).focus();
						
						errorFound	=	true;
					} else {
						thisValue = parseFloat($(this).attr('value'));	
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
		
		function calculateVariance(theItem) {
		totalValue		=	parseFloat($("#total"+theItem).text());	
		totalValue		=	Math.round(totalValue*100)/100;	
		theRPRO			=	parseFloat($("#rpro"+theItem).attr('value'));
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
				totalColValue   +=      parseFloat($(this).attr('value'));
			});
			$('.totalcol').each(function() {
				grandTotal	+=	parseFloat($(this).text());
			});
			$('.rpro').each(function() {
				rPRO		+=	parseFloat($(this).attr('value'));
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
				if	($(this).attr('value')	== '') {
					$(this).attr('value','0.00');
				}
			});
			calculateTotal(1);
			calculateTotal(2);
			calculateTotal(3);
			calculateTotal(4);
			
			$('.rpro').each(function() {
			       theItem		=	$(this).attr('rel');
			       totalValue	=	parseFloat($("#total"+theItem).text());
			       theRPRO		=	parseFloat($(this).attr('value'));
			       variance	=	totalValue - theRPRO;
			       variance	=	Math.round(variance*100)/100;
			       if (variance < 0) {
				       $("#variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
			       } else {
				       $("#variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
			       }
			       calculateTotal(1);
		       }); 
		}


		
		$('.rpro').change(function() {
			if (isNaN($(this).attr('value'))) {
				alert ('not a valid number (use the 1.23 format)');
				$(this).css('background-color','#ffa1a1');
				$(this).focus();
			} else {
				theItem		=	$(this).attr('rel');
				totalValue	=	parseFloat($("#total"+theItem).text());
				theRPRO		=	parseFloat($(this).attr('value'));
				variance	=	totalValue - theRPRO;
				if (variance < 0) {
					$("#variance"+theItem).html('<font color="red"><b>$ '+variance+'</b></font>');
				} else {
					$("#variance"+theItem).html('<font color="black"><b>$ '+variance+'</b></font>');
				}
				calculateTotal(1);
			}
		});
		$('.report_date').change(function() {
			theDate		=	$(this).attr('value');
			theStore	=	$('#curstore').attr('value');
			$.ajax({url: "ajax.php", type: "POST", async: false, data:{date: theDate, store: theStore},
			success: function(data) {	
				if (data == 'new form') {
					if (shown == 0 && populate == 0) {
						$("#tablediv").css('display','block');
						$("#tooltip").fadeIn(750).delay(7000).fadeOut(750); //css('display','block');
						shown = 1;
					}
				}
				else {
					 location="index.php?view_id="+data;
				}

			}
			});
		});
		var shown = 0;
		<?php if (!empty($items)) { ?>
		calculateAll();		
		<?php } ?>
		
		$("#service_head_count").blur( function () {
			var rc = false;
			var v = $("#service_head_count").val();
			rc = ( ( parseInt( v * 2 ) / 2 ) == v );
			if ( rc )
			{
				$("#service_labor_goal").val( CurrencyFormatted( (v * 2) * 100 ) );
				$("#shc_formError").text("");
			} else {
				$("#shc_formError").text("must be whole or half").css("color", "red");
			}
		});
		
		$("#service_labor_completed").blur(function()
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
		
		$(".submit").click(function()
		{
			var dataForm = $("#recon_form").serialize();
			$.ajax({
				type: "POST",
				url: "process.php",
				data: dataForm,
				success: function(response) {
					$('.submit').css("display","none");
					$('.status').css({"background-color":"#33FF00", "display":"block"}).fadeIn('slow').html(response);
				}
			});
			return false;
		});
		
});
	
</script>
</head>

	

<body>
		<div id="tooltip" class="tooltip">
			<h3>Notice:</h3>
			<p class="notice">Make sure you CLICK "enter"<br /><br />when you are finished!</p>
		</div>	
<div id="container">

		<div class="status"></div>
		<div id="i">

			<ul id="navbar">
				<li class="size-border"><a href="#"><span><?php echo $selectedStore['name'];?><br /> </span></a>
					<ul><form id="recon_form">
						<?php
						// Generate the store popup
						while($r  =  mysql_fetch_assoc($allStores)) { ?>
							<li>
								<a href="index.php?store_id=<?php echo $r['id'];?>"><?php echo $r['name'];?></a>
							</li>					
						<?php	} ?>
					</ul>
				</li>
			</ul> 
			<b>By: </b><select name="username" id="employee_name">
			<option value="0">-- Select Employee</option>
			<?php 	$empies	=	mysql_query($empQuery) or die($empQuery);
						while($e	=	mysql_fetch_assoc($empies)) { ?>
							<option value="<?php echo $e['id'];?>" <?php if (!empty($items) && ($e['id'] == $items['user'])) { echo 'selected="selected"'; }?>><?php echo $e['firstname'].' '.$e['lastname'];?></option>
						<?php } ?>
			</select><br />		
			<input type="hidden" name="store_id" id="curstore" value="<?php echo $selectedStore['id'];?>" />
			<input type="hidden" name="action" value="<?php if (!empty($items)) { echo 'edit'; }?>" />
			<input type="hidden" name="headers_id" value="<?php if (!empty($items)) { echo $id; }?>" />
			<input type="hidden" name="checklist_id" value="<?php if (!empty($items)) { echo $checklist_id; }?>" />
			<b>Date: </b> <input id="datepicker" type="text" class="report_date" name="report_date" value="<?php if (!empty($items)) { echo $items['date'];}?>" /><br /><br />
		</div>
		<div style="clear:both;"></div>
		<div id="tablediv" style="margin-top:25px;<?php if (!empty($items)) { echo 'display:block'; }?>">
			<div id="tablecontent">
				<table id="table" style="width:90%" style="float:left;margin-left:50px;">
					<thead>
						<tr>
						<td width="150">&nbsp;</td>
						<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { ?>
							<td width="80" style="<? if ($i==1) { echo 'margin-left:140px;';}?>">Term <?php echo $i;?></td>
						<?php } ?>
						<td width="80">&nbsp;</td>
						<td width="80" >Totals</td>
						<td width="80" >RPro</td>
						<td width="80" >Variance</td>
						</tr> 
					</thead>
					<tbody> 
					<?php while ($r	=	mysql_fetch_assoc($allTypes) ) { ?>
					<?php $totals = 0; ?>
						<tr>
							<td width="150"><strong><?php echo $r['name']; ?></strong></td>
							<?php $r['newname']	=	friendly_seo_string($r['name']); ?>
						<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { 
							if(($r['newname'] == 'paper-checks' || $r['newname'] == 'cash') && $i > 1) { ?>
								<td width="80"></td>
							<?php continue; } else { ?> 
							<td width="80" style="<? if ($i==1) { echo 'margin-left:140px;';}?>">$<input class="matrix <?php echo $i;?>" rel="<?php echo $r['newname'];?>" name="item[<?php echo $r['name'];?>][]" type="text" size="8"
							value="<?php if (!empty($items)) {echo $items[$r['name']][$i]; } else { echo '0.00';}?>" /></td>
							<?php continue; }	?>
							<td width="80" style="<? if ($i==1) { echo 'margin-left:140px;';}?>">$<input class="matrix <?php echo $i;?>" rel="<?php echo $r['newname'];?>" name="item[<?php echo $r['name'];?>][]" type="text" size="8" 
							value="<?php if (!empty($items)) { if (!empty($items[$r['name']][$i])) {echo $items[$r['name']][$i]; } else { echo '0.00';}} else { echo '0.00';}?>" /></td>
						<?php } ?>
							<td width="80" align="right">$</td>
							<td width="80" class="totalcol" id="total<?php echo $r['newname'];?>"><?php if (!empty($items)) { if(!empty($items[$r['name']]['total'])) { echo $items[$r['name']]['total']; } else { echo '0.00';} } else { echo '0.00';}?></td>
							<td width="80" >$<input id="rpro<?php echo $r['newname'];?>" name="rpro[<?php echo $r['name'];?>]" class="rpro" rel="<?php echo $r['newname'];?>" type="text" size="8"
							value="<?php if (!empty($items)) { if($items[$r['name']][0] != '') { echo $items[$r['name']][0]; } else { echo '0.00';}}else{echo '0.00';}?>" /></td>
							<td width="80" id="variance<?php echo $r['newname'];?>" class="variance">$ 0.00</td>						
						</tr>
					<?php  } ?>
						<tr><td colspan="7"><hr style="width:95%;" /></td></tr>
						<tr class="totals">
							<td width="150"><strong>TOTALS</strong></td>
						<?php for($i = 1;$i < ($selectedStore['num_terms']+1);$i++) { ?>
							<td id="total<?php echo $i;?>" width="80" style="<? if ($i==1) { echo 'margin-left:140px;';}?>">$ 0.00<input type="hidden" class="matrix" rel="thetotal" name="total[term<?php echo $i;?>]" /></td>
						<?php } ?>
							<td width="80" align="right">$</td>
							<td width="80" id="totalthetotal">0.00</td>
							<td width="80" id="rprototal">$ 0.00</td>
							<td width="80" id="variancethetotal">$ 0.00</td>						
						</tr>
					</tbody>
				</table>
					<div style="clear:both;"></div>
					<div id="textboxes">
						<div class="float_left">
							<h2>Comments:</h2>
							<textarea rows="8" cols="50" name="comment"><?php if (!empty($items)) { echo $items['comment']; } else { echo ""; }  ?></textarea>
						</div>
						<div class="float_right">
							<h2>Huddle Topic:</h2>
							<input type="text" name="huddle" size="50" value="<?php if (!empty($items)) { echo $checklist['huddle_topic']; } ?>" />
							<br />
						</div>
					</div>
					<div id="zones">
						<?php while ($r	=	mysql_fetch_assoc($allZones)) {  ?>
							<div style="width: 170px;float:left;margin-left:20px;">
								<p style="text-align:center;font-size:15px;font-weight:bold;margin-bottom: 0px;"><?php echo $r['name'];?></p>
								<select name="zone[<?php echo $r['id'];?>]" style="width:170px;">
									<?php $allEmps	=	mysql_query($empQuery) or die($empQuery);
										while($e	=	mysql_fetch_assoc($allEmps)) { ?>
										<option value="<?php echo $e['id'];?>"
										<?php if (!empty($zoneEmps)) { 
												foreach ($zoneEmps as $zoneID => $employeeID) {
													if ($r['id'] == $zoneID && $e['id'] == $employeeID) {
														echo ' selected="selected"';
													}
												}
											} ?>
										><?php echo $e['firstname'].' '.$e['lastname'];?></option>
									<?php } ?>
								</select>
							</div>
						<?php } ?>
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
									<input readonly type="text" value="$0.00" size="15" name="laborgoal" id="service_labor_goal" />
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
					<div class="status"></div>
					<input id="submit_recon" class="submit" type="button" value="<? if (!empty($items)) { echo 'Update report';} else { echo 'Submit report'; }?>" />
				</form>
		</div>
	<br /><br /><br /><br /><br />
	<p>&nbsp;</p>
	</div>
</body>
</html>