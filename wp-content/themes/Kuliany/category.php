<?php
/**
 * The template for displaying Category pages
 *
 * @package techism
 */

get_header(); ?>

	<section id="primary" class="site-content">
		<div id="content" role="main">

			<?php if (have_posts()) : ?>
				<?php


				$current_cat_id = get_query_var('cat');
				$args = array('category__and' => array($current_cat_id), 'orderby' => 'post_date', 'order' => 'DESC', 'posts_per_page' => 1, 'post_status' => 'publish');
				query_posts($args);
				/* Start the Loop */
				while (have_posts()) : the_post();

					if (in_category($category_id)) continue;
					/* Include the post format-specific template for the content. If you want to
					 * this in a child theme then include a file called called content-___.php
					 * (where ___ is the post format) and that will be used instead.
					 */
					get_template_part('content-page', get_post_format());

				endwhile;

				techism_content_nav('nav-below');
				?>

			<?php else : ?>
				<?php get_template_part('content', 'none'); ?>
			<?php endif; ?>

			<?php $childCategories = $categories = get_categories(array("parent" => $current_cat_id,"orderby"=>"id"));

			?>
			<?php if (count($childCategories)): ?>
				<div id="categories-list">
					<?php foreach ($childCategories as $category): ?>
						<header class="entry-header">
							<h1 class="entry-title"><a href="<?= get_category_link($category->term_id) ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'techism' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?= $category->name ?></a></h1>
						</header><!-- .entry-header -->

					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
		<!-- #content -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>