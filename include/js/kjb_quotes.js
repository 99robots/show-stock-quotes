/*
 * Created by Kyle Benk
 * http://kylebenkapps.com
 *
 * Google Finance Credit to http://jsfiddle.net/A4jKT/4/
 */

jQuery(document).ready(function($) {

	var url = "http://finance.google.com/finance/info?client=ig";

	var tables = $(".kjb_show_stock_quotes_table");

	for (var x = 0; x < tables.length; x++) {

		var stocks = $("#kjb_show_stock_quotes_widget_" + $(tables[x]).attr('id')).val();

		get_stock_data(url, $(tables[x]).attr('id'), $("#kjb_show_stock_quotes_id_color_" + $(tables[x]).attr('id')).val(), stocks);
	}

	  function commaSeparateNumber(val){
	    while (/(\d+)(\d{3})/.test(val.toString())){
	      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
	    }
	    return val;
	  }

	function get_stock_data(url, table_id, color, stocks) {

    	/*
$.ajax({
    		dataType: "text",
			url: url + '&q=' + stocks,
		})
*/
		 $.getJSON(url + '&q=' + stocks + "&callback=?")
	        .done(function (data) {

	        	/*
data = data.substr(3);

				data = data.replace("/ ^[\b \t \n \r \]*$", '');

	        	data = jQuery.parseJSON(unescape(data));
*/


		        if (typeof(data) != "undefined" && data !== null) {

		        	for (q = 0; q < data.length; q++) {

			        	var symbol = data[q].t.replace('^', '-');
						symbol = symbol.replace('.', '_');
						var last_price = parseFloat(data[q].l.replace(',', ''));
						var last_change = parseFloat(data[q].c);

						if (last_change <= 0) {
				        	if (color == 'change') {
					        	$(".kjb_show_stock_quotes_quote_" + table_id + symbol).attr('style', 'border: none; color:red; text-align:right');
				        	}else {
					        	$(".kjb_show_stock_quotes_quote_" + table_id + symbol).attr('style', 'border: none; text-align:right');
				        	}

							$(".kjb_show_stock_quotes_change_" + symbol).attr('style', 'border: none; color:red; text-align:right');
							$(".kjb_show_stock_quotes_change_p_" + symbol).attr('style', 'border: none;color:red; text-align:right');
				        }else{
				        	if (color == 'change') {
				        	 $(".kjb_show_stock_quotes_quote_" + table_id + symbol).attr('style', 'border: none;color:green; text-align:right');
				        	}else {
					        	$(".kjb_show_stock_quotes_quote_" + table_id + symbol).attr('style', 'border: none; text-align:right');
				        	}

							$(".kjb_show_stock_quotes_change_" + symbol).attr('style', 'border: none;color:green; text-align:right');
							$(".kjb_show_stock_quotes_change_p_" + symbol).attr('style', 'border: none;color:green; text-align:right');
				        }

				        var price = (Math.round(last_price * 100) / 100).toFixed(2);
				        var change = (Math.round(last_change * 100) / 100).toFixed(2);

				        $(".kjb_show_stock_quotes_quote_" + table_id + symbol).text('$' + commaSeparateNumber(price));
						$(".kjb_show_stock_quotes_change_" + symbol).text(change);
						$(".kjb_show_stock_quotes_change_p_" + symbol).text(data[q].cp + '%');

						if (last_price == 0) {
							if (color == 'change') {
								$(".kjb_show_stock_quotes_quote_" + table_id + symbol).attr('style', 'border: none;color:red; text-align:right');
							}else {
					        	$(".kjb_show_stock_quotes_quote_" + table_id + symbol).attr('style', 'border: none; text-align:right');
				        	}

							$(".kjb_show_stock_quotes_change_" + symbol).attr('style', 'border: none;color:red; text-align:right');
							$(".kjb_show_stock_quotes_change_p_" + symbol).attr('style', 'border: none;color:red; text-align:right');
							$(".kjb_show_stock_quotes_quote_" + table_id + symbol).text('Invalid');
							$(".kjb_show_stock_quotes_change_" + symbol).text('Invalid');
						}
		        	}

		        }
	    })
	        .fail(function (jqxhr, textStatus, error) {
	        	var err = textStatus + ", " + error;
	        	//console.log(err);
	    });
	}
});