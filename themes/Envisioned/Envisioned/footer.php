		</div> <!-- end #content-shadow -->
	</div> <!-- end #content -->

	<div id="footer">
		<div id="footer-top">
			<div id="footer-content">
				<div class="container">
					<div id="footer-widgets" class="clearfix">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer') ) : ?>
						<?php endif; ?>
					</div> <!-- end #footer-widgets -->
				</div> <!-- end .container -->
				<div id="footer-bottom" class="clearfix">
					<div class="container">
						<?php
							$menuClass = 'bottom-nav';
							$footerNav = '';

							if (function_exists('wp_nav_menu')) $footerNav = wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false, 'depth' => '1' ) );
							if ($footerNav == '') show_page_menu($menuClass);
							else echo($footerNav);
						?>

						<p id="copyright"><?php esc_html_e('Designed by ','Envisioned'); ?> <a href="http://www.elegantthemes.com" title="Premium WordPress Themes">Elegant Themes</a> | <?php esc_html_e('Powered by ','Envisioned'); ?> <a href="http://www.wordpress.org">WordPress</a></p>
					</div> <!-- end .container -->
				</div> <!-- end #footer-bottom -->
			</div> <!-- end #footer-content -->
		</div> <!-- end #footer-top -->
	</div> <!-- end #footer -->

	<?php get_template_part('includes/scripts'); ?>
	<?php wp_footer(); ?>
</body>
</html>
