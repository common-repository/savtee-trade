<?php

class UpAjax extends UPMain
{
    public $response = '';
    
    public function getPostElementDataView()
    {
        $post = $this->__getPostElementData();
        $attr = (($this->_req['wysiwyg'] == true) ? 'class="wysiwyg"' : '');
        $attr .= ' id="input-html-main"';
        
        $view = '
            <div id="dwLighbox-inner-wrapper">
                <form id="postElementDataForm" method="post" autocomplete="off">
                    ' . $this->_renderFormInputField(
                            $this->_req['variant'],
                            'html',
                            $post,
                            $attr
                        ) . 
                        $this->_renderFormInputField(
                            'hidden',
                            'elem',
                            $this->_req['elem']
                        ) .
                        $this->_renderFormInputField(
                            'hidden',
                            'pid',
                            $this->_req['pid']
                        ) .
                        $this->_renderFormInputField(
                            'hidden',
                            'meta',
                            $this->_req['meta']
                        ) . '<br />' .
                        ((isset($this->settings["{$this->_req['elem']}_maxchars"])) ? $this->displayMaxCharsBar($this->settings["{$this->_req['elem']}_maxchars"], $post) : '') .
                        get_submit_button($this->_conf['LANGUAGE']['BTN_SUBMIT'], 'primary', 'submit', true) . '
                </form>
            </div>
        ';
        
