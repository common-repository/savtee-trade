<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// first check if yoast seo plugin active
if (!is_plugin_active('wordpress-seo/wp-seo.php')) {
    
    // meta title tag
    add_action('add_meta_boxes', function() {
        global $upconfig;
        add_meta_box('meta-title-tag', $upconfig['LANGUAGE']['PLUGIN_NAME'] . ' - Meta Title Tag', 'viewTitleTagInputField', ['page', 'post'], 'normal', 'high');
    });
    
    function viewTitleTagInputField()
    {
        global $UPAdminPlugin;
        
        $req = get_post_custom($post->ID);
        $value = ((isset($req['_yoast_wpseo_title'])) ? $req['_yoast_wpseo_title'][0] : '');
        $view = $UPAdminPlugin->getOptionPageLink();
        $view .= '
            <p>
                <label for="_yoast_wpseo_title">Meta Title Tag (max. 55 Charakter optimal)</label><br />
                <input style="width: 100%;" data-count="1" type="text" name="_yoast_wpseo_title" id="_yoast_wpseo_title" class="checkmaxchars" value="' . $value . '" />
                ' . $UPAdminPlugin->displayMaxCharsBar($UPAdminPlugin->settings['_yoast_wpseo_title_maxchars'], $value, 1) . '
            </p>
        ';
        
        echo $view;
    }



    // meta description tag
    add_action('add_meta_boxes', function() {
        global $upconfig;
        add_meta_box('meta-description-tag', $upconfig['LANGUAGE']['PLUGIN_NAME'] . ' - Meta Description Tag', 'viewDescriptionTagInputField', ['page', 'post'], 'normal', 'high');
    });

    function viewDescriptionTagInputField()
    {
        global $UPAdminPlugin;
        
        $req = get_post_custom($post->ID);
        $value = ((isset($req['_yoast_wpseo_metadesc'])) ? $req['_yoast_wpseo_metadesc'][0] : '');
        $view = $UPAdminPlugin->getOptionPageLink();
        $view .= '
            <p>
                <label for="_yoast_wpseo_metadesc">Meta Description Tag (max. 156 Charakter optimal)</label><br />
                <textarea data-count="2" name="_yoast_wpseo_metadesc" id="_yoast_wpseo_metadesc" class="meta-textarea checkmaxchars">' . $value . '</textarea>
                ' . $UPAdminPlugin->displayMaxCharsBar($UPAdminPlugin->settings['_yoast_wpseo_metadesc_maxchars'], $value, 2) . '
            </p>
        ';
        
        echo $view;
    }


    // save meta tags
    add_action('save_post', function($pID) {
        if (!current_user_can('edit_post', $pID)) {
            return 0;
        }
        
        if (isset($_POST['_yoast_wpseo_title'])) {
            update_post_meta($pID, '_yoast_wpseo_title', $_POST['_yoast_wpseo_title']);
        }
        
        if (isset($_POST['_yoast_wpseo_metadesc'])) {
            update_post_meta($pID, '_yoast_wpseo_metadesc', $_POST['_yoast_wpseo_metadesc']);
        }
    });
    
    
    // display meta data in the header
    add_action('wp_head', function() {
        global $post;
        $data = ((isset($post)) ? get_post_custom($post->ID) : false);
        
        if ($data !== false) {
            $view = '
                ' . /*<meta name="keywords" content="' . $data['_yoast_wpseo_title'][0] . '" />' . "\n" . '*/ '
                <meta name="description" content="' . $data['_yoast_wpseo_metadesc'][0] . '" />' . "\n
            ";
            
            echo $view;
        }
    }, 2);
    
    // seo title tag
    add_filter('document_title_parts', function($title_parts) {
        global $post;
        $data = ((isset($post)) ? get_post_custom($post->ID) : false);
        
        if ($data !== false && $data['_yoast_wpseo_title'][0] != '') {
            $title_parts['title'] = $data['_yoast_wpseo_title'][0];
        }
        
        return $title_parts;
    });
}