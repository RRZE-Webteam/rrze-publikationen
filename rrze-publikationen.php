<?php

/*
  Plugin Name: RRZE-Publikationen
  Plugin URI: https://github.com/RRZE-Webteam/rrze-publikationen
  Version: 1.1.0
  Description: Plugin zum Verwalten von Publikationen (Inhaltstyp und Shortcodes)
  Author: RRZE-Webteam
  Author URI: http://blogs.fau.de/webworking/
  Text Domain: rrze-publikationen
  Domain Path: /languages
  License: GPLv2 or later
 */

/*
  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/*
  Verzeichnisschema:

  cms-basis
  |-- languages
  |   +-- cms-basis.pot         Vorlagedatei falls Übersetzungen in andere Sprachen nötig werden
  |   +-- cms-basis-en_US.po    Englische Übersetzungsdatei (kann mit poedit angepasst werden)
  |   +-- cms-basis-en_US.mo    Englische Übersetzungsdatei (wird beim Speichern in poedit aktualisiert)
  +-- README.md
  +-- rrze-publikationen.php
 */

add_action('plugins_loaded', array('RRZE_Publikationen', 'instance'));

register_activation_hook(__FILE__, array('RRZE_Publikationen', 'activation'));
register_deactivation_hook(__FILE__, array('RRZE_Publikationen', 'deactivation'));

/*
 * CMS-Basis-Klasse
 */

class RRZE_Publikationen {
    /*
     * Name der Variable unter der die Einstellungen des Plugins gespeichert werden.
     * string
     */

    const option_name = 'rrze_publikationen';

    /*
     * Name der Text-Domain.
     * string
     */
    const textdomain = 'rrze-publikationen';

    /*
     * Minimal erforderliche PHP-Version.
     * string
     */
    const php_version = '5.3';

    /*
     * Minimal erforderliche WordPress-Version.
     * string
     */
    const wp_version = '4.1';

    /*
     * Optionen des Pluginis
     * object
     */

    protected static $options;

    /*
     * "Screen ID" der Einstellungsseite
     * string
     */
    protected $admin_settings_page;

    /*
     * Bezieht sich auf eine einzige Instanz dieser Klasse.
     * mixed
     */
    protected static $instance = null;

    /*
     * Erstellt und gibt eine Instanz der Klasse zurück.
     * Es stellt sicher, dass von der Klasse genau ein Objekt existiert (Singleton Pattern).
     * @return object
     */

