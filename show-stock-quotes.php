<?php
/*
 * Plugin Name: Show Stock Quotes
 * Plugin URI: https://draftpress.com/docs/show-stock-quotes/
 * Description: Display up to 20 stock quotes per portfolio.  Each widget instance is considered a portfolio, so just add more widget instances for more portfolios.
 * Version: 2.3.2
 * Author: 99 Robots
 * Author URI: https://draftpress.com
 * License: GPL2
 */

/*
** GOOGLE FINACE DISCLIAMER **

http://www.google.com/intl/en-US/googlefinance/disclaimer/

Data is provided by financial exchanges and may be delayed as specified by financial exchanges or our data providers. Google does not verify any data and disclaims any obligation to do so.

Google, its data or content providers, the financial exchanges and each of their affiliates and business partners (A) expressly disclaim the accuracy, adequacy, or completeness of any data and (B) shall not be liable for any errors, omissions or other defects in, delays or interruptions in such data, or for any actions taken in reliance thereon. Neither Google nor any of our information providers will be liable for any damages relating to your use of the information provided herein. As used here, â€œbusiness partnersâ€? does not refer to an agency, partnership, or joint venture relationship between Google and any such parties.

You agree not to copy, modify, reformat, download, store, reproduce, reprocess, transmit or redistribute any data or information found herein or use any such data or information in a commercial enterprise without obtaining prior written consent. All data and information is provided â€œas isâ€? for personal informational purposes only, and is not intended for trading purposes or advice. Please consult your broker or financial representative to verify pricing before executing any trade.

Either Google or its third party data or content providers have exclusive proprietary rights in the data and information provided.

Please find all listed exchanges and indices covered by Google along with their respective time delays from the table on the left.

Advertisements presented on Google Finance are solely the responsibility of the party from whom the ad originates. Neither Google nor any of its data licensors endorses or is responsible for the content of any advertisement or any goods or services offered therein.
*/

/**
 * Register widget
 */
function wps_show_stock_quotes_register() {
	register_widget( 'WPsite_Show_Stocks' );
}
add_action( 'widgets_init', 'wps_show_stock_quotes_register' );

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", array( 'WPsite_Show_Stocks', 'plugin_links' ) );
add_action( 'wp_enqueue_scripts', array( 'WPsite_Show_Stocks', 'frontend_scripts' ) );

class WPsite_Show_Stocks extends WP_Widget {

	/**
	 * Hooks to 'plugin_action_links_' filter
	 *
	 * @since 1.0.0
	 */
	static function plugin_links( $links ) {

		$widget_link = '<a href="widgets.php">Widget</a>';
		array_unshift( $links, $widget_link );

		return $links;
	}

	static function frontend_scripts() {

		wp_enqueue_style( 'kjb_quotes_css_src', plugins_url( 'include/css/kjb_quotes.css', __FILE__ ) );
		wp_enqueue_script( 'kjb_quotes_js_src', plugins_url( 'include/js/kjb_quotes.js', __FILE__ ), array( 'jquery', 'jquery-ui-core' ) );
	}

