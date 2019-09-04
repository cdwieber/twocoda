<?php get_header(); ?>

<div class="container">
	<?php get_template_part('includes/breadcrumbs'); ?>
	<div id="content-area" class="clearfix">
		<div id="left-area">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="entry post clearfix">
				<?php if (get_option('envisioned_integration_single_top') <> '' && get_option('envisioned_integrate_singletop_enable') == 'on') echo(get_option('envisioned_integration_single_top')); ?>

				<?php get_template_part('includes/postinfo'); ?>

				<?php
					$et_envisioned_settings = maybe_unserialize( get_post_meta(get_the_ID(),'et_envisioned_settings',true) );
					$et_post_type = isset( $et_envisioned_settings['et_post_type'] ) ? (int) $et_envisioned_settings['et_post_type'] : 1;
				?>

				<?php if ( $et_post_type == 1 ) { ?>
					<?php if (get_option('envisioned_thumbnails') == 'on') { ?>
						<?php
							$thumb = '';
							$width = 213;
							$height = 213;
							$classtext = 'post-thumb';
							$titletext = get_the_title();
							$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Entry');
							$thumb = $thumbnail["thumb"];
						?>

						<?php if($thumb <> '') { ?>
							<div class="post-thumbnail">
								<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
								<span class="post-overlay"></span>
							</div> 	<!-- end .post-thumbnail -->
						<?php } ?>
					<?php } ?>
				<?php } ?>

				<?php if ( $et_post_type == 2 ) { ?>
					<?php
						$thumb = '';
						$width = 608;
						$height = 9999;
						$classtext = 'post-gallery-thumb';
						$titletext = get_the_title();
						$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,true,'Postgallery');
						$thumb = $thumbnail["thumb"];
					?>

					<?php if($thumb <> '') { ?>
						<div class="post-gallery-thumbnail">
							<div class="gallery-thumbnail">
								<a href="<?php echo esc_url( $thumbnail["fullpath"] ); ?>" class="fancybox" title="<?php the_title(); ?>">
									<?php if ($thumbnail["use_timthumb"]) { ?>
										<img src="<?php echo esc_attr(print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext, false, false, false)); ?>" alt="<?php the_title(); ?>" />
									<?php } else { ?>
										<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
									<?php } ?>
									<span class="post-overlay"></span>
								</a>
							</div> 	<!-- end .gallery-thumbnail -->
						</div> 	<!-- end .post-gallery-thumbnail -->
						<div class="clear"></div>
					<?php } ?>
				<?php } ?>

				<?php if ( $et_post_type == 3 ) { ?>
					<div class="post-gallery-thumbnail">
						<?php
							$video = get_post_meta(get_the_ID(), 'et_videolink', true);
							$video_embed = $wp_embed->shortcode( '', esc_url( $video ) );
							if ( $video_embed == '<a href="'.$video.'">'.$video.'</a>' ) $video_embed = $video_manual_embed;

							$video_embed = preg_replace('/<embed /','<embed wmode="transparent" ',$video_embed);
							$video_embed = preg_replace('/<\/object>/','<param name="wmode" value="transparent" /></object>',$video_embed);

							$video_embed = preg_replace("/width=\"[0-9]*\"/", "width=608", $video_embed);
							$video_embed = preg_replace("/height=\"[0-9]*\"/", "height=376", $video_embed);

							echo $video_embed;
						?>
					</div> <!-- end .post-gallery-thumbnail -->
				<?php } ?>

				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<p><strong>'.esc_html__('Pages','Envisioned').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
				<?php edit_post_link(esc_html__('Edit this page','Envisioned')); ?>
			</div> <!-- end .entry -->

			<?php if (get_option('envisioned_integration_single_bottom') <> '' && get_option('envisioned_integrate_singlebottom_enable') == 'on') echo(get_option('envisioned_integration_single_bottom')); ?>

			<?php if (get_option('envisioned_468_enable') == 'on') { ?>
					  <?php if(get_option('envisioned_468_adsense') <> '') echo(get_option('envisioned_468_adsense'));
					else { ?>
					   <a href="<?php echo esc_url(get_option('envisioned_468_url')); ?>"><img src="<?php echo esc_attr(get_option('envisioned_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
			   <?php } ?>
			<?php } ?>

			<?php if (get_option('envisioned_show_postcomments') == 'on') comments_template('', true); ?>
		<?php endwhile; endif; ?>
		</div> 	<!-- end #left-area -->

		<?php get_sidebar(); ?>
	</div> <!-- end #content-area -->
</div> <!-- end .container -->

<?php get_footer(); ?>