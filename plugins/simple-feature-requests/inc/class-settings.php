<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * JCK_SFR_Settings.
 *
 * @class    JCK_SFR_Settings
 * @version  1.0.0
 * @category Class
 * @author   Iconic
 */
class JCK_SFR_Settings
{
    /**
     * Run.
     */
    public static function run()
    {
        add_action( 'admin_menu', array( __CLASS__, 'add_menu_items' ), 10 );
        add_action( 'admin_head', array( __CLASS__, 'menu_highlight' ) );
        add_filter( 'wpsf_menu_icon_url_jck_sfr', array( __CLASS__, 'menu_icon' ) );
        add_filter( 'wpsf_menu_position_jck_sfr', array( __CLASS__, 'menu_position' ) );
    }
    
    /**
     * Rename dashboard page.
     */
    public static function add_menu_items()
    {
        $settings_framework = JCK_SFR_Core_Settings::$settings_framework;
        add_submenu_page(
            'jck-sfr-settings',
            'Add New',
            'Add New',
            'manage_options',
            'post-new.php?post_type=cpt_feature_requests',
            null
        );
        add_submenu_page(
            'jck-sfr-settings',
            __( 'Simple Feature Requests', 'simple-feature-requests' ),
            __( 'Settings', 'simple-feature-requests' ),
            'manage_options',
            'jck-sfr-settings',
            array( $settings_framework, 'settings_page_content' )
        );
    }
    
    /**
     * Keep menu open.
     *
     * Highlights the wanted admin (sub-) menu items for the CPT.
     */
    public static function menu_highlight()
    {
        global  $parent_file, $submenu_file, $post_type ;
        if ( $post_type !== 'cpt_feature_requests' ) {
            return;
        }
        $screen = get_current_screen();
        if ( $screen->base !== 'post' ) {
            return;
        }
        $parent_file = 'edit.php?post_type=cpt_feature_requests';
        $submenu_file = 'post-new.php?post_type=cpt_feature_requests';
    }
    
    /**
     * Add menu icon.
     *
     * @param string $icon_url
     *
     * @return string
     */
    public static function menu_icon( $icon_url )
    {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOC4xLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9Ik1lZ2FwaG9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTE3LjIyMyw3LjAzYy0xLjU4NC0zLjY4Ni00LjEzMi02LjQ5LTUuNDIxLTUuOTY3Yy0yLjE4OSwwLjg5MSwxLjMwNCw1LjE2NC05LjQ0Nyw5LjUzMw0KCWMtMC45MjksMC4zNzktMS4xNjQsMS44ODgtMC43NzUsMi43OTJjMC4zODgsMC45MDIsMS42NTgsMS44MDEsMi41ODcsMS40MjRjMC4xNjEtMC4wNjYsMC43NTEtMC4yNTYsMC43NTEtMC4yNTYNCgljMC42NjMsMC44OTEsMS4zNTcsMC4zNjMsMS42MDQsMC45MjhjMC4yOTYsMC42OCwwLjkzOSwyLjE1OCwxLjE1OCwyLjY2YzAuMjE5LDAuNTAyLDAuNzE1LDAuOTY3LDEuMDc1LDAuODMNCgljMC4zNTktMC4xMzcsMS41ODItMC42MDIsMi4wNS0wLjc3OWMwLjQ2OC0wLjE3OCwwLjU3OS0wLjU5NiwwLjQzNi0wLjkyNGMtMC4xNTQtMC4zNTUtMC43ODYtMC40NTktMC45NjctMC44NzMNCgljLTAuMTgtMC40MTItMC43NjktMS43MzgtMC45MzgtMi4xNTZjLTAuMjMtMC41NjgsMC4yNTktMS4wMzEsMC45Ny0xLjEwNGM0Ljg5NC0wLjUxMiw1LjgwOSwyLjUxMiw3LjQ3NSwxLjgzNA0KCUMxOS4wNjgsMTQuNDQ3LDE4LjgwNiwxMC43MTMsMTcuMjIzLDcuMDN6IE0xNi42NzIsMTMuMDA2Yy0wLjI4NywwLjExNS0yLjIxMy0xLjQwMi0zLjQ0My00LjI2Nw0KCWMtMS4yMzEtMi44NjMtMS4wNzYtNS40OC0wLjc5LTUuNTk3YzAuMjg2LTAuMTE1LDIuMTY1LDEuNzE3LDMuMzk1LDQuNThDMTcuMDY1LDEwLjU4NSwxNi45NTgsMTIuODg5LDE2LjY3MiwxMy4wMDZ6Ii8+DQo8L3N2Zz4NCg==';
    }
    
    /**
     * Change menu position.
     *
     * @param int|null $position
     *
     * @return int|null
     */
    public static function menu_position( $position )
    {
        return 30;
    }
    
    /**
     * Get support button.
     *
     * @return string
     */
    public static function support_link()
    {
        return sprintf( '<a href="%s" class="button button-secondary" target="_blank">%s</a>', 'http://www.simplefeaturerequests.com/support?utm_source=simple-feature-requests&utm_medium=insideplugin', __( 'Submit Ticket', 'simple-feature-requests' ) );
    }
    
    /**
     * Get documentation button.
     *
     * @return string
     */
    public static function documentation_link()
    {
        return sprintf( '<a href="%s" class="button button-secondary" target="_blank">%s</a>', 'http://www.simplefeaturerequests.com/docs?utm_source=simple-feature-requests&utm_medium=insideplugin', __( 'Read Documentation', 'simple-feature-requests' ) );
    }
    
    /**
     * Get settings.
     *
     * @return array|bool
     */
    public static function get_settings()
    {
        global  $simple_feature_requests_class ;
        if ( empty($simple_feature_requests_class) ) {
            return false;
        }
        $settings = $simple_feature_requests_class->settings;
        if ( empty($settings) ) {
            return false;
        }
        return $settings::$settings;
    }

}