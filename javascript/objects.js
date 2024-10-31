(function($) {

    $.UbiLite.Ref = {
        
        'doc': $(document),
        'win': $(window)
        
    };
    
    $.UbiLite.Sel = {
        'tabs': '.tabs',
        'editElem': '.edit-element',
        'postElementDataForm': '#postElementDataForm',
        'widgetMenu': '.up-widget-menu',
        'postWidgetMenu': '#posts .up-widget-menu',
        'pageWidgetMenu': '#pages .up-widget-menu',
        'widgetMenuLink': '.up-widget-menu a',
        'row': '.select-row',
        'permalink': '.permalink',
        'maxchars': '.maxchars-bar',
        'chars': '.chars',
        'progress': '.maxchars-bar-progress',
        'wrap_left': '.wrap_left',
        'wrap_right': '.wrap_right',
        'wrap_right_content': '.wrap_right .secondaryContent',
        'loading': '.loading',
        'editCats': '#nav-wp_categories',
        'editNote': '#nav-note',
        'postCategoriesForm': '#postCategoriesForm',
        'postNoteForm': '#postNoteForm',
        'mySeoNote': '.my-seo-note',
        'favorites': '#favorites',
        'setFav': '.favorite-selection-area i',
        'favoritesList': '#favorites-list',
        'removeFav': '.remove-fav',
        'secondaryContent': '.secondaryContent',
        'btnReset': '.btn-reset',
        'selTab': '.sel-tab',
        'burger': '#burgerMenuButton'
    };
    
    $.UbiLite.Var = {
        'ajaxURL': '/wp-admin/admin-ajax.php',
        'action': {
            'getPostElementForm': 'getPostElementForm',
            'savePostElementForm': 'savePostElementForm',
            'getSecondaryElements': 'getSecondaryElements',
            'getCategoriesForm': 'getCategoriesForm',
            'saveCategoriesForm': 'saveCategoriesForm',
            'getFavoriteSelection': 'getFavoriteSelection',
            'getNoteForm': 'getNoteForm',
            'saveNoteForm': 'saveNoteForm',
            'setFavoriteStatus': 'setFavoriteStatus',
            'updateFavoriteContent': 'updateFavoriteContent',
            'removeFavorite': 'removeFavorite'
        }
        
    };
    
    $.UbiLite.Func = {
    
        'loadTinyMCE': function() {
            $.getScript('//cdn.jsdelivr.net/tinymce/4.1.2/tinymce.min.js', function(data) {
                tinymce.init({selector: 'textarea.wysiwyg'});
            });
        },
        'getCurrent': function(elem, pid) {
            return '.current_' + elem + '_' + pid;
        },
        'getCheckedInput': function(selector) {
            return 'input[name=' + selector + ']:checked';
        },
        'fixTableRowHeight': function() {
            var maxHeight = null;
            $($.UbiLite.Sel.row).each(function() {
                var height = $(this).height();
                if(maxHeight == null || height > maxHeight) maxHeight = height;
            }).height(maxHeight);
        },
        'checkMaxChars': function(elem, count) {
            var $maxchars = $($.UbiLite.Sel.maxchars + '-' + count),
                maxchars = $maxchars.data('maxchars'),
                chars = $(elem).val().length,
                $chars = $($.UbiLite.Sel.chars + '-' + count),
                $progress = $($.UbiLite.Sel.progress + '-' + count),
                progress = ((100 * chars) / maxchars);
            
            $chars.html(chars);
            $progress.css({
                'width': progress + '%',
                'max-width': '100%'
            });
            
            if (progress <= 70) {
                $progress.addClass('progress-low');
            } else if (progress > 70 && progress <= 100) {
                $progress.removeClass('progress-low');
            }
            
            if (chars > maxchars) {
                $maxchars.addClass('alert');
                $progress.addClass('alert-bg');
            } else if ($maxchars.hasClass('alert')) {
                $maxchars.removeClass('alert');
                $progress.removeClass('alert-bg');
            }
            
            //console.log(maxchars + ': ' + chars);
        }
    
    };

})(jQuery);