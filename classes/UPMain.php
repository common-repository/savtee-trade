<?php

class UPMain
{
    public $settings = [];
    protected $_tableNameFavorites;
    protected $_db;
    protected $_conf;
    protected $_widgetMenuEntrys = [];
    protected $_favoritesSelection = [];
    public $logo;
    public $loading;
    public $_req = [];
    public $burgerMenuButton = '<i class="fa fa-bars" aria-hidden="true"></i>';
    
    protected $_plugin_options = [
        '_yoast_wpseo_title_maxchars'       => 50,
        '_yoast_wpseo_metadesc_maxchars'    => 156,
        'post_title_maxchars'               => 55,
        'note'                              => 'Put your notes into here'
    ];
    
    public function __construct()
    {
        global $wpdb, $upconfig;
        
        $this->_conf = $upconfig;
        
        $this->_req = array_merge($_POST, $_GET, $_REQUEST);
        $this->_db = $wpdb;
        $this->_tableNameFavorites = $this->_db->prefix . $upconfig['DATABASE']['DB_TABLE_NAME_FAVORITES'];
        
        $logo = $this->_conf['PATH']['UP_PLUGIN_URL']
                    . $this->_conf['PATH']['UP_PLUGIN_LOGO_PATH'] . 'logo.png';
        $this->logo = '<img 
                        alt="' . $this->_conf['LANGUAGE']['PLUGIN_NAME'] . '"
                        title="' . $this->_conf['LANGUAGE']['PLUGIN_NAME'] . '"
                        id="up-logo" src="' . $logo . '"
                    />';
        $loading = $this->_conf['PATH']['UP_PLUGIN_URL']
                    . $this->_conf['PATH']['UP_PLUGIN_IMG_PATH'] . 'loading.gif';
        $this->loading = '<img 
                            class="loading" src="' . $loading . '"
                    />';
        
        $this->_fillSettings();
        
        $this->_widgetMenuEntrys = [
            'post_title'                => [
                'title'     => $this->_conf['LANGUAGE']['H1_HEADING'],
                'variant'   => 'text',
                'wysiwyg'   => false,
                'meta'      => false,
                'icon'      => 'fa fa-pencil-square-o',
                'maxchars'  => $this->_settings['post_title_maxchars']
            ],
            'post_name'                => [
                'title'     => $this->_conf['LANGUAGE']['PERMALINK'],
                'variant'   => 'text',
                'wysiwyg'   => false,
                'meta'      => false,
                'icon'      => 'fa fa-pencil-square-o'
            ],
            '_yoast_wpseo_title'            => [
                'title'     => $this->_conf['LANGUAGE']['META_TITLE_TAG'],
                'variant'   => 'text',
                'wysiwyg'   => false,
                'meta'      => true,
                'icon'      => 'fa fa-pencil-square-o',
                'maxchars'  => $this->_settings['meta_title_tag_maxchars']
            ],
            '_yoast_wpseo_metadesc'      => [
                'title'     => $this->_conf['LANGUAGE']['META_DESCRIPTION_TAG'],
                'variant'   => 'textarea',
                'wysiwyg'   => false,
                'meta'      => true,
                'icon'      => 'fa fa-pencil-square-o',
                'maxchars'  => $this->_settings['meta_description_tag_maxchars'] 
            ],
            'post_content'              => [
                'title'     => $this->_conf['LANGUAGE']['CONTENT'],
                'variant'   => 'textarea',
                'wysiwyg'   => true,
                'meta'      => false,
                'icon'      => 'fa fa-pencil-square-o'
            ],
            'wp_terms'              => [
                'title'     => $this->_conf['LANGUAGE']['WP_TERMS'],
                'variant'   => 'textarea',
                'wysiwyg'   => false,
                'meta'      => false,
                'icon'      => 'fa fa-pencil-square-o'
            ],
            'wp_categories' => [
                'title'     => $this->_conf['LANGUAGE']['WP_CATEGORES'],
                'variant'   => 'textarea',
                'wysiwyg'   => false,
                'meta'      => false,
                'icon'      => 'fa fa-pencil-square-o'
            ],
            'note' => [
                'title'     => $this->_conf['LANGUAGE']['NOTE'],
                'variant'   => 'textarea',
                'wysiwyg'   => false,
                'meta'      => false,
                'icon'      => 'fa fa-pencil-square-o'
            ]
        ];
        
        $this->_favoritesSelection = [
            '0' => [
                'default' => 'fa fa-star-o',
                'selected' => 'fa fa-star'
            ],
            '1' => [
                'default' => 'fa fa-star-o',
                'selected' => 'fa fa-star'
            ],
            '2' => [
                'default' => 'fa fa-star-o',
                'selected' => 'fa fa-star'
            ],
            '3' => [
                'default' => 'fa fa-star-o',
                'selected' => 'fa fa-star'
            ]
        ];
    }
    
