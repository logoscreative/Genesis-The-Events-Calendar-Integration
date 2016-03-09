<?php
/**
 * Genesis + The Events Calendar Integration
 *
 * Integrates the things from: https://theeventscalendar.com/knowledgebase/genesis-theme-framework-integration/
 *
 * @package   genesis_tec_integration
 * @author    Cliff Seal <cliff@evermo.re>
 * @link      http://evermo.re
 * @copyright 2016 Logos Creative
 *
 * @wordpress-plugin
 * Plugin Name: Genesis + The Events Calendar Integration
 * Plugin URI:  http://evermo.re
 * Description: Make The Events Calendar and Genesis work nicely together.
 * Version:     0.1
 * Author:      Evermore <cliff@evermo.re>
 * Author URI:  http://evermo.re
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Plugin class.
 *
 * @package genesis_tec_integration
 * @author  Cliff Seal <cliff@evermo.re>
 */
class genesis_tec_integration {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1
	 *
	 * @const   string
	 */
	const VERSION = '0.1';

	/**
	 * Instance of this class.
	 *
	 * @since    0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.1
	 */
	private function __construct() {

		add_filter( 'genesis_pre_get_option_content_archive', array( $this, 'genesis_tec_integration_tribe_genesis_event_archive_full_content' ) );

		/**
		 * @TODO: Create conditional options for template layouts
	     */

		// Target all Event Views (Month, List, Map etc)
		add_filter( 'genesis_site_layout', array( $this, 'genesis_tec_integration_tribe_genesis_view_layouts' ) );

		// Target all Event Views (Month, List, Map etc), Single Events, Single Venues, and Single Organizers
		add_filter( 'genesis_site_layout', array( $this, 'genesis_tec_integration_tribe_genesis_all_layouts' ) );

		// Target Single Events, Single Venues, and Single Organizers
		add_filter( 'genesis_site_layout', array( $this, 'genesis_tec_integration_tribe_genesis_single_layouts' ) );

		// Target Community Events Edit Form and My Event's List
		add_filter( 'genesis_site_layout', array( $this, 'genesis_tec_integration_tribe_genesis_community_layouts' ) );

		/**
		 * </@TODO>
		 */

		add_filter( 'tribe_events_single_event_before_the_content', array( $this, 'genesis_tec_integration_tribe_genesis_event_share' ) );

		add_action( 'pre_get_posts', array( $this, 'genesis_tec_integration_tribe_genesis_hide_author_single_events' ) );

		add_action( 'wp_head', array( $this, 'genesis_tec_integration_tribe_genesis_css_fixer' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Filter for Full Content on The Events Calender(3.8) Views Page in Genesis(2.1.2) when using Default Page Template in Event Display Settings
	 * Shows Event Calendar Views Such as Month, List, etc even if Content Archive Setting in Genesis is set to Display post excerpts
	 * The Events Calendar @3.10
	 * Genesis @2.1.2
	 *
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	 */

	public function genesis_tec_integration_tribe_genesis_event_archive_full_content() {

		if ( class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Pro__Main' ) ) {
			if( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() || tribe_is_map() || tribe_is_photo() || tribe_is_week() || ( tribe_is_recurring_event() && ! is_singular( 'tribe_events' ) ) ) {
				return 'full';
			}
		} elseif ( class_exists( 'Tribe__Events__Main' ) && !class_exists( 'Tribe__Events__Pro__Main' ) ) {
			if( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() ) {
				return 'full';
			}
		}
	}

	/**
	* Genesis Page Layout of The Event Calendar Main Events Templates (Month, List, Photo, Etc..)
	* The Events Calendar @3.10
	* Genesis @2.1.2
	* Options - full-width-content, content-sidebar, sidebar-content, content-sidebar-sidebar, sidebar-sidebar-content, sidebar-content-sidebar
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	*/

	public function genesis_tec_integration_tribe_genesis_view_layouts() {

		if ( class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Pro__Main' ) ) {
			if( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() || tribe_is_map() || tribe_is_photo() || tribe_is_week() ) {
				return 'full-width-content';
			}
		} elseif ( class_exists( 'Tribe__Events__Main' ) && !class_exists( 'Tribe__Events__Pro__Main' ) ) {
			if( tribe_is_month() || tribe_is_upcoming() || tribe_is_past() || tribe_is_day() ) {
				return 'full-width-content';
			}
		}
	}

	/**
	* Genesis Layout of The Event Calendar Views for all Templates
	* The Events Calendar @3.10
	* Genesis @2.1.2
	* Options - full-width-content, content-sidebar, sidebar-content, content-sidebar-sidebar, sidebar-sidebar-content, sidebar-content-sidebar
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	*/

	public function genesis_tec_integration_tribe_genesis_all_layouts() {

		if ( class_exists( 'Tribe__Events__Main' ) && tribe_is_event_query() ) {

			return 'full-width-content';

		}

	}

	/**
	* Genesis Layout of The Event Calendar Single Templates
	* The Events Calendar @3.10
	* Genesis @2.1.2
	* Options - full-width-content, content-sidebar, sidebar-content, content-sidebar-sidebar, sidebar-sidebar-content, sidebar-content-sidebar
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	*/

	public function genesis_tec_integration_tribe_genesis_single_layouts() {

		if ( is_singular( 'tribe_events' ) || is_singular( 'tribe_venue' ) || is_singular( 'tribe_organizer' ) ) {
			return 'full-width-content';
		}

	}

	/**
	* Genesis Layout of The Event Calendar Single Templates
	* The Events Calendar @3.10
	* Genesis @2.1.2
	* Options - full-width-content, content-sidebar, sidebar-content, content-sidebar-sidebar, sidebar-sidebar-content, sidebar-content-sidebar
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	*/

	public function genesis_tec_integration_tribe_genesis_community_layouts() {

		if ( function_exists('tribe_is_community_my_events_page') && ( tribe_is_community_my_events_page() || tribe_is_community_edit_event_page() ) ) {
			return 'full-width-content';
		}

	}

	/**
	 * The Events Calendar - Include Genesis Simple Sharing Above Single Events Content
	 *
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	 */

	public function genesis_tec_integration_tribe_genesis_event_share( $post_info ) {

		if ( is_singular('tribe_events') && function_exists( 'genesis_share_get_icon_output' ) ) {
			global $Genesis_Simple_Share;
			$share =  genesis_share_get_icon_output( 'entry-meta', $Genesis_Simple_Share->icons );
			$event_sharing = '<div class="alignleft info-left">' . $share . '</div><br>';
			echo $event_sharing;
		}
	}

	/**
	* Genesis Remove Author Box from Single Templates for the The Events Calendar
	*
	*
	*/

	public function genesis_tec_integration_tribe_genesis_hide_author_single_events( $query ) {
		if ( !is_admin() && ( is_singular( 'tribe_events' ) || ( isset( $query->query['post_type'] ) && ( $query->query['post_type'] == 'tribe_venue' || $query->query['post_type'] == 'tribe_organizer' ) ) ) ) {
			remove_action( 'genesis_after_entry', 'genesis_do_author_box_single', 8 );
		}
	}

	/**
	 * The Events Calendar - Include Genesis Simple Sharing Above Single Events Content
	 *
	 * @since     0.1
	 *
	 * @return    object    An instance of the form
	 */

	public function genesis_tec_integration_tribe_genesis_css_fixer() {

		echo '
		<style type="text/css">#tribe-community-events input,#tribe-community-events select,#tribe-community-events textarea{min-height:0;padding:12px;width:auto}.events-archive .featured-content .type-post header.entry-header,.single-tribe_events .featured-content .type-post header.entry-header,.single-tribe_organizer .featured-content .type-post header.entry-header,.single-tribe_venue .featured-content .type-post header.entry-header{display:block}.tribe-filters-open,.tribe-filters-open body{overflow:visible!important}</style>';

	}

}

genesis_tec_integration::get_instance();
