<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php elegant_description(); ?>
<?php elegant_keywords(); ?>
<?php elegant_canonical(); ?>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie6style.css" />
	<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/DD_belatedPNG_0.0.8a-min.js"></script>
	<script type="text/javascript">DD_belatedPNG.fix('img#logo, span.overlay, a.zoom-icon, a.more-icon, #menu, #menu-right, #menu-content, ul#top-menu ul, #featured a#left-arrow, #featured a#right-arrow, #top-bottom, #top .container, #recent-projects, #recent-projects-right, #recent-projects-content, .project-overlay, span#down-arrow, #footer-content, #footer-top, .footer-widget ul li, span.post-overlay, #content-area, .avatar-overlay, .comment-arrow');</script>
<![endif]-->
<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie7style.css" />
<![endif]-->
<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie8style.css" />
<![endif]-->

<script type="text/javascript">
	document.documentElement.className = 'js';
</script>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
	<div id="top">
		<div id="top-wrapper">
			<div id="top-content">
				<div id="bottom-light">
					<div class="container">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<?php $logo = (get_option('envisioned_logo') <> '') ? get_option('envisioned_logo') : get_template_directory_uri().'/images/logo.png'; ?>
							<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" id="logo"/>
						</a>
						<?php do_action('et_header'); ?>
						<div id="menu" class="clearfix">
							<div id="menu-right">
								<div id="menu-content">
									<?php $menuClass = 'nav';
									$menuID = 'top-menu';
									$primaryNav = '';
									if (function_exists('wp_nav_menu')) {
										$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'menu_id' => $menuID, 'echo' => false ) );
									};
									if ($primaryNav == '') { ?>
										<ul id="<?php echo esc_attr( $menuID ); ?>" class="<?php echo esc_attr( $menuClass ); ?>">
											<?php if (get_option('envisioned_home_link') == 'on') { ?>
												<li <?php if (is_home()) echo('class="current_page_item"') ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Home','Envisioned') ?></a></li>
											<?php }; ?>

											<?php show_page_menu($menuClass,false,false); ?>
											<?php show_categories_menu($menuClass,false); ?>
										</ul> <!-- end ul#nav -->
									<?php }
									else echo($primaryNav); ?>

									<div id="social-icons">
										<?php
											$et_rss_url = get_option('envisioned_rss_url') <> '' ? get_option('envisioned_rss_url') : get_bloginfo('rss2_url');
											if ( get_option('envisioned_show_twitter_icon') == 'on' ) $social_icons['twitter'] = array('image' => get_template_directory_uri() . '/images/twitter.png', 'url' => get_option('envisioned_twitter_url'), 'alt' => 'Twitter' );
											if ( get_option('envisioned_show_rss_icon') == 'on' ) $social_icons['rss'] = array('image' => get_template_directory_uri() . '/images/rss.png', 'url' => $et_rss_url, 'alt' => 'Rss' );
											if ( get_option('envisioned_show_facebook_icon') == 'on' ) $social_icons['facebook'] = array('image' => get_template_directory_uri() . '/images/facebook.png', 'url' => get_option('envisioned_facebook_url'), 'alt' => 'Facebook' );
											$social_icons = apply_filters('et_social_icons', $social_icons);
											if ( !empty($social_icons) ) {
												foreach ($social_icons as $icon) {
													echo "<a href='" . esc_url($icon['url']) . "' target='_blank'><img alt='" . esc_attr($icon['alt']) . "' src='" . esc_url($icon['image']) . "' /></a>";
												}
											}
										?>
									</div> <!-- end #social-icons -->
								</div> <!-- end #menu-content -->
							</div> <!-- end #menu-right -->
						</div> <!-- end #menu -->

						<?php if ( !is_home() ) get_template_part('includes/top_info'); ?>
					</div> <!-- end .container -->

					<?php if ( is_home() && get_option('envisioned_featured') == 'on' ) get_template_part('includes/featured'); ?>
				</div> <!-- end #bottom-light -->
			</div> <!-- end #top-content -->
		</div> <!-- end #top-wrapper -->
	</div> <!-- end #top -->

	<div id="content">
		<div id="content-shadow">