    protected function _printToScreen($output)
    {
        // bad but necessary
        echo $output;
    }
    
    public function getBurgerMenuButtonView()
    {
        $view = '<div id="burgerMenuButton" class="clickable"> ' . $this->burgerMenuButton . '</div>';
        return $view;
    }
    
    public function getOptionPageLink()
    {
        return '<a href="' . admin_url('options-general.php?page=' . $this->_conf['LANGUAGE']['PLUGIN_URL_PREFIX'] . '-options') . '">' . $this->_conf['LANGUAGE']['SETTINGS'] . '</a>';
    }
    
    protected function _getLinkView($target, $title, $attr = '')
    {
        return '<a href="' . $target . '" ' . $attr . '>' . $title . '</a>';
    }
    
    protected function _renderFormInputField($variant = 'text', $name, $value = '', $attr = '')
    {
        if ($variant == 'text' || $variant == 'radio' || $variant == 'hidden' || $variant == 'checkbox') {
            return '<input ' . $attr . ' type="' . $variant . '" name="' . $name . '" value="' . $value . '" />';
        } else if ($variant == 'textarea') {
            return '<textarea name="' . $name .'" ' . $attr . '>' . $value . '</textarea>';
            // NOTE: following not working with fancybox/lightbox
            wp_editor(
                $value,
                'wysiwyg',
                [
                    'textarea_name'     => $name,
                ]
            );
        }
    }
    
    protected function _getFavoriteTabIconView($tab, $option)
    {
        return '<i class="favorites_' . $tab .' ' . (($this->_hasFavorites($tab)) ? $option['selected'] : $option['default']) . '" aria-hidden="true"></i>';
    }
    
    protected function _fillSettings()
    {
        $plugin_settings = [];
        foreach ($this->_plugin_options as $key => $value) {
            $this->settings[$key] = get_option($key);
        }
    }
    
    protected function _displayFavoritesSelectionGroup($key, $icon, $postID)
    {
        $tableName = $this->_tableNameFavorites . '_' . $key;
        return '<span class="favorite-selection-area">
                    <i class="' . (($this->_isFavoriteSet($tableName, $postID)) ? $icon['selected'] : $icon['default']) . ' favorites_' . $key . ' clickable setfav_' . $key . '"
                       data-key="' . $key . '" data-post="' . $postID . '"
                       aria-hidden="true"></i>
                </span>
        ';
    }
    
