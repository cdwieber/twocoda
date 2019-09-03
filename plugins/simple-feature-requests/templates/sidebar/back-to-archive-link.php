<?php
/**
 * The Template for displaying the back to archive link.
 *
 * @author        James Kemp
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! is_single() ) {
	return;
}
?>

<div class="jck-sfr-sidebar-widget jck-sfr-sidebar-widget--back">
	<?php JCK_SFR_Template_Methods::back_to_archive_link(); ?>
</div>
