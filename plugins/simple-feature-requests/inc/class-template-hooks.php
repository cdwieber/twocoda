<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Template Hooks.
 */
class JCK_SFR_Template_Hooks
{
    /**
     * Run class.
     */
    public static function run()
    {
        $action_hooks = array(
            'jck_sfr_before_main_content'  => array( array(
            'function' => array( 'JCK_SFR_Notices', 'print_notices' ),
            'priority' => 10,
        ), array(
            'function' => array( __CLASS__, 'submission_form' ),
            'priority' => 20,
        ), array(
            'function' => array( __CLASS__, 'filters' ),
            'priority' => 30,
        ) ),
            'jck_sfr_before_single_loop'   => array( array(
            'function' => array( 'JCK_SFR_Notices', 'print_notices' ),
            'priority' => 10,
        ) ),
            'jck_sfr_loop'                 => array( array(
            'function' => array( __CLASS__, 'loop_content' ),
            'priority' => 10,
        ) ),
            'jck_sfr_loop_item_vote_badge' => array( array(
            'function' => array( 'JCK_SFR_Template_Methods', 'loop_item_vote_badge' ),
            'priority' => 10,
        ) ),
            'jck_sfr_loop_item_title'      => array( array(
            'function' => array( 'JCK_SFR_Template_Methods', 'loop_item_title' ),
            'priority' => 10,
        ) ),
            'jck_sfr_loop_item_text'       => array( array(
            'function' => array( __CLASS__, 'loop_item_text' ),
            'priority' => 10,
        ) ),
            'jck_sfr_loop_item_meta'       => array( array(
            'function' => array( 'JCK_SFR_Template_Methods', 'loop_item_status_badge' ),
            'priority' => 10,
        ), array(
            'function' => array( 'JCK_SFR_Template_Methods', 'loop_item_author' ),
            'priority' => 20,
        ), array(
            'function' => array( 'JCK_SFR_Template_Methods', 'loop_item_comment_count' ),
            'priority' => 30,
        ) ),
            'jck_sfr_loop_item_after_meta' => array( array(
            'function' => array( 'JCK_SFR_Template_Methods', 'comments' ),
            'priority' => 10,
        ) ),
            'jck_sfr_no_requests_found'    => array( array(
            'function' => array( __CLASS__, 'no_requests_found' ),
            'priority' => 10,
        ) ),
            'jck_sfr_after_main_content'   => array( array(
            'function' => array( __CLASS__, 'pagination' ),
            'priority' => 10,
        ) ),
            'jck_sfr_sidebar'              => array(
            array(
            'function' => array( __CLASS__, 'back_to_archive_link' ),
            'priority' => 10,
        ),
            array(
            'function' => array( __CLASS__, 'login' ),
            'priority' => 20,
        ),
            array(
            'function' => array( __CLASS__, 'top_requests__premium_only' ),
            'priority' => 30,
        ),
            array(
            'function' => array( __CLASS__, 'taxonomies__premium_only' ),
            'priority' => 40,
        )
        ),
            'jck_sfr_login_form'           => array( array(
            'function' => array( __CLASS__, 'login_form_fields' ),
            'priority' => 10,
        ) ),
            'jck_sfr_submission_form'      => array( array(
            'function' => array( __CLASS__, 'login_form_fields' ),
            'priority' => 20,
        ) ),
            'jck_sfr_after_columns'        => array( array(
            'function' => array( __CLASS__, 'credit' ),
            'priority' => 10,
        ) ),
        );
        foreach ( $action_hooks as $hook => $actions ) {
            foreach ( $actions as $action ) {
                $defaults = array(
                    'priority' => 10,
                    'args'     => 1,
                );
                $action = wp_parse_args( $action, $defaults );
                if ( !method_exists( $action['function'][0], $action['function'][1] ) ) {
                    continue;
                }
                add_action(
                    $hook,
                    $action['function'],
                    $action['priority'],
                    $action['args']
                );
            }
        }
    }
    
    /**
     * Include template.
     *
     * @param string $name
     * @param array  $args
     */
    public static function include_template( $name, $args = array() )
    {
        $path = sprintf( '%s%s.php', JCK_SFR_TEMPLATES_PATH, $name );
        if ( !file_exists( $path ) ) {
            return;
        }
        extract( $args );
        include $path;
    }
    
    /**
     * Submission form.
     */
    public static function submission_form()
    {
        self::include_template( 'archive/submission-form' );
    }
    
    /**
     * Filters.
     */
    public static function filters()
    {
        self::include_template( 'archive/filters' );
    }
    
    /**
     * Loop content.
     */
    public static function loop_content()
    {
        self::include_template( 'loop/content' );
    }
    
