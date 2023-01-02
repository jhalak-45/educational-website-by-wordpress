<?php

/**
 * Plugin Name: Menu Image
 * Description: Improve your navigation menu items with images, logos, icons, buttons.
 * Version: 3.0.8
 * Plugin URI: https://www.freshlightlab.com/menu-image-wordpress-plugin/?utm_source=wprepo-menu-image&utm_medium=wprepo_readme&utm_campaign=Plugin+URI
 * Author: Freshlight Lab
 * Author URI: https://www.freshlightlab.com/?utm_source=wprepo-menu-image&utm_medium=wprepo_readme&utm_campaign=Author+URI
 * Tested up to: 5.9
 * Text Domain: menu-image
 * Domain Path: /languages/
 * License: GPLv2
 */
if ( !defined( 'ABSPATH' ) ) {
    die;
}
define( 'MENU_IMAGE_VERSION', '3.0.8' );
define( 'MENU_IMAGE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MENU_IMAGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
/**
 * Provide attaching images to menu items.
 *
 * @package Menu_Image
 */
class WP_Menu_Image
{
    private  $image_size_1 = '' ;
    private  $image_size_2 = '' ;
    private  $image_size_3 = '' ;
    /**
     * Self provided image sizes for most menu usage.
     *
     * @var array
     */
    protected  $image_sizes = array() ;
    public  $mi_fs ;
    /**
     * List of used attachment ids grouped by size.
     *
     * Need to list all ids to prevent Jetpack Phonon in image_downsize filter.
     *
     * @var array
     */
    private  $used_attachments = array() ;
    /**
     * List of file extensions that allowed to resize and display as image.
     *
     * @var array
     */
    private  $additionalDisplayableImageExtensions = array( 'ico' ) ;
    /**
     * List of processed menu item ids.
     *
     * Mark item id as processed for new core version, 'cause old themes
     * doesn't supports filters like `nav_menu_link_attributes` and the only
     * way is to override whole element in filter `walker_nav_menu_start_el`.
     *
     * @var array
     */
    private  $processed = array() ;
    /**
     * Plugin constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Build the menu image item settings HTML.
     */
    public function get_menu_image_item_settings()
    {
        $menu_title = '';
        if ( isset( $_POST['menu_item_id'] ) ) {
            $menu_item_id = absint( $_POST['menu_item_id'] );
        }
        if ( isset( $_POST['menu_id'] ) ) {
            $menu_id = absint( $_POST['menu_id'] );
        }
        if ( isset( $_POST['menu_title'] ) ) {
            $menu_title = sanitize_text_field( $_POST['menu_title'] );
        }
        $output = '<div class="menu-image-item-settings-content" data-menu-id="' . $menu_id . '" data-menu-item-id="' . $menu_item_id . '">';
        $output .= '<div id="menu-image-modal-header"><h2 class="active" data-target="menu-image-icon-settings">' . __( 'Image & Icon', 'menu-image' ) . '</h2><h2 data-target="menu-image-button-settings">' . __( 'Buttons', 'menu-image' ) . '</h2><h2 data-target="menu-image-notifications-settings">' . __( 'Badges & Bubbles', 'menu-image' ) . '</h2><div class="menu-image-close-overlay"><span class="close-text">' . __( 'Close', 'menu-image' ) . '</span><span class="dashicons dashicons-no-alt"></span></div>';
        $output .= '</div><div id="menu-image-modal-body">';
        $output .= $this->wp_post_thumbnail_html( $menu_item_id, $menu_title );
        $output .= '</div></div></div>';
        echo  $output ;
        wp_die();
    }
    
    /**
     * Init Menu Image.
     */
    public function init_menu_image()
    {
        $this->add_image_sizes();
        // Add new admin menu options page for Menu image.
        add_action( 'admin_menu', array( $this, 'create_menu_image_options_page' ) );
        // Register Menu Image settings.
        add_action( 'admin_init', array( $this, 'register_menu_image_settings' ) );
        // Init Freemius.
        $this->mi_fs = $this->mi_fs();
        // Uninstall Action.
        $this->mi_fs->add_action( 'after_uninstall', array( $this, 'mm_fs_uninstall_cleanup' ) );
        // Freemius is loaded.
        do_action( 'mi_fs_loaded' );
        // Actions.
        add_action( 'init', array( $this, 'menu_image_init' ) );
        add_action(
            'wp_ajax_set-menu-item-settings',
            array( $this, 'menu_image_save_post_action' ),
            10,
            3
        );
        add_action( 'admin_head-nav-menus.php', array( $this, 'menu_image_admin_head_nav_menus_action' ) );
        add_action( 'toplevel_page_menu-image-options', array( $this, 'menu_image_admin_head_nav_menus_action' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'menu_image_add_inline_style_action' ) );
        add_action( 'admin_action_delete-menu-item-image', array( $this, 'menu_image_delete_menu_item_image_action' ) );
        add_action( 'wp_ajax_set-menu-item-thumbnail', array( $this, 'wp_ajax_set_menu_item_thumbnail' ) );
        add_action( 'wp_ajax_get_menu_image_item_settings', array( $this, 'get_menu_image_item_settings' ) );
        // Add support of WPML menus sync.
        add_action(
            'wp_update_nav_menu_item',
            array( $this, 'wp_update_nav_menu_item_action' ),
            10,
            2
        );
        add_action( 'admin_init', array( $this, 'admin_init' ), 99 );
        // Add menu custom fields.
        add_action(
            'wp_nav_menu_item_custom_fields',
            array( $this, 'menu_image_menu_custom_fields' ),
            1,
            2
        );
        // Add support for additional image types.
        add_filter(
            'file_is_displayable_image',
            array( $this, 'file_is_displayable_image' ),
            10,
            2
        );
        add_filter(
            'jetpack_photon_override_image_downsize',
            array( $this, 'jetpack_photon_override_image_downsize_filter' ),
            10,
            2
        );
        add_filter(
            'wp_get_attachment_image_attributes',
            array( $this, 'wp_get_attachment_image_attributes' ),
            99,
            3
        );
        // Add support for Max Megamenu.
        
        if ( function_exists( 'max_mega_menu_is_enabled' ) ) {
            add_filter(
                'megamenu_nav_menu_link_attributes',
                array( $this, 'menu_image_nav_menu_link_attributes_filter' ),
                10,
                3
            );
            add_filter(
                'megamenu_the_title',
                array( $this, 'menu_image_nav_menu_item_title_filter' ),
                10,
                2
            );
        }
        
        
        if ( !get_option( 'menu_image_disable_mobile' ) || get_option( 'menu_image_disable_mobile' ) && !wp_is_mobile() ) {
            // Filters.
            add_filter( 'wp_setup_nav_menu_item', array( $this, 'menu_image_wp_setup_nav_menu_item' ) );
            add_filter(
                'nav_menu_link_attributes',
                array( $this, 'menu_image_nav_menu_link_attributes_filter' ),
                10,
                4
            );
            add_filter( 'manage_nav-menus_columns', array( $this, 'menu_image_nav_menu_manage_columns' ), 11 );
            add_filter(
                'nav_menu_item_title',
                array( $this, 'menu_image_nav_menu_item_title_filter' ),
                10,
                4
            );
            add_filter(
                'the_title',
                array( $this, 'menu_image_nav_menu_item_title_filter' ),
                10,
                4
            );
        }
        
        // Menu image requires FontAwesome plugin notice.
        add_action( 'wp_ajax_dismiss_wp_menu_image_fa', array( $this, 'dismiss_wp_menu_image_fa_notice' ) );
    }
    
    /**
     * Admin init action with lowest execution priority
     */
    public function admin_init()
    {
        // Admin Scripts.
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }
    
    /**
     * Add Image sizes
     */
    public function add_image_sizes()
    {
        $this->image_size_1 = get_option( 'menu_image_size_1', '24x24' );
        $this->image_size_2 = get_option( 'menu_image_size_2', '36x36' );
        $this->image_size_3 = get_option( 'menu_image_size_3', '48x48' );
        $image_parts_1 = explode( 'x', $this->image_size_1 );
        $image_parts_2 = explode( 'x', $this->image_size_2 );
        $image_parts_3 = explode( 'x', $this->image_size_3 );
        /**
         * Self provided image sizes for most menu usage.
         *
         * @var array
         */
        $this->image_sizes = array(
            'menu-' . $this->image_size_1 => array( $image_parts_1[0], $image_parts_1[1], false ),
            'menu-' . $this->image_size_2 => array( $image_parts_2[0], $image_parts_2[1], false ),
            'menu-' . $this->image_size_3 => array( $image_parts_3[0], $image_parts_3[1], false ),
        );
    }
    
    /*
     * Create a helper function for easy SDK access.
     */
    public function mi_fs()
    {
        global  $mi_fs ;
        
        if ( !isset( $mi_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_4123_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_4123_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $mi_fs = fs_dynamic_init( array(
                'id'              => '4123',
                'slug'            => 'menu-image',
                'type'            => 'plugin',
                'public_key'      => 'pk_1a1cac31f5af1ba3d31bd86fe0e8b',
                'is_premium'      => false,
                'premium_suffix'  => 'Premium',
                'has_addons'      => false,
                'has_paid_plans'  => true,
                'has_affiliation' => 'selected',
                'trial'           => array(
                'days'               => 7,
                'is_require_payment' => true,
            ),
                'menu'            => array(
                'slug' => 'menu-image-options',
            ),
                'is_live'         => true,
            ) );
        }
        
        return $mi_fs;
    }
    
    /**
     * Filter adds additional validation for image type
     *
     * @param bool   $result
     * @param string $path
     *
     * @return bool
     */
    public function file_is_displayable_image( $result, $path )
    {
        if ( $result ) {
            return true;
        }
        $fileExtension = pathinfo( $path, PATHINFO_EXTENSION );
        return in_array( $fileExtension, $this->additionalDisplayableImageExtensions );
    }
    
    /**
     * Create the Menu Image options page
     */
    public function create_menu_image_options_page()
    {
        add_menu_page(
            'Menu Image',
            'Menu Image',
            'manage_options',
            'menu-image-options',
            array( $this, 'menu_image_options_page_html' ),
            'dashicons-menu',
            150
        );
    }
    
    /**
     * Validate Options of the submission form.
     */
    public function handle_options_form()
    {
        
        if ( !isset( $_POST['menu_image_form'] ) || !wp_verify_nonce( $_POST['menu_image_form'], 'menu_image_options_update' ) ) {
            ?>
			<div class="error">
			   <p><?php 
            _e( 'Sorry, your nonce was not correct. Please try again.', 'menu-image' );
            ?></p>
			</div> 
			<?php 
        } else {
            // Handle our form data.
            $enable_menu_image_hover = '0';
            $disable_in_mobile = '0';
            // If the value of the Menu Image Hover is set.
            if ( isset( $_POST['menu_image_hover'] ) ) {
                $enable_menu_image_hover = $_POST['menu_image_hover'];
            }
            // If the value of the Disable in Mobile Devices is set.
            if ( isset( $_POST['menu_image_disable_mobile'] ) ) {
                $disable_in_mobile = $_POST['menu_image_disable_mobile'];
            }
            $menu_image_size_1 = $_POST['menu_image_size_1'];
            $menu_image_size_2 = $_POST['menu_image_size_2'];
            $menu_image_size_3 = $_POST['menu_image_size_3'];
            $image_parts_1 = explode( 'x', $menu_image_size_1 );
            $image_parts_2 = explode( 'x', $menu_image_size_2 );
            $image_parts_3 = explode( 'x', $menu_image_size_3 );
            // Validate the menu image size format.
            
            if ( 2 === count( $image_parts_1 ) && 2 === count( $image_parts_2 ) && 2 === count( $image_parts_3 ) ) {
                update_option( 'menu_image_size_1', $menu_image_size_1 );
                update_option( 'menu_image_size_2', $menu_image_size_2 );
                update_option( 'menu_image_size_3', $menu_image_size_3 );
                update_option( 'menu_image_disable_mobile', $disable_in_mobile );
                update_option( 'menu_image_hover', $enable_menu_image_hover );
                ?>
				<div class="updated">
					<p><?php 
                _e( 'Your Menu Image settings were saved!', 'menu-image' );
                ?></p>
				</div>
				<?php 
            } else {
                ?>
				<div class="error">
					<p><?php 
                _e( 'Sorry, your image size format is not correct. Please try again.', 'menu-image' );
                ?></p>
				</div> 
				<?php 
            }
        
        }
    
    }
    
    /**
     * Create the Menu Image options page HTML
     */
    public function menu_image_options_page_html()
    {
        // check user capabilities.
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        // check if we are updating the options.
        if ( isset( $_POST['updated'] ) && 'true' === $_POST['updated'] ) {
            $this->handle_options_form();
        }
        ?>
		<style>
			.menu-image-settings-header .fs-notice {
				margin-top: 90px!important;
				margin-right: 20px!important;
			}
			.menu-image-settings-wrapper {
				background: #f1f1f1;
				padding: 20px;
			}
			.menu-image-settings-wrapper h2 {
				color: #094c68;
				border: 1px solid #DDD;
				padding: 10px;
				background: #fbfbfb;
				font-weight: 400;
			}
			.menu-image-settings-wrapper .form-table th {
				font-weight: 500;
			}
			.menu-image-settings-wrapper span.helper {
				padding-left: 10px;
			}
			span.menu-image-settings-h1 {
				padding: 15px;
				float: left;
				font-size: 2em;
				color: #fff;
			}
			.menu-image-logo-admin {
				float: left;
				width: 280px;
			}
			.menu-image-settings-header {
				padding: 20px 0px 20px 20px;
				background: #f7f7f7;
				margin-top:20px;
			}
			.menu-image-support-icon {
				margin-top: 25px;
				right: 70px;
				position: absolute;
				color: #004d6b;
			}
			.menu-image-support-icon a, .menu-image-doc-icon a  {
				text-decoration: none;
				color: #004d6b;
			}
			.menu-image-doc-icon {
				color: #004d6b;
				margin-top: 25px;
				right: 170px;
				position: absolute;
			}
			.menu-image-doc-icon a:hover, .menu-image-support-icon a:hover {
				color: #a0d203;
			}
			.toplevel_page_menu-image-options .wrap {
				display: grid;
			}
			
		</style>
		<div class="wrap">
			<h1><?php 
        _e( 'Menu Image Settings', 'menu-image' );
        ?></h1>
			<div class='menu-image-settings-header'>
				<img src='<?php 
        echo  MENU_IMAGE_PLUGIN_URL ;
        ?>includes/assets/img/menu-image-60.webp' class="menu-image-logo-admin">
				<div class="menu-image-doc-icon"><a href="https://www.freshlightlab.com/documentation/?utm_source=menu-image-settings&utm_medium=user%20website&utm_campaign=documentation_link_menu_image" target="_blank"><i class="dashicons-before dashicons-admin-page"></i><span>Documentation</span></a></div>
				<div class="menu-image-support-icon">
					<a href="https://www.freshlightlab.com/contact-us/?utm_source=menu-image-settings&utm_medium=user%20website&utm_campaign=support_link_menu_image" target="_blank">
						<i class="dashicons dashicons-admin-users "></i>
						<span><?php 
        _e( "Support", 'mobile-menu' );
        ?></span>
					</a>
				</div>
			</div>
			<div class="menu-image-settings-wrapper">
				<form method="POST">
					<?php 
        wp_nonce_field( 'menu_image_options_update', 'menu_image_form' );
        ?>
					<?php 
        do_settings_sections( 'menu-image-general-settings-group' );
        ?>
					<?php 
        do_settings_sections( 'menu-image-settings-group' );
        ?>
					<?php 
        settings_fields( 'menu-image-settings-group' );
        ?>
					<input type="hidden" name="updated" value="true" />
					<?php 
        submit_button();
        ?>
				</form>
			</div>
		</div>
		<?php 
    }
    
    /*
     * Render the HTML of menu_image_size_1 option.
     */
    function menu_image_size_1_render()
    {
        ?>
		<input name="menu_image_size_1" type="text" value="<?php 
        echo  get_option( 'menu_image_size_1', '24x24' ) ;
        ?>" /><span class="helper"><?php 
        _e( 'Use this format (24x24), width and height.', 'menu-image' );
        ?></span>
		<?php 
    }
    
    /*
     * Render the HTML of menu_image_size_2 option.
     */
    function menu_image_size_2_render()
    {
        ?>
		<input name="menu_image_size_2" type="text" value="<?php 
        echo  get_option( 'menu_image_size_2', '36x36' ) ;
        ?>" /><span class="helper"><?php 
        _e( 'Use this format (36x36), width and height.', 'menu-image' );
        ?></span>
		<?php 
    }
    
    /*
     * Render the HTML of menu_image_size_3 option.
     */
    function menu_image_size_3_render()
    {
        ?>
		<input name="menu_image_size_3" type="text" value="<?php 
        echo  get_option( 'menu_image_size_3', '48x48' ) ;
        ?>" /><span class="helper"><?php 
        _e( 'Use this format (48x48), width and height.', 'menu-image' );
        ?></span>
		</br></br><span class="helper"> If you change the image sizes after uploading the images you will need to regenerate all thumbnails using this </span><a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">plugin</a>.<p>It will also be necessary to select the icon image again in the menu items if you replaced any of the used custom image sizes.</p>
		<?php 
    }
    
    /*
     * Render the HTML of menu_image_hover option.
     */
    function menu_image_hover_render()
    {
        ?>
		<input name="menu_image_hover" type="checkbox" value="1" <?php 
        checked( '1', get_option( 'menu_image_hover', '1' ) );
        ?> /><span class="helper"><?php 
        _e( 'Enable the image on hover field', 'menu-image' );
        ?></span>
		<?php 
    }
    
    /*
     * Register Menu Image settings
     */
    public function register_menu_image_settings()
    {
        add_settings_section(
            'menu_image_general_options_section',
            __( 'General options', 'menu-image' ),
            '',
            'menu-image-general-settings-group'
        );
        add_settings_field(
            'menu_image_hover',
            __( 'Menu image Hover', 'menu-image' ),
            array( $this, 'menu_image_hover_render' ),
            'menu-image-general-settings-group',
            'menu_image_general_options_section'
        );
        add_settings_section(
            'menu_image_sizes_section',
            __( 'Image Sizes', 'menu-image' ),
            '',
            'menu-image-settings-group'
        );
        add_settings_field(
            'menu_image_size_1',
            __( '1st Menu Image size', 'menu-image' ),
            array( $this, 'menu_image_size_1_render' ),
            'menu-image-settings-group',
            'menu_image_sizes_section'
        );
        add_settings_field(
            'menu_image_size_2',
            __( '2nd Menu Image size', 'menu-image' ),
            array( $this, 'menu_image_size_2_render' ),
            'menu-image-settings-group',
            'menu_image_sizes_section'
        );
        add_settings_field(
            'menu_image_size_3',
            __( '3rd Menu Image size', 'menu-image' ),
            array( $this, 'menu_image_size_3_render' ),
            'menu-image-settings-group',
            'menu_image_sizes_section'
        );
    }
    
    /**
     * Initialization action.
     *
     * Adding image sizes for most popular menu icon sizes. Adding thumbnail
     * support to menu post type.
     */
    public function menu_image_init()
    {
        add_post_type_support( 'nav_menu_item', array( 'thumbnail' ) );
        $this->image_sizes = apply_filters( 'menu_image_default_sizes', $this->image_sizes );
        if ( is_array( $this->image_sizes ) ) {
            foreach ( $this->image_sizes as $name => $params ) {
                add_image_size(
                    $name,
                    $params[0],
                    $params[1],
                    ( array_key_exists( 2, $params ) ? $params[2] : false )
                );
            }
        }
        load_plugin_textdomain( 'menu-image', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }
    
    /**
     * Adding images as screen options.
     *
     * If not checked screen option 'image', uploading form not showed.
     *
     * @param array $columns
     *
     * @return array
     */
    public function menu_image_nav_menu_manage_columns( $columns )
    {
        return $columns + array(
            'image' => __( 'Image', 'menu-image' ),
        );
    }
    
    /**
     * Saving post action.
     *
     * Saving uploaded images and attach/detach to image post type.
     *
     * @param int     $post_id
     * @param WP_Post $post
     */
    public function menu_image_save_post_action( $post_id )
    {
        
        if ( !isset( $_POST['menu_item_nonce'] ) || !wp_verify_nonce( $_POST['menu_item_nonce'], 'update-menu-item' ) || !current_user_can( 'manage_options' ) ) {
            ?>
		
			<div class="error">
				<p><?php 
            _e( 'Unauthorized Access. Please try again.', 'menu-image' );
            ?></p>
			</div>
			<?php 
        } else {
            $menu_image_settings = array(
                'menu_item_image_title_position',
                'menu_item_image_size',
                'menu_item_image_button',
                'menu_image_icon',
                'menu_item_image_type',
                'menu_item_image_notification'
            );
            
            if ( isset( $_POST['menu_item_id'] ) ) {
                $post_id = $_POST['menu_item_id'];
            } else {
                return '';
            }
            
            foreach ( $menu_image_settings as $setting_name ) {
                if ( isset( $_POST[$setting_name] ) ) {
                    update_post_meta( $post_id, "_{$setting_name}", esc_sql( $_POST[$setting_name] ) );
                }
            }
        }
    
    }
    
    /**
     * Save item settings while WPML sync menus.
     *
     * @param $item_menu_id
     * @param $menu_item_db_id
     */
    public function wp_update_nav_menu_item_action( $item_menu_id, $menu_item_db_id )
    {
        global  $sitepress, $icl_menus_sync ;
        
        if ( class_exists( 'SitePress' ) && $sitepress instanceof SitePress && class_exists( 'ICLMenusSync' ) && $icl_menus_sync instanceof ICLMenusSync ) {
            static  $run_times = array() ;
            $menu_image_settings = array(
                'menu_item_image_size',
                'menu_item_image_title_position',
                'thumbnail_id',
                'thumbnail_hover_id'
            );
            // iterate synchronized menus.
            foreach ( $icl_menus_sync->menus as $menu_id => $menu_data ) {
                if ( !isset( $_POST['sync']['add'][$menu_id] ) ) {
                    continue;
                }
                // remove cache and get language current item menu.
                $cache_key = md5( serialize( array( $item_menu_id, 'tax_nav_menu' ) ) );
                $cache_group = 'get_language_for_element';
                wp_cache_delete( $cache_key, $cache_group );
                $lang = $sitepress->get_language_for_element( $item_menu_id, 'tax_nav_menu' );
                if ( !isset( $run_times[$menu_id][$lang] ) ) {
                    $run_times[$menu_id][$lang] = 0;
                }
                // Count static var for each menu id and saved item language
                // and get original item id from counted position of synchronized
                // items from POST data. That's all magic.
                $post_item_ids = array();
                foreach ( $_POST['sync']['add'][$menu_id] as $id => $lang_array ) {
                    if ( array_key_exists( $lang, $lang_array ) ) {
                        $post_item_ids[] = $id;
                    }
                }
                if ( !array_key_exists( $run_times[$menu_id][$lang], $post_item_ids ) ) {
                    continue;
                }
                $orig_item_id = $post_item_ids[$run_times[$menu_id][$lang]];
                // iterate all item settings and save it for new item.
                $orig_item_meta = get_metadata( 'post', $orig_item_id );
                foreach ( $menu_image_settings as $meta ) {
                    if ( isset( $orig_item_meta["_{$meta}"] ) && isset( $orig_item_meta["_{$meta}"][0] ) ) {
                        update_post_meta( $menu_item_db_id, "_{$meta}", $orig_item_meta["_{$meta}"][0] );
                    }
                }
                $run_times[$menu_id][$lang]++;
                break;
            }
        }
    
    }
    
    /**
     * Load menu image meta for each menu item.
     *
     * @since 2.0
     */
    public function menu_image_wp_setup_nav_menu_item( $item )
    {
        // Get Thumbnail ID.
        if ( !isset( $item->thumbnail_id ) ) {
            $item->thumbnail_id = get_post_thumbnail_id( $item->ID );
        }
        // Get Thumbnail Hover id.
        if ( !isset( $item->thumbnail_hover_id ) ) {
            $item->thumbnail_hover_id = get_post_meta( $item->ID, '_thumbnail_hover_id', true );
        }
        // Get Image Size.
        if ( !isset( $item->image_size ) ) {
            $item->image_size = get_post_meta( $item->ID, '_menu_item_image_size', true );
        }
        // Get Title Position.
        if ( !isset( $item->title_position ) ) {
            $item->title_position = get_post_meta( $item->ID, '_menu_item_image_title_position', true );
        }
        $menu_item_type = get_post_meta( $item->ID, '_menu_item_image_type', true );
        $item->menu_image_icon_type = $menu_item_type;
        return $item;
    }
    
    /**
     * Filters the HTML attributes applied to a menu item's anchor element.
     *
     * @param array $atts {
     *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
     *
     *     @type string $title  Title attribute.
     *     @type string $target Target attribute.
     *     @type string $rel    The rel attribute.
     *     @type string $href   The href attribute.
     * }
     * @param WP_Post  $item  The current menu item.
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param int      $depth Depth of menu item. Used for padding.
     *
     * @return array Link attributes.
     */
    public function menu_image_nav_menu_link_attributes_filter(
        $atts,
        $item,
        $args,
        $depth = null
    )
    {
        
        if ( isset( $item->thumbnail_id ) && '' !== $item->thumbnail_id && $item->thumbnail_id > 0 ) {
            $this->setProcessed( $item->ID );
            $position = ( $item->title_position ? $item->title_position : apply_filters( 'menu_image_default_title_position', 'after' ) );
            $class = ( !empty($atts['class']) ? $atts['class'] : '' );
            $class .= " menu-image-title-{$position}";
            
            if ( $item->thumbnail_hover_id ) {
                $class .= ' menu-image-hovered';
            } elseif ( $item->thumbnail_id ) {
                $class .= ' menu-image-not-hovered';
            }
            
            // Fix dropdown menu for Flatsome theme.
            if ( !empty($args->walker) && class_exists( 'FlatsomeNavDropdown' ) && $args->walker instanceof FlatsomeNavDropdown && !is_null( $depth ) && $depth === 0 ) {
                $class .= ' nav-top-link';
            }
            $atts['class'] = trim( $class );
        }
        
        return $atts;
    }
    
    /**
     * Replacement default menu item output.
     *
     * @param string $title Default item output
     * @param object $item  Menu item data object.
     * @param int    $depth Depth of menu item. Used for padding.
     * @param object $args
     *
     * @return string
     */
    public function menu_image_nav_menu_item_title_filter(
        $title,
        $item = null,
        $depth = null,
        $args = null
    )
    {
        if ( strpos( $title, 'menu-image' ) > 0 || !is_nav_menu_item( $item ) || !isset( $item ) ) {
            return $title;
        }
        if ( is_numeric( $item ) && $item < 0 ) {
            return $title;
        }
        if ( is_numeric( $item ) && $item > 0 ) {
            $item = wp_setup_nav_menu_item( get_post( $item ) );
        }
        $image = '';
        $position = '';
        $class = '';
        if ( isset( $item->menu_image_icon_type ) ) {
            // Check if we will add an icon or image to the menu item.
            
            if ( $item->menu_image_icon_type != 'icon' ) {
                
                if ( isset( $item->thumbnail_id ) && '' !== $item->thumbnail_id && $item->thumbnail_id > 0 ) {
                    $image_size = ( $item->image_size ? $item->image_size : apply_filters( 'menu_image_default_size', 'menu-36x36' ) );
                    $position = ( $item->title_position ? $item->title_position : apply_filters( 'menu_image_default_title_position', 'after' ) );
                    $class = "menu-image-title-{$position}";
                    $this->setUsedAttachments( $image_size, $item->thumbnail_id );
                    
                    if ( $item->thumbnail_hover_id ) {
                        $this->setUsedAttachments( $image_size, $item->thumbnail_hover_id );
                        $hover_image_src = wp_get_attachment_image_src( $item->thumbnail_hover_id, $image_size );
                        $margin_size = $hover_image_src[1];
                        $image = "<span class='menu-image-hover-wrapper'>";
                        $image .= wp_get_attachment_image(
                            $item->thumbnail_id,
                            $image_size,
                            false,
                            array(
                            'class' => "menu-image {$class}",
                            'alt'   => get_post_meta( $item->thumbnail_id, '_wp_attachment_image_alt', true ),
                        )
                        );
                        $image .= wp_get_attachment_image(
                            $item->thumbnail_hover_id,
                            $image_size,
                            false,
                            array(
                            'class' => "hovered-image {$class}",
                            'style' => "margin-left: -{$margin_size}px;",
                            'alt'   => get_post_meta( $item->thumbnail_hover_id, '_wp_attachment_image_alt', true ),
                        )
                        );
                        $image .= '</span>';
                    } elseif ( $item->thumbnail_id ) {
                        $image = wp_get_attachment_image(
                            $item->thumbnail_id,
                            $image_size,
                            false,
                            array(
                            'class' => "menu-image {$class}",
                            'alt'   => get_post_meta( $item->thumbnail_id, '_wp_attachment_image_alt', true ),
                        )
                        );
                    }
                    
                    $class .= ' menu-image-title';
                }
            
            } else {
                $selected_icon = get_post_meta( $item->ID, '_menu_image_icon', true );
                $position = ( $item->title_position ? $item->title_position : apply_filters( 'menu_image_default_title_position', 'after' ) );
                $class = "menu-image-title-{$position}";
                $image = '<span class="dashicons ' . $selected_icon . ' ' . $position . '-menu-image-icons"></span>';
                $class .= ' menu-image-title';
            }
        
        }
        $none = '';
        // Sugar.
        $image = apply_filters( 'menu_image_img_html', $image );
        switch ( $position ) {
            case 'hide':
            case 'before':
            case 'above':
                $item_args = array(
                    $none,
                    $class,
                    $title,
                    $image
                );
                break;
            case 'after':
            default:
                $item_args = array(
                    $image,
                    $class,
                    $title,
                    $none
                );
                break;
        }
        if ( $class != '' ) {
            $title = vsprintf( '%s<span class="%s">%s</span>%s', $item_args );
        }
        return $title;
    }
    
    /**
     * Replacement default menu item output.
     *
     * @param string $item_output Default item output.
     * @param object $item        Menu item data object.
     * @param int    $depth       Depth of menu item. Used for padding.
     * @param object $args
     *
     * @return string
     */
    public function menu_image_nav_menu_item_filter(
        $item_output,
        $item,
        $depth,
        $args
    )
    {
        if ( $this->isProcessed( $item->ID ) ) {
            return $item_output;
        }
        $attributes = ( !empty($item->attr_title) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '' );
        $attributes .= ( !empty($item->target) ? ' target="' . esc_attr( $item->target ) . '"' : '' );
        $attributes .= ( !empty($item->xfn) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '' );
        $attributes .= ( !empty($item->url) ? ' href="' . esc_attr( $item->url ) . '"' : '' );
        $attributes_array = shortcode_parse_atts( $attributes );
        $image_size = ( $item->image_size ? $item->image_size : apply_filters( 'menu_image_default_size', 'menu-36x36' ) );
        $position = ( $item->title_position ? $item->title_position : apply_filters( 'menu_image_default_title_position', 'after' ) );
        $class = "menu-image-title-{$position}";
        $this->setUsedAttachments( $image_size, $item->thumbnail_id );
        $image = '';
        
        if ( $item->thumbnail_hover_id ) {
            $this->setUsedAttachments( $image_size, $item->thumbnail_hover_id );
            $hover_image_src = wp_get_attachment_image_src( $item->thumbnail_hover_id, $image_size );
            $margin_size = $hover_image_src[1];
            $image = "<span class='menu-image-hover-wrapper'>";
            $image .= wp_get_attachment_image(
                $item->thumbnail_id,
                $image_size,
                false,
                "class=menu-image {$class}"
            );
            $image .= wp_get_attachment_image(
                $item->thumbnail_hover_id,
                $image_size,
                false,
                array(
                'class' => "hovered-image {$class}",
                'style' => "margin-left: -{$margin_size}px;",
            )
            );
            $image .= '</span>';
            $class .= ' menu-image-hovered';
        } elseif ( $item->thumbnail_id ) {
            $image = wp_get_attachment_image(
                $item->thumbnail_id,
                $image_size,
                false,
                "class=menu-image {$class}"
            );
            $class .= ' menu-image-not-hovered';
        }
        
        $attributes_array['class'] = $class;
        /**
         * Filter the menu link attributes.
         *
         * @since 2.6.7
         *
         * @param array  $attributes An array of attributes.
         * @param object $item      Menu item data object.
         * @param int    $depth     Depth of menu item. Used for padding.
         * @param object $args
         */
        $attributes_array = apply_filters(
            'menu_image_link_attributes',
            $attributes_array,
            $item,
            $depth,
            $args
        );
        $attributes = '';
        foreach ( $attributes_array as $attr_name => $attr_value ) {
            $attributes .= "{$attr_name}=\"{$attr_value}\" ";
        }
        $attributes = trim( $attributes );
        $item_output = "{$args->before}<a {$attributes}>";
        $link = $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $none = '';
        // Sugar.
        $image = apply_filters( 'menu_image_img_html', $image );
        switch ( $position ) {
            case 'hide':
            case 'before':
            case 'above':
                $item_args = array( $none, $link, $image );
                break;
            case 'after':
            default:
                $item_args = array( $image, $link, $none );
                break;
        }
        $item_output .= vsprintf( '%s<span class="menu-image-title">%s</span>%s', $item_args );
        $item_output .= "</a>{$args->after}";
        return $item_output;
    }
    
    /**
     * Loading additional stylesheet.
     *
     * Loading custom stylesheet to fix images positioning in match themes
     */
    public function menu_image_add_inline_style_action()
    {
        wp_register_style(
            'menu-image',
            plugins_url( '', __FILE__ ) . '/includes/css/menu-image.css',
            array(),
            MENU_IMAGE_VERSION
        );
        wp_enqueue_style( 'menu-image' );
        wp_enqueue_style( 'dashicons' );
    }
    
    public function wp_menu_image_fontawesome_admin_notice()
    {
        ?>

			<div class="wp-menu-image-notice notice notice-success is-dismissible" data-ajax-nonce="<?php 
        echo  wp_create_nonce( 'menu-image-fa-security-nonce' ) ;
        ?>">
				<span class="dashicons dashicons-warning"></span>

				<?php 
        _e( '<strong>Menu Image - In order to use the FontAwesome icons you need to install the official FontAwesome plugin and select the SVG tecnhonoly. Check <a href="https://www.freshlightlab.com/documentation/menu-image-docs/add-icons-to-wordpress-menu/?utm_source=menu-image-settings&utm_medium=user%20website&utm_campaign=install-fontawesome#fontawesome" target="_blank" >here</a> how to do it.</strong>', 'mobile-menu' );
        ?>
			</div>

	<?php 
    }
    
    /**
     * Dismiss the Menu Image Fontawesome Notice.
     */
    public function dismiss_wp_menu_image_fa_notice()
    {
        if ( check_ajax_referer( 'menu-image-fa-security-nonce', 'security' ) ) {
            update_option( 'wp_menu_image_fa_dismissed', 'yes' );
        }
        wp_die();
    }
    
    /**
     * Load Admin scripts
     */
    public function admin_enqueue_scripts( $hook )
    {
        //TODO: Add banner in Menu Image screen options
        if ( 'toplevel_page_menu-image-options' === $hook ) {
            // Check if FontAwesome.
            if ( !in_array( 'font-awesome/index.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && get_option( 'wp_menu_image_fa_dismissed', 'no' ) != 'yes' ) {
                add_action( 'admin_notices', array( $this, 'wp_menu_image_fontawesome_admin_notice' ) );
            }
        }
        
        if ( 'nav-menus.php' === $hook ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
        }
        
        if ( 'nav-menus.php' === $hook || 'toplevel_page_menu-image-options' === $hook ) {
            wp_enqueue_style( 'menu-image-admin-css', plugins_url( 'includes/css/menu-image-admin.css', __FILE__ ) );
        }
    }
    
    /**
     * Loading media-editor script ot nav-menus page.
     *
     * @since 2.0
     */
    public function menu_image_admin_head_nav_menus_action()
    {
        wp_enqueue_script(
            'menu-image-admin',
            plugins_url( '/includes/js/menu-image-admin.js', __FILE__ ),
            array( 'jquery' ),
            MENU_IMAGE_VERSION
        );
        wp_localize_script( 'menu-image-admin', 'menuImage', array(
            'l10n'     => array(
            'uploaderTitle'      => __( 'Chose menu image', 'menu-image' ),
            'uploaderButtonText' => __( 'Select', 'menu-image' ),
        ),
            'settings' => array(
            'nonce' => wp_create_nonce( 'update-menu-item' ),
        ),
        ) );
        wp_enqueue_media();
        wp_enqueue_style( 'editor-buttons' );
    }
    
    /**
     * When menu item removed remove menu image metadata.
     */
    public function menu_image_delete_menu_item_image_action()
    {
        $menu_item_id = (int) $_REQUEST['menu-item'];
        check_admin_referer( 'delete-menu_item_image_' . $menu_item_id );
        
        if ( is_nav_menu_item( $menu_item_id ) && has_post_thumbnail( $menu_item_id ) ) {
            delete_post_thumbnail( $menu_item_id );
            delete_post_meta( $menu_item_id, '_thumbnail_hover_id' );
            delete_post_meta( $menu_item_id, '_menu_item_image_size' );
            delete_post_meta( $menu_item_id, '_menu_item_image_title_position' );
            //TODO include other postmeta
        }
    
    }
    
    /**
     * Output HTML for the menu item images.
     *
     * @since 2.0
     *
     * @param int $item_id The post ID or object associated with the thumbnail, defaults to global $post.
     *
     * @return string html
     */
    public function wp_post_thumbnail_only_html( $item_id )
    {
        $default_size = apply_filters( 'menu_image_default_size', 'menu-24x24' );
        $markup = '<p class="description description-half" ><label>%s</label><a title="%s" href="#" class="set-post-thumbnail button%s" data-item-id="%s" style="height: auto;">%s</a>%s</p>';
        $thumbnail_id = get_post_thumbnail_id( $item_id );
        $content = sprintf(
            $markup,
            esc_html__( 'Image', 'menu-image' ),
            ( $thumbnail_id ? esc_attr__( 'Change menu item image', 'menu-image' ) : esc_attr__( 'Set menu item image', 'menu-image' ) ),
            '',
            $item_id,
            ( $thumbnail_id ? wp_get_attachment_image(
            $thumbnail_id,
            $default_size,
            '',
            array(
            'class' => 'menu-img-normal',
        )
        ) : esc_html__( 'Set image', 'menu-image' ) ),
            ( $thumbnail_id ? '<a href="#" class="remove-post-thumbnail">' . __( 'Remove', 'menu-image' ) . '</a>' : '' )
        );
        // Menu image on hover if enabled.
        
        if ( '1' == get_option( 'menu_image_hover', '1' ) ) {
            $hover_id = get_post_meta( $item_id, '_thumbnail_hover_id', true );
            $content .= sprintf(
                $markup,
                esc_html__( 'Image on hover', 'menu-image' ),
                ( $hover_id ? esc_attr__( 'Change menu item image on hover', 'menu-image' ) : esc_attr__( 'Set menu item image on hover', 'menu-image' ) ),
                ' hover-image',
                $item_id,
                ( $hover_id ? wp_get_attachment_image(
                $hover_id,
                $default_size,
                '',
                array(
                'class' => 'menu-img-hover',
            )
            ) : esc_html__( 'Set image on hover', 'menu-image' ) ),
                ( $hover_id ? '<a href="#" class="remove-post-thumbnail hover-image">' . __( 'Remove', 'menu-image' ) . '</a>' : '' )
            );
        }
        
        return $content;
    }
    
    public function menu_image_menu_custom_fields()
    {
        ?>
		<!--<i class='menu-image-item-settings mob-icon-mobile-2'><span class='dashicons dashicons-admin-generic'></span><span>Add Image/Icon</span></i>-->
		<button class="menu-image-button"><span class='dashicons dashicons-plus'></span><span>Add Image/Icon</span></button>
		<?php 
        return;
    }
    
    public function menu_image_build_dashicons_list( $item_id )
    {
        $dashicons = [];
        $dashicons = explode( ',', 'dashicons-menu, 
			dashicons-menu-alt, 
			dashicons-menu-alt2, 
			dashicons-menu-alt3, 
			dashicons-admin-site, 
			dashicons-admin-site-alt, 
			dashicons-admin-site-alt2, 
			dashicons-admin-site-alt3,
			dashicons-admin-site-alt2,
			dashicons-admin-site-alt3,
			dashicons-admin-site-alt3,
			dashicons-dashboard,
			dashicons-admin-post,
			dashicons-admin-media,
			dashicons-admin-links,
			dashicons-admin-page,
			dashicons-admin-comments,
			dashicons-admin-appearance,
			dashicons-admin-plugins,
			dashicons-plugins-checked,
			dashicons-admin-users,
			dashicons-admin-tools,
			dashicons-admin-settings,
			dashicons-admin-network,
			dashicons-admin-home,
			dashicons-admin-generic,
			dashicons-admin-collapse,
			dashicons-filter,
			dashicons-admin-customizer,
			dashicons-admin-multisite,
			dashicons-welcome-write-blog,
			dashicons-welcome-add-page,
			dashicons-welcome-view-site,
			dashicons-welcome-widgets-menus,
			dashicons-welcome-comments,
			dashicons-welcome-learn-more,
			dashicons-format-aside,
			dashicons-format-image,
			dashicons-format-gallery,
			dashicons-format-video,
			dashicons-format-status,
			dashicons-format-quote,
			dashicons-format-chat,
			dashicons-format-audio,
			dashicons-camera,
			dashicons-camera-alt,
			dashicons-images-alt,
			dashicons-images-alt2,
			dashicons-video-alt,
			dashicons-video-alt2,
			dashicons-video-alt3,
			dashicons-media-archive,
			dashicons-media-audio,
			dashicons-media-code,
			dashicons-media-default,
			dashicons-media-document,
			dashicons-media-interactive,
			dashicons-media-spreadsheet,
			dashicons-media-text,
			dashicons-media-video,
			dashicons-playlist-audio,
			dashicons-playlist-video,
			dashicons-controls-play,
			dashicons-controls-pause,
			dashicons-controls-forward,
			dashicons-controls-skipforward,
			dashicons-controls-back,
			dashicons-controls-skipback,
			dashicons-controls-repeat,
			dashicons-controls-volumeon,
			dashicons-controls-volumeoff,
			dashicons-image-crop,
			dashicons-image-rotate,
			dashicons-image-rotate-left,
			dashicons-image-rotate-right,
			dashicons-image-flip-vertical,
			dashicons-image-flip-horizontal,
			dashicons-image-filter,
			dashicons-undo,
			dashicons-redo,
			dashicons-database-add,
			dashicons-database,
			dashicons-database-export,
			dashicons-database-import,
			dashicons-database-remove,
			dashicons-database-view,
			dashicons-align-full-width,
			dashicons-align-pull-left,
			dashicons-align-pull-right,
			dashicons-align-wide,
			dashicons-block-default,
			dashicons-button,
			dashicons-cloud-saved,
			dashicons-cloud-upload,
			dashicons-columns,
			dashicons-cover-image,
			dashicons-ellipsis,
			dashicons-embed-audio,
			dashicons-embed-generic,
			dashicons-embed-photo,
			dashicons-embed-post,
			dashicons-embed-video,
			dashicons-exit,
			dashicons-heading,
			dashicons-html,
			dashicons-info-outline,
			dashicons-insert,
			dashicons-insert-after,
			dashicons-insert-before,
			dashicons-remove,
			dashicons-saved,
			dashicons-shortcode,
			dashicons-table-col-after,
			dashicons-table-col-before,
			dashicons-table-col-delete,
			dashicons-table-row-after,
			dashicons-table-row-before,
			dashicons-table-row-delete,
			dashicons-editor-bold,
			dashicons-editor-italic,
			dashicons-editor-ul,
			dashicons-editor-ol,
			dashicons-editor-ol-rtl,
			dashicons-editor-quote,
			dashicons-editor-alignleft,
			dashicons-editor-aligncenter,
			dashicons-editor-alignright,
			dashicons-editor-insertmore,
			dashicons-editor-spellcheck,
			dashicons-editor-expand,
			dashicons-editor-contract,
			dashicons-editor-kitchensink,
			dashicons-editor-underline,
			dashicons-editor-justify,
			dashicons-editor-textcolor,
			dashicons-editor-paste-word,
			dashicons-editor-paste-text,
			dashicons-editor-removeformatting,
			dashicons-editor-video,
			dashicons-editor-customchar,
			dashicons-editor-outdent,
			dashicons-editor-indent,
			dashicons-editor-help,
			dashicons-editor-strikethrough,
			dashicons-editor-unlink,
			dashicons-editor-rtl,
			dashicons-editor-ltr,
			dashicons-editor-break,
			dashicons-editor-code,
			dashicons-editor-paragraph,
			dashicons-editor-table,
			dashicons-align-left,
			dashicons-align-right,
			dashicons-align-center,
			dashicons-align-none,
			dashicons-lock,
			dashicons-unlock,
			dashicons-calendar,
			dashicons-calendar-alt,
			dashicons-visibility,
			dashicons-hidden,
			dashicons-post-status,
			dashicons-edit,
			dashicons-trash,
			dashicons-sticky,
			dashicons-external,
			dashicons-arrow-up,
			dashicons-arrow-down,
			dashicons-arrow-right,
			dashicons-arrow-left,
			dashicons-arrow-up-alt,
			dashicons-arrow-down-alt,
			dashicons-arrow-right-alt,
			dashicons-arrow-left-alt,
			dashicons-arrow-up-alt2,
			dashicons-arrow-down-alt2,
			dashicons-arrow-right-alt,
			dashicons-arrow-left-alt2,
			dashicons-sort,
			dashicons-leftright,
			dashicons-randomize,
			dashicons-list-view,
			dashicons-excerpt-view,
			dashicons-grid-view,
			dashicons-move,
			dashicons-share,
			dashicons-share-alt,
			dashicons-share-alt2,
			dashicons-rss,
			dashicons-email,
			dashicons-email-alt,
			dashicons-email-alt2,
			dashicons-networking,
			dashicons-amazon,
			dashicons-facebook,
			dashicons-facebook-alt,
			dashicons-google,
			dashicons-googleplus,
			dashicons-instagram,
			dashicons-linkedin,
			dashicons-pinterest,
			dashicons-podio,
			dashicons-reddit,
			dashicons-spotify,
			dashicons-twitch,
			dashicons-twitter,
			dashicons-twitter-alt,
			dashicons-whatsapp,
			dashicons-xing,
			dashicons-youtube,
			dashicons-hammer,
			dashicons-art,
			dashicons-migrate,
			dashicons-performance,
			dashicons-universal-access,
			dashicons-universal-access-alt,
			dashicons-tickets,
			dashicons-nametag,
			dashicons-clipboard,
			dashicons-heart,
			dashicons-megaphone,
			dashicons-schedule,
			dashicons-tide,
			dashicons-rest-api,
			dashicons-code-standards,
			dashicons-buddicons-activity,
			dashicons-buddicons-bbpress-logo,
			dashicons-buddicons-buddypress-logo,
			dashicons-buddicons-community,
			dashicons-buddicons-forums,
			dashicons-buddicons-friends,
			dashicons-buddicons-groups,
			dashicons-buddicons-pm,
			dashicons-buddicons-replies,
			dashicons-buddicons-topics,
			dashicons-buddicons-tracking,
			dashicons-wordpress,
			dashicons-wordpress-alt,
			dashicons-pressthis,
			dashicons-update,
			dashicons-update-alt,
			dashicons-screenoptions,
			dashicons-info,
			dashicons-cart,
			dashicons-feedback,
			dashicons-cloud,
			dashicons-translation,
			dashicons-tag,
			dashicons-category,
			dashicons-archive,
			dashicons-tagcloud,
			dashicons-text,
			dashicons-bell,
			dashicons-yes,
			dashicons-yes-alt,
			dashicons-no,
			dashicons-no-alt,
			dashicons-plus,
			dashicons-plus-alt,
			dashicons-plus-alt2,
			dashicons-minus,
			dashicons-dismiss,
			dashicons-marker,
			dashicons-star-filled,
			dashicons-star-half,
			dashicons-star-empty,
			dashicons-flag,
			dashicons-warning,
			dashicons-location,
			dashicons-location-alt,
			dashicons-vault,
			dashicons-shield,
			dashicons-shield-alt,
			dashicons-sos,
			dashicons-search,
			dashicons-slides,
			dashicons-text-page,
			dashicons-analytics,
			dashicons-chart-pie,
			dashicons-chart-bar,
			dashicons-chart-line,
			dashicons-chart-area,
			dashicons-groups,
			dashicons-businessman,
			dashicons-businesswoman,
			dashicons-businessperson,
			dashicons-id,
			dashicons-id-alt,
			dashicons-products,
			dashicons-awards,
			dashicons-forms,
			dashicons-testimonial,
			dashicons-portfolio,
			dashicons-book,
			dashicons-book-alt,
			dashicons-download,
			dashicons-upload,
			dashicons-backup,
			dashicons-clock,
			dashicons-lightbulb,
			dashicons-microphone,
			dashicons-desktop,
			dashicons-laptop,
			dashicons-tablet,
			dashicons-smartphone,
			dashicons-phone,
			dashicons-index-card,
			dashicons-carrot,
			dashicons-building,
			dashicons-store,
			dashicons-album,
			dashicons-palmtree,
			dashicons-tickets-alt,
			dashicons-money,
			dashicons-money-alt,
			dashicons-smiley,
			dashicons-thumbs-up,
			dashicons-thumbs-down,
			dashicons-layout,
			dashicons-paperclip,
			dashicons-color-picker,
			dashicons-edit-large,
			dashicons-edit-page,
			dashicons-airplane,
			dashicons-bank,
			dashicons-beer,
			dashicons-calculator,
			dashicons-car,
			dashicons-coffee,
			dashicons-drumstick,
			dashicons-food,
			dashicons-fullscreen-alt,
			dashicons-fullscreen-exit-alt,
			dashicons-games,
			dashicons-hourglass,
			dashicons-open-folder,
			dashicons-pdf,
			dashicons-pets,
			dashicons-printer,
			dashicons-privacy,
			dashicons-superhero,
			dashicons-superhero-alt' );
        // Get the selected icon if it exists.
        $selected_icon = get_post_meta( $item_id, '_menu_image_icon', true );
        // Dashicons Icons List.
        
        if ( strpos( $selected_icon, 'dashicons' ) !== false || $selected_icon == '' ) {
            echo  '<div class="menu-image-dashicons-list active">' ;
        } else {
            echo  '<div class="menu-image-dashicons-list">' ;
        }
        
        // Loop through the list of icons.
        foreach ( $dashicons as $icon ) {
            $icon = preg_replace( '/\\s+/', '', $icon );
            
            if ( $icon == $selected_icon ) {
                echo  '<span class="dashicons ' . $icon . ' menu-item-icon-selected"></span>' ;
            } else {
                echo  '<span class="dashicons ' . $icon . '"></span>' ;
            }
        
        }
        echo  '</div>' ;
    }
    
    public function menu_image_build_fa_list( $item_id )
    {
        $icons_brand = [];
        $icons_solid = [];
        $icons_regular = [];
        $fa_solid = explode( ',', 'fas fa-ad,fas fa-address-book,fas fa-address-card,fas fa-adjust,fas fa-air-freshener,fas fa-align-center,fas fa-align-justify,fas fa-align-left,fas fa-align-right,fas fa-allergies,fas fa-ambulance,fas fa-american-sign-language-interpreting,fas fa-anchor,fas fa-angle-double-down,fas fa-angle-double-left,fas fa-angle-double-right,fas fa-angle-double-up,fas fa-angle-down,fas fa-angle-left,fas fa-angle-right,fas fa-angle-up,fas fa-angry,fas fa-ankh,fas fa-apple-alt,fas fa-archive,fas fa-archway,fas fa-arrow-alt-circle-down,fas fa-arrow-alt-circle-left,fas fa-arrow-alt-circle-right,fas fa-arrow-alt-circle-up,fas fa-arrow-circle-down,fas fa-arrow-circle-left,fas fa-arrow-circle-right,fas fa-arrow-circle-up,fas fa-arrow-down,fas fa-arrow-left,fas fa-arrow-right,fas fa-arrow-up,fas fa-arrows-alt,fas fa-arrows-alt-h,fas fa-arrows-alt-v,fas fa-assistive-listening-systems,fas fa-asterisk,fas fa-at,fas fa-atlas,fas fa-atom,fas fa-audio-description,fas fa-award,fas fa-baby,fas fa-baby-carriage,fas fa-backspace,fas fa-backward,fas fa-bacon,fas fa-bacteria,fas fa-bacterium,fas fa-bahai,fas fa-balance-scale,fas fa-balance-scale-left,fas fa-balance-scale-right,fas fa-ban,fas fa-band-aid,fas fa-barcode,fas fa-bars,fas fa-baseball-ball,fas fa-basketball-ball,fas fa-bath,fas fa-battery-empty,fas fa-battery-full,fas fa-battery-half,fas fa-battery-quarter,fas fa-battery-three-quarters,fas fa-bed,fas fa-beer,fas fa-bell,fas fa-bell-slash,fas fa-bezier-curve,fas fa-bible,fas fa-bicycle,fas fa-biking,fas fa-binoculars,fas fa-biohazard,fas fa-birthday-cake,fas fa-blender,fas fa-blender-phone,fas fa-blind,fas fa-blog,fas fa-bold,fas fa-bolt,fas fa-bomb,fas fa-bone,fas fa-bong,fas fa-book,fas fa-book-dead,fas fa-book-medical,fas fa-book-open,fas fa-book-reader,fas fa-bookmark,fas fa-border-all,fas fa-border-none,fas fa-border-style,fas fa-bowling-ball,fas fa-box,fas fa-box-open,fas fa-box-tissue,fas fa-boxes,fas fa-braille,fas fa-brain,fas fa-bread-slice,fas fa-briefcase,fas fa-briefcase-medical,fas fa-broadcast-tower,fas fa-broom,fas fa-brush,fas fa-bug,fas fa-building,fas fa-bullhorn,fas fa-bullseye,fas fa-burn,fas fa-bus,fas fa-bus-alt,fas fa-business-time,fas fa-calculator,fas fa-calendar,fas fa-calendar-alt,fas fa-calendar-check,fas fa-calendar-day,fas fa-calendar-minus,fas fa-calendar-plus,fas fa-calendar-times,fas fa-calendar-week,fas fa-camera,fas fa-camera-retro,fas fa-campground,fas fa-candy-cane,fas fa-cannabis,fas fa-capsules,fas fa-car,fas fa-car-alt,fas fa-car-battery,fas fa-car-crash,fas fa-car-side,fas fa-caravan,fas fa-caret-down,fas fa-caret-left,fas fa-caret-right,fas fa-caret-square-down,fas fa-caret-square-left,fas fa-caret-square-right,fas fa-caret-square-up,fas fa-caret-up,fas fa-carrot,fas fa-cart-arrow-down,fas fa-cart-plus,fas fa-cash-register,fas fa-cat,fas fa-certificate,fas fa-chair,fas fa-chalkboard,fas fa-chalkboard-teacher,fas fa-charging-station,fas fa-chart-area,fas fa-chart-bar,fas fa-chart-line,fas fa-chart-pie,fas fa-check,fas fa-check-circle,fas fa-check-double,fas fa-check-square,fas fa-cheese,fas fa-chess,fas fa-chess-bishop,fas fa-chess-board,fas fa-chess-king,fas fa-chess-knight,fas fa-chess-pawn,fas fa-chess-queen,fas fa-chess-rook,fas fa-chevron-circle-down,fas fa-chevron-circle-left,fas fa-chevron-circle-right,fas fa-chevron-circle-up,fas fa-chevron-down,fas fa-chevron-left,fas fa-chevron-right,fas fa-chevron-up,fas fa-child,fas fa-church,fas fa-circle,fas fa-circle-notch,fas fa-city,fas fa-clinic-medical,fas fa-clipboard,fas fa-clipboard-check,fas fa-clipboard-list,fas fa-clock,fas fa-clone,fas fa-closed-captioning,fas fa-cloud,fas fa-cloud-download-alt,fas fa-cloud-meatball,fas fa-cloud-moon,fas fa-cloud-moon-rain,fas fa-cloud-rain,fas fa-cloud-showers-heavy,fas fa-cloud-sun,fas fa-cloud-sun-rain,fas fa-cloud-upload-alt,fas fa-cocktail,fas fa-code,fas fa-code-branch,fas fa-coffee,fas fa-cog,fas fa-cogs,fas fa-coins,fas fa-columns,fas fa-comment,fas fa-comment-alt,fas fa-comment-dollar,fas fa-comment-dots,fas fa-comment-medical,fas fa-comment-slash,fas fa-comments,fas fa-comments-dollar,fas fa-compact-disc,fas fa-compass,fas fa-compress,fas fa-compress-alt,fas fa-compress-arrows-alt,fas fa-concierge-bell,fas fa-cookie,fas fa-cookie-bite,fas fa-copy,fas fa-copyright,fas fa-couch,fas fa-credit-card,fas fa-crop,fas fa-crop-alt,fas fa-cross,fas fa-crosshairs,fas fa-crow,fas fa-crown,fas fa-crutch,fas fa-cube,fas fa-cubes,fas fa-cut,fas fa-database,fas fa-deaf,fas fa-democrat,fas fa-desktop,fas fa-dharmachakra,fas fa-diagnoses,fas fa-dice,fas fa-dice-d20,fas fa-dice-d6,fas fa-dice-five,fas fa-dice-four,fas fa-dice-one,fas fa-dice-six,fas fa-dice-three,fas fa-dice-two,fas fa-digital-tachograph,fas fa-directions,fas fa-disease,fas fa-divide,fas fa-dizzy,fas fa-dna,fas fa-dog,fas fa-dollar-sign,fas fa-dolly,fas fa-dolly-flatbed,fas fa-donate,fas fa-door-closed,fas fa-door-open,fas fa-dot-circle,fas fa-dove,fas fa-download,fas fa-drafting-compass,fas fa-dragon,fas fa-draw-polygon,fas fa-drum,fas fa-drum-steelpan,fas fa-drumstick-bite,fas fa-dumbbell,fas fa-dumpster,fas fa-dumpster-fire,fas fa-dungeon,fas fa-edit,fas fa-egg,fas fa-eject,fas fa-ellipsis-h,fas fa-ellipsis-v,fas fa-envelope,fas fa-envelope-open,fas fa-envelope-open-text,fas fa-envelope-square,fas fa-equals,fas fa-eraser,fas fa-ethernet,fas fa-euro-sign,fas fa-exchange-alt,fas fa-exclamation,fas fa-exclamation-circle,fas fa-exclamation-triangle,fas fa-expand,fas fa-expand-alt,fas fa-expand-arrows-alt,fas fa-external-link-alt,fas fa-external-link-square-alt,fas fa-eye,fas fa-eye-dropper,fas fa-eye-slash,fas fa-fan,fas fa-fast-backward,fas fa-fast-forward,fas fa-faucet,fas fa-fax,fas fa-feather,fas fa-feather-alt,fas fa-female,fas fa-fighter-jet,fas fa-file,fas fa-file-alt,fas fa-file-archive,fas fa-file-audio,fas fa-file-code,fas fa-file-contract,fas fa-file-csv,fas fa-file-download,fas fa-file-excel,fas fa-file-export,fas fa-file-image,fas fa-file-import,fas fa-file-invoice,fas fa-file-invoice-dollar,fas fa-file-medical,fas fa-file-medical-alt,fas fa-file-pdf,fas fa-file-powerpoint,fas fa-file-prescription,fas fa-file-signature,fas fa-file-upload,fas fa-file-video,fas fa-file-word,fas fa-fill,fas fa-fill-drip,fas fa-film,fas fa-filter,fas fa-fingerprint,fas fa-fire,fas fa-fire-alt,fas fa-fire-extinguisher,fas fa-first-aid,fas fa-fish,fas fa-fist-raised,fas fa-flag,fas fa-flag-checkered,fas fa-flag-usa,fas fa-flask,fas fa-flushed,fas fa-folder,fas fa-folder-minus,fas fa-folder-open,fas fa-folder-plus,fas fa-font,fas fa-font-awesome-logo-full,fas fa-football-ball,fas fa-forward,fas fa-frog,fas fa-frown,fas fa-frown-open,fas fa-funnel-dollar,fas fa-futbol,fas fa-gamepad,fas fa-gas-pump,fas fa-gavel,fas fa-gem,fas fa-genderless,fas fa-ghost,fas fa-gift,fas fa-gifts,fas fa-glass-cheers,fas fa-glass-martini,fas fa-glass-martini-alt,fas fa-glass-whiskey,fas fa-glasses,fas fa-globe,fas fa-globe-africa,fas fa-globe-americas,fas fa-globe-asia,fas fa-globe-europe,fas fa-golf-ball,fas fa-gopuram,fas fa-graduation-cap,fas fa-greater-than,fas fa-greater-than-equal,fas fa-grimace,fas fa-grin,fas fa-grin-alt,fas fa-grin-beam,fas fa-grin-beam-sweat,fas fa-grin-hearts,fas fa-grin-squint,fas fa-grin-squint-tears,fas fa-grin-stars,fas fa-grin-tears,fas fa-grin-tongue,fas fa-grin-tongue-squint,fas fa-grin-tongue-wink,fas fa-grin-wink,fas fa-grip-horizontal,fas fa-grip-lines,fas fa-grip-lines-vertical,fas fa-grip-vertical,fas fa-guitar,fas fa-h-square,fas fa-hamburger,fas fa-hammer,fas fa-hamsa,fas fa-hand-holding,fas fa-hand-holding-heart,fas fa-hand-holding-medical,fas fa-hand-holding-usd,fas fa-hand-holding-water,fas fa-hand-lizard,fas fa-hand-middle-finger,fas fa-hand-paper,fas fa-hand-peace,fas fa-hand-point-down,fas fa-hand-point-left,fas fa-hand-point-right,fas fa-hand-point-up,fas fa-hand-pointer,fas fa-hand-rock,fas fa-hand-scissors,fas fa-hand-sparkles,fas fa-hand-spock,fas fa-hands,fas fa-hands-helping,fas fa-hands-wash,fas fa-handshake,fas fa-handshake-alt-slash,fas fa-handshake-slash,fas fa-hanukiah,fas fa-hard-hat,fas fa-hashtag,fas fa-hat-cowboy,fas fa-hat-cowboy-side,fas fa-hat-wizard,fas fa-hdd,fas fa-head-side-cough,fas fa-head-side-cough-slash,fas fa-head-side-mask,fas fa-head-side-virus,fas fa-heading,fas fa-headphones,fas fa-headphones-alt,fas fa-headset,fas fa-heart,fas fa-heart-broken,fas fa-heartbeat,fas fa-helicopter,fas fa-highlighter,fas fa-hiking,fas fa-hippo,fas fa-history,fas fa-hockey-puck,fas fa-holly-berry,fas fa-home,fas fa-horse,fas fa-horse-head,fas fa-hospital,fas fa-hospital-alt,fas fa-hospital-symbol,fas fa-hospital-user,fas fa-hot-tub,fas fa-hotdog,fas fa-hotel,fas fa-hourglass,fas fa-hourglass-end,fas fa-hourglass-half,fas fa-hourglass-start,fas fa-house-damage,fas fa-house-user,fas fa-hryvnia,fas fa-i-cursor,fas fa-ice-cream,fas fa-icicles,fas fa-icons,fas fa-id-badge,fas fa-id-card,fas fa-id-card-alt,fas fa-igloo,fas fa-image,fas fa-images,fas fa-inbox,fas fa-indent,fas fa-industry,fas fa-infinity,fas fa-info,fas fa-info-circle,fas fa-italic,fas fa-jedi,fas fa-joint,fas fa-journal-whills,fas fa-kaaba,fas fa-key,fas fa-keyboard,fas fa-khanda,fas fa-kiss,fas fa-kiss-beam,fas fa-kiss-wink-heart,fas fa-kiwi-bird,fas fa-landmark,fas fa-language,fas fa-laptop,fas fa-laptop-code,fas fa-laptop-house,fas fa-laptop-medical,fas fa-laugh,fas fa-laugh-beam,fas fa-laugh-squint,fas fa-laugh-wink,fas fa-layer-group,fas fa-leaf,fas fa-lemon,fas fa-less-than,fas fa-less-than-equal,fas fa-level-down-alt,fas fa-level-up-alt,fas fa-life-ring,fas fa-lightbulb,fas fa-link,fas fa-lira-sign,fas fa-list,fas fa-list-alt,fas fa-list-ol,fas fa-list-ul,fas fa-location-arrow,fas fa-lock,fas fa-lock-open,fas fa-long-arrow-alt-down,fas fa-long-arrow-alt-left,fas fa-long-arrow-alt-right,fas fa-long-arrow-alt-up,fas fa-low-vision,fas fa-luggage-cart,fas fa-lungs,fas fa-lungs-virus,fas fa-magic,fas fa-magnet,fas fa-mail-bulk,fas fa-male,fas fa-map,fas fa-map-marked,fas fa-map-marked-alt,fas fa-map-marker,fas fa-map-marker-alt,fas fa-map-pin,fas fa-map-signs,fas fa-marker,fas fa-mars,fas fa-mars-double,fas fa-mars-stroke,fas fa-mars-stroke-h,fas fa-mars-stroke-v,fas fa-mask,fas fa-medal,fas fa-medkit,fas fa-meh,fas fa-meh-blank,fas fa-meh-rolling-eyes,fas fa-memory,fas fa-menorah,fas fa-mercury,fas fa-meteor,fas fa-microchip,fas fa-microphone,fas fa-microphone-alt,fas fa-microphone-alt-slash,fas fa-microphone-slash,fas fa-microscope,fas fa-minus,fas fa-minus-circle,fas fa-minus-square,fas fa-mitten,fas fa-mobile,fas fa-mobile-alt,fas fa-money-bill,fas fa-money-bill-alt,fas fa-money-bill-wave,fas fa-money-bill-wave-alt,fas fa-money-check,fas fa-money-check-alt,fas fa-monument,fas fa-moon,fas fa-mortar-pestle,fas fa-mosque,fas fa-motorcycle,fas fa-mountain,fas fa-mouse,fas fa-mouse-pointer,fas fa-mug-hot,fas fa-music,fas fa-network-wired,fas fa-neuter,fas fa-newspaper,fas fa-not-equal,fas fa-notes-medical,fas fa-object-group,fas fa-object-ungroup,fas fa-oil-can,fas fa-om,fas fa-otter,fas fa-outdent,fas fa-pager,fas fa-paint-brush,fas fa-paint-roller,fas fa-palette,fas fa-pallet,fas fa-paper-plane,fas fa-paperclip,fas fa-parachute-box,fas fa-paragraph,fas fa-parking,fas fa-passport,fas fa-pastafarianism,fas fa-paste,fas fa-pause,fas fa-pause-circle,fas fa-paw,fas fa-peace,fas fa-pen,fas fa-pen-alt,fas fa-pen-fancy,fas fa-pen-nib,fas fa-pen-square,fas fa-pencil-alt,fas fa-pencil-ruler,fas fa-people-arrows,fas fa-people-carry,fas fa-pepper-hot,fas fa-percent,fas fa-percentage,fas fa-person-booth,fas fa-phone,fas fa-phone-alt,fas fa-phone-slash,fas fa-phone-square,fas fa-phone-square-alt,fas fa-phone-volume,fas fa-photo-video,fas fa-piggy-bank,fas fa-pills,fas fa-pizza-slice,fas fa-place-of-worship,fas fa-plane,fas fa-plane-arrival,fas fa-plane-departure,fas fa-plane-slash,fas fa-play,fas fa-play-circle,fas fa-plug,fas fa-plus,fas fa-plus-circle,fas fa-plus-square,fas fa-podcast,fas fa-poll,fas fa-poll-h,fas fa-poo,fas fa-poo-storm,fas fa-poop,fas fa-portrait,fas fa-pound-sign,fas fa-power-off,fas fa-pray,fas fa-praying-hands,fas fa-prescription,fas fa-prescription-bottle,fas fa-prescription-bottle-alt,fas fa-print,fas fa-procedures,fas fa-project-diagram,fas fa-pump-medical,fas fa-pump-soap,fas fa-puzzle-piece,fas fa-qrcode,fas fa-question,fas fa-question-circle,fas fa-quidditch,fas fa-quote-left,fas fa-quote-right,fas fa-quran,fas fa-radiation,fas fa-radiation-alt,fas fa-rainbow,fas fa-random,fas fa-receipt,fas fa-record-vinyl,fas fa-recycle,fas fa-redo,fas fa-redo-alt,fas fa-registered,fas fa-remove-format,fas fa-reply,fas fa-reply-all,fas fa-republican,fas fa-restroom,fas fa-retweet,fas fa-ribbon,fas fa-ring,fas fa-road,fas fa-robot,fas fa-rocket,fas fa-route,fas fa-rss,fas fa-rss-square,fas fa-ruble-sign,fas fa-ruler,fas fa-ruler-combined,fas fa-ruler-horizontal,fas fa-ruler-vertical,fas fa-running,fas fa-rupee-sign,fas fa-sad-cry,fas fa-sad-tear,fas fa-satellite,fas fa-satellite-dish,fas fa-save,fas fa-school,fas fa-screwdriver,fas fa-scroll,fas fa-sd-card,fas fa-search,fas fa-search-dollar,fas fa-search-location,fas fa-search-minus,fas fa-search-plus,fas fa-seedling,fas fa-server,fas fa-shapes,fas fa-share,fas fa-share-alt,fas fa-share-alt-square,fas fa-share-square,fas fa-shekel-sign,fas fa-shield-alt,fas fa-shield-virus,fas fa-ship,fas fa-shipping-fast,fas fa-shoe-prints,fas fa-shopping-bag,fas fa-shopping-basket,fas fa-shopping-cart,fas fa-shower,fas fa-shuttle-van,fas fa-sign,fas fa-sign-in-alt,fas fa-sign-language,fas fa-sign-out-alt,fas fa-signal,fas fa-signature,fas fa-sim-card,fas fa-sink,fas fa-sitemap,fas fa-skating,fas fa-skiing,fas fa-skiing-nordic,fas fa-skull,fas fa-skull-crossbones,fas fa-slash,fas fa-sleigh,fas fa-sliders-h,fas fa-smile,fas fa-smile-beam,fas fa-smile-wink,fas fa-smog,fas fa-smoking,fas fa-smoking-ban,fas fa-sms,fas fa-snowboarding,fas fa-snowflake,fas fa-snowman,fas fa-snowplow,fas fa-soap,fas fa-socks,fas fa-solar-panel,fas fa-sort,fas fa-sort-alpha-down,fas fa-sort-alpha-down-alt,fas fa-sort-alpha-up,fas fa-sort-alpha-up-alt,fas fa-sort-amount-down,fas fa-sort-amount-down-alt,fas fa-sort-amount-up,fas fa-sort-amount-up-alt,fas fa-sort-down,fas fa-sort-numeric-down,fas fa-sort-numeric-down-alt,fas fa-sort-numeric-up,fas fa-sort-numeric-up-alt,fas fa-sort-up,fas fa-spa,fas fa-space-shuttle,fas fa-spell-check,fas fa-spider,fas fa-spinner,fas fa-splotch,fas fa-spray-can,fas fa-square,fas fa-square-full,fas fa-square-root-alt,fas fa-stamp,fas fa-star,fas fa-star-and-crescent,fas fa-star-half,fas fa-star-half-alt,fas fa-star-of-david,fas fa-star-of-life,fas fa-step-backward,fas fa-step-forward,fas fa-stethoscope,fas fa-sticky-note,fas fa-stop,fas fa-stop-circle,fas fa-stopwatch,fas fa-stopwatch-20,fas fa-store,fas fa-store-alt,fas fa-store-alt-slash,fas fa-store-slash,fas fa-stream,fas fa-street-view,fas fa-strikethrough,fas fa-stroopwafel,fas fa-subscript,fas fa-subway,fas fa-suitcase,fas fa-suitcase-rolling,fas fa-sun,fas fa-superscript,fas fa-surprise,fas fa-swatchbook,fas fa-swimmer,fas fa-swimming-pool,fas fa-synagogue,fas fa-sync,fas fa-sync-alt,fas fa-syringe,fas fa-table,fas fa-table-tennis,fas fa-tablet,fas fa-tablet-alt,fas fa-tablets,fas fa-tachometer-alt,fas fa-tag,fas fa-tags,fas fa-tape,fas fa-tasks,fas fa-taxi,fas fa-teeth,fas fa-teeth-open,fas fa-temperature-high,fas fa-temperature-low,fas fa-tenge,fas fa-terminal,fas fa-text-height,fas fa-text-width,fas fa-th,fas fa-th-large,fas fa-th-list,fas fa-theater-masks,fas fa-thermometer,fas fa-thermometer-empty,fas fa-thermometer-full,fas fa-thermometer-half,fas fa-thermometer-quarter,fas fa-thermometer-three-quarters,fas fa-thumbs-down,fas fa-thumbs-up,fas fa-thumbtack,fas fa-ticket-alt,fas fa-times,fas fa-times-circle,fas fa-tint,fas fa-tint-slash,fas fa-tired,fas fa-toggle-off,fas fa-toggle-on,fas fa-toilet,fas fa-toilet-paper,fas fa-toilet-paper-slash,fas fa-toolbox,fas fa-tools,fas fa-tooth,fas fa-torah,fas fa-torii-gate,fas fa-tractor,fas fa-trademark,fas fa-traffic-light,fas fa-trailer,fas fa-train,fas fa-tram,fas fa-transgender,fas fa-transgender-alt,fas fa-trash,fas fa-trash-alt,fas fa-trash-restore,fas fa-trash-restore-alt,fas fa-tree,fas fa-trophy,fas fa-truck,fas fa-truck-loading,fas fa-truck-monster,fas fa-truck-moving,fas fa-truck-pickup,fas fa-tshirt,fas fa-tty,fas fa-tv,fas fa-umbrella,fas fa-umbrella-beach,fas fa-underline,fas fa-undo,fas fa-undo-alt,fas fa-universal-access,fas fa-university,fas fa-unlink,fas fa-unlock,fas fa-unlock-alt,fas fa-upload,fas fa-user,fas fa-user-alt,fas fa-user-alt-slash,fas fa-user-astronaut,fas fa-user-check,fas fa-user-circle,fas fa-user-clock,fas fa-user-cog,fas fa-user-edit,fas fa-user-friends,fas fa-user-graduate,fas fa-user-injured,fas fa-user-lock,fas fa-user-md,fas fa-user-minus,fas fa-user-ninja,fas fa-user-nurse,fas fa-user-plus,fas fa-user-secret,fas fa-user-shield,fas fa-user-slash,fas fa-user-tag,fas fa-user-tie,fas fa-user-times,fas fa-users,fas fa-users-cog,fas fa-users-slash,fas fa-utensil-spoon,fas fa-utensils,fas fa-vector-square,fas fa-venus,fas fa-venus-double,fas fa-venus-mars,fas fa-vest,fas fa-vest-patches,fas fa-vial,fas fa-vials,fas fa-video,fas fa-video-slash,fas fa-vihara,fas fa-virus,fas fa-virus-slash,fas fa-viruses,fas fa-voicemail,fas fa-volleyball-ball,fas fa-volume-down,fas fa-volume-mute,fas fa-volume-off,fas fa-volume-up,fas fa-vote-yea,fas fa-vr-cardboard,fas fa-walking,fas fa-wallet,fas fa-warehouse,fas fa-water,fas fa-wave-square,fas fa-weight,fas fa-weight-hanging,fas fa-wheelchair,fas fa-wifi,fas fa-wind,fas fa-window-close,fas fa-window-maximize,fas fa-window-minimize,fas fa-window-restore,fas fa-wine-bottle,fas fa-wine-glass,fas fa-wine-glass-alt,fas fa-won-sign,fas fa-wrench,fas fa-x-ray,fas fa-yen-sign,fas fa-yin-yang' );
        $fa_regular = explode( ',', 'far fa-address-book,far fa-address-card,far fa-angry,far fa-arrow-alt-circle-down,far fa-arrow-alt-circle-left,far fa-arrow-alt-circle-right,far fa-arrow-alt-circle-up,far fa-bell,far fa-bell-slash,far fa-bookmark,far fa-building,far fa-calendar,far fa-calendar-alt,far fa-calendar-check,far fa-calendar-minus,far fa-calendar-plus,far fa-calendar-times,far fa-caret-square-down,far fa-caret-square-left,far fa-caret-square-right,far fa-caret-square-up,far fa-chart-bar,far fa-check-circle,far fa-check-square,far fa-circle,far fa-clipboard,far fa-clock,far fa-clone,far fa-closed-captioning,far fa-comment,far fa-comment-alt,far fa-comment-dots,far fa-comments,far fa-compass,far fa-copy,far fa-copyright,far fa-credit-card,far fa-dizzy,far fa-dot-circle,far fa-edit,far fa-envelope,far fa-envelope-open,far fa-eye,far fa-eye-slash,far fa-file,far fa-file-alt,far fa-file-archive,far fa-file-audio,far fa-file-code,far fa-file-excel,far fa-file-image,far fa-file-pdf,far fa-file-powerpoint,far fa-file-video,far fa-file-word,far fa-flag,far fa-flushed,far fa-folder,far fa-folder-open,far fa-font-awesome-logo-full,far fa-frown,far fa-frown-open,far fa-futbol,far fa-gem,far fa-grimace,far fa-grin,far fa-grin-alt,far fa-grin-beam,far fa-grin-beam-sweat,far fa-grin-hearts,far fa-grin-squint,far fa-grin-squint-tears,far fa-grin-stars,far fa-grin-tears,far fa-grin-tongue,far fa-grin-tongue-squint,far fa-grin-tongue-wink,far fa-grin-wink,far fa-hand-lizard,far fa-hand-paper,far fa-hand-peace,far fa-hand-point-down,far fa-hand-point-left,far fa-hand-point-right,far fa-hand-point-up,far fa-hand-pointer,far fa-hand-rock,far fa-hand-scissors,far fa-hand-spock,far fa-handshake,far fa-hdd,far fa-heart,far fa-hospital,far fa-hourglass,far fa-id-badge,far fa-id-card,far fa-image,far fa-images,far fa-keyboard,far fa-kiss,far fa-kiss-beam,far fa-kiss-wink-heart,far fa-laugh,far fa-laugh-beam,far fa-laugh-squint,far fa-laugh-wink,far fa-lemon,far fa-life-ring,far fa-lightbulb,far fa-list-alt,far fa-map,far fa-meh,far fa-meh-blank,far fa-meh-rolling-eyes,far fa-minus-square,far fa-money-bill-alt,far fa-moon,far fa-newspaper,far fa-object-group,far fa-object-ungroup,far fa-paper-plane,far fa-pause-circle,far fa-play-circle,far fa-plus-square,far fa-question-circle,far fa-registered,far fa-sad-cry,far fa-sad-tear,far fa-save,far fa-share-square,far fa-smile,far fa-smile-beam,far fa-smile-wink,far fa-snowflake,far fa-square,far fa-star,far fa-star-half,far fa-sticky-note,far fa-stop-circle,far fa-sun,far fa-surprise,far fa-thumbs-down,far fa-thumbs-up,far fa-times-circle,far fa-tired,far fa-trash-alt,far fa-user,far fa-user-circle,far fa-window-close,far fa-window-maximize,far fa-window-minimize,far fa-window-restore' );
        $fa_brands = explode( ',', 'fab fa-500px,fab fa-accessible-icon,fab fa-accusoft,fab fa-acquisitions-incorporated,fab fa-adn,fab fa-adversal,fab fa-affiliatetheme,fab fa-airbnb,fab fa-algolia,fab fa-alipay,fab fa-amazon,fab fa-amazon-pay,fab fa-amilia,fab fa-android,fab fa-angellist,fab fa-angrycreative,fab fa-angular,fab fa-app-store,fab fa-app-store-ios,fab fa-apper,fab fa-apple,fab fa-apple-pay,fab fa-artstation,fab fa-asymmetrik,fab fa-atlassian,fab fa-audible,fab fa-autoprefixer,fab fa-avianex,fab fa-aviato,fab fa-aws,fab fa-bandcamp,fab fa-battle-net,fab fa-behance,fab fa-behance-square,fab fa-bimobject,fab fa-bitbucket,fab fa-bitcoin,fab fa-bity,fab fa-black-tie,fab fa-blackberry,fab fa-blogger,fab fa-blogger-b,fab fa-bluetooth,fab fa-bluetooth-b,fab fa-bootstrap,fab fa-btc,fab fa-buffer,fab fa-buromobelexperte,fab fa-buy-n-large,fab fa-buysellads,fab fa-canadian-maple-leaf,fab fa-cc-amazon-pay,fab fa-cc-amex,fab fa-cc-apple-pay,fab fa-cc-diners-club,fab fa-cc-discover,fab fa-cc-jcb,fab fa-cc-mastercard,fab fa-cc-paypal,fab fa-cc-stripe,fab fa-cc-visa,fab fa-centercode,fab fa-centos,fab fa-chrome,fab fa-chromecast,fab fa-cloudflare,fab fa-cloudscale,fab fa-cloudsmith,fab fa-cloudversify,fab fa-codepen,fab fa-codiepie,fab fa-confluence,fab fa-connectdevelop,fab fa-contao,fab fa-cotton-bureau,fab fa-cpanel,fab fa-creative-commons,fab fa-creative-commons-by,fab fa-creative-commons-nc,fab fa-creative-commons-nc-eu,fab fa-creative-commons-nc-jp,fab fa-creative-commons-nd,fab fa-creative-commons-pd,fab fa-creative-commons-pd-alt,fab fa-creative-commons-remix,fab fa-creative-commons-sa,fab fa-creative-commons-sampling,fab fa-creative-commons-sampling-plus,fab fa-creative-commons-share,fab fa-creative-commons-zero,fab fa-critical-role,fab fa-css3,fab fa-css3-alt,fab fa-cuttlefish,fab fa-d-and-d,fab fa-d-and-d-beyond,fab fa-dailymotion,fab fa-dashcube,fab fa-deezer,fab fa-delicious,fab fa-deploydog,fab fa-deskpro,fab fa-dev,fab fa-deviantart,fab fa-dhl,fab fa-diaspora,fab fa-digg,fab fa-digital-ocean,fab fa-discord,fab fa-discourse,fab fa-dochub,fab fa-docker,fab fa-draft2digital,fab fa-dribbble,fab fa-dribbble-square,fab fa-dropbox,fab fa-drupal,fab fa-dyalog,fab fa-earlybirds,fab fa-ebay,fab fa-edge,fab fa-edge-legacy,fab fa-elementor,fab fa-ello,fab fa-ember,fab fa-empire,fab fa-envira,fab fa-erlang,fab fa-ethereum,fab fa-etsy,fab fa-evernote,fab fa-expeditedssl,fab fa-facebook,fab fa-facebook-f,fab fa-facebook-messenger,fab fa-facebook-square,fab fa-fantasy-flight-games,fab fa-fedex,fab fa-fedora,fab fa-figma,fab fa-firefox,fab fa-firefox-browser,fab fa-first-order,fab fa-first-order-alt,fab fa-firstdraft,fab fa-flickr,fab fa-flipboard,fab fa-fly,fab fa-font-awesome,fab fa-font-awesome-alt,fab fa-font-awesome-flag,fab fa-font-awesome-logo-full,fab fa-fonticons,fab fa-fonticons-fi,fab fa-fort-awesome,fab fa-fort-awesome-alt,fab fa-forumbee,fab fa-foursquare,fab fa-free-code-camp,fab fa-freebsd,fab fa-fulcrum,fab fa-galactic-republic,fab fa-galactic-senate,fab fa-get-pocket,fab fa-gg,fab fa-gg-circle,fab fa-git,fab fa-git-alt,fab fa-git-square,fab fa-github,fab fa-github-alt,fab fa-github-square,fab fa-gitkraken,fab fa-gitlab,fab fa-gitter,fab fa-glide,fab fa-glide-g,fab fa-gofore,fab fa-goodreads,fab fa-goodreads-g,fab fa-google,fab fa-google-drive,fab fa-google-pay,fab fa-google-play,fab fa-google-plus,fab fa-google-plus-g,fab fa-google-plus-square,fab fa-google-wallet,fab fa-gratipay,fab fa-grav,fab fa-gripfire,fab fa-grunt,fab fa-guilded,fab fa-gulp,fab fa-hacker-news,fab fa-hacker-news-square,fab fa-hackerrank,fab fa-hips,fab fa-hire-a-helper,fab fa-hive,fab fa-hooli,fab fa-hornbill,fab fa-hotjar,fab fa-houzz,fab fa-html5,fab fa-hubspot,fab fa-ideal,fab fa-imdb,fab fa-innosoft,fab fa-instagram,fab fa-instagram-square,fab fa-instalod,fab fa-intercom,fab fa-internet-explorer,fab fa-invision,fab fa-ioxhost,fab fa-itch-io,fab fa-itunes,fab fa-itunes-note,fab fa-java,fab fa-jedi-order,fab fa-jenkins,fab fa-jira,fab fa-joget,fab fa-joomla,fab fa-js,fab fa-js-square,fab fa-jsfiddle,fab fa-kaggle,fab fa-keybase,fab fa-keycdn,fab fa-kickstarter,fab fa-kickstarter-k,fab fa-korvue,fab fa-laravel,fab fa-lastfm,fab fa-lastfm-square,fab fa-leanpub,fab fa-less,fab fa-line,fab fa-linkedin,fab fa-linkedin-in,fab fa-linode,fab fa-linux,fab fa-lyft,fab fa-magento,fab fa-mailchimp,fab fa-mandalorian,fab fa-markdown,fab fa-mastodon,fab fa-maxcdn,fab fa-mdb,fab fa-medapps,fab fa-medium,fab fa-medium-m,fab fa-medrt,fab fa-meetup,fab fa-megaport,fab fa-mendeley,fab fa-microblog,fab fa-microsoft,fab fa-mix,fab fa-mixcloud,fab fa-mixer,fab fa-mizuni,fab fa-modx,fab fa-monero,fab fa-napster,fab fa-neos,fab fa-nimblr,fab fa-node,fab fa-node-js,fab fa-npm,fab fa-ns8,fab fa-nutritionix,fab fa-octopus-deploy,fab fa-odnoklassniki,fab fa-odnoklassniki-square,fab fa-old-republic,fab fa-opencart,fab fa-openid,fab fa-opera,fab fa-optin-monster,fab fa-orcid,fab fa-osi,fab fa-page4,fab fa-pagelines,fab fa-palfed,fab fa-patreon,fab fa-paypal,fab fa-penny-arcade,fab fa-perbyte,fab fa-periscope,fab fa-phabricator,fab fa-phoenix-framework,fab fa-phoenix-squadron,fab fa-php,fab fa-pied-piper,fab fa-pied-piper-alt,fab fa-pied-piper-hat,fab fa-pied-piper-pp,fab fa-pied-piper-square,fab fa-pinterest,fab fa-pinterest-p,fab fa-pinterest-square,fab fa-playstation,fab fa-product-hunt,fab fa-pushed,fab fa-python,fab fa-qq,fab fa-quinscape,fab fa-quora,fab fa-r-project,fab fa-raspberry-pi,fab fa-ravelry,fab fa-react,fab fa-reacteurope,fab fa-readme,fab fa-rebel,fab fa-red-river,fab fa-reddit,fab fa-reddit-alien,fab fa-reddit-square,fab fa-redhat,fab fa-renren,fab fa-replyd,fab fa-researchgate,fab fa-resolving,fab fa-rev,fab fa-rocketchat,fab fa-rockrms,fab fa-rust,fab fa-safari,fab fa-salesforce,fab fa-sass,fab fa-schlix,fab fa-scribd,fab fa-searchengin,fab fa-sellcast,fab fa-sellsy,fab fa-servicestack,fab fa-shirtsinbulk,fab fa-shopify,fab fa-shopware,fab fa-simplybuilt,fab fa-sistrix,fab fa-sith,fab fa-sketch,fab fa-skyatlas,fab fa-skype,fab fa-slack,fab fa-slack-hash,fab fa-slideshare,fab fa-snapchat,fab fa-snapchat-ghost,fab fa-snapchat-square,fab fa-soundcloud,fab fa-sourcetree,fab fa-speakap,fab fa-speaker-deck,fab fa-spotify,fab fa-squarespace,fab fa-stack-exchange,fab fa-stack-overflow,fab fa-stackpath,fab fa-staylinked,fab fa-steam,fab fa-steam-square,fab fa-steam-symbol,fab fa-sticker-mule,fab fa-strava,fab fa-stripe,fab fa-stripe-s,fab fa-studiovinari,fab fa-stumbleupon,fab fa-stumbleupon-circle,fab fa-superpowers,fab fa-supple,fab fa-suse,fab fa-swift,fab fa-symfony,fab fa-teamspeak,fab fa-telegram,fab fa-telegram-plane,fab fa-tencent-weibo,fab fa-the-red-yeti,fab fa-themeco,fab fa-themeisle,fab fa-think-peaks,fab fa-tiktok,fab fa-trade-federation,fab fa-trello,fab fa-tripadvisor,fab fa-tumblr,fab fa-tumblr-square,fab fa-twitch,fab fa-twitter,fab fa-twitter-square,fab fa-typo3,fab fa-uber,fab fa-ubuntu,fab fa-uikit,fab fa-umbraco,fab fa-uncharted,fab fa-uniregistry,fab fa-unity,fab fa-unsplash,fab fa-untappd,fab fa-ups,fab fa-usb,fab fa-usps,fab fa-ussunnah,fab fa-vaadin,fab fa-viacoin,fab fa-viadeo,fab fa-viadeo-square,fab fa-viber,fab fa-vimeo,fab fa-vimeo-square,fab fa-vimeo-v,fab fa-vine,fab fa-vk,fab fa-vnv,fab fa-vuejs,fab fa-watchman-monitoring,fab fa-waze,fab fa-weebly,fab fa-weibo,fab fa-weixin,fab fa-whatsapp,fab fa-whatsapp-square,fab fa-whmcs,fab fa-wikipedia-w,fab fa-windows,fab fa-wix,fab fa-wizards-of-the-coast,fab fa-wodu,fab fa-wolf-pack-battalion,fab fa-wordpress,fab fa-wordpress-simple,fab fa-wpbeginner,fab fa-wpexplorer,fab fa-wpforms,fab fa-wpressr,fab fa-xbox,fab fa-xing,fab fa-xing-square,fab fa-y-combinator,fab fa-yahoo,fab fa-yammer,fab fa-yandex,fab fa-yandex-international,fab fa-yarn,fab fa-yelp,fab fa-yoast,fab fa-youtube,fab fa-youtube-square,fab fa-zhihu' );
        // Get the selected icon if it exists.
        $selected_icon = get_post_meta( $item_id, '_menu_image_icon', true );
        // Fontawesome Solid Icons List.
        
        if ( strpos( $selected_icon, 'fa-' ) !== false ) {
            echo  '<div class="menu-image-fontawesome-list active">' ;
        } else {
            echo  '<div class="menu-image-fontawesome-list">' ;
        }
        
        
        if ( !in_array( 'font-awesome/index.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            _e( '<br><br><span>In order to use the FontAwesome icons you need to install the official FontAwesome plugin and select the SVG tecnhonoly. Check <a href="https://www.freshlightlab.com/documentation/menu-image-docs/add-icons-to-wordpress-menu/?utm_source=menu-image-settings&utm_medium=user%20website&utm_campaign=install-fontawesome#fontawesome" target="_blank" >here</a> how to do it.', 'mobile-menu' );
        } else {
            echo  '<ul class="menu-image-icons-fa-list-header"><li data-tab-id="fa-brands-list" class="active">Brands</li><li data-tab-id="fa-solid-list">Solid</li><li data-tab-id="fa-regular-list">Regular</li></ul>' ;
            // Fontawesome Solid Icons List.
            echo  '<div class="menu-image-fa-solid-list">' ;
            // Loop through the list of icons.
            foreach ( $fa_solid as $icon ) {
                
                if ( $icon == $selected_icon ) {
                    echo  '<i class="' . $icon . ' fa-2x menu-item-icon-selected"></i>' ;
                } else {
                    echo  '<i class="' . $icon . ' fa-2x"></i>' ;
                }
            
            }
            echo  '</div>' ;
            // Fontawesome Regular Icons List.
            echo  '<div class="menu-image-fa-regular-list">' ;
            foreach ( $fa_regular as $icon ) {
                
                if ( $icon == $selected_icon ) {
                    echo  '<i class="' . $icon . ' fa-2x menu-item-icon-selected"></i>' ;
                } else {
                    echo  '<i class="' . $icon . ' fa-2x"></i>' ;
                }
            
            }
            echo  '</div>' ;
            // Fontawesome Brands Icons List.
            echo  '<div class="active menu-image-fa-brands-list">' ;
            foreach ( $fa_brands as $icon ) {
                
                if ( $icon == $selected_icon ) {
                    echo  '<i class="' . $icon . ' fa-2x menu-item-icon-selected"></i>' ;
                } else {
                    echo  '<i class="' . $icon . ' fa-2x"></i>' ;
                }
            
            }
            echo  '</div>' ;
        }
        
        // Closing final div.
        echo  '</div>' ;
    }
    
    /**
     * Output HTML for the menu item images section.
     *
     * @since 2.0
     *
     * @param int $item_id The post ID or object associated with the thumbnail, defaults to global $post.
     *
     * @return string html
     */
    public function wp_post_thumbnail_html( $item_id, $menu_title )
    {
        $default_size = apply_filters( 'menu_image_default_size', 'menu-36x36' );
        $content = $this->wp_post_thumbnail_only_html( $item_id );
        // Get the Menu item image size.
        $image_size = get_post_meta( $item_id, '_menu_item_image_size', true );
        if ( !$image_size ) {
            $image_size = $default_size;
        }
        // Get the Menu Item action type(button, icon, image).
        $menu_item_type = get_post_meta( $item_id, '_menu_item_image_type', true );
        if ( !$menu_item_type ) {
            $menu_item_type = 'image';
        }
        // Get the title position.
        $title_position = get_post_meta( $item_id, '_menu_item_image_title_position', true );
        if ( !$title_position ) {
            $title_position = apply_filters( 'menu_image_default_title_position', 'after' );
        }
        ob_start();
        ?>

		<div class="menu-item-image-options">
		<div class="menu-image-icon-settings menu-image-container">
			<!-- Title position -->
			<div class="menu-image-field-holder">
				<div class="menu-image-label">
					<label><?php 
        _e( 'Title position', 'menu-image' );
        ?></label>
				</div>
				<div class="menu-image-field">
				<?php 
        $positions = array(
            'hide'   => __( 'Hide', 'menu-image' ),
            'above'  => __( 'Above', 'menu-image' ),
            'below'  => __( 'Below', 'menu-image' ),
            'before' => __( 'Before', 'menu-image' ),
            'after'  => __( 'After', 'menu-image' ),
        );
        foreach ( $positions as $position => $label ) {
            printf(
                "<input type='radio' name='menu_item_image_title_position' value='%s'%s/> %s%s",
                esc_attr( $position ),
                ( $title_position == $position ? ' checked="checked"' : '' ),
                $label,
                ( $position != 'after' ? ' | ' : '' )
            );
        }
        ?>
				<p class="description">Controls the position of the title reggarding the image or icon</p>
				</div>
			</div>

			<!-- Icon/Image -->
			<div class="menu-image-field-holder">
				<div class="menu-image-label">
					<label><?php 
        _e( 'Use Icon/image: ', 'menu-image' );
        ?></label>
				</div>
				<div class="menu-image-field">

				<?php 
        $types = array(
            'image' => __( 'Image', 'menu-image' ),
            'icon'  => __( 'Icon', 'menu-image' ),
        );
        foreach ( $types as $type => $label ) {
            printf(
                "<input type='radio' name='menu_item_image_type' value='%s'%s/> %s",
                esc_attr( $type ),
                ( $menu_item_type == $type ? ' checked="checked"' : '' ),
                $label
            );
        }
        ?>

				<p class="description">You can use an image or an Icon</p>
				</div>
			</div>

			<!-- Image header -->
			<div class="menu-image-field-holder menu-item-images menu-item-image-type" style="min-height:70px">
				<?php 
        echo  $content ;
        ?>
			</div>

			<!-- Icon/Image -->
			<div class="menu-image-field-holder menu-item-image-type">
				<div class="menu-image-label">
					<label for="menu_item_image_size">
						<?php 
        _e( 'Image size', 'menu-image' );
        ?>
					</label>
				</div>
				<div class="menu-image-field">	
					<select id="menu_item_image_size"
							class="widefat edit-menu-item-image-size"
							name="menu_item_image_size">
						<option value='full' <?php 
        echo  ( $image_size == 'full' ? ' selected="selected"' : '' ) ;
        ?>><?php 
        _e( 'Original Size', 'menu-image' );
        ?></option>
						<?php 
        foreach ( get_intermediate_image_sizes() as $size ) {
            printf(
                "<option value='%s'%s>%s</option>\n",
                esc_attr( $size ),
                ( $image_size == $size ? ' selected="selected"' : '' ),
                ucfirst( $size )
            );
            ?>
						<?php 
        }
        ?>
					</select>
				</div>
			</div>

			<!-- Icons List -->
			<div class="menu-image-icons-list menu-item-icon-type">
				<ul class="menu-image-icons-list-header">
					<li data-tab-id="dashicons-list">Dashicons</li>
					<li data-tab-id="fontawesome-list">Fontawesome</li>
				</ul>

			<?php 
        $this->menu_image_build_dashicons_list( $item_id );
        $this->menu_image_build_fa_list( $item_id );
        ?>

			</div>
			</div>

			<div class="menu-image-button-settings menu-image-container" style="display:none;">

			<!-- Menu Image Premium button options -->
			<?php 
        ?>
					<p>Improve your site marketing by creating Call to action buttons. This is available in the Professional version.</p>
					<div class="prodemo-imgs"> 
						<h4>Some examples:</h4>
						<img src='<?php 
        echo  MENU_IMAGE_PLUGIN_URL ;
        ?>includes/assets/img/pro-button-1.png'>
						<img src='<?php 
        echo  MENU_IMAGE_PLUGIN_URL ;
        ?>includes/assets/img/pro-button-2.png'>
					</div>
					<p style="font-weight: 600;font-size:16px;">You would like to have the same in your website?<a href="<?php 
        echo  $this->mi_fs->get_upgrade_url() ;
        ?>&cta=upsell-button-upgrade-cta" class="mi-button-professional-upgrade"><?php 
        _e( 'Upgrade Now!', 'mobile-menu' );
        ?></a></p>
					<p>Not sure if it has the right features?  <a href="<?php 
        echo  $this->mi_fs->get_trial_url() ;
        ?>"><?php 
        echo  esc_html( 'Start a Free trial', 'menu-image' ) ;
        ?></a></p>
				<?php 
        // Close premium blok
        ?>
			</div>

			<div class="menu-image-notifications-settings menu-image-container" style="display:none;">
			<!-- Menu Immage Premium badges and bubble options -->
			<?php 
        ?>
				<p>Grab your user attention with those notifications and badges.This is available in the Professional version.</p>
				<div class="prodemo-imgs">
					<h4>Some examples:</h4>
					<img src='<?php 
        echo  MENU_IMAGE_PLUGIN_URL ;
        ?>includes/assets/img/pro-badge-1.png'>
					<img src='<?php 
        echo  MENU_IMAGE_PLUGIN_URL ;
        ?>includes/assets/img/pro-badge-2.png'>
					<img src='<?php 
        echo  MENU_IMAGE_PLUGIN_URL ;
        ?>includes/assets/img/pro-badge-3.png'>
				</div>
				<p style="font-weight: 600;font-size:16px;">You would like to have the same in your website?<a href="<?php 
        echo  $this->mi_fs->get_upgrade_url() ;
        ?>&cta=upsell-badge-upgrade-cta" class="mi-button-professional-upgrade"><?php 
        _e( 'Upgrade Now!', 'mobile-menu' );
        ?></a></p>
				<p>Not sure if it has the right features?  <a href="<?php 
        echo  $this->mi_fs->get_trial_url() ;
        ?>"><?php 
        echo  esc_html( 'Start a Free trial', 'menu-image' ) ;
        ?></a></p>
			<?php 
        // Close premium blok
        ?>
			</div>
			<div class="menu-item-preview">
				<h4>Preview</h4>
				<span class="title-text"><?php 
        echo  $menu_title ;
        ?></span>

				<?php 
        ?>

			</div>
		</div>

		<?php 
        submit_button();
        $content = ob_get_clean();
        /**
         * Filter the admin menu item thumbnail HTML markup to return.
         *
         * @since 2.0
         *
         * @param string $content Admin menu item images HTML markup.
         * @param int    $item_id Post ID.
         */
        return apply_filters( 'admin_menu_item_thumbnail_html', $content, $item_id );
    }
    
    /**
     * Update item thumbnail via ajax action.
     *
     * @since 2.0
     */
    public function wp_ajax_set_menu_item_thumbnail()
    {
        $json = !empty($_REQUEST['json']);
        $post_ID = intval( $_POST['post_id'] );
        if ( !current_user_can( 'edit_post', $post_ID ) ) {
            wp_die( -1 );
        }
        $thumbnail_id = intval( $_POST['thumbnail_id'] );
        $is_hovered = (bool) $_POST['is_hover'];
        check_ajax_referer( 'update-menu-item' );
        
        if ( $thumbnail_id == '-1' ) {
            
            if ( $is_hovered ) {
                $success = delete_post_meta( $post_ID, '_thumbnail_hover_id' );
            } else {
                $success = delete_post_thumbnail( $post_ID );
            }
        
        } else {
            
            if ( $is_hovered ) {
                $success = update_post_meta( $post_ID, '_thumbnail_hover_id', $thumbnail_id );
            } else {
                $success = set_post_thumbnail( $post_ID, $thumbnail_id );
            }
        
        }
        
        
        if ( $success ) {
            $return = $this->wp_post_thumbnail_only_html( $post_ID );
            ( $json ? wp_send_json_success( $return ) : wp_die( $return ) );
        }
        
        wp_die( 0 );
    }
    
    /**
     * Prevent jetpack Phonon applied for menu item images.
     *
     * @param bool  $prevent
     * @param array $data
     *
     * @return bool
     */
    public function jetpack_photon_override_image_downsize_filter( $prevent, $data )
    {
        return $this->isAttachmentUsed( $data['attachment_id'], $data['size'] );
    }
    
    /**
     * Set used attachment ids.
     *
     * @param string $size
     * @param int    $id
     */
    public function setUsedAttachments( $size, $id )
    {
        $this->used_attachments[$size][] = $id;
    }
    
    /**
     * Check if attachment is used in menu items.
     *
     * @param int    $id
     * @param string $size
     *
     * @return bool
     */
    public function isAttachmentUsed( $id, $size = null )
    {
        
        if ( !is_null( $size ) ) {
            return is_string( $size ) && isset( $this->used_attachments[$size] ) && in_array( $id, $this->used_attachments[$size] );
        } else {
            foreach ( $this->used_attachments as $used_attachment ) {
                if ( in_array( $id, $used_attachment ) ) {
                    return true;
                }
            }
            return false;
        }
    
    }
    
    /**
     * Filters the list of attachment image attributes.
     *
     * @since 2.8.0
     *
     * @param array        $attr       Attributes for the image markup.
     * @param WP_Post      $attachment Image attachment post.
     * @param string|array $size       Requested size. Image size or array of width and height values
     *                                 (in that order). Default 'thumbnail'.
     *
     * @return array Valid array of image attributes.
     */
    public function wp_get_attachment_image_attributes( $attr, $attachment, $size )
    {
        if ( $this->isAttachmentUsed( $attachment->ID, $size ) ) {
            unset( $attr['sizes'], $attr['srcset'] );
        }
        return $attr;
    }
    
    /**
     * Mark item as processed to prevent re-processing it again.
     *
     * @param int $id
     */
    protected function setProcessed( $id )
    {
        $this->processed[] = $id;
    }
    
    /**
     * Check if was already processed.
     *
     * @param int $id
     *
     * @return bool
     */
    protected function isProcessed( $id )
    {
        return in_array( $id, $this->processed );
    }

}
$menu_image = new WP_Menu_Image();
$menu_image->init_menu_image();
//require_once( ABSPATH . 'wp-admin/includes/nav-menu.php' );