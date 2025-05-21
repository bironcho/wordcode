<?php
/*
Plugin Name: Filtro ricerca categorie WordPress
Description: Aggiunge un campo di ricerca per filtrare le categorie nell’editor classico e nella pagina delle categorie.
Version: 1.1
Author: Silvio Ricci
*/

add_action('admin_enqueue_scripts', function($hook) {
    // Carica solo nelle schermate desiderate
    //if (in_array($hook, ['post.php', 'post-new.php', 'edit-tags.php'])) {
    if (in_array($hook, ['post.php', 'post-new.php', 'edit-tags.php', 'term.php'])) {
        wp_enqueue_script('jquery');
        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($) {

                // ===== 1. FILTRO NEL BLOCCO CATEGORIE (Editor Classico) =====
                if ($('#categorychecklist').length) {
                    var wrapper = $('<div>', {
                        css: {
                            position: 'relative',
                            marginBottom: '8px'
                        }
                    });

                    var searchField = $('<input>', {
                        type: 'text',
                        id: 'category-search',
                        placeholder: 'Cerca categoria...',
                        css: {
                            width: '100%',
                            padding: '5px 28px 5px 5px',
                            boxSizing: 'border-box'
                        }
                    });

                    var clearBtn = $('<span>', {
                        html: '&times;',
                        css: {
                            position: 'absolute',
                            right: '8px',
                            top: '50%',
                            transform: 'translateY(-50%)',
                            cursor: 'pointer',
                            color: '#999',
                            fontSize: '18px',
                            fontWeight: 'bold',
                            display: 'none'
                        },
                        title: 'Pulisci'
                    });

                    wrapper.append(searchField).append(clearBtn);
                    $('#categorychecklist').before(wrapper);

                    searchField.on('input', function() {
                        clearBtn.toggle($(this).val().length > 0).trigger('keyup');
                    });

                    searchField.on('keyup', function() {
                        var filter = $(this).val().toLowerCase();
                        $('#categorychecklist li').each(function() {
                            var label = $(this).text().toLowerCase();
                            $(this).toggle(label.indexOf(filter) > -1);
                        });
                    });

                    clearBtn.on('click', function() {
                        searchField.val('').trigger('input');
                        $('#categorychecklist li').show();
                    });
                }

                // ===== 2. FILTRO NEL MENU A TENDINA CATEGORIA GENITORE =====
                if ($('#parent').length) {
                    var parentWrap = $('<div>', {
                        css: {
                            position: 'relative',
                            marginBottom: '8px'
                        }
                    });

                    var parentSearch = $('<input>', {
                        type: 'text',
                        id: 'parent-category-search',
                        placeholder: 'Filtra categoria genitore...',
                        css: {
                            width: '100%',
                            padding: '5px 28px 5px 5px',
                            boxSizing: 'border-box'
                        }
                    });

                    var clearParent = $('<span>', {
                        html: '&times;',
                        css: {
                            position: 'absolute',
                            right: '8px',
                            top: '50%',
                            transform: 'translateY(-50%)',
                            cursor: 'pointer',
                            color: '#999',
                            fontSize: '18px',
                            fontWeight: 'bold',
                            display: 'none'
                        },
                        title: 'Pulisci'
                    });

                    parentWrap.append(parentSearch).append(clearParent);
                    $('#parent').before(parentWrap);

                    parentSearch.on('input', function() {
                        clearParent.toggle($(this).val().length > 0);
                        var filter = $(this).val().toLowerCase();

                        $('#parent option').each(function() {
                            var text = $(this).text().toLowerCase();
                            if (filter === '' || text.indexOf(filter) > -1) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    });

                    clearParent.on('click', function() {
                        parentSearch.val('').trigger('input');
                        $('#parent option').show();
                    });
                }
            });
        ");
    }
});

