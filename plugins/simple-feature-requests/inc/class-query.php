<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Modify front-end post queries and loops.
 */
class JCK_SFR_Query
{
    /**
     * Run class.
     */
    public static function run()
    {
        add_action(
            'pre_get_posts',
            array( __CLASS__, 'search' ),
            100,
            1
        );
        add_action(
            'pre_get_posts',
            array( __CLASS__, 'prepare_main_query' ),
            100,
            1
        );
        add_action(
            'pre_get_posts',
            array( __CLASS__, 'filter' ),
            100,
            1
        );
    }
    
    /**
     * @param WP_Query $query
     *
     * @return bool
     */
    public static function is_query_modification_allowed( $query )
    {
        if ( $query->get( 'post_type' ) !== "cpt_feature_requests" ) {
            return false;
        }
        $is_ajax = $query->get( 'jck_sfr_ajax' );
        if ( $is_ajax ) {
            return true;
        }
        if ( is_admin() && empty($is_ajax) ) {
            return false;
        }
        if ( is_admin() || !$query->is_main_query() || !is_post_type_archive( 'cpt_feature_requests' ) ) {
            return false;
        }
        return true;
    }
    
    /**
     * Add current user's pending posts.
     *
     * @param WP_Query $query
     */
    public static function prepare_main_query( $query )
    {
        if ( !self::is_query_modification_allowed( $query ) ) {
            return;
        }
        $other_user_pending_ids = self::get_other_user_pending_ids();
        $query->set( 'post__not_in', $other_user_pending_ids );
        $query->set( 'post_status', self::get_post_stati_to_query() );
        $query->set( 'posts_per_page', apply_filters( 'jck_sfr_per_page', 10 ) );
        self::set_status_query( $query );
    }
    
    /**
     * Get status query.
     *
     * @param WP_Query $query
     */
    public static function set_status_query( $query )
    {
        $is_ajax = $query->get( 'jck_sfr_ajax' );
        $search = $query->get( 's' );
        // If ajax or search, allow all statuses.
        if ( $is_ajax && !empty($search) || !empty($search) ) {
            return;
        }
        $status = ( isset( $_REQUEST['status'] ) ? sanitize_text_field( $_REQUEST['status'] ) : false );
        $meta_query = (array) $query->get( 'meta_query' );
        $meta_query['status'] = array(
            'key'     => 'jck_sfr_status',
            'value'   => array( 'completed', 'declined' ),
            'compare' => 'NOT IN',
        );
        
        if ( $status ) {
            $meta_query['status']['value'] = array( $status );
            $meta_query['status']['compare'] = 'IN';
        }
        
        $query->set( 'meta_query', $meta_query );
    }
    
    /**
     * Modify query based on filters.
     *
     * @param WP_Query $query
     */
    public static function filter( $query )
    {
        if ( !self::is_query_modification_allowed( $query ) ) {
            return;
        }
        if ( empty($_REQUEST['filter']) ) {
            return;
        }
        switch ( $_REQUEST['filter'] ) {
            case 'top':
                $query->set( 'meta_key', 'jck_sfr_votes' );
                $query->set( 'orderby', 'meta_value_num post_date' );
                $query->set( 'order', 'DESC' );
                break;
            case 'my-requests':
                $query->set( 'author', get_current_user_id() );
                break;
        }
    }
    
    /**
     * Modify query based on search.
     *
     * @param WP_Query $query
     */
    public static function search( $query )
    {
        if ( !self::is_query_modification_allowed( $query ) ) {
            return;
        }
        if ( empty($_REQUEST['search']) ) {
            return;
        }
        $search = sanitize_text_field( $_REQUEST['search'] );
        $query->set( 's', $search );
    }
    
    /**
     * Get post stati to query based on current user.
     *
     * @return array|string
     */
    public static function get_post_stati_to_query()
    {
        if ( !is_user_logged_in() ) {
            return 'publish';
        }
        return array( 'publish', 'pending' );
    }
    
    /**
     * Get pending post IDs of all other users.
     *
     * @return array
     */
    public static function get_other_user_pending_ids()
    {
        global  $wpdb ;
        static  $ids = null ;
        if ( !is_null( $ids ) ) {
            return $ids;
        }
        
        if ( !is_user_logged_in() ) {
            $ids = array();
            return $ids;
        }
        
        $current_user_id = get_current_user_id();
        $ids = $wpdb->get_col( $wpdb->prepare( "\n\t\t\tSELECT ID \n\t\t\tFROM {$wpdb->posts}\n\t\t\tWHERE post_author != %d\n\t\t\t\tAND post_type = 'cpt_feature_requests'\n\t\t\t\tAND post_status = 'pending'\n\t\t\t", $current_user_id ) );
        return $ids;
    }
    
    /**
     * Get top requests.
     *
     * @return array
     */
    public static function get_top_requests()
    {
        $transient_name = 'jck_sfr_top_requests';
        $top_requests = get_transient( $transient_name );
        if ( !empty($top_requests) ) {
            return $top_requests;
        }
        $top_requests = array();
        $args = array(
            'post_type'      => JCK_SFR_Post_Types::$key,
            'posts_per_page' => 5,
            'meta_key'       => 'jck_sfr_votes',
            'orderby'        => 'meta_value_num post_date',
            'order'          => 'DESC',
        );
        $query = new WP_Query( $args );
        if ( !$query->have_posts() ) {
            return $top_requests;
        }
        foreach ( $query->posts as $request ) {
            $top_requests[] = new JCK_SFR_Feature_Request( $request );
        }
        set_transient( $transient_name, $top_requests, 12 * HOUR_IN_SECONDS );
        return $top_requests;
    }

}