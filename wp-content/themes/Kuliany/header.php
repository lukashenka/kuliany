<?php
/**
 * The Header template for our theme
 *
 * @package techism
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo('charset'); ?>"/>
	<meta name="viewport" content="width=device-width"/>
	<title><?php wp_title('|', true, 'right'); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11"/>
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>
	<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/assets/js/html5.js" type="text/javascript"></script>
	<![endif]-->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">


	<div id="main" class="wrapper">

		<div id="left-sidebar-top" class="widget-area" role="complementary">
			<?php do_action('before_sidebar'); ?>

			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-top-1')): ?>

			<?php endif; ?>
		</div>
		<!-- #left-sidebar -->
		<div id="header">
			<header id="masthead" class="site-header" role="banner">
				<hgroup>
					<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"
					                          title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>"
					                          rel="home"><?php bloginfo('name'); ?></a></h1>

					<h2 class="site-description"><?php bloginfo('description'); ?></h2>
				</hgroup>
				<?php if (get_header_image()) : ?>
					<a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php header_image(); ?>"
					                                                     class="header-image"
					                                                     width="<?php echo get_custom_header()->width; ?>"
					                                                     height="<?php echo get_custom_header()->height; ?>"
					                                                     alt=""/></a>
				<?php else: ?>
				<?php endif; ?>
			</header>
			<!-- #masthead -->
		</div>
		<div id="right-sidebar-top" class="widget-area" role="complementary">
			<?php do_action('before_sidebar'); ?>

			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-top-2')): ?>

			<?php endif; ?>
		</div>
		<!-- #right-sidebar -->

		<div class="clear"></div>
		<nav id="site-navigation" class="main-navigation" role="navigation">
			<h3 class="menu-toggle"><?php _e('Menu', 'techism'); ?></h3>
			<a class="assistive-text" href="#content"
			   title="<?php esc_attr_e('Skip to content', 'techism'); ?>"><?php _e('Skip to content', 'techism'); ?></a>
			<?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'nav-menu', "depth" => -1)); ?>
		</nav>
		<!-- #site-navigation -->