	public function __construct() {
		$widget_ops = array( 'classname' => 'kjb_show_stocks', 'description' => 'Display stock data in real-time.' );

		$this->options[] = array(
			'name'  => 'title',
			'label' => 'Title',
			'type'	=> 'text',
			'default' => 'Stocks',
		);

		for ( $i = 1; $i < 21; $i++ ) {
			$this->options[] = array(
				'name'	=> 'stock_' . $i,
				'label'	=> 'Stock Tickers',
				'type'	=> 'text',
				'default' => '',
			);
		}

		parent::__construct( false, 'Show Stock Data', $widget_ops );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {

		extract( $args );

		$title = $instance['title'];

		echo $before_widget;

		if ( '' !== $title ) {
			echo $before_title . $title . $after_title;
		} else {
			echo 'Make sure settings are saved.';
		}

		$tickers = array();

		for ( $i = 1; $i < 21; $i++ ) {

			$ticker = $instance[ 'stock_' . $i ];

			if ( '' !== $ticker ) {
				$tickers[] = $ticker;
			}
		}
		//Display all stock data
		?>
		<table class="kjb_show_stock_quotes_table" id="<?php echo $this->id; ?>">
			<col width='25%'>
			<col width='25%'>
			<col width='25%'>
			<col width='25%'>

			<tbody>
				<?php
				foreach ( $tickers as $ticker ) {

					$new_ticker = str_replace( '^', '-', $ticker );
					$new_ticker = str_replace( '.', '_', $new_ticker );
				?>
					<tr>
						<td class="kjb_show_stock_quotes_ticker"> <a target="_blank" href="http://finance.yahoo.com/q?s=<?php echo $ticker; ?>"><?php echo $ticker; ?></a></td>
						<td class="kjb_show_stock_quotes_quote_<?php echo $this->id . $new_ticker; ?> kjb_show_stock_quotes_error"></td>
						<td class="kjb_show_stock_quotes_change_<?php echo $new_ticker; ?> kjb_show_stock_quotes_error"></td>
						<td class="kjb_show_stock_quotes_change_p_<?php echo $new_ticker; ?> kjb_show_stock_quotes_error"></td>
					</tr>

					<tr style="display: none;">
						<td>
							<input style="display:none;" id="kjb_show_stock_quotes_widget_<?php echo $this->id; ?>" value="<?php echo implode( ',', $tickers ) ?>"/>
						</td>
					</tr>

					<tr style="display: none;">
						<td>
							<input style="display:none;" id="kjb_show_stock_quotes_id_color_<?php echo $this->id; ?>" value="<?php echo isset( $instance['quote_display_color'] ) ? $instance['quote_display_color'] : 'change'; ?>"/>
						</td>
					</tr>
				<?php }
				?>

			</tbody>
		</table>

		<ul style="list-style-type:circle;" id="kjb_show_stock_quotes_rss_<?php echo $this->id; ?>" style="border:none;">

		</ul>
		<?php

		echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['quote_display_color'] = ( ! empty( $new_instance['quote_display_color'] ) ) ? strip_tags( $new_instance['quote_display_color'] ) : '';

		foreach ( $this->options as $val ) {
			$instance[ $val['name'] ] = strip_tags( isset( $new_instance[ $val['name'] ] ) ? $new_instance[ $val['name'] ] : '' );
		}

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {

		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title' );
		$quote_display_color = isset( $instance['quote_display_color'] ) ? $instance['quote_display_color'] : 'change';
		?>
		<!-- Title -->
		<p>
			<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<!-- Quote Display Color -->
		<p>
			<label><?php _e( 'Quote Display Color' ); ?></label><br/>
			<input type="radio" id="<?php echo $this->get_field_id( 'quote_display_color' ); ?>" name="<?php echo $this->get_field_name( 'quote_display_color' ); ?>" value="black" <?php echo isset( $quote_display_color ) && 'black' === $quote_display_color ? 'checked' : '' ?>/><label><?php esc_html_e( 'Same as symbol' ) ?></label><br/>
			<input type="radio" id="<?php echo $this->get_field_id( 'quote_display_color' ); ?>" name="<?php echo $this->get_field_name( 'quote_display_color' ); ?>" value="change" <?php echo isset( $quote_display_color ) && 'change' === $quote_display_color ? 'checked' : '' ?>/><label><?php esc_html_e( 'Same as change color' ) ?></label>
		</p>

		<!-- Stock Tickers -->
		<p>
			<label><?php esc_html_e( 'Stock Tickers' ) ?></label>
			<ol>

			<?php
			for ( $i = 1; $i < 21; $i++ ) {
				$stock = isset( $instance[ 'stock_' . $i ] ) ? $instance[ 'stock_' . $i ] : '';
				?>
				<li><input class="widefat" id="<?php echo $this->get_field_id( 'stock_' . $i ) ?>" name="<?php echo $this->get_field_name( 'stock_' . $i ) ?>" type="text" value="<?php echo esc_attr( $stock ); ?>" /></li>
				<?php
			}
			?>
			</ol>
		</p>
		<?php
	}
}
