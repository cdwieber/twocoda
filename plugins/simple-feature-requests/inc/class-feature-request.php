<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Feature Request Class.
 */
class JCK_SFR_Feature_Request {
	/**
	 * @var WP_Post
	 */
	public $post;

	/**
	 * Voters.
	 *
	 * @var null|array
	 */
	protected $voters = null;

	/**
	 * Construct post.
	 *
	 * @param int|WP_Post $post
	 */
	public function __construct( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		$this->post = $post;
	}

	/**
	 * Get votes count.
	 *
	 * @return int
	 */
	public function get_votes_count() {
		return $this->get_meta( 'votes' );
	}

	/**
	 * Get voters.
	 *
	 * @param bool $include_athor
	 *
	 * @return array
	 */
	public function get_voters( $include_athor = true ) {
		if ( ! is_null( $this->voters ) ) {
			return $this->voters;
		}

		$args = array(
			'meta_query' => array(
				array(
					'key'     => JCK_SFR_User::$votes_meta_key,
					'value'   => sprintf( 'i:%d;', $this->post->ID ),
					'compare' => 'LIKE',
				),
			),
		);

		if ( ! $include_athor ) {
			$args['exlcude'] = array( $this->post->post_author );
		}

		$this->voters = get_users( $args );

		return $this->voters;
	}

	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function get_status() {
		$status = $this->get_meta( 'status' );

		if ( ! $status ) {
			$status = jck_sfr_get_default_post_status();
		}

		return $status;
	}

	/**
	 * Update meta.
	 *
	 * @param $key
	 * @param $value
	 */
	public function update_meta( $key, $value ) {
		$value    = apply_filters( 'jck_sfr_update_meta_value', $value, $key, $this );
		$meta_key = sprintf( 'jck_sfr_%s', $key );

		update_post_meta( $this->post->ID, $meta_key, $value );

		do_action( 'jck_sfr_meta_updated', $key, $value, $this );
		do_action( 'jck_sfr_' . $key . '_updated', $value, $this );
	}

	/**
	 * Get meta.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get_meta( $key ) {
		$meta_key = sprintf( 'jck_sfr_%s', $key );
		$meta     = get_post_meta( $this->post->ID, $meta_key, true );

		return apply_filters( 'jck_sfr_get_meta', $meta, $key, $this );
	}

	/**
	 * Increment votes count.
	 *
	 * @param string   $type add|remove
	 * @param int      $inc
	 * @param null|int $user_id
	 *
	 * @return array
	 */
	public function set_votes_count( $type = 'add', $inc = 1, $user_id = null ) {
		$return = array(
			'success'             => false,
			'reason'              => null,
			'updated_votes_count' => null,
		);

		$user_instance = JCK_SFR_User::instance();
		$vote          = $type === 'add' ? $user_instance->add_vote( $this->post->ID, $user_id ) : $user_instance->remove_vote( $this->post->ID, $user_id );

		if ( ! $vote['success'] ) {
			return $vote;
		}

		$votes_count = $this->get_votes_count();
		$votes_count = $type === 'add' ? $votes_count + $inc : $votes_count - $inc;

		$this->set_votes( $votes_count );

		$return['success']             = true;
		$return['updated_votes_count'] = $votes_count;

		return $return;
	}

	/**
	 * Set votes.
	 *
	 * @param int $votes
	 */
	public function set_votes( $votes ) {
		$votes = absint( $votes );

		$this->update_meta( 'votes', $votes );
	}

	/**
	 * Set status.
	 *
	 * @param string $status
	 */
	public function set_status( $status, $force = false ) {
		if ( empty( $status ) ) {
			return;
		}

		if ( ! $force && $status === $this->get_status() ) {
			return;
		}

		$statuses = jck_sfr_get_statuses();

		if ( ! isset( $statuses[ $status ] ) ) {
			$status = 'pending';
		}

		$this->update_meta( 'status', $status );
	}

	/**
	 * Update taxonomies.
	 *
	 * @param array|bool $taxonomies
	 * @param bool       $append
	 */
	public function update_taxonomies( $taxonomies, $append = false ) {
		if ( empty( $taxonomies ) ) {
			return;
		}

		foreach ( $taxonomies as $taxonomy => $terms ) {
			wp_set_post_terms( $this->post->ID, $terms, $taxonomy, $append );

			do_action( 'jck_sfr_taxonomy_updated', $taxonomy, $terms, $this );
			do_action( 'jck_sfr_' . $taxonomy . '_updated', $terms, $this );
		}
	}

	/**
	 * Has user voted?
	 *
	 * @return bool
	 */
	public function has_user_voted() {
		$user = JCK_SFR_User::instance();

		return $user->has_voted( $this->post->ID );
	}

	/**
	 * Get vote button text.
	 *
	 * @return string
	 */
	public function get_vote_button_text() {
		$has_voted = $this->has_user_voted();
		$text      = $has_voted ? __( 'Voted', 'simple - feature - requests' ) : __( 'Vote', 'simple - feature - requests' );

		return apply_filters( 'jck_sfr_vote_button_text', $text, $has_voted, $this );
	}

	/**
	 * Is pending?
	 *
	 * @return bool
	 */
	public function is_pending() {
		return $this->post->post_status === 'pending';
	}

	/**
	 * Is single?
	 *
	 * @return bool
	 */
	public function is_single() {
		return is_singular( 'cpt_feature_requests' );
	}

	/**
	 * Loop item wrapper class.
	 */
	public function wrapper_class() {
		$classes = array(
			'jck-sfr-loop-item-wrapper',
		);

		if ( is_singular( 'cpt_feature_requests' ) ) {
			$classes[] = 'jck-sfr-loop-item-wrapper--single';
		}

		if ( is_post_type_archive( 'cpt_feature_requests' ) ) {
			$classes[] = 'jck-sfr-loop-item-wrapper--archive';
		}

		$classes = apply_filters( 'jck_sfr_loop_item_wrapper_class', $classes );

		printf( 'class="%s"', implode( ' ', array_map( 'esc_attr', $classes ) ) );
	}

	/**
	 * Loop item wrapper class.
	 */
	public function item_class() {
		$classes = apply_filters( 'jck_sfr_loop_item_class', array(
			'jck-sfr-loop-item',
		) );

		printf( 'class="%s"', implode( ' ', array_map( 'esc_attr', $classes ) ) );
	}
}