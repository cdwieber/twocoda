<?php get_header(); ?>

<div class="container">
	<?php get_template_part('includes/breadcrumbs'); ?>
	<div id="content-area" class="clearfix">
		<div id="left-area">
			<p><?php esc_html_e('The page you requested could not be found. Try refining your search, or use the navigation above to locate the post.','Envisioned'); ?></p>
		</div> 	<!-- end #left-area -->

		<?php get_sidebar(); ?>
	</div> <!-- end #content-area -->
</div> <!-- end .container -->

<?php get_footer(); ?>