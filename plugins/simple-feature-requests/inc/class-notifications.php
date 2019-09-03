<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

/**
 * Notifications.
 */
class JCK_SFR_Notifications
{
    /**
     * @var string
     */
    private static  $process_queue_name = 'jck_sfr_process_email_queue' ;
    /**
     * @var string
     */
    private static  $queue_option_name = 'jck_sfr_email_queue' ;
    /**
     * Run.
     */
    public static function run()
    {
        add_action( self::$process_queue_name, array( __CLASS__, 'process_email_queue' ) );
    }
    
    /**
     * @return array
     */
    private static function get_email_queue()
    {
        $queue = (array) get_option( self::$queue_option_name, array() );
        $queue = array_filter( $queue );
        return $queue;
    }
    
    /**
     * Add an email tot he queue.
     *
     * @param $args
     *
     * @return bool
     */
    private static function add_to_email_queue( $args )
    {
        $queue = self::get_email_queue();
        $queue[] = $args;
        return self::set_email_queue( $queue );
    }
    
    /**
     * Set the email queue option.
     *
     * @param $queue
     *
     * @return bool
     */
    private static function set_email_queue( $queue )
    {
        return update_option( self::$queue_option_name, $queue );
    }
    
    /**
     * Schedule an email in the queue.
     *
     * @param array $args
     */
    public static function queue_wp_mail( $args )
    {
        self::add_to_email_queue( $args );
        // schedule event to process all queued emails
        
        if ( !wp_next_scheduled( self::$process_queue_name ) ) {
            // schedule event to be fired right away
            wp_schedule_single_event( time(), self::$process_queue_name );
            // send off a request to wp-cron on shutdown
            add_action( 'shutdown', 'spawn_cron' );
        }
    
    }
    
    /**
     * Processes the email queue.
     */
    public static function process_email_queue()
    {
        $queue = self::get_email_queue();
        if ( empty($queue) ) {
            return;
        }
        // send each queued email
        foreach ( $queue as $key => $args ) {
            unset( $queue[$key] );
            if ( empty($args['to']) ) {
                continue;
            }
            $defaults = array(
                'headers'     => '',
                'attachments' => array(),
            );
            $args = wp_parse_args( $args, $defaults );
            wp_mail(
                $args['to'],
                $args['subject'],
                $args['message'],
                $args['headers'],
                $args['attachments']
            );
        }
        // update queue with removed values
        self::set_email_queue( $queue );
    }
    
    /**
     * Is this a comment for a feature request?
     *
     * @param int $comment_id
     *
     * @return bool
     */
    public static function is_comment_for_request( $comment_id )
    {
        $comment = get_comment( $comment_id );
        $post_id = absint( $comment->comment_post_ID );
        $post_type = get_post_type( $post_id );
        return $post_type === JCK_SFR_Post_Types::$key;
    }

}