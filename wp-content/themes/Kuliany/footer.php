<?php
/**
 * The template for displaying the footer
 *
 * @package techism
 */
?>
	</div><!-- #main .wrapper -->
	
		<div id="footer-sidebar" class="container widget-area">
	<!--footer sidebar-->
	<div id="footer-1" class="footer-sidebar" role="complementary">
		<ul class="foo">	
				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<?php dynamic_sidebar( 'footer-1' ); ?>
				<?php endif; ?>
	</div><!-- #footer-sidebar 1 -->

	<div id="footer-2" class="footer-sidebar" role="complementary">
		<ul class="foo">	
				<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
				<?php dynamic_sidebar( 'footer-2' ); ?>
				<?php endif; ?>
	</div><!-- #footer-sidebar 2 -->

	<div id="footer-3" class="footer-sidebar" role="complementary">
		<ul class="foo">	
				<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
				<?php dynamic_sidebar( 'footer-3' ); ?>
				<?php endif; ?>
	</div><!-- #footer-sidebar 3 -->

	<div id="footer-4" class="footer-sidebar" role="complementary">
		<ul class="foo">	
				<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
				<?php dynamic_sidebar( 'footer-4' ); ?>
				<?php endif; ?>
	</div><!-- #footer-sidebar 4 -->
		
		</div>
		
	
	<footer id="colophon" role="contentinfo">
		<div class="site-info">

		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>