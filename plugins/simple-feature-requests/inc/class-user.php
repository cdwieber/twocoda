<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * User methods.
 */
class JCK_SFR_User {
	/**
	 * The single instance of the class.
	 *
	 * @var JCK_SFR_User
	 */
	protected static $_instance = null;

	/**
	 * Array of votes.
	 *
	 * @var null|array
	 */
	public $votes = null;

	/**
	 * Votes meta key.
	 *
	 * @var string
	 */
	public static $votes_meta_key = '_jck_sfr_votes';

	/**
	 * Main notices instance.
	 *
	 * @return JCK_SFR_User
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Run.
	 */
	public static function run() {
		add_action( 'admin_init', array( __CLASS__, 'redirect_voters' ), 10 );
		add_action( 'template_redirect', array( __CLASS__, 'handle_login' ), 10 );
	}

	/**
	 * Redirect subscribers.
	 */
	public static function redirect_voters() {
		$user = wp_get_current_user();

		$role = ! empty( $user->roles ) ? $user->roles[0] : false;

		if ( $role !== 'subscriber' || ( defined('DOING_AJAX') && DOING_AJAX ) ) {
			return;
		}

		wp_safe_redirect( get_post_type_archive_link( 'cpt_feature_requests' ), 302 );
		exit;
	}

	/**
	 * Handle login from form.
	 */
	public static function handle_login() {
		if ( ! isset( $_POST['jck-sfr-login'] ) ) {
			return;
		}

		$notices         = JCK_SFR_Notices::instance();
		$nonce           = filter_input( INPUT_POST, 'jck-sfr-login-nonce', FILTER_SANITIZE_STRING );
		$username        = trim( filter_input( INPUT_POST, 'jck-sfr-login-username', FILTER_SANITIZE_STRING ) );
		$email           = trim( filter_input( INPUT_POST, 'jck-sfr-login-email', FILTER_SANITIZE_STRING ) );
		$password        = trim( filter_input( INPUT_POST, 'jck-sfr-login-password', FILTER_SANITIZE_STRING ) );
		$repeat_password = trim( filter_input( INPUT_POST, 'jck-sfr-login-repeat-password', FILTER_SANITIZE_STRING ) );
		$user_type       = trim( filter_input( INPUT_POST, 'jck-sfr-login-user-type', FILTER_SANITIZE_STRING ) );

		if ( ! wp_verify_nonce( $nonce, 'jck-sfr-login' ) ) {
			$notices->add( __( 'There was an error logging you in.', 'simple-feature-requests' ), 'error' );
		}

		if ( $user_type === 'register' ) {
			JCK_SFR_User::register( $username, $email, $password, $repeat_password );
		} else {
			JCK_SFR_User::login( $email, $password );
		}

		if ( $notices->has_notices() ) {
			return;
		}

		wp_safe_redirect( get_post_type_archive_link( 'cpt_feature_requests' ), 302 );
		exit;
	}

	/**
	 * @param null $user_id
	 *
	 * @return string
	 */
	public static function get_username( $user_id = null ) {
		$current_user = is_null( $user_id ) ? wp_get_current_user() : get_user_by( 'id', $user_id );

		return $current_user->user_login;
	}

	/**
	 * Get user votes.
	 *
	 * @param null|int $user_id
	 *
	 * @return array
	 */
	public function get_votes( $user_id = null ) {
		$user_id = self::get_user_id( $user_id );

		if ( ! $user_id ) {
			return array();
		}

		if ( ! is_null( $this->votes ) ) {
			return $this->votes;
		}

		$votes = get_user_meta( $user_id, self::$votes_meta_key, true );

		if ( ! $votes ) {
			$this->votes = array();

			return $this->votes;
		}

		$this->votes = $votes;

		return $this->votes;
	}

	/**
	 * Has voted?
	 *
	 * @param int      $post_id
	 * @param null|int $user_id
	 *
	 * @return bool
	 */
	public function has_voted( $post_id, $user_id = null ) {
		$votes = $this->get_votes( $user_id );

		return apply_filters( 'jck_sfr_has_voted', isset( $votes[ $post_id ] ), $post_id, $votes );
	}