    /**
     * Loop item text.
     *
     * @param JCK_SFR_Feature_Request $feature_request
     */
    public static function loop_item_text( $feature_request )
    {
        
        if ( $feature_request->is_single() ) {
            the_content();
        } else {
            the_excerpt();
        }
    
    }
    
    /**
     * No requests found.
     */
    public static function no_requests_found()
    {
        self::include_template( 'loop/no-requests-found' );
    }
    
    /**
     * Pagination.
     */
    public static function pagination()
    {
        self::include_template( 'loop/pagination' );
    }
    
    /**
     * Login.
     */
    public static function login()
    {
        self::include_template( 'sidebar/login' );
    }
    
    /**
     * Login form fields.
     */
    public static function login_form_fields()
    {
        if ( is_user_logged_in() ) {
            return;
        }
        self::include_template( 'components/login-form-fields' );
    }
    
    /**
     * Back to archive link.
     */
    public static function back_to_archive_link()
    {
        self::include_template( 'sidebar/back-to-archive-link' );
    }
    
    /**
     * Credit.
     */
    public static function credit()
    {
        ?>
		<p class="jck-sfr-credit">
			<?php 
        _e( 'Powered by', 'simple-feature-requests' );
        ?>
			<img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj48c3ZnIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIHZpZXdCb3g9IjAgMCAyMCAyMCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4bWw6c3BhY2U9InByZXNlcnZlIiB4bWxuczpzZXJpZj0iaHR0cDovL3d3dy5zZXJpZi5jb20vIiBzdHlsZT0iZmlsbC1ydWxlOmV2ZW5vZGQ7Y2xpcC1ydWxlOmV2ZW5vZGQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS1taXRlcmxpbWl0OjEuNDE0MjE7Ij48cGF0aCBkPSJNMTcuMjIzLDcuMDNjLTEuNTg0LC0zLjY4NiAtNC4xMzIsLTYuNDkgLTUuNDIxLC01Ljk2N2MtMi4xODksMC44OTEgMS4zMDQsNS4xNjQgLTkuNDQ3LDkuNTMzYy0wLjkyOSwwLjM3OSAtMS4xNjQsMS44ODggLTAuNzc1LDIuNzkyYzAuMzg4LDAuOTAyIDEuNjU4LDEuODAxIDIuNTg3LDEuNDI0YzAuMTYxLC0wLjA2NiAwLjc1MSwtMC4yNTYgMC43NTEsLTAuMjU2YzAuNjYzLDAuODkxIDEuMzU3LDAuMzYzIDEuNjA0LDAuOTI4YzAuMjk2LDAuNjggMC45MzksMi4xNTggMS4xNTgsMi42NmMwLjIxOSwwLjUwMiAwLjcxNSwwLjk2NyAxLjA3NSwwLjgzYzAuMzU5LC0wLjEzNyAxLjU4MiwtMC42MDIgMi4wNSwtMC43NzljMC40NjgsLTAuMTc4IDAuNTc5LC0wLjU5NiAwLjQzNiwtMC45MjRjLTAuMTU0LC0wLjM1NSAtMC43ODYsLTAuNDU5IC0wLjk2NywtMC44NzNjLTAuMTgsLTAuNDEyIC0wLjc2OSwtMS43MzggLTAuOTM4LC0yLjE1NmMtMC4yMywtMC41NjggMC4yNTksLTEuMDMxIDAuOTcsLTEuMTA0YzQuODk0LC0wLjUxMiA1LjgwOSwyLjUxMiA3LjQ3NSwxLjgzNGMxLjI4NywtMC41MjUgMS4wMjUsLTQuMjU5IC0wLjU1OCwtNy45NDJabS0wLjU1MSw1Ljk3NmMtMC4yODcsMC4xMTUgLTIuMjEzLC0xLjQwMiAtMy40NDMsLTQuMjY3Yy0xLjIzMSwtMi44NjMgLTEuMDc2LC01LjQ4IC0wLjc5LC01LjU5N2MwLjI4NiwtMC4xMTUgMi4xNjUsMS43MTcgMy4zOTUsNC41OGMxLjIzMSwyLjg2MyAxLjEyNCw1LjE2NyAwLjgzOCw1LjI4NFoiIHN0eWxlPSJmaWxsOiMzMzM7ZmlsbC1ydWxlOm5vbnplcm87Ii8+PC9zdmc+" alt="" width="20" height="20">
			<a href="https://wordpress.org/plugins/simple-feature-requests/" target="_blank"><?php 
        _e( 'Simple feature Requests', 'simple-feature-requests' );
        ?></a>
		</p>
		<?php 
    }

}