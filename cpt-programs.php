<?php
/**
 * Plugin Name: CPT Programs
 * Plugin URI: https://github.com/GreaterJacksonHabitat/cpt-programs
 * Description: Programs Custom Post Type
 * Version: 0.1.0
 * Text Domain: cpt-programs
 * Author: Eric Defore
 * Author URI: https://realbigmarketing.com/
 * Contributors: d4mation
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CPT_Programs' ) ) {

	/**
	 * Main CPT_Programs class
	 *
	 * @since	  1.0.0
	 */
	class CPT_Programs {
		
		/**
		 * @var			CPT_Programs $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			CPT_Programs $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true CPT_Programs
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %s or higher to be installed!', 'Outdated Dependency Error', 'cpt-programs' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>WordPress</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'CPT_Programs_VER' ) ) {
				// Plugin version
				define( 'CPT_Programs_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'CPT_Programs_DIR' ) ) {
				// Plugin path
				define( 'CPT_Programs_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'CPT_Programs_URL' ) ) {
				// Plugin URL
				define( 'CPT_Programs_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'CPT_Programs_FILE' ) ) {
				// Plugin File
				define( 'CPT_Programs_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = CPT_Programs_DIR . '/languages/';
			$lang_dir = apply_filters( 'cpt_programs_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'cpt-programs' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'cpt-programs', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/cpt-programs/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/cpt-programs/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'cpt-programs', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/cpt-programs/languages/ folder
				load_textdomain( 'cpt-programs', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'cpt-programs', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'cpt-programs',
				CPT_Programs_URL . 'assets/css/style.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Programs_VER
			);
			
			wp_register_script(
				'cpt-programs',
				CPT_Programs_URL . 'assets/js/script.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Programs_VER,
				true
			);
			
			wp_localize_script( 
				'cpt-programs',
				'cPTPrograms',
				apply_filters( 'cpt_programs_localize_script', array() )
			);
			
			wp_register_style(
				'cpt-programs-admin',
				CPT_Programs_URL . 'assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Programs_VER
			);
			
			wp_register_script(
				'cpt-programs-admin',
				CPT_Programs_URL . 'assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CPT_Programs_VER,
				true
			);
			
			wp_localize_script( 
				'cpt-programs-admin',
				'cPTPrograms',
				apply_filters( 'cpt_programs_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true CPT_Programs
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \CPT_Programs The one true CPT_Programs
 */
add_action( 'plugins_loaded', 'cpt_programs_load' );
function cpt_programs_load() {

	require_once __DIR__ . '/core/cpt-programs-functions.php';
	CPTPROGRAMS();

}
