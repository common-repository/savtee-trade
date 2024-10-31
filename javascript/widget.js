'use strict';

(function($) {
    $.UbiLite.Widget = function () {
        
        $.UbiLite.Ref.doc.ready(function() {
            $.UbiLite.Ref.win.navpos = null;
            $.UbiLite.Ref.win.SideContentorgWidth = $($.UbiLite.Sel.wrap_right).css('right');
            
            $('.checkmaxchars').each(function() {
                var count = $(this).data('count');
                $.UbiLite.Func.checkMaxChars($(this), count);
            });
            
            $($.UbiLite.Sel.tabs).tabs();
            
            var maxHeight = null;
            $.UbiLite.Func.fixTableRowHeight();
            
            if ($($.UbiLite.Sel.widgetMenu).length > 0) {
                $.UbiLite.Ref.win.navpos = $($.UbiLite.Sel.widgetMenu).offset();
            } else {
                $.UbiLite.Ref.win.navpos = 0;
            }
            
            $($.UbiLite.Sel.wrap_left + ' table td:first').trigger('click');
            
            var winHeight = $.UbiLite.Ref.win.height();
            $($.UbiLite.Sel.secondaryContent).height(winHeight - 50);
            
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.selTab, function(e) {
            var href = $(this).data('href');
            $(href + ' table td:first').trigger('click');
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.editElem, function(e) {
            e.preventDefault();
            
            var pid = $($.UbiLite.Func.getCheckedInput('element-id')).val(),
                elem = $(this).data('element'),
                variant = $(this).data('variant'),
                meta = $(this).data('meta'),
                wysiwyg = $(this).data('wysiwyg');
                
            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.getPostElementForm,
                    ajax: true,
                    pid: pid,
                    elem: elem,
                    variant: variant,
                    meta: meta,
                    wysiwyg: wysiwyg
                },
                success: function(data) {
                    //$.fancybox.open('<div>' + data + '</div>');
                    $.dwLightbox('open', data, {
                        closeButton: '<i class="fa fa-times" aria-hidden="true"></i>'
                    });
                    
                    var count = 0;
            
                    if ($('#input-html-main').attr('data-count')) {
                        count = $('#input-html-main').data('count');
                    }
            
                    $.UbiLite.Func.checkMaxChars($('#input-html-main'), count);
                    $.UbiLite.Func.loadTinyMCE();
                }
            });
        });
        
        $.UbiLite.Ref.doc.on('submit', $.UbiLite.Sel.postElementDataForm, function(e) {
            e.preventDefault();

            var pid = $('input[name=pid]').val(),
                elem = $('input[name=elem]').val(),
                html = $('input[name=html], textarea[name=html]').val(),
                meta = $('input[name=meta]').val();

            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.savePostElementForm,
                    ajax: true,
                    pid: pid,
                    elem: elem,
                    html: html,
                    meta: meta
                },
                success: function(data) {
                    $($.UbiLite.Func.getCurrent(elem, pid)).html(html.substring(0, 800));
                    //$.fancybox.close();
                    $.dwLightbox('close');
                }
            });
            
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.editCats, function(e) {
            e.preventDefault();
            
            var pid = $($.UbiLite.Func.getCheckedInput('element-id')).val();
                
            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.getCategoriesForm,
                    ajax: true,
                    pid: pid
                },
                success: function(data) {
                    //$.fancybox.open('<div>' + data + '</div>');
                    $.dwLightbox('open', data, {
                        closeButton: '<i class="fa fa-times" aria-hidden="true"></i>'
                    });
                }
            });
        });
        
        $.UbiLite.Ref.doc.on('submit', $.UbiLite.Sel.postCategoriesForm, function(e) {
            e.preventDefault();

            var pid = $('input[name=pid]').val();
                
            var cats = $('input:checkbox:checked').map(function() {
                    return this.value;
                }).get();
                
            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.saveCategoriesForm,
                    ajax: true,
                    pid: pid,
                    cats: cats
                },
                success: function(data) {
                    $($.UbiLite.Func.getCurrent('wp_categories', pid)).html(data);
                    //$.fancybox.close();
                    $.dwLightbox('close');
                }
            });
            
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.editNote, function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.getNoteForm,
                    ajax: true
                },
                success: function(data) {
                    //$.fancybox.open('<div>' + data + '</div>');
                    $.dwLightbox('open', data, {
                        closeButton: '<i class="fa fa-times" aria-hidden="true"></i>'
                    });
                }
            });
        });
        
        $.UbiLite.Ref.doc.on('submit', $.UbiLite.Sel.postNoteForm, function(e) {
            e.preventDefault();
            
            var note = $('textarea[name=note]').val();
            
            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.saveNoteForm,
                    ajax: true,
                    note: note
                },
                success: function(data) {
                    $($.UbiLite.Sel.mySeoNote).html(data);
                    //$.fancybox.close();
                    $.dwLightbox('close');
                }
            });
            
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.row, function(e) {
            if (
                !$(e.target).is('input[name=element-id]') &&
                !$(e.target).is($.UbiLite.Sel.permalink) &&
                !$(e.target).is($.UbiLite.Sel.setFav)
                ) {
                
                $($.UbiLite.Sel.wrap_right_content).html("");
                
                $($.UbiLite.Sel.loading).show();
                
                $('table tr').not(this).removeClass('active-row');
                $('table tr[class^=menu-line-], table tr[class^=status-line-]').addClass('hidethis');
                
                var pid = $(this).first().find('input[name=element-id]').val();
                
                if (!$(this).hasClass('active-row')) {
                    $(this).first().find('input[name=element-id]').trigger('click');
                    $($.UbiLite.Sel.widgetMenu).removeClass('disabled');
                    $('.menu-line-' + pid + ', .status-line-' + pid).removeClass('hidethis');
                } else {
                    $($.UbiLite.Sel.widgetMenu).addClass('disabled');
                    $('.menu-line-' + pid + ', .status-line-' + pid).addClass('hidethis');
                }
                
                $(this).toggleClass('active-row');
                 
                $.ajax({
                    url: $.UbiLite.Var.ajaxURL,
                    type: 'POST',
                    data: {
                        action: $.UbiLite.Var.action.getSecondaryElements,
                        ajax: true,
                        pid: pid,
                    },
                    success: function(data) {
                        $($.UbiLite.Sel.wrap_right_content).html(data);
                        $($.UbiLite.Sel.loading).hide();
                    }
                });
            }
        });
        
        var $tab = '#posts';
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.tabs + ' .ui-tabs-nav li a', function() {
            $tab = $(this).attr('href');
        });

        $.UbiLite.Ref.win.on('scroll', function() {
            var navtop = $.UbiLite.Ref.win.navpos.top;
            var navwidth = $($tab + ' ' + $.UbiLite.Sel.widgetMenu).width();
            var winwidth = $.UbiLite.Ref.win.width();
            $($tab + ' ' + $.UbiLite.Sel.widgetMenu).parent().find('th, td').each(function() {
                $(this).attr("data-item-width", $(this).width());
            }); 
            var wintop = $(this).scrollTop();
            
            if (navtop <= wintop && wintop > 1 && winwidth > 1660) {
                //$($tab + ' ' + $.UbiLite.Sel.widgetMenu).addClass('fixed');
                $($tab + ' ' + $.UbiLite.Sel.secondaryContent).addClass('fixed');
                $($tab + ' ' + $.UbiLite.Sel.widgetMenu).width(navwidth);
                $($tab + ' ' + $.UbiLite.Sel.widgetMenu).parent().find('th, td').each(function() {
                    $(this).width($(this).data('item-width'));
                });
                
            } else {
                $($tab + ' ' + $.UbiLite.Sel.widgetMenu).removeClass('fixed');
                $($tab + ' ' + $.UbiLite.Sel.secondaryContent).removeClass('fixed');
            }
        });
        $.UbiLite.Ref.doc.on('keyup', '#input-html-main, .checkmaxchars', function() {
            var count = 0;
            
            if ($(this).attr('data-count')) {
                count = $(this).data('count');
            }
            
            $.UbiLite.Func.checkMaxChars($(this), count);
        });
        /* deprecated
        $.UbiLite.Ref.doc.on('click', 'input:checkbox.fav-selection', function() {
            var favorites = $('input:checkbox:checked').map(function() {
                    return this.value;
                }).get();
            
            if (favorites.length > 0) {
                $($.UbiLite.Sel.favorites).removeClass('hidethis');
            } else {
                $($.UbiLite.Sel.favorites).addClass('hidethis');
            }
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.favorites, function() {
            var favorites = $('input:checkbox:checked').map(function() {
                    return this.value;
                }).get();
            
            if (favorites.length > 0) {
                $.ajax({
                    url: $.UbiLite.Var.ajaxURL,
                    type: 'POST',
                    data: {
                        action: $.UbiLite.Var.action.getFavoriteSelection,
                        ajax: true,
                        favorites: favorites,
                    },
                    success: function(data) {
                        $($.UbiLite.Sel.favoritesList).html(data);
                        $.UbiLite.Func.fixTableRowHeight();
                    }
                });
            }
        });
        * 
        * NEW multiple favorites
        */
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.setFav + ', ' + $.UbiLite.Sel.removeFav, function() {
            var favID = $(this).data('key');
            var postID = $(this).data('post');
                
            $.ajax({
                url: $.UbiLite.Var.ajaxURL,
                type: 'POST',
                data: {
                    action: $.UbiLite.Var.action.setFavoriteStatus,
                    ajax: true,
                    pid: postID,
                    fav: favID
                },
                success: function(data) {
                    if (data != "") {
                        $('.fav-group_' + postID).html(data);
                    }
                    
                    $.ajax({
                        url: $.UbiLite.Var.ajaxURL,
                        type: 'POST',
                        data: {
                            action: $.UbiLite.Var.action.updateFavoriteContent,
                            ajax: true,
                            fav: favID
                        },
                        success: function(data) {
                        var data = $.parseJSON(data);
                        console.log(data);
                            $('#favorites-list_' + favID).html(data.content);
                            $('#tab-favorites_' + favID + ' i').replaceWith(data.tab_icon);
                        }
                    });
                }
            });
            
            $.UbiLite.Ref.doc.ajaxComplete(function() {
                $.UbiLite.Func.fixTableRowHeight();
            });
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.btnReset, function() {
            var form = $(this).data('form');
            var page = $('input[name=page]').val();
            document.getElementById(form).reset();
            
            location.href = '?page=' + page;
        });
        
        $.UbiLite.Ref.doc.on('click', $.UbiLite.Sel.burger, function() {
            if (!$(this).hasClass('open')) {
                $($.UbiLite.Sel.wrap_right).animate({
                    'right': '0'
                }, 801);
            } else {
                $($.UbiLite.Sel.wrap_right).animate({
                    'right': $.UbiLite.Ref.win.SideContentorgWidth
                }, 301);
            }
            
            $(this).toggleClass('open');
        });
    }

$.UbiLite.Widget();

})(jQuery);