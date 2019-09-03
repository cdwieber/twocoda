<?php
/**
 * The Template for displaying a single feature request.
 *
 * @author        James Kemp
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'jck-sfr' ); ?>

<?php
/**
 * jck_sfr_before_wrapper hook.
 */
do_action( 'jck_sfr_before_wrapper' );
?>

	<div class="jck-sfr-container">
		<?php
		/**
		 * jck_sfr_before_columns hook.
		 */
		do_action( 'jck_sfr_before_columns' );
		?>

		<div class="jck-sfr-container__col jck-sfr-container__col--1">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					/**
					 * jck_sfr_before_single_loop hook.
					 *
					 * @hooked JCK_SFR_Notices::print_notices() - 10
					 */
					do_action( 'jck_sfr_before_single_loop' );

					/**
					 * jck_sfr_loop hook.
					 *
					 * @hooked JCK_SFR_Template_Hooks::loop_content() - 10
					 */
					do_action( 'jck_sfr_loop' );

					/**
					 * jck_sfr_after_single_loop hook.
					 *
					 * @hooked JCK_SFR_Template_Hooks::comments() - 10
					 */
					do_action( 'jck_sfr_after_single_loop' );
					?>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>

		<div class="jck-sfr-container__col jck-sfr-container__col--2">
			<?php
			/**
			 * jck_sfr_sidebar hook.
			 */
			do_action( 'jck_sfr_sidebar' );
			?>
		</div>

		<?php
		/**
		 * jck_sfr_after_columns hook.
		 */
		do_action( 'jck_sfr_after_columns' );
		?>
	</div>

<?php
/**
 * jck_sfr_after_wrapper hook.
 */
do_action( 'jck_sfr_after_wrapper' );
?>

<?php get_footer( 'jck-sfr' ); ?>