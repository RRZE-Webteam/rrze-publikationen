<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since RRZE 2015 1.0
 */
global $options;
global $post;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	// Post thumbnail.
	if (has_post_thumbnail($post->ID)) {
		echo get_the_post_thumbnail($post->ID, 'thumbnail');
	}
	?>

	<header class="entry-header">
		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<p>
			<?php
			if (get_post_meta($post->ID, 'rrze_publications_autoren', true)) {
				print get_post_meta($post->ID, 'rrze_publications_autoren', true) . ': <b>' . get_the_title() . '</b><br />';
			}
			if (get_post_meta($post->ID, 'rrze_publications_zusatz', true)) {
				print get_post_meta($post->ID, 'rrze_publications_zusatz', true) . '<br />';
			}
			if (get_post_meta($post->ID, 'rrze_publications_ort', true) || get_post_meta($post->ID, 'rrze_publications_verlag', true) || get_post_meta($post->ID, 'rrze_publications_jahr', true)) {
				print (get_post_meta($post->ID, 'rrze_publications_ort', true) ? get_post_meta($post->ID, 'rrze_publications_ort', true) . ': ' : '');
				print (get_post_meta($post->ID, 'rrze_publications_verlag', true) ? get_post_meta($post->ID, 'rrze_publications_verlag', true) : '') . ' ';
				print (get_post_meta($post->ID, 'rrze_publications_jahr', true) ? get_post_meta($post->ID, 'rrze_publications_jahr', true) : '') . '<br />';
			}
			?>
		</p>

		<?php if (get_post_meta($post->ID, 'rrze_publications_preis', true) || get_post_meta($post->ID, 'rrze_publications_vorraetig', true)) {
			?>
			<p>
				<?php setlocale(LC_MONETARY, get_locale());
				print (get_post_meta($post->ID, 'rrze_publications_preis', true) ? __('Preis: ', RRZE_Publikationen::textdomain) . money_format('%.2n', get_post_meta($post->ID, 'rrze_publications_preis', true)) : '');
				?>
				<br />
				<?php print __('Vorrätig:', RRZE_Publikationen::textdomain) . ' ' . (get_post_meta($post->ID, 'rrze_publications_vorraetig', true) == 1 ? __('ja', RRZE_Publikationen::textdomain) : __('nein', RRZE_Publikationen::textdomain)); ?>

			</p>
		<?php } ?>

		<h3><?php _e('Inhalt:', RRZE_Publikationen::textdomain); ?></h3>
		<p><?php the_content(); ?></p>

	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link(__('Edit', RRZE_Publikationen::textdomain), '<span class="edit-link">', '</span>'); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->

