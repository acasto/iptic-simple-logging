<?php

namespace Iptic\SL;

/*
 * Class to display the logged information in a table on a page in the admin section
 */
class Admin {
	
	/**
	 * Name of the plugin, static for use in static closures for actions and filters
	 *
	 * @return string
	 * @since 0.1.0
	 * @access private
	 */
    private static function _plugin_name(): string {
        return Util::plugin_name();
    }

	public function __construct() {
        // setting up actions and filters
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		// adding a bit of complexity below to limit the loading of the scripts to the particular admin page
		$instance = $this;
		add_action( 'current_screen', static function() use ( $instance ) {
			if ( get_current_screen()->id === 'toplevel_page_' . Util::plugin_slug() ) {
				add_action( 'admin_enqueue_scripts',  static function() {
					wp_enqueue_style( self::_plugin_name(), plugins_url( '../assets/css/admin/style.css', __FILE__ ) );
                    wp_enqueue_style( 'datatables-css', plugins_url( '../assets/DataTables/datatables.min.css', __FILE__ ) );
                    wp_enqueue_script( 'datatables-js', plugins_url( '../assets/DataTables/datatables.min.js', __FILE__ ), array( 'jquery' ), '1.13.4', true );
				} );
				add_action( 'admin_footer', array( $instance, 'init_datatables' ) );
			}
		} );
	}
	
	/**
	 * Add the admin menu item
	 */
	public function add_admin_menu(): void {
		$menu_name = apply_filters( self::_plugin_name() . '_menu_name', 'Simple Logging' );
		add_menu_page(
			'Simple Logging',
			$menu_name,
			'manage_options',
			Util::plugin_slug(),
			array( $this, 'display_admin' ) );
	}
	
	/**
	 * Display the admin page
	 */
	public function display_admin(): void {
		if ( user_can( get_current_user_id(), 'manage_options' ) ) {
			echo self::display_logs();
		}
	}
	
	/**
	 * Display the logging table // taken from a shortcode in the Rebar plugin
	 * @noinspection PhpConditionAlreadyCheckedInspection
	 */
	public static function display_logs(): string {
		$atts = array(
			'user' => '',
			'facility' => '',
			'level' => '',
			'message' => '',
			'data' => '',
			'tz_local' => 'true', // show the time in the local timezone
			'comp' => '',
			'sort' => '',
			'order_by' => '',
			'limit' => '500', // set an arbitrary limit to prevent the table from getting too big
			'offset' => '',
		);
        
        $atts = apply_filters( self::_plugin_name() . '_log_table_atts', $atts );
		
		$log = Log::get_logs( $atts, 'ARRAY_A' );
  
		ob_start();
		echo '<table class="' . self::_plugin_name() . '-log-table">';
		echo '<thead><th>Time (local)</th><th>Facility</th><th>Level</th><th>User</th><th>Message</th><th>Data</th></thead><tbody>';
		foreach ( $log as $entry ) {
			if ( $atts['tz_local'] ) {
				$entry['time'] = get_date_from_gmt( $entry['time'], 'Y-m-d H:i:s' );
			}
			echo '<tr><td>' . $entry['time'] . '</td><td>' . $entry['facility'] . '</td><td>' . $entry['level'] . '</td><td>' . $entry['user'] . '</td><td>' . esc_html($entry['message']) . '</td><td>' . esc_html($entry['data']) . '</td></tr>';
		}
		echo '</tbody></table>';
		return ob_get_clean();
	}
	
	public function init_datatables(): void {
		?>
		<script>
            jQuery(document).ready(function($) {
                $('.<?= self::_plugin_name() ?>-log-table').DataTable({
                    //"order": [[ 0, "desc" ]], // Sort the table by the first column in descending order by default
                    "order": [], // Don't sort the table by any column by default
                    "pagingType": "full_numbers", // Show pagination controls
                    "lengthChange": true, // show the option to change the number of entries per page
                    "searching": true, // show search
                    "info": true, // show table information display
                    "language": {
                        "search": "Search log:", // Change the search placeholder text
                    },
                });
            });
		</script>
		<?php
	}
	
	
	
	
}