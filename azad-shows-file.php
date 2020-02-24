<?php
/* 
Plugin Name: Azad Shows File
Description: Azad shows file is an option to show in your toolbar what file or template is used to display the page you are currently viewing. You can click the file name to directly edit it throught the theme editor.
Plugin URI: gittechs.com/plugin/azad-shows-file
Author: Md. Abul Kalam Azad
Author URI: gittechs.com/author
Author Email: webdevazad@gmail.com
Version: 1.0.0
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: azad-shows-file
Domain Path: /languages

@package: azad-shows-file
*/ 

// EXIT IF ACCESSED DIRECTLY
defined('ABSPATH') || exit;

if(file_exists(dirname(__FILE__) . '/vendor/autoload.php')){
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

if ( class_exists( 'Inc\\Init' ) ) :    
    Inc\Init::register_services();
endif;

// require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// //$plugin_data = get_plugin_data( __FILE__ );

// define( 'AZAD_WP_STARTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// define( 'AZAD_WP_STARTER_PLUGIN_VERSION_PATH', plugin_dir_path( __FILE__ ) );
// define( 'AZAD_WP_STARTER_PLUGIN_VERSION_PLUGIN', plugin_basename( __FILE__ ) );
// define( 'AZAD_WP_STARTER_PLUGIN_VERSION', $plugin_data['Version'] );
// define( 'AZAD_WP_STARTER_PLUGIN_NAME', $plugin_data['Name'] );

// if(! class_exists('Azad_WP_Starter_Plugin')){
//     final class Azad_WP_Starter_Plugin{
// 		public static $instance = null;
//         public function __construct(){
// 			// add_action('plugins_loaded',array($this,'constants'),1);
//             // add_action('plugins_loaded',array($this,'i18n'),2);
//             // add_action('plugins_loaded',array($this,'includes'),3);
//             // add_action('plugins_loaded',array($this,'admin'),4);
//             // add_action('admin_enqueue_scripts',array($this,'azad_admin_acripts'));
//             // add_action('wp_enqueue_scripts',array($this,'azad_public_acripts'));
//         }
//         public function constants(){}
//         public function i18n(){}
// 		public function includes(){
// 			require_once(plugin_dir_path(__FILE__).'/admin/Azad_Display.php');
// 		}
// 		public function azad_public(){
// 			$instance = call_user_func(array(get_class($GLOBALS['azad_public']),'_get_instance'));
//             $instance->azad_footer();
// 		}
// 		
//         public static function _get_instance(){
//             if(is_null(self::$instance) && ! isset(self::$instance) && ! (self::$instance instanceof self)){
//                 self::$instance = new self();            
//             }
//             return self::$instance;
//         }
//     }
// }
// if(! function_exists('load_azad_wp_starter_plugin')){
//     function load_azad_wp_starter_plugin(){
//         return Azad_WP_Starter_Plugin::_get_instance();
//     }
// }
// //$GLOBALS['load_azad_wp_starter_plugin'] = load_azad_wp_starter_plugin();

class Azad_Shows_File{
    const OPTION_INSTALL_DATE = 'whatthefile-install-date';
	const OPTION_ADMIN_NOTICE_KEY = 'whatthefile-hide-notice';

	/** @var string $template_name */
	private $template_name = '';

	/** @var array $template_parts */
    private $template_parts = array();
    
    /**
	 * Method run on plugin activation
	 */
	public static function plugin_activation() {
		// include nag class
		require_once( plugin_dir_path( __FILE__ ) . '/classes/class-nag.php' );

		// insert install date
		WTF_Nag::insert_install_date();
	}

	/**
	 * Method run on plugin activation
	 */
    public function __construct(){
        add_action('init',array( $this, 'frontend_hooks' ) );
    }
    public function frontend_hooks(){
        // Don't run in admin or if the admin bar isn't showing
		if ( is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// WTF actions and filers
		add_action( 'wp_head', array( $this, 'print_css' ) );
		add_filter( 'template_include', array( $this, 'save_current_page' ), 1000 );
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 1000 );

		// BuddyPress support
		if ( class_exists( 'BuddyPress' ) ) {
			add_action( 'bp_core_pre_load_template', array( $this, 'save_buddy_press_template' ) );
		}

		// Template part hooks
		add_action( 'all', array( $this, 'save_template_parts' ), 1, 3 );
    }
    /**
	 * Get the current page
	 *
	 * @return string
	 */
	private function get_current_page() {
		return $this->template_name;
	}
    /**
	 * Check if file exists in child theme
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	private function file_exists_in_child_theme( $file ) {
		return file_exists( STYLESHEETPATH . '/' . $file );
	}
    /**
	 * Returns if direct file editing through WordPress is allowed
	 *
	 * @return bool
	 */
	private function is_file_editing_allowed() {
		$allowed = true;
		if ( ( defined( 'DISALLOW_FILE_EDIT' ) && true == DISALLOW_FILE_EDIT ) || ( defined( 'DISALLOW_FILE_MODS' ) && true == DISALLOW_FILE_MODS ) ) {
			$allowed = false;
		}

		return $allowed;
	}
    /**
	 * Save the template parts in our array
	 *
	 * @param $tag
	 * @param null $slug
	 * @param null $name
	 */
	public function save_template_parts( $tag, $slug = null, $name = null ) {
		if ( 0 !== strpos( $tag, 'get_template_part_' ) ) {
			return;
		}

		// Check if slug is set
		if ( $slug != null ) {

			// Templates array
			$templates = array();

			// Add possible template part to array
			if ( $name != null ) {
				$templates[] = "{$slug}-{$name}.php";
			}

			// Add possible template part to array
			$templates[] = "{$slug}.php";

			// Get the correct template part
			$template_part = str_replace( get_template_directory() . '/', '', locate_template( $templates ) );
			$template_part = str_replace( get_stylesheet_directory() . '/', '', $template_part );

			// Add template part if found
			if ( $template_part != '' ) {
				$this->template_parts[] = $template_part;
			}
		}

	}
    /**
	 * Save the current page in our local var
	 *
	 * @param $template_name
	 *
	 * @return mixed
	 */
	public function save_current_page( $template_name ) {
		$this->template_name = basename( $template_name );

		// Do Roots Theme check
		if ( function_exists( 'roots_template_path' ) ) {
			$this->template_name = basename( roots_template_path() );
		} else if( function_exists( 'Roots\Sage\Wrapper\template_path' ) ) {
			$this->template_name = basename( Roots\Sage\Wrapper\template_path() );
		}

		return $template_name;
    }
    /**
	 * Add the admin bar menu
	 */
	public function admin_bar_menu() {
		global $wp_admin_bar;

		// Check if direct file editing is allowed
		$edit_allowed = $this->is_file_editing_allowed();

		// Add top menu
		$wp_admin_bar->add_menu( array(
			'id'     => 'wtf-bar',
			'parent' => 'top-secondary',
			'title'  => __( 'What The File', 'what-the-file' ),
			'href'   => false
		) );

		// Check if template file exists in child theme
		$theme = get_stylesheet();
		if ( ! $this->file_exists_in_child_theme( $this->get_current_page() ) ) {
			$theme = get_template();
		}

		// Add current page
		$wp_admin_bar->add_menu( array(
			'id'     => 'wtf-bar-template-file',
			'parent' => 'wtf-bar',
			'title'  => $this->get_current_page(),
			'href'   => ( ( $edit_allowed ) ? get_admin_url() . 'theme-editor.php?file=' . $this->get_current_page() . '&theme=' . $theme : false )
		) );

		// Check if theme uses template parts
		if ( count( $this->template_parts ) > 0 ) {

			// Add template parts menu item
			$wp_admin_bar->add_menu( array(
				'id'     => 'wtf-bar-template-parts',
				'parent' => 'wtf-bar',
				'title'  => 'Template Parts',
				'href'   => false
			) );

			// Loop through template parts
			foreach ( $this->template_parts as $template_part ) {

				// Check if template part exists in child theme
				$theme = get_stylesheet();
				if ( ! $this->file_exists_in_child_theme( $template_part ) ) {
					$theme = get_template();
				}

				// Add template part to sub menu item
				$wp_admin_bar->add_menu( array(
					'id'     => 'wtf-bar-template-part-' . $template_part,
					'parent' => 'wtf-bar-template-parts',
					'title'  => $template_part,
					'href'   => ( ( $edit_allowed ) ? get_admin_url() . 'theme-editor.php?file=' . $template_part . '&theme=' . $theme : false )
				) );
			}

		}

		// Add powered by
		$wp_admin_bar->add_menu( array(
			'id'     => 'wtf-bar-powered-by',
			'parent' => 'wtf-bar',
			'title'  => 'Powered by Never5',
			'href'   => 'http://www.never5.com?utm_source=plugin&utm_medium=wtf-bar&utm_campaign=what-the-file'
		) );

	}
    /**
	 * Print the custom CSS
	 */
   public function print_css() {
       echo "<style type=\"text/css\" media=\"screen\">#wp-admin-bar-wtf-bar > .ab-item{padding-right:26px !important;background: url('" . plugins_url( 'assets/images/never5-logo.png', __FILE__ ) . "')center right no-repeat !important;} #wp-admin-bar-wtf-bar.hover > .ab-item {background-color: #32373c !important; } #wp-admin-bar-wtf-bar #wp-admin-bar-wtf-bar-template-file .ab-item, #wp-admin-bar-wtf-bar #wp-admin-bar-wtf-bar-template-parts {text-align:right;} #wp-admin-bar-wtf-bar-template-parts.menupop > .ab-item:before{ right:auto !important; }#wp-admin-bar-wtf-bar-powered-by{text-align: right;}#wp-admin-bar-wtf-bar-powered-by a{color:#ffa100 !important;}</style>\n";
   }
}

function azad_shows_file(){
    new Azad_Shows_File();
}
add_action('plugins_loaded','azad_shows_file');

// Register hook
//register_activation_hook( __FILE__, array( 'WhatTheFile', 'plugin_activation' ) );