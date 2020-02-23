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

// // EXIT IF ACCESSED DIRECTLY
// defined('ABSPATH') || exit;

// if(file_exists(dirname(__FILE__) . '/vendor/autoload.php')){
//     require_once dirname(__FILE__) . '/vendor/autoload.php';
// }

// if ( class_exists( 'Inc\\Init' ) ) :    
//     Inc\Init::register_services();
// endif;

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
// 		public function azad_admin(){}
// 		public function azad_public(){
// 			$instance = call_user_func(array(get_class($GLOBALS['azad_public']),'_get_instance'));
//             $instance->azad_footer();
// 		}
// 		public function azad_admin_acripts(){
			
// 			wp_register_style('id','url','dep','version','bool');
//             wp_register_style('id',plugins_url(''),'dep','version','bool');
//             wp_register_style('id','url','dep','version','bool');
//             wp_enqueue_style('id');
//             wp_register_script('id','url','dep','version','bool');
//             wp_register_script('id',plugins_url(''),'dep','version','bool');
//             wp_enqueue_script('id');
			
// 			wp_register_script( 'azad-nice-scroll', plugins_url( 'js/nicescroll.js', __FILE__ ), 'jquery', 1.0, true );
//             wp_enqueue_script('jquery');
//             wp_enqueue_script('azad-nice-scroll');
//         }
//         public function azad_public_acripts(){
// 			wp_register_script( 'azad-nice-scroll', plugins_url( 'js/nicescroll.js', __FILE__ ), 'jquery', 1.0, true );
//             wp_enqueue_script('jquery');
//             wp_enqueue_script('azad-nice-scroll');
//         }
//         public static function _get_instance(){
//             if(is_null(self::$instance) && ! isset(self::$instance) && ! (self::$instance instanceof self)){
//                 self::$instance = new self();            
//             }
//             return self::$instance;
//         }
//         public function __destruct(){}
//     }
// }
// if(! function_exists('load_azad_wp_starter_plugin')){
//     function load_azad_wp_starter_plugin(){
//         return Azad_WP_Starter_Plugin::_get_instance();
//     }
// }
// //$GLOBALS['load_azad_wp_starter_plugin'] = load_azad_wp_starter_plugin();

// function dwwp_add_google_link(){
//     global $wp_admin_bar;
//     $args = array(
//         'id'=>'google_analytics',
//         'title'=>'Google Analytics',
//         'href'=>'http://google.com'
//     );
//     $wp_admin_bar->add_menu($args);
// }
// //add_action('wp_before_admin_bar_render','dwwp_add_google_link');
// function dwwp_alter_book_icons($args){
//     $args['menu_icon'] = 'dashicons-book-alt';
//     return $args;
// }
// //add_filter('dwwp_post_type_args','dwwp_alter_book_icons');

class Azad_Shows_File{
    public function __construct(){
        add_action('init',array( $this, 'frontend_hooks' ) );
    }
    public function frontend_hooks(){
        // Don't run in admin or if the admin bar isn't showing
		if ( is_admin() || ! is_admin_bar_showing() ) {
			return;
		}
        add_action( 'wp_head', array( $this, 'print_css' ) );
        add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 1000 );
    }
    /**
	 * Add the admin bar menu
	 */
	public function admin_bar_menu() {
        global $wp_admin_bar;
    }
    public function print_css(){
        echo "<style>body{color:red;}</style>";
    }
    public function __destruct(){}
}

function azad_shows_file(){
    new Azad_Shows_File();
}
add_action('plugins_loaded','azad_shows_file');