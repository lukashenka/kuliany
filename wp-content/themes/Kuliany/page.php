<?php
/**
 * The template for displaying all pages
 *
 * @package techism
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">
			<header id="masthead" class="site-header" role="banner">
				<hgroup>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
				</hgroup>
				<?php if ( get_header_image() ) : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php header_image(); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo 	get_custom_header()->height; ?>" alt="" /></a>
				<?php else: ?>
				<?php endif;?>

				<nav id="site-navigation" class="main-navigation" role="navigation">
					<h3 class="menu-toggle"><?php _e( 'Menu', 'techism' ); ?></h3>
					<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to content', 'techism' ); ?>"><?php _e( 'Skip to content', 'techism' ); ?></a>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu',"depth"=>-1 ) ); ?>
				</nav><!-- #site-navigation -->

			</header><!-- #masthead -->
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>