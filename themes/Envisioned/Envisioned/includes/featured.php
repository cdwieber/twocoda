<div id="featured">
	<div class="container">
		<?php
			$i=1;

			$featured_cat = get_option('envisioned_feat_cat');
			$featured_num = (int) get_option('envisioned_featured_num');
			$width = 500;
			$height = 335;

			if (get_option('envisioned_use_pages') == 'false') query_posts("posts_per_page=$featured_num&cat=".get_catId($featured_cat));
			else {
				global $pages_number;

				if (get_option('envisioned_feat_pages') <> '') $featured_num = count(get_option('envisioned_feat_pages'));
				else $featured_num = $pages_number;

				$et_featured_pages_args = array(
					'post_type' => 'page',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'posts_per_page' => (int) $featured_num,
				);

				if ( is_array( et_get_option( 'envisioned_feat_pages', '', 'page' ) ) )
					$et_featured_pages_args['post__in'] = (array) array_map( 'intval', et_get_option( 'envisioned_feat_pages', '', 'page' ) );

				query_posts( $et_featured_pages_args );
			}

			while (have_posts()) : the_post();
				$et_link = get_post_meta(get_the_ID(),'Link',true) ? get_post_meta(get_the_ID(),'Link',true) : get_permalink();
		?>
				<div class="slide<?php if ( $i == 1 ) echo ' active'; ?>">
					<?php
						$post_title = get_the_title();
						$thumbnail = get_thumbnail($width,$height,'thumb',$post_title,$post_title,true,'Featured');
						$thumb = $thumbnail["thumb"];

						print_thumbnail($thumb, $thumbnail["use_timthumb"], $post_title, $width, $height);
					?>
					<div class="description"><h2><?php truncate_title(68); ?></h2></div>

					<div class="additional">
						<a class="prevslide" href="#"><?php esc_html_e('Previous','Envisioned'); ?></a>
						<a class="nextslide" href="#"><?php esc_html_e('Next','Envisioned'); ?></a>
						<a class="featured-zoom <?php if (get_post_meta(get_the_ID(),'et_videolink',true)) echo 'et_video_lightbox'; else echo 'fancybox'; ?>" href="<?php if (get_post_meta(get_the_ID(),'et_videolink',true)) echo esc_url(get_post_meta(get_the_ID(),'et_videolink',true)); else echo esc_attr($thumbnail["fullpath"]); ?>"<?php if (!get_post_meta(get_the_ID(),'et_videolink',true)) echo ' rel="featured"'; ?> title="<?php the_title(); ?>"><?php esc_html_e('Zoom In','Envisioned'); ?></a>
						<a class="featured-more" href="<?php echo esc_url($et_link); ?>"><?php esc_html_e('Read More','Envisioned'); ?></a>
					</div> <!-- end .additional -->
				</div> <!-- end .slide -->
		<?php
				$i++;
			endwhile; wp_reset_query();
		?>
	</div> <!-- end .container -->

	<div id="slider-left-overlay"></div>
	<div id="slider-right-overlay"></div>
</div> <!-- end #featured -->