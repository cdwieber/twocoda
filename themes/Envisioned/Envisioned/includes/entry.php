<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="post entry clearfix">
		<?php
			$thumb = '';
			$width = 212;
			$height = 213;
			$classtext = 'post-thumb';
			$titletext = get_the_title();
			$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Entry');
			$thumb = $thumbnail["thumb"];
		?>
		<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<?php get_template_part('includes/postinfo'); ?>

		<?php if($thumb <> '' && get_option('envisioned_thumbnails_index') == 'on') { ?>
			<div class="post-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
					<span class="post-overlay"></span>
				</a>
			</div> 	<!-- end .post-thumbnail -->
		<?php } ?>
		<?php if (get_option('envisioned_blog_style') == 'on') the_content(''); else { ?>
			<p><?php truncate_post(600); ?></p>
		<?php }; ?>
		<a href="<?php the_permalink(); ?>" class="readmore"><span><?php esc_html_e('Read More','Envisioned'); ?></span></a>
	</div> 	<!-- end .post-->
<?php endwhile; ?>
	<div class="clear"></div>
	<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
	else { ?>
		 <?php get_template_part('includes/navigation'); ?>
	<?php } ?>
<?php else : ?>
	<?php get_template_part('includes/no-results'); ?>
<?php endif; ?>