<?php
$today  = date("m-d-Y");
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
        
        <style>
            body {
                padding-top: 60px;
                padding-bottom: 40px;
            }
        </style>
        <script src="js/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="container">

            <!-- Main hero unit for a primary marketing message or call to action -->
            <div class="hero-unit">
                <h1 class="text-left">Recon 
		    <span class="store-select">
			<select name="store_id" id="store_id" class="large-font">
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
			</select>
		    </span>
		</h1>
            </div>
	    <div id="form" style="display:none;">
                <form id="reconform">
			<div class="status"></div>
			<div id="i">
			    <input id="date" type="text" name="date" class="large-font" value="<?php echo $today; ?>" readonly="true" tabindex="-1" /> 
			    <input type="text" size="20" name="username" id="employee_name" value="" placeholder="Reported By:" />
			</div>
			<div id="tablediv">
				<table>
					<thead>
						<tr>
						<td width="150">&nbsp;</td>
						<td class="header table_column" >Actual</td>
						<td class="header table_column" >RPro</td>
						<td class="header table_column" >Variance</td>
						</tr> 
					</thead>
					<tbody> 
																
						<tr>
							<td width="150"><strong>Cash</strong></td>
													 
							<td class="table_column actual">$ <input id="cash" class="col1" rel="cash" name="item[Cash]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">$ <input id="rprocash" class="col2" rel="cash" name="rpro[Cash]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancecash" class="variance table_column col3"></td>						
						</tr>
																
						<tr>
							<td width="150"><strong>VISA</strong></td>
													 
							<td class="table_column actual">$ <input id="visa" class="col4" rel="visa" name="item[VISA]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$ <input id="rprovisa" class="col2" rel="visa" name="rpro[VISA]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>MC</strong></td>
													 
							<td class="table_column actual">$ <input id="mc" class="col4" rel="mc" name="item[MC]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$ <input id="rpromc" name="rpro[MC]" class="col2" rel="mc" type="text" size="8"	value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>DISCOVER</strong></td>
													 
							<td class="table_column actual">$ <input id="discover" class="col4" rel="discover" name="item[DISCOVER]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$ <input id="rprodiscover" name="rpro[DISCOVER]" class="col2" rel="discover" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>AMEX</strong></td>
													 
							<td class="table_column actual">$ <input id="amex" class="col4" rel="amex" name="item[AMEX]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$ <input id="rproamex" name="rpro[AMEX]" class="col2" rel="amex" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>DEBIT</strong></td>
													 
							<td class="table_column actual">$ <input id="debit" class="col4" rel="debit" name="item[DEBIT]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$ <input id="rprodebit" name="rpro[DEBIT]" class="col2" rel="debit" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>GE Capital</strong></td>
													 
							<td class="table_column actual">$ <input id="ge-money" class="col1" rel="ge-money" name="item[GE_Capital]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">$ <input id="rproge-money" name="rpro[GE_Capital]" class="col2" rel="ge-money" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancege-money" class="variance table_column col3"></td>
						</tr>
																
						<tr>
							<td width="150"><strong>Amazon</strong></td>
													 
							<td class="table_column actual">$ <input id="amazon" class="col4" rel="amazon" name="item[Amazon]" type="text" size="8" value="" readonly="true" tabindex="-1" /></td>
							<td class="table_column rpro">$ <input id="rproamazon" name="rpro[Amazon]" class="col2" rel="amazon" type="text" size="8" value="" placeholder="0.00" /></td>
							<td></td>
						</tr>
																
						<tr>
							<td width="150"><strong>Paypal</strong></td>
													 
							<td class="table_column actual">$ <input id="paypal" class="col1" rel="paypal" name="item[Paypal]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">$ <input id="rpropaypal" name="rpro[Paypal]" class="col2" rel="paypal" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancepaypal" class="variance table_column col3"></td>						
						</tr>
																
						<tr>
							<td width="150"><strong>Wire</strong></td>
													 
							<td class="table_column actual">$ <input id="wire" class="col1" rel="wire" name="item[Wire]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">$ <input id="rprowire" name="rpro[Wire]" class="col2" rel="wire" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancewire" class="variance table_column col3"></td>						
						</tr>
																
						<tr>
							<td width="150"><strong>Misc</strong></td>
													 
							<td class="table_column actual">$ <input id="misc" class="col1" rel="misc" name="item[Misc]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">$ <input id="rpromisc" name="rpro[Misc]" class="col2" rel="misc" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="variancemisc" class="variance table_column col3"></td>						
						</tr>
						<tr><td colspan="4"><hr style="width:95%;" /></td></tr>
						<tr class="totals">
							<td width="150"><strong>TOTALS</strong></td>
							<td class="totals table_column actual" id="total_actual"></td>
							<td class="totals table_column rpro" id="total_rpro"></td>
							<td class="totals table_column variance" id="total_variance"></td>
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td width="150"><strong>Gift Cards Loaded</strong></td>
							<td class="table_column actual">
								$<input id="gc_loaded" class="gc_matrix" rel="loaded" name="item[Gift_Cards_Loaded]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">
								$<input id="gc_rpro_loaded" class="gc_rpro" rel="loaded" name="gc_rpro[Gift_Cards_Loaded]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="gc_variance_loaded" class="table_column variance"></td>						
						</tr>
					
						<tr>
							<td width="150"><strong>Gift Cards Redeemed</strong></td>
							<td class="table_column actual">
								$<input id="gc_redeemed" class="gc_matrix" rel="redeemed" name="item[Gift_Cards_Redeemed]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td class="table_column rpro">
								$<input id="gc_rpro_redeemed" class="gc_rpro" rel="redeemed" name="gc_rpro[Gift_Cards_Redeemed]" type="text" size="8" value="" placeholder="0.00" /></td>
							<td id="gc_variance_redeemed" class="table_column variance"></td>						
						</tr>
					
					</tbody>
				</table>
			</div>
			<div id="service">
				<span id="serviceHeading"><strong>Service</strong></span>  <span id="shc_formError"></span><br />
				<label for="headcount">Head count </label><input type="number" size="7" min=0 max=10 pattern="\d+" name="headcount" id="service_head_count" value="" />
				<label for="labor_completed">Labor completed</label><input type="number" size="7" name="labor_completed" id="service_labor_completed" value="" />
				<label for="laborgoal">Labor goal</label><input readonly type="text" size="7" name="laborgoal" id="service_labor_goal" value="" tabindex="-1" />
			</div>
			<div id="textboxes">
				<textarea class="comments" rows="8" cols="45" name="comment" placeholder="Comments"></textarea>
				<textarea class="huddle" rows="8" cols="45" name="huddle" placeholder="This morning's huddle topic"></textarea>
			</div>
			<input id="action" name="action" type="hidden" value="" />
			<input id="store" name="store" type="hidden" value="" />
			<div class="well" style="max-width: 400px;">
			    <button id="submit_recon" class="submit btn btn-large btn-block btn-primary" type="button">Submit Report</button>
			</div>
                </form>
	    </div>
            <hr>

            <footer>
                <p>&copy; Mike's Bikes 2013</p>
            </footer>

        </div> <!-- /container -->

        <script src="js/jquery-2.0.2.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
