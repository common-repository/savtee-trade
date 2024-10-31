<?php

class UPAdminOptions extends UPMain
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function install()
    {
        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset = $this->_db->get_charset_collate();

        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->_tableNameFavorites . '_0 (
                      id int(11) NOT NULL AUTO_INCREMENT,
                      customer_id int(11) NOT NULL DEFAULT 0,
                      post_id int(11) NOT NULL DEFAULT 0,
                      PRIMARY KEY (id)
                      ) ' . $charset . ';
        ';
        
        dbDelta($query);
        
        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->_tableNameFavorites . '_1 (
                      id int(11) NOT NULL AUTO_INCREMENT,
                      customer_id int(11) NOT NULL DEFAULT 0,
                      post_id int(11) NOT NULL DEFAULT 0,
                      PRIMARY KEY (id)
                      ) ' . $charset . ';
        ';
        
        dbDelta($query);
        
        
        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->_tableNameFavorites . '_2 (
                      id int(11) NOT NULL AUTO_INCREMENT,
                      customer_id int(11) NOT NULL DEFAULT 0,
                      post_id int(11) NOT NULL DEFAULT 0,
                      PRIMARY KEY (id)
                      ) ' . $charset . ';
        ';
        
        dbDelta($query);
        
        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->_tableNameFavorites . '_3 (
                      id int(11) NOT NULL AUTO_INCREMENT,
                      customer_id int(11) NOT NULL DEFAULT 0,
                      post_id int(11) NOT NULL DEFAULT 0,
                      PRIMARY KEY (id)
                      ) ' . $charset . ';
        ';
        
        dbDelta($query);
        
        $this->__registerPluginOptions();
    }
    
    public function uninstall()
    {
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            die;
        }
        
        $query = 'DROP TABLE IF EXISTS ' . $this->_tableNameFavorites . '_0;
                  DROP TABLE IF EXISTS ' . $this->_tableNameFavorites . '_1;
                  DROP TABLE IF EXISTS ' . $this->_tableNameFavorites . '_2;
                  DROP TABLE IF EXISTS ' . $this->_tableNameFavorites . '_3;
        ';
        
        $this->_db->query($query);
        $this->__unRegisterPluginOptions();
    }
    
    public function getOptionPage()
    {
        if (isset($this->_req['submit-up-options'])) {
            $this->__saveOptions();
            // reload settings in the current instance
            $this->_fillSettings();
        }
        
        add_options_page(
            $this->_conf['LANGUAGE']['SETTINGS'],
            $this->_conf['LANGUAGE']['SETTINGS'],
            'manage_options',
            $this->_conf['LANGUAGE']['PLUGIN_URL_PREFIX'] . '-options', 
            [
                &$this,
                'getOptionPageView'
            ]
        );
    }
    
    public function addSettingsActionLink($links)
    {
        return array_merge(
            $links,
            [
                $this->getOptionPageLink()
            ]
        );
    }
    
    public function getOptionPageView()
    {
        $view = '<div id="up_option_wrapper" class="wrap">';
        $view .= '<h3>' . $this->_conf['LANGUAGE']['SETTINGS'] . '</h3>';
        
        $this->__renderOptionPageForm($view);
        
        $view .= '</div>';
        
        $this->_printToScreen($view);
    }
    
    private function __registerPluginOptions()
    {
        foreach ($this->_plugin_options as $key => $value) {
            add_option($key, $value);
        }
    }
    
    private function __unRegisterPluginOptions()
    {
        foreach ($this->_plugin_options as $key => $value) {
            delete_option($key);
        }
    }
    
    private function __renderOptionPageForm(&$view)
    {
        $view .=
        '<div>
            <form id="ubilite-form" class="form-table" method="post" action="">
                <table>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label style="font-weight: bold;" for="post_title_maxchars">' . $this->_conf['LANGUAGE']['MAX_CHARS'] . ' ' . $this->_conf['LANGUAGE']['H1_HEADING'] . ':</label>
                        </th>
                        <td>
                            ' . $this->_renderFormInputField(
                                    'text',
                                    'option[post_title_maxchars]',
                                    $this->settings['post_title_maxchars']
                                ) .'
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label style="font-weight: bold;" for="_yoast_wpseo_title_maxchars">' . $this->_conf['LANGUAGE']['MAX_CHARS'] . ' ' . $this->_conf['LANGUAGE']['META_TITLE_TAG'] . ':</label>
                        </th>
                        <td>
                            ' . $this->_renderFormInputField(
                                    'text',
                                    'option[_yoast_wpseo_title_maxchars]',
                                    $this->settings['_yoast_wpseo_title_maxchars']
                                ) .'
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label style="font-weight: bold;" for="_yoast_wpseo_metadesc_maxchars">' . $this->_conf['LANGUAGE']['MAX_CHARS'] . ' ' . $this->_conf['LANGUAGE']['META_DESCRIPTION_TAG'] . ':</label>
                        </th>
                        <td>
                            ' . $this->_renderFormInputField(
                                    'text',
                                    'option[_yoast_wpseo_metadesc_maxchars]',
                                    $this->settings['_yoast_wpseo_metadesc_maxchars']
                                ) .'
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" height="20"></td>
                    </tr>
                    <tr>
                        <td>
                            ' . get_submit_button($this->_conf['LANGUAGE']['BTN_SUBMIT'], 'primary', 'submit-up-options', true); '
                        </td>
                </table>
            </form>';
    }
    
    private function __saveOptions()
    {
        foreach ($this->_plugin_options as $option => $value) {
            if ($option != 'note') {
                update_option(
                    $option,
                    ((isset($this->_req['option'][$option])) ? $this->_req['option'][$option] : 0)
                );
            }
            
        }
    }
}