<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get statuses.
 *
 * @param array $excludes
 *
 * @return array
 */
function jck_sfr_get_statuses( $excludes = array() ) {
	$statuses = apply_filters( 'jck_sfr_statuses', array(
		'pending'      => __( 'Pending', 'jck_sfr' ),
		'publish'      => __( 'Published', 'jck_sfr' ),
		'under-review' => __( 'Under Review', 'jck_sfr' ),
		'planned'      => __( 'Planned', 'jck_sfr' ),
		'started'      => __( 'Started', 'jck_sfr' ),
		'completed'    => __( 'Completed', 'jck_sfr' ),
		'declined'     => __( 'Declined', 'jck_sfr' ),
	) );

	if ( ! empty( $excludes ) ) {
		foreach ( $excludes as $exclude ) {
			unset( $statuses[ $exclude ] );
		}
	}

	return $statuses;
}

/**
 * Get terms for filter.
 *
 * @param string $taxonomy
 * @param bool   $hide_empty
 *
 * @return array
 */
function jck_sfr_get_term_options( $taxonomy, $hide_empty = true ) {
	static $filter_terms = array();

	$key = sprintf( '%s_%d', $taxonomy, $hide_empty );

	if ( ! empty( $filter_terms[ $key ] ) ) {
		return $filter_terms[ $key ];
	}

	$filter_terms[ $key ] = array();

	$terms = get_terms( $taxonomy, array(
		'hide_empty' => $hide_empty,
	) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return $filter_terms[ $key ];
	}

	foreach ( $terms as $term ) {
		$filter_terms[ $key ][ $term->slug ] = $term->name;
	}

	return $filter_terms[ $key ];
}

/**
 * Get status label.
 *
 * @param $status
 *
 * @return string
 */
function jck_sfr_get_status_label( $status ) {
	$statuses = jck_sfr_get_statuses();

	if ( ! isset( $statuses[ $status ] ) ) {
		return '';
	}

	return $statuses[ $status ];
}

/**
 * Get status colours.
 *
 * @param $status
 *
 * @return mixed
 */
function jck_sfr_get_status_colors( $status ) {
	$colors = apply_filters( 'jck_sfr_status_colors', array(
		'pending'      => array(
			'background' => '#FFE26C',
			'color'      => '#0F0F14',
		),
		'publish'      => array(
			'background' => '#CCC',
			'color'      => '#0F0F14',
		),
		'completed'    => array(
			'background' => '#4dcea6',
			'color'      => '#fff',
		),
		'under-review' => array(
			'background' => '#CCC',
			'color'      => '#0F0F14',
		),
		'planned'      => array(
			'background' => '#FFC05F',
			'color'      => '#0F0F14',
		),
		'declined'     => array(
			'background' => '#F45B7C',
			'color'      => '#fff',
		),
		'started'      => array(
			'background' => '#568ECD',
			'color'      => '#fff',
		),
		'default'      => array(
			'background' => '#CCC',
			'color'      => '#0F0F14',
		),
	) );

	return isset( $colors[ $status ] ) ? $colors[ $status ] : $colors['default'];
}

/**
 * Get default post status.
 *
 * @return string
 */
function jck_sfr_get_default_post_status() {
	return apply_filters( 'jck_sfr_get_default_post_status', 'pending' );
}

/**
 * Get archive URL with filters.
 *
 * @param array $excludes
 *
 * @return string
 */
function jck_sfr_get_archive_url_with_filters( $excludes = array() ) {
	$url_parts = array(
		'base'  => get_post_type_archive_link( 'cpt_feature_requests' ),
		'query' => $_SERVER['QUERY_STRING'],
	);

	// Remove params of "pending" request.
	$excludes[] = 'p';
	$excludes[] = 'post_type';

	if ( ! empty( $excludes ) && ! empty( $url_parts['query'] ) ) {
		parse_str( $url_parts['query'], $query );

		foreach ( $excludes as $exclude ) {
			unset( $query[ $exclude ] );
		}

		$url_parts['query'] = http_build_query( $query );
	}

	if ( empty( $url_parts['query'] ) ) {
		return $url_parts['base'];
	}

	return implode( '?', $url_parts );
}