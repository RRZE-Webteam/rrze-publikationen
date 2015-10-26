<?php

require_once("helper-functions.php");

class Publications_Custom_Fields {

	const textdomain = RRZE_Publikationen::textdomain;

	public function __construct() {
		add_action('load-post.php', array(&$this, 'rrze_publications_setup'));
		add_action('load-post-new.php', array(&$this, 'rrze_publications_setup'));
	}

	public function rrze_publications_setup() {
		add_action('add_meta_boxes_page', array(&$this, 'rrze_publications_add_metabox_page'));
		add_action('save_post', array(&$this, 'rrze_publications_save_metabox'), 10, 2);
	}

	public function rrze_publications_add_metabox_page() {
		add_meta_box(
			'rrze_publications_metabox',
			__('Daten zur Publikation', self::textdomain),
			array( &$this, 'rrze_publications_do_metabox'),
			'publication',
			'normal',
			'core'
		);
		/*add_meta_box(
			'rrze_publications_price_metabox',
			__('VerwaltungX', self::textdomain),
			array( &$this, 'rrze_publications_price_do_metabox'),
			'publication',
			'normal',
			'core'
		);*/
	}

	/*
	 * Metabox für Publikationsdaten
	 */

	public static function rrze_publications_do_metabox($object, $box) {
		//$options = RRZE_Page_Sidebar::get_options();
		wp_nonce_field(basename(__FILE__), 'rrze_publications_metabox_nonce');

		if (!current_user_can('edit_page', $object->ID))
			return;

		// ID (nur Information)
		echo '<p>Publikations-ID: ' . get_the_ID();
		echo '<br/><span class="howto">('. __('Für die Anzeige einer einzelnen Publikation per Shortcode', self::textdomain) . ')</span></p>';

		// Autoren
		$rrze_publications_autoren = get_post_meta($object->ID, 'rrze_publications_autoren', true);
		Publications_Helper::rrze_publications_form_text (
			'rrze_publications_autoren',
			$rrze_publications_autoren,
			__('Autor(en)', self::textdomain)
		);
		// Ort
		$rrze_publications_ort = get_post_meta($object->ID, 'rrze_publications_ort', true);
		Publications_Helper::rrze_publications_form_text (
			'rrze_publications_ort',
			$rrze_publications_ort,
			__('Ort', self::textdomain)
		);
		// Ort
		$rrze_publications_verlag = get_post_meta($object->ID, 'rrze_publications_verlag', true);
		Publications_Helper::rrze_publications_form_text (
			'rrze_publications_verlag',
			$rrze_publications_verlag,
			__('Verlag', self::textdomain)
		);
		// Jahr
		$rrze_publications_jahr = get_post_meta($object->ID, 'rrze_publications_jahr', true);
		Publications_Helper::rrze_publications_form_number (
			'rrze_publications_jahr',
			$rrze_publications_jahr,
			__('Erscheinungsjahr', self::textdomain),
			'',
			0,
			date("Y") + 5,
			1
		);
		// ISBN
		$rrze_publications_isbn = get_post_meta($object->ID, 'rrze_publications_isbn', true);
		Publications_Helper::rrze_publications_form_text (
			'rrze_publications_isbn',
			$rrze_publications_isbn,
			__('ISBN', self::textdomain)
		);
		// Zusatzinformationen (z.B. "in:")
		$rrze_publications_zusatz = get_post_meta($object->ID, 'rrze_publications_zusatz', true);
		Publications_Helper::rrze_publications_form_textarea (
			'rrze_publications_zusatz',
			$rrze_publications_zusatz,
			__('Weitere Informationen', self::textdomain),
			__('z.B. "in: XYZ"', self::textdomain)
		);
	}

	/*
	 * Metabox für Verkaufsdaten
	 */

	public static function rrze_publications_price_do_metabox($object, $box) {
		//$options = RRZE_Page_Sidebar::get_options();
		wp_nonce_field(basename(__FILE__), 'rrze_publications_price_metabox_nonce');

		if (!current_user_can('edit_page', $object->ID))
			return;

		// Preis
		$rrze_publications_preis = get_post_meta($object->ID, 'rrze_publications_preis', true);
		Publications_Helper::rrze_publications_form_currency (
			'rrze_publications_preis',
			$rrze_publications_preis,
			__('Preis', self::textdomain),
			'',
			0,
			0,
			0.01
		);
		// Vorrätig Ja/Nein
		$rrze_publications_vorraetig = get_post_meta($object->ID, 'rrze_publications_vorraetig', true);
		Publications_Helper::rrze_publications_form_check (
			'rrze_publications_vorraetig',
			$rrze_publications_vorraetig,
			__('Vorrätig', self::textdomain)
		);
	}

	/* Save the meta box's post/page metadata. */

	public function rrze_publications_save_metabox($post_id, $post) {

		if (!isset($_POST['rrze_publications_metabox_nonce']) || !wp_verify_nonce($_POST['rrze_publications_metabox_nonce'], basename(__FILE__)))
			return $post_id;

		/* Check if the current user has permission to edit the post. */
		if (!current_user_can('edit_page', $post_id))
			return;

		$newval = ( isset($_POST['rrze_publications_autoren']) ? sanitize_text_field($_POST['rrze_publications_autoren']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_autoren', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_autoren', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_autoren', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_autoren', $oldval);
		}

		$newval = ( isset($_POST['rrze_publications_ort']) ? sanitize_text_field($_POST['rrze_publications_ort']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_ort', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_ort', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_ort', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_ort', $oldval);
		}

		$newval = ( isset($_POST['rrze_publications_verlag']) ? sanitize_text_field($_POST['rrze_publications_verlag']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_verlag', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_verlag', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_verlag', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_verlag', $oldval);
		}

		$newval = ( isset($_POST['rrze_publications_jahr']) ? sanitize_text_field($_POST['rrze_publications_jahr']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_jahr', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_jahr', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_jahr', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_jahr', $oldval);
		}


		$newval = ( isset($_POST['rrze_publications_isbn']) ? sanitize_text_field($_POST['rrze_publications_isbn']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_isbn', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_isbn', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_isbn', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_isbn', $oldval);
		}


		$newval = ( isset($_POST['rrze_publications_zusatz']) ? sanitize_text_field($_POST['rrze_publications_zusatz']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_zusatz', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_zusatz', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_zusatz', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_zusatz', $oldval);
		}

		$newval = ( isset($_POST['rrze_publications_preis']) ? sanitize_text_field($_POST['rrze_publications_preis']) : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_preis', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_preis', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_preis', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_preis', $oldval);
		}

		$newval = ( isset($_POST['rrze_publications_vorraetig']) ? $_POST['rrze_publications_vorraetig'] : 0 );
		$oldval = get_post_meta($post_id, 'rrze_publications_vorraetig', true);

		if (!empty(trim($newval))) {
			if (isset($oldval) && ($oldval != $newval)) {
				update_post_meta($post_id, 'rrze_publications_vorraetig', $newval);
			} else {
				add_post_meta($post_id, 'rrze_publications_vorraetig', $newval, true);
			}
		} elseif ($oldval) {
			delete_post_meta($post_id, 'rrze_publications_vorraetig', $oldval);
		}

	}

}
