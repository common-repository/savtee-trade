<?php

class UPAdminPlugin extends UPMain
{
    protected $_jsRequirements = [
        'jquery',
        'jquery-ui-core',
        'jquery-ui-tabs'
    ];
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function addAdminBarLink()
    {
        add_action(
            'admin_bar_menu',
            [
                &$this,
                'getAdminBarLink'
            ],
            4000
        );
    }
    
    public function enqueueScripts($hook_suffix)
    {
        if ($hook_suffix == 'toplevel_page_savtee') {
            // load stylesheet
            wp_enqueue_style('fancybox-stylesheet', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'stylesheet/jquery.fancybox.min.css');
            wp_enqueue_style('up_main-stylesheet', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'stylesheet/main.css');
            wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
            
            
            // load javascript
            wp_enqueue_script('fancybox-javascript', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'javascript/libraries/jquery.fancybox.min.js', $this->_jsRequirements);
            wp_enqueue_script('dwlightbox-javascript', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'javascript/libraries/jquery.dwLightbox.min.js', $this->_jsRequirements);
            wp_enqueue_script('up_init-javascript', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'javascript/init.js', $this->_jsRequirements);
            wp_enqueue_script('up_objects-javascript', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'javascript/objects.js', $this->_jsRequirements);
            wp_enqueue_script('up_widget-javascript', $this->_conf['PATH']['UP_PLUGIN_URL'] . 'javascript/widget.js', $this->_jsRequirements);
        }
    }
    
    public function getPluginPage()
    {
        add_menu_page(
            $this->_conf['LANGUAGE']['PLUGIN_NAME'],
            $this->_conf['LANGUAGE']['PLUGIN_NAME'],
            'manage_options',
            $this->_conf['LANGUAGE']['PLUGIN_URL_PREFIX'], 
            [
                &$this,
                'getPluginPageView'
            ]
        );
    }
    
    public function getPluginPageView()
    {
        $view = $this->logo;
        /*
        $view .= <h3>
                    ' . $this->_conf['LANGUAGE']['PLUGIN_NAME'] . '
                </h3>';
        */
        $view .= '<p>
                    ' . sprintf($this->_conf['LANGUAGE']['PLUGIN_SHORT_DESCRIPTION'], $this->getOptionPageLink()) . '
                </p>';
        
        $this->__renderPluginPage($view);
        
        $this->_printToScreen($view);
    }
    
    public function getAdminBarLink() {
        global $wp_admin_bar;
        $args = [
            'id'    => 'savtee',
            'title' => $this->_conf['LANGUAGE']['PLUGIN_NAME'], 
            'href'  => $this->__getPluginPageLinkRaw(), 
            'meta'  => [
                'class' => 'savtee', 
                'title' => $this->_conf['LANGUAGE']['PLUGIN_NAME']
            ]
        ];
        $wp_admin_bar->add_node($args);
    }
    
    private function __renderPluginPage(&$view)
    {
        $keyword = ((isset($_GET['search_keyword'])) ? $_GET['search_keyword'] : "");
        
        $view .= '
            <div id="up_plugin_wrapper" class="wrap">
                <div class="tabs">
                    <ul>
                        <li><a href="#posts" data-href="#posts" class="sel-tab">' . $this->_conf['LANGUAGE']['POSTS'] . '</a></li>
                        <li><a href="#pages" data-href="#pages" class="sel-tab">' . $this->_conf['LANGUAGE']['SITES'] . '</a></li>
                        ' . /* deprecated <li id="favorites" class="hidethis"><a href="#favorites-list">' . $this->_conf['LANGUAGE']['FAVORITES'] . '</a></li> */ '
                        ' . $this->__renderFavoriteTabs() . '
                        <li>' . $this->__renderSearchFormView($keyword) . '</li>
                    </ul>
                    <div id="posts">
                        ' . $this->displayPosts($this->__getPosts('post', $keyword)) . '
                    </div>
                    <div id="pages">
                        ' . $this->displayPosts($this->__getPosts('page', $keyword)) . '
                    </div>
                    ' . $this->__renderFavoriteContentView() . '
                </div>
            </div>
        ';
    }
    
    private function __renderFavoriteTabs()
    {
        $tabs = $this->_favoritesSelection;
        
        $view = '';
        
        foreach ($tabs as $tab => $option) {
            $view .= '<li><a id="tab-favorites_' . $tab . '" href="#favorites-list_' . $tab .'" data-href="#favorites-list_' . $tab .'" class="sel-tab">
                    ' . $this->_getFavoriteTabIconView($tab, $option) . '</a></li>';
        }
        
        return $view;
    }
    
