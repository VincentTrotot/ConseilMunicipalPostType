<?php

namespace VincentTrotot\ConseilMunicipal;

use Timber\Timber;
use Symfony\Component\HttpFoundation\Request;

class ConseilMunicipalPostType
{
    public function __construct()
    {
        add_action('init', [$this, 'createPostType']);
        add_filter('manage_edit-vt_cm_columns', [$this, 'editColumns']);

        add_action('admin_init', [$this, 'setupMetabox']);

        add_action('save_post', [$this, 'save']);

        add_filter('post_updated_messages', [$this, 'updatedMessages']);
    }

    /**
     * Création du custom post type  \
     * hook: admin_init
     */
    public function createPostType()
    {
        $labels = [
            'name' => _x('Conseils municipaux', 'conseils_municipaux'),
            'all_items' => __('Tous les conseils'),
            'singular_name' => _x('Conseils municipaux', 'conseils_municipaux'),
            'add_new' => _x('Ajouter un conseil municipal', 'conseils_municipaux'),
            'add_new_item' => __('Ajouter un conseil municipal'),
            'edit_item' => __('Modifier le conseil municipal'),
            'new_item' => __('Nouveau conseil municipal'),
            'view_item' => __('Voir le conseil municipal'),
            'search_items' => __('Rechercher dans les conseils municipaux'),
            'not_found' =>  __('Pas de conseil trouvé'),
            'not_found_in_trash' => __('Pas de conseil trouvé dans la corbeille'),
            'parent_item_colon' => '',
        ];
    
        $args = [
            'label' => __('Conseils municipaux'),
            'labels' => $labels,
            'public' => true,
            'can_export' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            '_builtin' => false,
            '_edit_link' => 'post.php?post=%d', // ?
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-playlist-audio',
            'hierarchical' => false,
            'rewrite' =>[ 'slug' => 'conseil-municipal' ],
            'has_archive' => 'conseils-municipaux',
            'supports'=>['title', 'editor', 'thumbnail', 'excerpt', 'author'] ,
            'show_in_nav_menus' => true,
            'taxonomies' =>[ 'vt_cm_category', 'post_tag']
        ];
    
        register_post_type('vt_cm', $args);
    }

    /**
     * Paramétrage des colonnes  \
     * hook: manage_edit-vt_cm_columns
     */
    public function editColumns($columns)
    {
        $columns = [
            "cb" => "<input type=\"checkbox\" />",
            "title" => "Conseil municipal",
            ];

        return $columns;
    }

    /**
     * Paramétrage de la meta box  \
     * hook: admin_init
     */
    public function setupMetabox()
    {
        add_meta_box(
            'vt_cm_meta',
            'Documents',
            [$this, 'displayMetabox'],
            'vt_cm',
            'side'
        );
    }

    /**
     * Affiche la meta box
     */
    public function dusplayMetabox()
    {

        $context['post'] = new ConseilMunicipal();
        $context['nonce'] = wp_create_nonce('vt-conseils_municipaux-nonce');
        
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        } else {
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
        }

        Timber::render('templates/cm-meta-box.html.twig', $context);
    }

    /**
     * Sauvegarde des données  \
     * hook: save_post
     */
    public function save()
    {
        global $post;

        // - still require nonce

        if (!isset($_POST['vt-conseils_municipaux-nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['vt-conseils_municipaux-nonce'], 'vt-conseils_municipaux-nonce')) {
            return $post->ID;
        }

        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }


        // ordre du jour
        update_post_meta($post->ID, "url-ordre-du-jour", $_POST['url-ordre-du-jour']);
        update_post_meta($post->ID, "name-ordre-du-jour", $_POST['name-ordre-du-jour']);

        // compte rendu PDF
        update_post_meta($post->ID, "url-pdf", $_POST['url-pdf']);
        update_post_meta($post->ID, "name-pdf", $_POST['name-pdf']);

        // compte rendu audio
        update_post_meta($post->ID, "url-audio", $_POST['url-audio']);
        update_post_meta($post->ID, "name-audio", $_POST['name-audio']);
    }

    /**
     * Paramétrage des messages de mise à jour  \
     * hook: post_updated_messages
     */
    public function updatedMessages($messages) : string
    {
        global $post, $post_ID;
        $request = new Request($_GET);
        $revision = $request->query->get('revision');

        $messages['vt_cm'] = [
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf(
                __('Conseil municipal mis à jour. <a href="%s">Voir le conseil municipal</a>'),
                esc_url(get_permalink($post_ID))
            ),
            2 => __('Champ mis à jour.'),
            3 => __('Champ supprimé.'),
            4 => __('Conseil municipal mis à jour.'),
            /* translators: %s: date and time of the revision */
            5 => $revision ? sprintf(
                __('Event restored to revision from %s'),
                wp_post_revision_title((int) $revision, false)
            ) : false,
            6 => sprintf(
                __('Conseil municipal publié. <a href="%s">Voir le conseil municipal</a>'),
                esc_url(get_permalink($post_ID))
            ),
            7 => __('Conseil municipal sauvegardé.'),
            8 => sprintf(
                __('Conseil municipal soumis. <a target="_blank" href="%s">Prévisualiser le conseil municipal</a>'),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
            9 => sprintf(
                __(
                    'Conseil municipal programmé pour : '
                    .'<strong>%1$s</strong>. '
                    .'<a target="_blank" href="%2$s">Prévisualiser le conseil municipal</a>'
                ),
                date_i18n(__('M j, Y @ G:i'), strtotime($post->post_date)),
                esc_url(get_permalink($post_ID))
            ),
            10 => sprintf(
                __(
                    'Brouillon du conseil municipal mis à jour.'
                    .'<a target="_blank" href="%s">Prévisualiser le conseil municipal</a>'
                ),
                esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))
            ),
        ];

        return $messages;
    }
}
