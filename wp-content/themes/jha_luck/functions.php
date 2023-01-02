<?php
if (!file_exists(get_template_directory() . '/wp-bootstrap-navwalker.php')) {
    // File does not exist... return an error.
    return new WP_Error('class-wp-bootstrap-navwalker-missing', __('It appears the class-wp-bootstrap-navwalker.php file may be missing.', 'wp-bootstrap-navwalker'));
} else {
    // File exists... require it.
    require_once get_template_directory() . '/wp-bootstrap-navwalker.php';
}

if (!function_exists('theme_enquee_scripts')) {
    function theme_enquee_scripts()
    {
        //css
        wp_enqueue_style('style', get_template_directory_uri() . '/style.css');
        wp_enqueue_style('style', get_template_directory_uri() . '/boxicons.min.css');
        wp_enqueue_style('fontawesome', get_template_directory_uri() . '/fontawesome/css/all.css');

        wp_enqueue_style('bootstrap_css', get_template_directory_uri() . '/css/bootstrap.min.css');
        wp_enqueue_style('bootstrap_css', get_template_directory_uri() . '/css/bootstrap.css');
        wp_enqueue_style('bootstrap_css', get_template_directory_uri() . '/css/bootstrap.rtl.css');

        //js
        wp_enqueue_script('javascript', get_template_directory_uri() . '/js/bootstrap.min.js');
        wp_enqueue_script('javascript', get_template_directory_uri() . '/js/bootstrap.js');
        wp_enqueue_script('javascript', get_template_directory_uri() . '/js/bootstrap.bundle.min.js');
        wp_enqueue_script('javascript ', get_template_directory_uri() . '/js/bootstrap.bundle.js');
    }
}
add_action('wp_enqueue_scripts', 'theme_enquee_scripts');

add_theme_support('menus');

function wp_theme_setup()
{

    register_nav_menus(array(
        'primary' => __('Primary Menu', 'primary menu'),
        'category_class12' => __('class12 Menu', 'subject list class 12 menu'),
        'category_class11' => __('Class11 Menu', 'subject list class 11 menu'),
        'category_class10' => __('class10 Menu', 'subject list class 10 menu'),
        'category_class9' => __('class9 Menu', 'subject list class 9 menu'),


    ));
}
add_action('after_setup_theme', 'wp_theme_setup');

function themename_custom_logo_setup()
{
    $defaults = array(
        'height'      => 80,
        'width'       => 100,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array('site-title', 'site-description'),
    );
    add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', 'themename_custom_logo_setup');

add_theme_support( 'post-thumbnails' );


if (function_exists('register_sidebar')) {

    register_sidebar(array(
        'name' => 'footer center ',
        'id' => 'footer_center',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));

    register_sidebar(array(
        'name' => 'footer sidebar 1',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));
}

class bootstrap_5_wp_nav_menu_walker extends Walker_Nav_menu
{
    private $current_item;
    private $dropdown_menu_alignment_values = [
        'dropdown-menu-start',
        'dropdown-menu-end',
        'dropdown-menu-sm-start',
        'dropdown-menu-sm-end',
        'dropdown-menu-md-start',
        'dropdown-menu-md-end',
        'dropdown-menu-lg-start',
        'dropdown-menu-lg-end',
        'dropdown-menu-xl-start',
        'dropdown-menu-xl-end',
        'dropdown-menu-xxl-start',
        'dropdown-menu-xxl-end'
    ];

    function start_lvl(&$output, $depth = 0, $args = null)
    {
        $dropdown_menu_class[] = '';
        foreach ($this->current_item->classes as $class) {
            if (in_array($class, $this->dropdown_menu_alignment_values)) {
                $dropdown_menu_class[] = $class;
            }
        }
        $indent = str_repeat("\t", $depth);
        $submenu = ($depth > 0) ? ' sub-menu' : '';
        $output .= "\n$indent<ul class=\"dropdown-menu$submenu " . esc_attr(implode(" ", $dropdown_menu_class)) . " depth_$depth\">\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $this->current_item = $item;

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $li_attributes = '';
        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;

        $classes[] = ($args->walker->has_children) ? 'dropdown' : '';
        $classes[] = 'nav-item';
        $classes[] = 'nav-item-' . $item->ID;
        if ($depth && $args->walker->has_children) {
            $classes[] = 'dropdown-menu dropdown-menu-end';
        }

        $class_names =  join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li ' . $id . $value . $class_names . $li_attributes . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $active_class = ($item->current || $item->current_item_ancestor || in_array("current_page_parent", $item->classes, true) || in_array("current-post-ancestor", $item->classes, true)) ? 'active' : '';
        $nav_link_class = ($depth > 0) ? 'dropdown-item ' : 'nav-link ';
        $attributes .= ($args->walker->has_children) ? ' class="' . $nav_link_class . $active_class . ' dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : ' class="' . $nav_link_class . $active_class . '"';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
function cptui_register_my_cpts_notices() {

	/**
	 * Post Type: Notice.
	 */

	$labels = [
		"name" => esc_html__( "Notice", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Notice", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Notice", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "notices", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-calendar-alt",
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields", "comments", "revisions", "author", "page-attributes", "post-formats" ],
		"show_in_graphql" => false,
	];

	register_post_type( "notices", $args );
}

add_action( 'init', 'cptui_register_my_cpts_notices' );


add_theme_support('widgets');
// add_theme_suppor('Template');