	/**
	 * Add vote.
	 *
	 * @param          $post_id
	 * @param null|int $user_id
	 *
	 * @return array
	 */
	public function add_vote( $post_id, $user_id = null ) {
		$return = array(
			'success' => false,
			'reason'  => null,
		);

		$user_id = self::get_user_id( $user_id );

		if ( $user_id === 0 ) {
			$return['reason'] = __( 'You need to login to vote for a feature.', 'simple-feature-requests' );

			return $return;
		}

		if ( $this->has_voted( $post_id ) ) {
			$return['reason'] = __( 'You have already voted for this feature.', 'simple-feature-requests' );

			return $return;
		}

		$votes             = $this->get_votes( $user_id );
		$votes[ $post_id ] = 1;
		$this->votes       = $votes;

		$update = update_user_meta( $user_id, self::$votes_meta_key, $votes );

		if ( ! $update ) {
			$return['reason'] = __( 'There was an error adding your vote.', 'simple-feature-requests' );

			return $return;
		}

		$return['success'] = true;

		return $return;
	}

	/**
	 * Remove vote.
	 *
	 * @param int      $post_id
	 * @param null|int $user_id
	 *
	 * @return array
	 */
	public function remove_vote( $post_id, $user_id = null ) {
		$return = array(
			'success' => false,
			'reason'  => null,
		);

		$user_id = self::get_user_id( $user_id );

		if ( $user_id === 0 ) {
			$return['reason'] = __( 'You need to login to remove a vote from a feature.', 'simple-feature-requests' );

			return $return;
		}

		if ( ! $this->has_voted( $post_id ) ) {
			$return['reason'] = __( 'You have not voted for this feature yet.', 'simple-feature-requests' );

			return $return;
		}

		$post_author_id = (int) get_post_field( 'post_author', $post_id );

		if ( $post_author_id === $user_id ) {
			$return['reason'] = __( 'You cannot remove a vote for your own request.', 'simple-feature-requests' );

			return $return;
		}

		$votes = $this->get_votes( $user_id );
		unset( $votes[ $post_id ] );

		$this->votes = $votes;

		$update = update_user_meta( $user_id, self::$votes_meta_key, $votes );

		if ( ! $update ) {
			$return['reason'] = __( 'There was an error removing your vote.', 'simple-feature-requests' );

			return $return;
		}

		$return['success'] = true;

		return $return;
	}

	/**
	 * Validate user logging in.
	 *
	 * @param string|null $email
	 * @param string|null $password
	 *
	 * @return bool|WP_User|WP_Error
	 */
	public static function login( $email, $password ) {
		if ( is_user_logged_in() ) {
			return wp_get_current_user();
		}

		$error   = false;
		$notices = JCK_SFR_Notices::instance();

		if ( empty( $email ) ) {
			$notices->add( __( 'Please enter an email.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( empty( $password ) ) {
			$notices->add( __( 'Please enter a password.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( $error ) {
			return false;
		}

		$credentials = array(
			'user_login'    => $email,
			'user_password' => $password,
			'remember'      => true,
		);

		$user = wp_signon( $credentials, is_ssl() );

		if ( is_wp_error( $user ) ) {
			$notices->add( $user->get_error_message(), 'error' );
		}

		return $user;
	}

	/**
	 * Register user.
	 *
	 * @param string|null $username
	 * @param string|null $email
	 * @param string|null $password
	 * @param string|null $repeat_password
	 *
	 * @return bool|WP_User|WP_Error
	 */
	public static function register( $username, $email, $password, $repeat_password ) {
		$error   = false;
		$notices = JCK_SFR_Notices::instance();

		if ( empty( $username ) ) {
			$notices->add( __( 'Please enter a username.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( empty( $email ) ) {
			$notices->add( __( 'Please enter an email.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( empty( $password ) ) {
			$notices->add( __( 'Please enter a password.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( empty( $repeat_password ) ) {
			$notices->add( __( 'Please enter a repeat password.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( $password !== $repeat_password ) {
			$notices->add( __( 'Your passwords did not match.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( $error ) {
			return false;
		}

		$username_exists = username_exists( $username );
		$email_exists    = email_exists( $email );

		if ( $username_exists ) {
			$notices->add( __( 'That username has already been registered.', 'simple-feature-requests' ), 'error' );
			$error = true;
		}

		if ( $email_exists ) {
			$notices->add( __( 'That email has already been registered.', 'simple-feature-requests' ) );
			$error = true;
		}

		if ( $error ) {
			return false;
		}

		$user_id = wp_create_user( $username, $password, $email );

		if ( ! $user_id || is_wp_error( $user_id ) ) {
			$notices->add( __( 'There was an issue registering your account. Please try again.', 'simple-feature-requests' ), 'error' );

			return false;
		}

		return self::login( $email, $password );
	}

	/**
	 * Get user_id.
	 *
	 * @param int|null $user_id
	 *
	 * @return int
	 */
	public static function get_user_id( $user_id = null ) {
		return ! is_null( $user_id ) ? $user_id : get_current_user_id();
	}
}