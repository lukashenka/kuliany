<?php
/**
 * The template for displaying posts in the Image post format
 *
 * @package techism
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php the_content('Далей'); ?>
	</div>
	<!-- .entry-content -->

	<footer class="entry-meta">
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<h1><?php the_title(); ?></h1>

			<h2>
				<time class="entry-date"
				      datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo get_the_date(); ?></time>
			</h2>
		</a>
		<?php if (comments_open()) : ?>
			<div class="comments-link">
				<?php comments_popup_link('<span class="leave-reply">' . __('Leave a reply', 'techism') . '</span>', __('1 Reply', 'techism'), __('% Replies', 'techism')); ?>
			</div><!-- .comments-link -->
		<?php endif; // comments_open() ?>
		<?php edit_post_link(__('����������', 'techism'), '<span class="edit-link">', '</span>'); ?>
	</footer>
	<!-- .entry-meta -->
</article><!-- #post -->
