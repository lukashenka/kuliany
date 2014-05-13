<?php
/**
 * The main template file shows the homepage of the theme
 *
 * @package techism
 */

get_header(); ?>


<?php	
		$cat_id = get_theme_mod('techism_slider_category' );
		$post_num = get_theme_mod('techism_slider_postnum');
		
		// for debugging
		//echo '<pre>'. print_r($techism_options, true) .'</pre>';
	?>
		<?php if( is_front_page() && ! is_paged() && get_theme_mod( 'techism_slider_activate' ) == '1') { ?>
		<?php $args = array (
				'cat' => $cat_id,
				'showposts' => $post_num	
			)
			?>
		<?php $techism_query = new WP_Query($args); ?>
		<?php if ( $techism_query -> have_posts() ) : ?>
			<div class="flexslider">
			<ul class="slides">
			<?php while( $techism_query->have_posts() ) : $techism_query->the_post(); ?>
			
			<?php get_template_part( 'content', 'slider' ); ?>
			
			<?php endwhile; ?>
			
			</ul>
			</div> <!-- .slides -->
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
			<?php } ?>
		

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
		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php techism_content_nav( 'nav-below' ); ?>

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">

			<?php if ( current_user_can( 'edit_posts' ) ) :
				// Show a different message to a logged-in user who can add posts.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'No posts to display', 'techism' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'techism' ), admin_url( 'post-new.php' ) ); ?></p>
				</div><!-- .entry-content -->

			<?php else :
				// Show the default message to everyone else.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'techism' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'techism' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			<?php endif; // end current_user_can() check ?>

			</article><!-- #post-0 -->

		<?php endif; // end have_posts() check ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>