        $this->_printToScreen($view);
    }
    
    public function savePostElementData()
    {
        if ($this->_req['elem'] == 'wp_terms') {
            wp_set_post_terms($this->_req['pid'], $this->_req['html']);
            return;
        }
        
        if ($this->_req['meta'] == true) {
            update_post_meta($this->_req['pid'], $this->_req['elem'], $this->_req['html']);
        } else {
            $data = [
                'ID'                => $this->_req['pid'],
                $this->_req['elem'] => $this->_req['html']
            ];
            wp_update_post($data);
        }
    }
    
    public function saveCategoriesData()
    {
        if (isset($this->_req['cats']) && !is_null($this->_req['cats'])) {
            wp_set_post_categories($this->_req['pid'], $this->_req['cats']);
        }
        
        $this->_printToScreen($this->__getPostCategoriesView($this->_req['pid']));
    }
    
    public function saveNoteData()
    {
        $note = nl2br(((isset($this->_req['note'])) ? $this->_req['note'] : ''));
        $this->_db->update(
            $this->_tableName,
            ['option_value' => $note],
            ['option_key' => 'note']
        );
        
        $this->_printToScreen($note);
    }
    
    public function getSecondaryElementsView()
    {
        $post = $this->__getPostData();

        // content
        $view = '<table>
            <tr>
                <th>'
                    . $this->_conf['LANGUAGE']['CONTENT'] . '&nbsp;'
                    . $this->_displayWidgetMenuEntry(
                        'post_content',
                        $this->_widgetMenuEntrys['post_content']['title'],
                        $this->_widgetMenuEntrys['post_content']['variant'],
                        $this->_widgetMenuEntrys['post_content']['wysiwyg'],
                        $this->_widgetMenuEntrys['post_content']['meta'],
                        $this->_widgetMenuEntrys['post_content']['icon']
                    ).
                '</th>
            </tr>
            <tr>
                <td>
                    <span class="current_post_content_' . $post->ID . '">'
                    . substr(strip_tags($post->post_content), 0, 800) . '
                    </span>
                    ...
                </td>
            </tr>
        </table>';
        
        $view .= '<div class="spacer"></div>';
        
        if ($post->post_type !== 'page') {
            // terms
            $view .= '<table>
                <tr>
                    <th>'
                        . $this->_conf['LANGUAGE']['WP_TERMS'] . '&nbsp;'
                        . $this->_displayWidgetMenuEntry(
                            'wp_terms',
                            $this->_widgetMenuEntrys['wp_terms']['title'],
                            $this->_widgetMenuEntrys['wp_terms']['variant'],
                            $this->_widgetMenuEntrys['wp_terms']['wysiwyg'],
                            $this->_widgetMenuEntrys['wp_terms']['meta'],
                            $this->_widgetMenuEntrys['wp_terms']['icon']
                        ).
                    '</th>
                </tr>
                <tr>
                    <td>
                        <span class="current_wp_terms_' . $post->ID . '">'
                        . $this->__getPostTermsView($post->ID) . '
                        </span>
                    </td>
                </tr>
            </table>';
            
            $view .= '<div class="spacer"></div>';
            
            // categories
            $view .= '<table>
                <tr>
                    <th>'
                        . $this->_conf['LANGUAGE']['WP_CATEGORES'] . '&nbsp;'
                        . $this->_displayWidgetMenuEntry(
                            'wp_categories',
                            $this->_widgetMenuEntrys['wp_categories']['title'],
                            $this->_widgetMenuEntrys['wp_categories']['variant'],
                            $this->_widgetMenuEntrys['wp_categories']['wysiwyg'],
                            $this->_widgetMenuEntrys['wp_categories']['meta'],
                            $this->_widgetMenuEntrys['wp_categories']['icon'],
                            false
                        ).
                    '</th>
                </tr>
                <tr>
                    <td>
                        <span class="current_wp_categories_' . $post->ID . '">'
                        . $this->__getPostCategoriesView($post->ID) . '
                        </span>
                    </td>
                </tr>
            </table>';
        }
        
        $view .= '<div class="spacer"></div>';
        
        $view .= '<table>
                <tr>
                    <th>'
                        . $this->_conf['LANGUAGE']['NOTE'] . '&nbsp;'
                        . $this->_displayWidgetMenuEntry(
                            'note',
                            $this->_widgetMenuEntrys['note']['title'],
                            $this->_widgetMenuEntrys['note']['variant'],
                            $this->_widgetMenuEntrys['note']['wysiwyg'],
                            $this->_widgetMenuEntrys['note']['meta'],
                            $this->_widgetMenuEntrys['note']['icon'],
                            false
                        ).
                    '</th>
                </tr>
                <tr>
                    <td>
                        <span class="my-seo-note">'
                        . $this->__getNote() . '
                        </span>
                    </td>
                </tr>
            </table>';
        
        $this->_printToScreen($view);
    }
    
    public function getCategoriesFormView()
    {
        $top = '
            <div>
            <form id="postCategoriesForm" method="post" autocomplete="off">
            <div id="category-all">
            <ul id="categorychecklist">'
            . $this->_renderFormInputField(
            'hidden',
            'pid',
            $this->_req['pid']);
        
        $this->_printToScreen($top);
        
        wp_category_checklist($this->_req['pid'], 0, false, false, null, true);
        
        $bottom = '
            </ul>
            </div>
            ' . get_submit_button($this->_conf['LANGUAGE']['BTN_SUBMIT'], 'primary', 'submit', true) . '
            </form>
            </div>
        ';
        
        $this->_printToScreen($bottom);
    }
    
    public function getNoteFormView()
    {
        $view = '
            <div>
                <form id="postNoteForm" method="post" autocomplete="off">
                    ' . $this->_renderFormInputField($variant = 'textarea', 'note', $this->settings['note']) . '
                    ' . get_submit_button($this->_conf['LANGUAGE']['BTN_SUBMIT'], 'primary', 'submit', true) . '
                </form>
            </div>
        ';
        
        $this->_printToScreen($view);
    }
    
    public function setFavoriteStatus()
    {
        $pID = $this->_req['pid'];
        $favID = $this->_req['fav'];
        $match = [
            'post_id' => $pID
        ];
        
        $del = false;
            
        if ($this->_isFavoriteSet($this->_tableNameFavorites . '_' . $favID, $pID)) {
            $this->_db->delete($this->_tableNameFavorites . '_' . $favID, $match);
        } else {
            $this->_db->insert($this->_tableNameFavorites . '_' . $favID, $match);
        }
        
        // get hmtl response
        $this->response = $this->_displayFavoritesSelectionView($pID);
    }
    
    public function updateFavoriteContent()
    {
        $favID = $this->_req['fav'];
        
        $response = [];
        $response['content'] = $this->displayPosts($this->_getFavoriteData($favID), true, $favID);
        $response['tab_icon'] = $this->_getFavoriteTabIconView($favID, $this->_favoritesSelection[$favID]);
        //var_dump($response);
        $this->response = json_encode($response);
    }
    
    private function __getNote()
    {
        return $this->settings['note'];
    }
    
    private function __getPostElementData()
    {
        if ($this->_req['elem'] == 'wp_terms') {
            $terms = $this->__getPostTermsView($this->_req['pid']);
            return $terms;
        }
        
        $post = get_post($this->_req['pid']);
        return $post->{$this->_req['elem']};
    }
    
    private function __getPostData()
    {
        return get_post($this->_req['pid']);
    }
    
    private function __getPostTermsView($postID)
    {
        $terms = wp_get_post_terms($postID);
        $view = '';
        
        foreach ($terms as $term) {
            $view .= $term->slug . ((end($terms) !== $term) ? ', ' : '');
        }
        
        return $view;
    }
    
    private function __getPostCategoriesView($postID)
    {
        return get_the_category_list('', '', $postID);
    }
    /* deprecated
    public function getFavoriteSelectionView()
    {
        $posts = [];
        
        foreach ($this->_req['favorites'] as $post) {
            $posts[] = get_post($post);
        }
        
        return $this->__displayFavorites($posts);
    }
    */
    private function __displayFavorites($posts)
    {
        // deprecated $view = $this->__getWidgetMenuView('post');
        $view = '<div class="clearfix"><div class="wrap_left"><table>';
        $view .= '
            <tr class="disabled up-widget-menu">
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
            </tr>
        ';
        
        foreach ($posts as $post) {
            $view .= '
                <tr class="clickable select-row">
                    <td></td>
                    <td>
                        <input class="hidethis" type="radio" name="element-id" value="' . $post->ID . '" />
                        <span class="current_post_title_' . $post->ID . '">'
                            . $post->post_title . '
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
                </tr>
            ';
        }
        $view .= '</table></div>';
        $view .= '<div class="wrap_right"> ' . $this->loading . ' <div class="secondaryContent"></div></div></div>';
        
        $this->_printToScreen($view);
    }
}