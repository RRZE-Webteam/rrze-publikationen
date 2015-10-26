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

$autor = get_post_meta($post->ID, 'rrze_publications_autoren', true);
$titel = get_the_title();
$zusatz = get_post_meta($post->ID, 'rrze_publications_zusatz', true);
$ort = get_post_meta($post->ID, 'rrze_publications_ort', true);
$verlag = get_post_meta($post->ID, 'rrze_publications_verlag', true);
$jahr = get_post_meta($post->ID, 'rrze_publications_jahr', true);
$isbn = get_post_meta($post->ID, 'rrze_publications_isbn', true);
$preis = get_post_meta($post->ID, 'rrze_publications_preis', true);
$vorraetig = get_post_meta($post->ID, 'rrze_publications_vorraetig', true);
?>

<article <?php post_class(); ?> itemprop="mainEntity" itemscope="" itemtype="http://schema.org/Book">
	<?php
	// Post thumbnail.
	if (has_post_thumbnail($post->ID)) {
		echo '<div class="post-thumbnail">';
		echo get_the_post_thumbnail($post->ID, 'medium', array( 'itemprop' => 'image' ));
		echo '</div>';
	}
	?>

	<header class="entry-header">
		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<p>
			<?php
			print ($autor ? '<span itemprop="author" itemscope itemtype="http://schema.org/Person"><meta itemprop="name" content="' . $autor . '" />' . $autor . '</span>: ': '');
			print '<b><span itemprop="name">' . $titel . '</span></b><br />';
			print ($zusatz ? $zusatz . '<br />' : '');
			if ($ort || $verlag || $jahr) {
				print  ($ort ? $ort . ': ' : '');
				print ($verlag ? '<span itemprop="publisher" itemtype="http://schema.org/Organization" itemscope=""><meta itemprop="name" content="' . $verlag . '" />' . $verlag  . '</span>, ': '');
				print ($jahr ? '<span itemprop="datePublished" content="' . $jahr . '">' . $jahr . '</span>' : '');
				print '<br />';
			}
			print ($isbn ? __('ISBN', RRZE_Publikationen::textdomain) . ': <span itemprop="isbn">' . $isbn . '</span><br />' : '');
			?>
		</p>

		<?php if ($preis || $vorraetig) { ?>
			<p itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<?php setlocale(LC_MONETARY, get_locale());
				$locale_info = localeconv();
				print ($preis ? __('Preis: ', RRZE_Publikationen::textdomain)
					. '<span itemprop="price" content="' . $preis . '">'
					. money_format('%.2n', $preis)
					. '</span><meta itemprop="priceCurrency" content="'. $locale_info['int_curr_symbol'] . '" /><br />'
					: '');
				?>
				<?php print __('Vorrätig:', RRZE_Publikationen::textdomain) . ' '
					. ($vorraetig == 1
					? '<meta itemprop="availability" content="http://schema.org/InStock" />' . __('ja', RRZE_Publikationen::textdomain)
					: '<meta itemprop="availability" content="http://schema.org/OutOfStock"/>' . __('nein', RRZE_Publikationen::textdomain));
				?>

			</p>
		<?php } ?>

		<h3 style="clear: none;"><?php _e('Inhalt:', RRZE_Publikationen::textdomain); ?></h3>
		<div><?php the_content(); ?></div>

	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<span class="tags-links">
			<?php the_terms(
				$post->ID,
				'tags',
				'<span class="screen-reader-text">' . __('Schlagwörter', RRZE_Publikationen::textdomain) . ': </span>',
				', '
			); ?>
		</span>
		<?php edit_post_link(__('Edit', RRZE_Publikationen::textdomain), '<span class="edit-link">', '</span>'); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->