    public function displayPosts($posts, $isFav = false, $fav = -1)
    {
        // deprecated $view = $this->__getWidgetMenuView('post');
        $view = '<div class="clearfix"><div class="wrap_left"><table>';
        //$view .= $this->displayWidgetMenuView();
        
        foreach ($posts as $post) {
            $view .= $this->displayWidgetMenuView($post->ID) . '
                <tr class="clickable select-row">
                    <!--// deprecated 2017/12/18
                    <td><input type="checkbox" name="fav-selection[]" class="fav-selection" value="' . $post->ID . '" /></td>
                    -->
                    <td class="favorites-selection fav-group_' . $post->ID . '">
                        ' . (($isFav == false) ? $this->_displayFavoritesSelectionView($post->ID) : "") . '
                    </td>
                    <td>
                        <input class="hidethis" type="radio" name="element-id" value="' . $post->ID . '" />
                        <span class="current_post_title_' . $post->ID . '">'
                            . $post->post_title . '
                        </span>
                    </td>
                    <td>
                        <input class="hidethis" type="radio" name="element-id" value="' . $post->ID . '" />
                        <span class="current_post_name_' . $post->ID . '">'
                            . $post->post_name . '
                        </span>
                    </td>
                    <td>
                        <span class="current__yoast_wpseo_title_' . $post->ID . '">'
                            . $post->_yoast_wpseo_title . '
                        </span>
                    </td>
                    <td>
                        <span class="current__yoast_wpseo_metadesc_' . $post->ID . '">'
                            . $post->_yoast_wpseo_metadesc . '
                        </span>
                    </td>
                    <td>
                        ' . $this->_getLinkView(
                                get_permalink($post->ID),
                                $this->_conf['LANGUAGE']['LINK_TO_PREVIEW_ICO'],
                                'class="permalink" title="' . $this->_conf['LANGUAGE']['LINK_TO_PREVIEW_TXT'] . '"  target="_blank"'
                            ) . '
                    </td>
                    <td>
                        ' . $this->_getLinkView(
                                get_edit_post_link($post->ID),
                                $this->_conf['LANGUAGE']['LINK_TO_EDIT_PAGE_ICO'],
                                'class="permalink" title="' . $this->_conf['LANGUAGE']['LINK_TO_EDIT_PAGE_TXT'] . '" target="_blank"'
                            ) . '
                    </td>
                    <td>
                        ' . (($this->getLastPostRevisionLink($post->ID) !== false) ? $this->_getLinkView(
                                $this->getLastPostRevisionLink($post->ID),
                                $this->_conf['LANGUAGE']['LINK_TO_REVISION_PAGE_ICO'],
                                'class="permalink" title="' . $this->_conf['LANGUAGE']['LINK_TO_REVISION_PAGE_TXT'] . '" target="_blank"'
                            ) : "") . '
                    </td>
                    <td>
                        ' . (($isFav == true) ? '<span class="clickable remove-fav" data-key="' . $fav . '" data-post="' . $post->ID . '">' . $this->_conf['LANGUAGE']['DELETE'] . '</span>' : "") . '
                    </td>
                </tr>
                ' . $this->_displayPostStatusLine($post->ID, $post) . '
            ';
        }
        
        $view .= '</table></div>';
        $view .= ((sizeof($posts) == 0) ? $this->_conf['LANGUAGE']['NO_MATCH_SEAR'] : '');
        $view .= '<div class="wrap_right">'. $this->getBurgerMenuButtonView() . ' ' . $this->loading . ' <div class="secondaryContent"></div></div></div>';
        
        return $view;
    }
    
    public function displayWidgetMenuView($line)
    {
        return '
            <tr class="menu-line-' . $line . ' disabled up-widget-menu hidethis">
                <th></th>
                <th>'
                    . $this->_conf['LANGUAGE']['H1_HEADING'] . '&nbsp;'
                    . $this->_displayWidgetMenuEntry(
                        'post_title',
                        $this->_widgetMenuEntrys['post_title']['title'],
                        $this->_widgetMenuEntrys['post_title']['variant'],
                        $this->_widgetMenuEntrys['post_title']['wysiwyg'],
                        $this->_widgetMenuEntrys['post_title']['meta'],
                        $this->_widgetMenuEntrys['post_title']['icon']
                    ).
                '</th>
                <th>'
                    . $this->_conf['LANGUAGE']['PERMALINK'] . '&nbsp;'
                    . $this->_displayWidgetMenuEntry(
                        'post_name',
                        $this->_widgetMenuEntrys['post_name']['title'],
                        $this->_widgetMenuEntrys['post_name']['variant'],
                        $this->_widgetMenuEntrys['post_name']['wysiwyg'],
                        $this->_widgetMenuEntrys['post_name']['meta'],
                        $this->_widgetMenuEntrys['post_name']['icon']
                    ).
                '</th>
                <th>' 
                    . $this->_conf['LANGUAGE']['META_TITLE_TAG'] . '&nbsp;'
                    . $this->_displayWidgetMenuEntry(
                        '_yoast_wpseo_title',
                        $this->_widgetMenuEntrys['_yoast_wpseo_title']['title'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_title']['variant'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_title']['wysiwyg'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_title']['meta'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_title']['icon']
                    ).
                '</th>
                <th>'
                    . $this->_conf['LANGUAGE']['META_DESCRIPTION_TAG'] . '&nbsp;'
                    . $this->_displayWidgetMenuEntry(
                        '_yoast_wpseo_metadesc',
                        $this->_widgetMenuEntrys['_yoast_wpseo_metadesc']['title'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_metadesc']['variant'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_metadesc']['wysiwyg'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_metadesc']['meta'],
                        $this->_widgetMenuEntrys['_yoast_wpseo_metadesc']['icon']
                    ).
                '</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        ';
    }
    
    protected function _displayPostStatusLine($line, $post)
    {
        $view = '
            <tr class="status-line-' . $line . ' post-status-info hidethis">
                <td>&nbsp;</td>
                <td colspan="6">
        ';
        
        // focus keyword
        if (isset($post->_yoast_wpseo_focuskw) && $post->_yoast_wpseo_focuskw != "") {
            $view .= $this->_conf['LANGUAGE']['STATUS_FOKUSKW'] . ' <strong>' . $post->_yoast_wpseo_focuskw . '</strong> | ';
        }
        
        $view .= $post->post_status . ' | ' . $post->post_date . '
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        ';
        
        return $view;
    }
    
    protected function _getFavoriteSelection($favorites)
    {
        $favs = [];
        foreach ($favorites as $fav) {
            $favs[] = get_post($fav->post_id);
        }
        
        return $favs;
    }
    
    protected function _getFavoriteData($favID)
    {
        $tableName = $this->_tableNameFavorites . '_' . $favID;
        $data = $this->_db->get_results('SELECT post_id FROM ' . $tableName . ' ORDER BY id DESC');

        return $this->_getFavoriteSelection($data);
    }
    
    protected function _isFavoriteSet($tableName, $postID)
    {
        $result = $this->_db->get_results('SELECT * FROM ' . $tableName . ' WHERE post_id=' . (int) $postID);
        return ((sizeof($result) > 0) ? true : false);
    }
    
    protected function _hasFavorites($favID)
    {
        $tableName = $this->_tableNameFavorites . '_' . $favID;
        $result = $this->_db->get_results('SELECT * FROM ' . $tableName);
        return ((sizeof($result) > 0) ? true : false);
    }
    
    protected function _displayFavoritesSelectionView($postID)
    {
        $view = '';
        foreach ($this->_favoritesSelection as $fav => $icon) {
            $view .= $this->_displayFavoritesSelectionGroup($fav, $icon, $postID);
        }
        
        return $view;
    }
    
    protected function _displayWidgetMenuEntry(
        $element,                                   // string
        $title,                                     // string
        $variant,                                   // string
        $wysiwyg,                                   // boolean
        $meta,                                      // boolean
        $icon = 'fa fa-pencil-square-o',
        $editable = true
    )
    {
        return '<a href="#"
                id="nav-' . $element .'"
                class="' . (($editable == true) ? "edit-element" : "") . '"
                title="' . $title . ' editieren"
                data-element="' . $element . '"
                data-variant="' . $variant . '"
                data-wysiwyg="' . $wysiwyg . '"
                data-meta="' . $meta . '">
                <i class="' . $icon . '" aria-hidden="true"></i>
                </a>';
    }
    
    public function displayMaxCharsBar($maxchars, $content, $count = 0)
    {
        return '
                <div class="maxchars-bar-' . $count . '" data-maxchars="' . $maxchars . '" data-count="' . $count . '">
                    <div class="maxchars-bar-progress-' . $count . '"></div>
                    <div class="maxchars-bar-text">
                        <span class="chars-' . $count . '">' . strlen($content) . '</span> ' . $this->_conf['LANGUAGE']['OF'] . ' ' . $maxchars . ' ' . $this->_conf['LANGUAGE']['CHARS'] . '
                    </div>
                </div>
        ';
    }
    
    private function __getPostRevisions($postID)
    {
        return wp_get_post_revisions($postID);
    }
    
    private function __getLastPostRevision($postID)
    {
        $postRevisions = $this->__getPostRevisions($postID);
        
        if (is_array($postRevisions) && wp_is_post_revision(end($postRevisions))) {
            return wp_get_post_revision(reset($postRevisions));
        } else {
            return false;
        }
    }
    
    public function getLastPostRevisionLink($postID)
    {
        $lastRevision = $this->__getLastPostRevision($postID);
        
        if ($lastRevision !== false) {
            return 'revision.php?revision=' . $lastRevision->ID;
        } else {
            return false;
        }
    }
}