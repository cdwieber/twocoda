<?php get_header(); ?>

<div class="container">
	<?php get_template_part('includes/breadcrumbs'); ?>
	<div id="content-area" class="clearfix">
		<div id="left-area">
			<?php get_template_part('includes/entry'); ?>
		</div> 	<!-- end #left-area -->

		<?php get_sidebar(); ?>
	</div> <!-- end #content-area -->
</div> <!-- end .container -->

<?php get_footer(); ?>