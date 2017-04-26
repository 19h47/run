<?php 
/**
 * class Theme
 */
class Theme {
    
    /**
     * The name of the theme
     *
     * @access private
     */
    private $theme_name;
    

    /**
     * The version of this theme
     *
     * @access private
     */
    private $theme_version;
    

    /**
     * Initialize the class and set its properties.
     *
     * @param  $theme_name
     * @param  $theme_version
     * @access public
     */
    public function __construct( $theme_name, $theme_version ) {
        
        $this->theme_name = $theme_name;
        $this->theme_version = $theme_version;
 
        $this->load_dependencies();
    }


    /**
     * Load dependencies description
     * 
     * @access private
     */
    private function load_dependencies() {
        
        // require_once get_template_directory() . '/inc/admin.php';
        require_once get_template_directory() . '/inc/post-types/_includes.php';
        
        // new ADMIN( $this->get_theme_name(), $this->get_theme_version() );        
        new Custom_Post_Types( $this->get_theme_name(), $this->get_theme_version() );
    }


    /**
     * Setup
     * 
     * @access public
     */
    public function setup() {
        
        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );
        

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @see https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support( 'post-thumbnails' );
        

        /*
        * Switch default core markup for search form, comment form, and comments
        * to output valid HTML5.
        */
        add_theme_support( 
            'html5', 
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption'
            ) 
        );
        

        /**
         * Register nav menus
         */
        register_nav_menus( 
            array(
                'main'  => __( 'Menu Principal', $this->theme_name )            
            ) 
        );
        

        /**
         * Add excerpt on page
         *
         * @see  https://codex.wordpress.org/Function_Reference/add_post_type_support
         */
        add_post_type_support( 'page', 'excerpt' );


        /**
         * Add JavaScript detection
         */
        add_action( 'wp_head', array( $this, 'javascript_detection' ), 2 );


        /**
         * Add styles
         */
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );


        /**
         * Admin
         */
        add_action( 'admin_init',  array( $this, 'admin_init' ) );


        /**
         * Add scripts
         */
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );


         /**
         * Add favicons
         */
        add_action( 'wp_head', array( $this, 'favicons' ) );

    }


    /**
     * Admin init
     */
    public function admin_init() {
        
        /**
         * Editor style
         */
        add_editor_style( get_template_directory_uri() . '/dist/css/editor-style.css' );
    }


    /**
     * Enqueue styles.
     * 
     * @access public
     */
    public function enqueue_style() {
        
        // Add custom fonts, used in the main stylesheet.
        $webfonts = array();
        foreach ( $this->webfonts() as $name => $url ) {
            wp_register_style( 'font-' . $name, $url, array(), null );
            $webfonts[] = 'font-' . $name;
        }
        // Theme stylesheet
        wp_register_style(  $this->theme_name . '-global', get_template_directory_uri() . '/dist/css/global.css', $webfonts, null );
        wp_enqueue_style( $this->theme_name . '-global' );
    }


    /**
     * Enqueue scripts
     *
     * @access public
     */
    public function enqueue_scripts() {
        
        // Remove wp-embed script from WordPress
        wp_deregister_script( 'wp-embed' );
        // Remove native version of jQuery and use custom CDN version instead
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', '//code.jquery.com/jquery-3.2.1.min.js', false, null, true );
        
        //
        wp_register_script( $this->theme_name . '-main', get_template_directory_uri() . '/dist/js/bundle.js', array( 'jquery' ), null, true );
        
        // Localize script
        wp_localize_script( 
            $this->theme_name . '-main', 
            'wp', 
            array(
                'template_directory_uri'    => get_template_directory_uri(),
                'base_url'                  => site_url(),
                'home_url'                  => home_url( '/' ),
                'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                'nonce'                     => wp_create_nonce( 'wp_rest' ),
                'api_url'                   => esc_url_raw( get_rest_url() )
            )
        ); 
        wp_enqueue_script( $this->theme_name . '-main' );
    }
    

    /**
     * List webfonts used by the theme.
     * 
     * @return array
     * @access public
     */
    public function webfonts() {
        
        return array(
        	'work-sans' => 'https://fonts.googleapis.com/css?family=Work+Sans:100,200,300,400,500,600,700,800,900',
        );
    }
    /**
     * Handles JavaScript detection.
     * Adds a `js` class to the root `<html>` element when JavaScript is detected.
     *
     * @access public
     */
    public function javascript_detection() {
	   
	    ?>

        <script src="<?php echo get_template_directory_uri() ?>/dist/js/min/feature.min.js"></script>
        <script>
            document.documentElement.className = document.documentElement.className.replace('no-js', 'js');
            if (feature.touch && !navigator.userAgent.match(/Trident\/(6|7)\./)) {
                document.documentElement.className = document.documentElement.className.replace('no-touch', 'touch');
            }
         </script>

        <?php
    }


    /**
     * Retrieve the version number of the theme.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_theme_version() {
        
        return $this->theme_version;
    }


    /**
     * The name of the theme used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the theme.
     */
    public function get_theme_name() {
        
        return $this->theme_name;
    }


    /**
     * Add all favicon metas in <head>
     *
     * @see  http://realfavicongenerator.net/
     */
    function favicons() {
        $output = "";
        echo $output;
    }
}


/**
 * Begins execution of the theme.
 */
$theme = new Theme( 'Stepper', '1.0.0' );