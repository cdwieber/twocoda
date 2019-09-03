<?php
/**
 * The Template for displaying feature request archives.
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
			<?php
			/**
			 * jck_sfr_before_main_content hook.
			 *
			 * @hooked JCK_SFR_Notices::print_notices() - 10
			 * @hooked JCK_SFR_Template_Hooks::submission_form() - 20
			 * @hooked JCK_SFR_Template_Hooks::filters() - 30
			 */
			do_action( 'jck_sfr_before_main_content' );
			?>

			<div class="jck-sfr-content">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php
						/**
						 * jck_sfr_loop hook.
						 *
						 * @hooked JCK_SFR_Template_Hooks::loop_content() - 10
						 */
						do_action( 'jck_sfr_loop' );
						?>
					<?php endwhile; ?>
				<?php else: ?>

					<?php
					/**
					 * jck_sfr_no_requests_found hook.
					 *
					 * @hooked JCK_SFR_Template_Hooks::no_requests_found() - 10
					 */
					do_action( 'jck_sfr_no_requests_found' );
					?>

				<?php endif; ?>
			</div>

			<?php
			/**
			 * jck_sfr_after_main_content hook.
			 *
			 * @hooked JCK_SFR_Template_Hooks::pagination() - 10
			 */
			do_action( 'jck_sfr_after_main_content' );
			?>
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