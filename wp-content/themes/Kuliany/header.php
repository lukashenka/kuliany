<?php

require_once "nav-menu-template.php";
global $current_user;
get_currentuserinfo();
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

	<div id="head">
		<div id="main" class="wrapper">

			<div id="left-sidebar-top" class="widget-area" role="complementary">
				<div class="widget no-margin" style="background: #ffffff; height: 208px">
					<h2 class="widget-title" style="margin-bottom: 5px;">Пошта</h2>

					<?php if (!is_user_logged_in()): ?>
						<div id="registration-form">
							<form name="loginform" id="loginform" action="/wp-login.php"
							      method="post">
								<div class="form-group">

									<input type="text" id="username" name="log" placeholder="Лагін"/>
									<span>@kuliany.by</span>
								</div>
								<div class="clear"></div>
								<div class="form-group" style="margin-top:10px">

									<input type="password" name="pwd" id="password" placeholder="Пароль"/>
									<input style="width: 40%; margin-left: 5%" type="submit" name="wp-submit"
									       id="wp-submit" value="Цісні"/>
								</div>
								<div class="clear"></div>
								<div class="form-group">
									<a class="form-action" href="<?php echo get_page_link(301) ?>">Зарэгістравацца</a>
								</div>

								<input type="hidden" name="rememberme" id="rememberme" value="forever">
								<input type="hidden" name="redirect_to" value="<?php echo site_url($_SERVER['REQUEST_URI']) ?>">
							</form>
						</div>
					<?php else: ?>

						<div id="profile">
							<div id="welcome-message">
								<div>Прывітанне <span
										id="username"><?= $current_user->user_firstname ?> <?= $current_user->user_lastname ?></span>
								</div>
							</div>

							<div class="left half column">

								<div id="ava">
									<?php echo get_avatar(get_the_author_meta('ID'), array(100, 100), $default, get_the_author_meta('username')); ?>
								</div>
							</div>
							<div class="right half column">
								<div>
									<a class="form-action" target="_blank" href="/wp-admin/">Блог</a>
								</div>

								<div>
									<a class="form-action" target="_blank" href="https://mail.yandex.ru/for/kuliany.by">Пошта</a>
								</div>

								<div>
									<a class="form-action" href="<?php echo wp_logout_url($redirect); ?>&redirect_to=/">Выйсці</a>
								</div>
							</div>
						</div>
					<?php endif ?>
				</div>
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
		</div>
	</div>
	<div class="clear"></div>
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<h3 class="menu-toggle"><?php _e('Menu', 'techism'); ?></h3>
		<a class="assistive-text" href="#content"
		   title="<?php esc_attr_e('Skip to content', 'techism'); ?>"><?php _e('Skip to content', 'techism'); ?></a>
		<?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'nav-menu', 'walker' => new Kuliany_Walker_Nav_Menu)); ?>
	</nav>
	<!-- #site-navigation -->