    public static function instance() {

        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /*
     * Initialisiert das Plugin, indem die Lokalisierung, Hooks und Verwaltungsfunktionen festgesetzt werden.
     * @return void
     */

    private function __construct() {
        // Sprachdateien werden eingebunden.
        load_plugin_textdomain(self::textdomain, false, sprintf('%s/languages/', dirname(plugin_basename(__FILE__))));

        // Ab hier können weitere Hooks angelegt werden.

        add_action('init', array($this, 'publications_post_type'));
        add_filter('template_include', array($this, 'rrze_publications_include_template'), 1);

        include_once(plugin_dir_path(__FILE__) . 'includes/custom-fields.php');
        include_once(plugin_dir_path(__FILE__) . 'includes/helper-functions.php');

        $fields = new Publications_Custom_Fields();
        add_shortcode('publications', array(__CLASS__, 'rrze_publications_shortcode'));
    }

    /*
     * Wird durchgeführt wenn das Plugin aktiviert wird.
     * @return void
     */

    public static function activation() {
        // Überprüft die minimal erforderliche PHP- u. WP-Version.
        self::version_compare();

        // Ab hier können die Funktionen/Methoden hinzugefügt werden,
        // die bei der Aktivierung des Plugins aufgerufen werden müssen.
        // Bspw. wp_schedule_event, flush_rewrite_rules, etc.
    }

    /*
     * Wird durchgeführt wenn das Plugin deaktiviert wird
     * @return void
     */

    public static function deactivation() {
        // Hier können die Funktionen/Methoden hinzugefügt werden, die
        // bei der Deaktivierung des Plugins aufgerufen werden müssen.
        // Bspw. wp_clear_scheduled_hook
    }

    /*
     * Überprüft die minimal erforderliche PHP- u. WP-Version.
     * @return void
     */

    public static function version_compare() {
        $error = '';

        if (version_compare(PHP_VERSION, self::php_version, '<')) {
            $error = sprintf(__('Ihre PHP-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die PHP-Version %s.', self::textdomain), PHP_VERSION, self::php_version);
        }

        if (version_compare($GLOBALS['wp_version'], self::wp_version, '<')) {
            $error = sprintf(__('Ihre Wordpress-Version %s ist veraltet. Bitte aktualisieren Sie mindestens auf die Wordpress-Version %s.', self::textdomain), $GLOBALS['wp_version'], self::wp_version);
        }

        // Wenn die Überprüfung fehlschlägt, dann wird das Plugin automatisch deaktiviert.
        if (!empty($error)) {
            deactivate_plugins(plugin_basename(__FILE__), false, true);
            wp_die($error);
        }
    }

    function publications_post_type() {

        // Set UI labels for Custom Post Type
        $labels = array(
            'name' => _x('Publikationen', 'Post Type General Name', self::textdomain),
            'singular_name' => _x('Publikation', 'Post Type Singular Name', self::textdomain),
            'menu_name' => __('Publikationen', self::textdomain),
            'parent_item_colon' => __('Parent Movie', self::textdomain),
            'all_items' => __('Alle Publikationen', self::textdomain),
            'view_item' => __('Publikation ansehen', self::textdomain),
            'add_new_item' => __('Neue Publikation erstellen', self::textdomain),
            'add_new' => __('Erstellen', self::textdomain),
            'edit_item' => __('Publikation bearbeiten', self::textdomain),
            'update_item' => __('Speichern', self::textdomain),
            'search_items' => __('Suchen', self::textdomain),
            'not_found' => __('Nicht gefunden', self::textdomain),
            'not_found_in_trash' => __('Keine Publikationen im Papierkorb gefunden', self::textdomain),
        );

        // Set other options for Custom Post Type
        $args = array(
            'label' => __('publication', self::textdomain),
            'description' => __('Publikationen', self::textdomain),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields',),
            //'taxonomies'			=> array( 'publishers' ),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-book-alt',
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'rewrite' => array('slug' => 'publications'), // Permalinks format
            'register_meta_box_cb' => array($this, 'rrze_publications_add_metabox_page')
        );

        // Registering Custom Post Type
        register_post_type('publication', $args);

        register_taxonomy('tags', 'publication', array(
            'hierarchical' => true,
            'labels' => array(
                'name' => _x('Schlagworte', 'taxonomy general name', self::textdomain),
                'singular_name' => _x('Schlagwort', 'taxonomy singular name', self::textdomain),
                'search_items' => __('Schlagworte durchsuchen', self::textdomain),
                'all_items' => __('Alle Schlagworte', self::textdomain),
                'edit_item' => __('Schlagwort bearbeiten', self::textdomain),
                'update_item' => __('Schlagwort speichern', self::textdomain),
                'add_new_item' => __('Schlagwort erstellen', self::textdomain),
                'new_item_name' => __('Neue Bezeichnung', self::textdomain),
                'menu_name' => __('Schlagworte', self::textdomain),
            ),
            // Control the slugs used for this taxonomy
            'rewrite' => array(
                'slug' => 'publikationen-schlagworte', // This controls the base slug that will display before each term
                'with_front' => false, // Don't display the category base before "/locations/"
                'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
            ),
                )
        );
    }

    function rrze_publications_add_metabox_page() {
        add_meta_box(
                'rrze_publications_metabox', __('Daten zur Publikation', self::textdomain), array('Publications_Custom_Fields', 'rrze_publications_do_metabox'), 'publication', 'normal', 'core'
        );
        add_meta_box(
                'rrze_publications_price_metabox', __('Verwaltung', self::textdomain), array('Publications_Custom_Fields', 'rrze_publications_price_do_metabox'), 'publication', 'normal', 'core'
        );
    }

    function rrze_publications_include_template($template_path) {
        if (get_post_type() == 'publication') {
            if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('single-publication.php'))) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path(__FILE__) . 'single-publication.php';
                }
            }
        }
        return $template_path;
    }

    public static function rrze_publications_shortcode($atts) {
        global $post;
        /*
         * [publications show="list" link="yes" orderby="(author/year)"]
         * [publications show="table" cols="author|title|publisher|price|availible" link="no"]
         */
        extract(shortcode_atts(array(
            'show' => 'list',
            'link' => 'yes',
            'orderby' => 'year',
            'cols' => 'author|title|publisher|price|availible',
            'show_sold_out' => 'yes',
            'id' => '',
            'tags' => '',
            'class' => ''
                        ), $atts));
        $output = '';

        // Args for WP_Query
        $args = array(
            'post_type' => 'publication'
        );
        switch ($orderby) {
            case 'year':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'rrze_publications_jahr';
                $args['order'] = 'DESC';
                break;
            case 'author':
                $args['orderby'] = 'meta_value';
                $args['meta_key'] = 'rrze_publications_autoren';
                $args['order'] = 'ASC';
                break;
            case 'title':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
        }
        if ($show == 'single') {
            $args['p'] = $id;
        }
        if ($tags != '') {
            $args_tags = explode('|', $tags);
            $args['tags'] = implode(',', $args_tags);
        }

        // Table Columns
        $table_cols = explode('|', $cols);
        $table_content = array(
            'author' => array(
                'head' => __('Autor(en)', self::textdomain),
                'data' => 'rrze_publications_autoren'),
            'title' => array(
                'head' => __('Titel', self::textdomain),
                'data' => $post->post_title),
            'location' => array(
                'head' => __('Ort', self::textdomain),
                'data' => 'rrze_publications_ort'),
            'publisher' => array(
                'head' => __('Verlag', self::textdomain),
                'data' => 'rrze_publications_verlag'),
            'isbn' => array(
                'head' => __('ISBN', self::textdomain),
                'data' => 'rrze_publications_isbn'),
            'price' => array(
                'head' => __('Preis', self::textdomain),
                'data' => 'rrze_publications_preis', true),
            'availible' => array(
                'head' => __('Vorrätig', self::textdomain),
                'data' => 'rrze_publications_vorraetig', true),
            'updated' => array(
                'head' => __('Aktualisiert', self::textdomain),
                'data' => '')
        );

        $loop = new WP_Query($args);

        /* 		print '<pre>';
          //print_r($atts);
          print_r($args);
          print '</pre>';
         */

        if ($show == 'list' || $show == 'single') {
            $output .= '<ul style="list-style-type: none; margin-left: 0;">';
        }

        if ($show == 'table') {
            $output .= '<div style="overflow-x:auto;">'
                    . '<table style="min-width:600px; word-break:normal;" class="' . $class . '">';
            $output .= '<thead>';
            $output .= '<tr>';
            foreach ($table_cols as $table_col) {
                $output .= '<th>' . $table_content[$table_col]['head'] . '</th>';
            }
            $output .= '</tr>';
            $output .= '</thead>';
        }

        while ($loop->have_posts()) : $loop->the_post();

            // nicht vorrätige ausblenden, wenn gewünscht
            if ($show_sold_out == 'no' && get_post_meta($post->ID, 'rrze_publications_vorraetig', true) == 0):
                continue;
            endif;

            $autor = get_post_meta($post->ID, 'rrze_publications_autoren', true);
            $titel = get_the_title();
            $zusatz = get_post_meta($post->ID, 'rrze_publications_zusatz', true);
            $ort = get_post_meta($post->ID, 'rrze_publications_ort', true);
            $verlag = get_post_meta($post->ID, 'rrze_publications_verlag', true);
            $jahr = get_post_meta($post->ID, 'rrze_publications_jahr', true);
            $isbn = get_post_meta($post->ID, 'rrze_publications_isbn', true);
            $preis = get_post_meta($post->ID, 'rrze_publications_preis', true);
            $vorraetig = get_post_meta($post->ID, 'rrze_publications_vorraetig', true);

            if ($show == 'list' || $show == 'single') {
                // Liste oder Einzelansicht
                if ($show == 'single' && $id == '') {
                    $output .= '<p>Publikations-ID fehlt!</p>';
                    return;
                }
                $output .= '<li style="margin-bottom: 10px;" itemprop="mainEntity" itemscope="" itemtype="http://schema.org/Book">';
                $output .= ($link == 'yes' ? '<a href="' . esc_url(get_permalink($post->ID)) . '" itemprop="url">' : '');
                // Autor: Titel (ggf verlinkt)
                $output .= ($autor ? '<span itemprop="author" itemscope="" itemtype="http://schema.org/Person"><meta itemprop="name" content="' . $autor . '" />' . $autor . '</span>: ' : '');
                $output .= '<b><span itemprop="name">' . $titel . '</span></b>';
                $output .= ($link == 'yes' ? '</a>' : '');
                $output .= '<br />';

                // Zusatzinfos
                $output .= ($zusatz ? $zusatz . '<br />' : '');

                // Ort: Verlag, Jahr
                if ($ort || $verlag || $jahr) {
                    $output .= ($ort ? $ort . ': ' : '');
                    $output .= ($verlag ? '<span itemprop="publisher" itemtype="http://schema.org/Organization" itemscope=""><meta itemprop="name" content="' . $verlag . '" />' . $verlag . '</span>, ' : '');
                    $output .= ($jahr ? '<span itemprop="datePublished" content="' . $jahr . '">' . $jahr . '</span>' : '');
                    $output .= '<br />';
                }
                // ISBN
                if ($isbn) {
                    $output .= __('ISBN', self::textdomain) . ': <span itemprop="isbn">' . $isbn . '</span><br />';
                }


                // Preis
                if ($preis || $vorraetig) {
                    $output .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">';
                    $output .= ($preis ? __('Preis: ', RRZE_Publikationen::textdomain)
                                    . '<span itemprop="price" content="' . $preis . '">'
                                    . number_format($preis,2,',','.') . ' &euro;'
                                    . '</span><meta itemprop="priceCurrency" content="EUR" /><br />' : '');
                    // Verfügbarkeit
                    $output .= __('Vorrätig:', RRZE_Publikationen::textdomain) . ' '
                            . ($vorraetig == 1 ? '<meta itemprop="availability" content="http://schema.org/InStock" />' . __('ja', RRZE_Publikationen::textdomain) : '<meta itemprop="availability" content="http://schema.org/OutOfStock"/>' . __('nein', RRZE_Publikationen::textdomain));
                    $output .= '</div>';
                }
                $output .= '</li>';
            } else {
                //Tabelle
                $output .= '<tr>';
                foreach ($table_cols as $table_col) {
                    $output .= '<td>';
                    switch ($table_col) {
                        case 'title':
                            $output .= $titel;
                            break;
                        case 'price':
                            $output .= ($preis ? (number_format($preis,2,',','.') . ' &euro;') : '');
                            break;
                        case 'availible':
                            $output .= ($vorraetig == 1 ? __('ja', RRZE_Publikationen::textdomain) : __('nein', RRZE_Publikationen::textdomain));
                            break;
                        case 'updated':
                            $output .= get_the_modified_date('d.m.Y \&\n\d\a\s\h\; H:i');
                            break;
                        default:
                            $output .= get_post_meta($post->ID, $table_content[$table_col]['data'], true);
                    }
                    $output .= '</td>';
                }
                $output .= '</tr>';
            }

        endwhile;

        if ($show == 'list') {
            $output .= '</ul>';
        }

        if ($show == 'table') {
            $output .= '</table></div>';
        }

        wp_reset_postdata();

        return $output;
    }

}