    private function __renderFavoriteContentView()
    {
        $favs = $this->_favoritesSelection;
        
        $view = '';
        foreach ($favs as $fav => $option) {
            $view .= '<div id="favorites-list_' . $fav . '">' . $this->displayPosts($this->_getFavoriteData($fav), true, $fav) . '</div>';
        }
        
        return $view;
    }
    
    private function __getPosts($type = 'post', $keyword = '')
    {
        if ($keyword !== '') {
            $query = '
                SELECT *
                FROM ' . $this->_db->posts . ',
                ' . $this->_db->term_relationships . ',
                ' . $this->_db->term_taxonomy . ',
                ' . $this->_db->terms . '
                WHERE (
                    ' . $this->_db->terms . '.name = "' . $keyword . '"
                    OR
                    ' . $this->_db->posts . '.post_content LIKE "%' . $keyword . '%"
                    OR
                    ' . $this->_db->posts . '.post_title LIKE "%' . $keyword . '%"
                )
                AND ' . $this->_db->posts . '.ID = ' . $this->_db->term_relationships . '.object_id
                AND ' . $this->_db->term_relationships . '.term_taxonomy_id = ' . $this->_db->term_taxonomy . '.term_taxonomy_id
                AND ' . $this->_db->term_taxonomy . '.term_id = ' . $this->_db->terms . '.term_id
                AND ' . $this->_db->posts . '.post_type = "' . $type . '"
                ORDER BY ' . $this->_db->posts . '.post_date ASC
            ';
            
            return $this->_db->get_results($query, OBJECT_K);
        }
        return get_posts([
            'numberposts' => -1,
            'post_type' => $type,
            'orderby' => 'title',
            'order' => 'ASC'
        ]);
    }
    
    private function __checkEditable($option)
    {
        return (($this->settings[$option] == boolval(1)) ? true : false);
    }
    
    private function __displayEditLink(
        $postID,
        $element,
        $variant,
        $wysiwyg = false,
        $meta = false
    )
    {
        return '<a href="#"
                class="edit-element"
                title="editieren"
                data-post-id="' . $postID . '"
                data-element="' . $element . '"
                data-variant="' . $variant . '"
                data-wysiwyg="' . $wysiwyg . '"
                data-meta="' . $meta . '">
                <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>';
    }
    
    
    private function __getWidgetMenuView($type)
    {
        if ($type == 'post') {
            $view = '
                <nav class="up-widget-menu disabled">
                    <ul class="clearfix">';
                    foreach ($this->_widgetMenuEntrys as $element => $data) {
                        if ($this->__checkEditable($element)) {
                            $view .= '
                                <li>
                                    ' . $this->_displayWidgetMenuEntry($element, $data['title'], $data['variant'], $data['wysiwyg'], $data['meta'], $data['icon']) . '
                                </li>
                            ';
                        }
                    }
            $view .= '
                    </ul>
                </nav>
            ';
        } elseif ($type == 'site') {
            $view = '
                <nav class="up-widget-menu disabled">
                    <ul class="clearfix">';
                    foreach ($this->_widgetMenuEntrys as $element => $data) {
                        if ($this->__checkEditable($element) && $element !== 'tag') {
                            $view .= '
                                <li>
                                    ' . $this->_displayWidgetMenuEntry($element, $data['title'], $data['variant'], $data['wysiwyg'], $data['meta'], $data['icon']) . '
                                </li>
                            ';
                        }
                    }
            $view .= '
                    </ul>
                </nav>
            ';
        } else {
            return;
        }
        
        return $view;
    }
    
    private function __getPluginPageLinkRaw()
    {
        return admin_url('admin.php?page=' . $this->_conf['LANGUAGE']['PLUGIN_URL_PREFIX']);
    }
    
    private function __renderSearchFormView($keyword = '')
    {
        $view = '
            <form id="post-search" method="GET" >
                <input type="hidden" name="page" value="savtee" />
                <input type="text" name="search_keyword" placeholder="search keyword" value="' . $keyword . '" /><button class="btn-search" type="submit">' . $this->_conf['LANGUAGE']['GO_SEARCH'] . '</button><button class="btn-reset" form="post-search" data-form="post-search" type="reset">' . $this->_conf['LANGUAGE']['UNDO'] . '</button>
            </form>
        ';
        
        return $view;
    }
}