<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Setup post types.
 */
class JCK_SFR_Post_Types
{
    /**
     * Post type key.
     *
     * @var string
     */
    public static  $key = 'cpt_feature_requests' ;
    /**
     * Run class.
     */
    public static function run()
    {
        add_action( 'init', array( __CLASS__, 'add_post_types' ) );
        add_filter(
            'single_template',
            array( __CLASS__, 'load_single_template' ),
            10,
            3
        );
        add_filter(
            'archive_template',
            array( __CLASS__, 'load_archive_template' ),
            10,
            3
        );
        add_filter( 'manage_cpt_feature_requests_posts_columns', array( __CLASS__, 'admin_columns' ), 1000 );
        add_action(
            'manage_cpt_feature_requests_posts_custom_column',
            array( __CLASS__, 'admin_columns_content' ),
            10,
            2
        );
        add_filter( 'manage_edit-cpt_feature_requests_sortable_columns', array( __CLASS__, 'admin_sortable_columns' ) );
        add_action( 'pre_get_posts', array( __CLASS__, 'admin_columns_orderby' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
    }
    
    /**
     * Add post types.
     */
    public static function add_post_types()
    {
        self::add( array(
            'plural'       => __( 'Feature Requests', 'simple-feature-requests' ),
            'singular'     => __( 'Feature Request', 'simple-feature-requests' ),
            'menu_name'    => __( 'All Requests', 'simple-feature-requests' ),
            'key'          => self::$key,
            'rewrite_slug' => apply_filters( 'jck_sfr_archive_slug', 'feature-requests' ),
            'supports'     => array( 'title', 'editor', 'comments' ),
            'menu_icon'    => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOC4xLjEsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9Ik1lZ2FwaG9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9IjAgMCAyMCAyMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMjAgMjAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTE3LjIyMyw3LjAzYy0xLjU4NC0zLjY4Ni00LjEzMi02LjQ5LTUuNDIxLTUuOTY3Yy0yLjE4OSwwLjg5MSwxLjMwNCw1LjE2NC05LjQ0Nyw5LjUzMw0KCWMtMC45MjksMC4zNzktMS4xNjQsMS44ODgtMC43NzUsMi43OTJjMC4zODgsMC45MDIsMS42NTgsMS44MDEsMi41ODcsMS40MjRjMC4xNjEtMC4wNjYsMC43NTEtMC4yNTYsMC43NTEtMC4yNTYNCgljMC42NjMsMC44OTEsMS4zNTcsMC4zNjMsMS42MDQsMC45MjhjMC4yOTYsMC42OCwwLjkzOSwyLjE1OCwxLjE1OCwyLjY2YzAuMjE5LDAuNTAyLDAuNzE1LDAuOTY3LDEuMDc1LDAuODMNCgljMC4zNTktMC4xMzcsMS41ODItMC42MDIsMi4wNS0wLjc3OWMwLjQ2OC0wLjE3OCwwLjU3OS0wLjU5NiwwLjQzNi0wLjkyNGMtMC4xNTQtMC4zNTUtMC43ODYtMC40NTktMC45NjctMC44NzMNCgljLTAuMTgtMC40MTItMC43NjktMS43MzgtMC45MzgtMi4xNTZjLTAuMjMtMC41NjgsMC4yNTktMS4wMzEsMC45Ny0xLjEwNGM0Ljg5NC0wLjUxMiw1LjgwOSwyLjUxMiw3LjQ3NSwxLjgzNA0KCUMxOS4wNjgsMTQuNDQ3LDE4LjgwNiwxMC43MTMsMTcuMjIzLDcuMDN6IE0xNi42NzIsMTMuMDA2Yy0wLjI4NywwLjExNS0yLjIxMy0xLjQwMi0zLjQ0My00LjI2Nw0KCWMtMS4yMzEtMi44NjMtMS4wNzYtNS40OC0wLjc5LTUuNTk3YzAuMjg2LTAuMTE1LDIuMTY1LDEuNzE3LDMuMzk1LDQuNThDMTcuMDY1LDEwLjU4NSwxNi45NTgsMTIuODg5LDE2LjY3MiwxMy4wMDZ6Ii8+DQo8L3N2Zz4NCg==',
            'show_in_menu' => 'jck-sfr-settings',
        ) );
    }
    
    /**
     * Method: Add
     *
     * @since 1.0.0
     *
     * @param array $options
     */
    public static function add( $options )
    {
        $defaults = array(
            "plural"              => "",
            "singular"            => "",
            "key"                 => false,
            "rewrite_slug"        => false,
            "rewrite_with_front"  => false,
            "rewrite_feeds"       => true,
            "rewrite_pages"       => true,
            "menu_icon"           => "dashicons-admin-post",
            'hierarchical'        => false,
            'supports'            => array( 'title' ),
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'has_archive'         => true,
            'query_var'           => true,
            'can_export'          => true,
            'capability_type'     => 'post',
            'menu_name'           => false,
        );
        $options = wp_parse_args( $options, $defaults );
        
        if ( $options['key'] ) {
            $labels = array(
                'name'               => $options['plural'],
                'singular_name'      => $options['singular'],
                'add_new'            => _x( 'Add New', 'iconic-advanced-layered-nav' ),
                'add_new_item'       => _x( sprintf( 'Add New %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
                'edit_item'          => _x( sprintf( 'Edit %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
                'new_item'           => _x( sprintf( 'New %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
                'view_item'          => _x( sprintf( 'View %s', $options['singular'] ), 'iconic-advanced-layered-nav' ),
                'search_items'       => _x( sprintf( 'Search %s', $options['plural'] ), 'iconic-advanced-layered-nav' ),
                'not_found'          => _x( sprintf( 'No %s found', strtolower( $options['plural'] ) ), 'iconic-advanced-layered-nav' ),
                'not_found_in_trash' => _x( sprintf( 'No %s found in Trash', strtolower( $options['plural'] ) ), 'iconic-advanced-layered-nav' ),
                'parent_item_colon'  => _x( sprintf( 'Parent %s:', $options['singular'] ), 'iconic-advanced-layered-nav' ),
                'menu_name'          => ( $options['menu_name'] ? $options['menu_name'] : $options['plural'] ),
            );
            $args = array(
                'labels'              => $labels,
                'hierarchical'        => $options['hierarchical'],
                'supports'            => $options['supports'],
                'public'              => $options['public'],
                'show_ui'             => $options['show_ui'],
                'show_in_menu'        => $options['show_in_menu'],
                'menu_icon'           => $options['menu_icon'],
                'show_in_nav_menus'   => $options['show_in_nav_menus'],
                'publicly_queryable'  => $options['publicly_queryable'],
                'exclude_from_search' => $options['exclude_from_search'],
                'has_archive'         => $options['has_archive'],
                'query_var'           => $options['query_var'],
                'can_export'          => $options['can_export'],
                'capability_type'     => $options['capability_type'],
                'rewrite'             => false,
            );
            if ( $options['rewrite_slug'] ) {
                $args['rewrite'] = array(
                    "slug"       => $options['rewrite_slug'],
                    "with_front" => $options['rewrite_with_front'],
                    "feeds"      => $options['rewrite_feeds'],
                    "pages"      => $options['rewrite_pages'],
                );
            }
            register_post_type( $options['key'], $args );
        }
    
    }
    
    /**
     * Load single template.
     *
     * @param string $template
     * @param string $type
     * @param array  $templates
     *
     * @return string
     */
    public static function load_single_template( $template, $type, $templates )
    {
        global  $post ;
        if ( $post->post_type !== 'cpt_feature_requests' ) {
            return $template;
        }
        return JCK_SFR_TEMPLATES_PATH . 'single-feature-request.php';
    }
    
    /**
     * Load archive template.
     *
     * @param string $template
     * @param string $type
     * @param array  $templates
     *
     * @return string
     */
    public static function load_archive_template( $template, $type, $templates )
    {
        if ( self::get_archive_post_type() !== 'cpt_feature_requests' ) {
            return $template;
        }
        return JCK_SFR_TEMPLATES_PATH . 'archive-feature-requests.php';
    }
    
    /**
     * Get archive post type.
     *
     * @return string
     */
    public static function get_archive_post_type()
    {
        $post_types = array_filter( (array) get_query_var( 'post_type' ) );
        if ( count( $post_types ) != 1 ) {
            return 'post';
        }
        return reset( $post_types );
    }
    
    /**
     * Modify admin columns.
     *
     * @param array $columns
     *
     * @return array
     */
    public static function admin_columns( $columns )
    {
        foreach ( $columns as $key => $column ) {
            if ( strpos( $key, 'wpseo-' ) !== 0 ) {
                continue;
            }
            unset( $columns[$key] );
        }
        $date = $columns['date'];
        $comments = $columns['comments'];
        unset( $columns['post_type'], $columns['date'], $columns['comments'] );
        $columns['author'] = __( 'Author', 'simple-feature-requests' );
        $columns['status'] = __( 'Status', 'simple-feature-requests' );
        $columns['votes'] = __( 'Votes', 'simple-feature-requests' );
        $columns['comments'] = $comments;
        $columns['date'] = $date;
        return $columns;
    }
    
    /**
     * Add custom column content.
     *
     * @param string $column
     * @param int    $post_id
     */
    public static function admin_columns_content( $column, $post_id )
    {
        if ( in_array( $column, array(
            'cb',
            'title',
            'author',
            'date'
        ) ) ) {
            return;
        }
        $feature_request = new JCK_SFR_Feature_Request( $post_id );
        
        if ( $column === 'status' ) {
            echo  self::get_inline_status_badge( $feature_request ) ;
        } elseif ( $column === 'votes' ) {
            echo  $feature_request->get_votes_count() ;
        }
    
    }
    
    /**
     * Set sortable admin columns.
     *
     * @param array $columns
     *
     * @return array
     */
    public static function admin_sortable_columns( $columns )
    {
        $columns['votes'] = 'votes';
        return $columns;
    }
    
    /**
     * Set orderby for admin columns.
     *
     * @param WP_Query $query
     */
    public static function admin_columns_orderby( $query )
    {
        if ( !is_admin() || !$query->is_main_query() ) {
            return;
        }
        
        if ( $query->get( 'orderby' ) === 'votes' ) {
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'meta_key', 'jck_sfr_votes' );
            $query->set( 'meta_type', 'numeric' );
        }
    
    }
    
    /**
     * Add meta boxes.
     */
    public static function add_meta_boxes()
    {
        add_meta_box(
            'jck-sfr-meta',
            esc_html__( 'Information', 'simple-feature-requests' ),
            array( __CLASS__, 'information_meta_box' ),
            'cpt_feature_requests',
            'side',
            'default'
        );
    }
    
    public static function information_meta_box()
    {
        global  $post ;
        if ( !$post ) {
            return;
        }
        $feature_request = new JCK_SFR_Feature_Request( $post );
        $statuses = jck_sfr_get_statuses();
        $status = $feature_request->get_status();
        $author = get_userdata( $post->post_author );
        $author_url = $url = add_query_arg( array(
            'author'    => $author->ID,
            'post_type' => 'cpt_feature_requests',
        ), 'edit.php' );
        ?>
		<style>
			.jck-sfr-meta-table {
				border: none !important;
			}

			.jck-sfr-meta-table th,
			.jck-sfr-meta-table td {
				padding-left: 0;
			}

			.jck-sfr-meta-table th {
				width: 60px;
			}
		</style>
		<table class="jck-sfr-meta-table widefat fixed">
			<tr>
				<th>Status</th>
				<td>
					<select name="jck_sfr_status">
						<?php 
        foreach ( $statuses as $key => $label ) {
            ?>
							<option value="<?php 
            echo  esc_attr( $key ) ;
            ?>" <?php 
            selected( $key, $status );
            ?>><?php 
            echo  $label ;
            ?></option>
						<?php 
        }
        ?>
					</select>
					<?php 
        // echo self::get_inline_status_badge( $feature_request );
        ?>
				</td>
			</tr>
			<tr>
				<th>Votes</th>
				<td><?php 
        echo  $feature_request->get_votes_count() ;
        ?></td>
			</tr>
			<tr>
				<th>Author</th>
				<td><?php 
        printf( '<a href="%s">%s</a>', $author_url, $author->user_login );
        ?></td>
			</tr>
		</table>
		<?php 
    }
    
    /**
     * @param JCK_SFR_Feature_Request $feature_request
     *
     * @return string
     */
    public static function get_inline_status_badge( $feature_request )
    {
        $status = $feature_request->get_status();
        $status_label = jck_sfr_get_status_label( $status );
        $status_colors = jck_sfr_get_status_colors( $status );
        return sprintf(
            '<span style="
				background-color: %s; 
				height: 24px;
				line-height: 24px;
				text-transform: uppercase;
				font-size: 11px;
				padding: 0 8px;
				display: inline-block;
				vertical-align: middle;
				letter-spacing: .5px;
				font-family: Arial,sans-serif;
				border-radius: 3px;
				color: %s;
            ">%s</span>',
            $status_colors['background'],
            $status_colors['color'],
            $status_label
        );
    }

}