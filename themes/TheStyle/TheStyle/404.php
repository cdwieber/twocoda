<?php get_header(); ?>

<?php get_template_part('includes/breadcrumbs'); ?>

<div id="content" class="clearfix fullwidth">
	<div id="left-area">
		<div id="post" class="post">
			<div class="post-content clearfix">
				<div class="post-text">
					<h1 class="title"><?php esc_html_e('No Results Found','TheStyle'); ?></h1>

					<div class="hr"></div>
					<p><?php esc_html_e('The page you requested could not be found. Try refining your search, or use the navigation above to locate the post.','TheStyle'); ?></p>
				</div> <!-- .post-text -->
			</div> <!-- .post-content -->
		</div> <!-- #post -->
	</div> <!-- #left-area -->
	<?php get_sidebar(); ?>
</div> <!-- #content -->

<div id="content-bottom-bg"></div>

<?php get_footer(); ?>