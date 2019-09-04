<?php get_header(); ?>

<?php if ( get_option('envisioned_blog_style') == 'false' ) { ?>
	<?php if ( get_option('envisioned_display_blurbs') == 'on' ){ ?>
		<div id="services">
			<div class="container clearfix">
				<?php for ($i=1; $i <= 3; $i++) { ?>
					<?php query_posts('page_id=' . get_pageId(html_entity_decode(get_option('envisioned_home_page_'.$i)))); while (have_posts()) : the_post(); ?>
						<?php
							global $more; $more = 0;
						?>
						<div class="service<?php if ( $i == 3 ) echo ' last'; ?>">
							<?php
								$et_service_image = get_post_meta($post->ID,'Icon',true) ? get_post_meta($post->ID,'Icon',true) : '';
								$et_service_link = get_post_meta($post->ID,'etlink',true) ? get_post_meta($post->ID,'etlink',true) : get_permalink();
							?>
							<?php if ($et_service_image <> '') { ?>
								<img src="<?php echo esc_url($et_service_image); ?>" alt="<?php the_title(); ?>" class="icon" />
							<?php } ?>
							<h3 class="title"><?php the_title(); ?></h3>
							<?php the_content(''); ?>

							<a href="<?php echo esc_url($et_service_link); ?>" class="readmore"><span><?php esc_html_e('Read More','Envisioned'); ?></span></a>
						</div> <!-- end .service -->
					<?php endwhile; wp_reset_query(); ?>
				<?php } ?>
			</div> <!-- end .container -->
		</div> <!-- end #services -->
	<?php } ?>

	<?php if ( get_option('envisioned_quote') == 'on' ) { ?>
		<div id="quote">
			<div id="quote-inner">
				<div class="container">
					<span id="quote-icon"></span>
					<p><?php echo get_option('envisioned_quote_text'); ?></p>
					<a href="<?php echo esc_url(get_option('envisioned_quote_url')); ?>" class="additional-info"><span><?php esc_html_e('Additional Info','Envisioned'); ?></span></a>
				</div> <!-- end .container -->
			</div> <!-- end #quote-inner -->
		</div> <!-- end #quote -->
	<?php } ?>

	<?php if ( get_option('envisioned_display_media') == 'on' ){ ?>
		<div id="home-gallery">
			<div class="container clearfix">
				<?php
					$media_current_post = 1;
				?>
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php
						$width = 140;
						$height = 94;
						$titletext = get_the_title();
						$thumbnail = get_thumbnail($width,$height,'project-image',$titletext,$titletext,true,'Media');
						$thumb = $thumbnail["thumb"];
						$et_medialink = get_post_meta($post->ID,'et_medialink',true) ? get_post_meta($post->ID,'et_medialink',true) : '';
						$et_videolink = get_post_meta($post->ID,'et_videolink',true) ? get_post_meta($post->ID,'et_videolink',true) : '';
					?>
					<div class="project<?php if ( $media_current_post % 5 == 0 ) echo ' last'; ?>">
						<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, 'project-image'); ?>
						<span class="project-overlay"></span>
						<?php if ( $et_medialink <> '' ) { ?>
							<a href="<?php echo esc_url($et_medialink); ?>" class="zoom-icon">
						<?php } elseif ( $et_videolink <> '' ) { ?>
							<a href="<?php echo esc_url($et_videolink); ?>" class="et-video zoom-icon et_video_lightbox" title="<?php echo esc_attr($titletext); ?>">
						<?php } else { ?>
							<a href="<?php echo esc_url($thumbnail["fullpath"]); ?>" class="zoom-icon fancybox" rel="media" title="<?php echo esc_attr($titletext); ?>">
						<?php } ?><?php esc_html_e('Zoom In','Envisioned'); ?>
							</a>
							<a href="<?php the_permalink(); ?>" class="more-icon"><?php esc_html_e('Read more','Envisioned'); ?></a>
					</div> 	<!-- end .project -->
				<?php $media_current_post++;
				endwhile; ?>
				<?php endif; ?>
			</div> <!-- end .container -->
		</div> <!-- end #home-gallery -->
	<?php } ?>
<?php } else { ?>
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
				<?php get_template_part('includes/entry','home'); ?>
			</div> 	<!-- end #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- end #content-area -->
	</div> <!-- end .container -->

<?php } ?>

<?php get_footer(); ?>