<?php
/*
Plugin Name: Filtro ricerca categorie WordPress
Description: Aggiunge un campo di ricerca per filtrare le categorie nell’editor classico e nella pagina delle categorie.
Version: 1.1
Author: Silvio Ricci
*/

// Aggiunge uno script alla pagina di amministrazione di WordPress specifica
add_action('admin_enqueue_scripts', function($hook) {
    // Carica lo script solo nelle schermate desiderate del backend
    // L'array contiene i nomi delle pagine in cui il filtro deve essere attivo
    // Possibili valori di $hook: post.php (modifica articolo), post-new.php (nuovo articolo), edit-tags.php (lista categorie), term.php (modifica categoria)
    if (in_array($hook, ['post.php', 'post-new.php', 'edit-tags.php', 'term.php'])) {
        // Garantisce che jQuery sia caricato
        wp_enqueue_script('jquery');

        // Inserisce uno script JS personalizzato nella pagina
        wp_add_inline_script('jquery', "
            jQuery(document).ready(function($) {

                // ===== 1. FILTRO NEL BLOCCO CATEGORIE (Editor Classico) =====
                // Controlla se esiste il blocco delle categorie (usato nell'editor classico)
                if ($('#categorychecklist').length) {
                    // Crea un wrapper per il campo di ricerca
                    var wrapper = $('<div>', {
                        css: {
                            position: 'relative',
                            marginBottom: '8px'
                        }
                    });

                    // Crea il campo di input per la ricerca delle categorie
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

                    // Crea il pulsante per cancellare il testo del campo di ricerca
                    var clearBtn = $('<span>', {
                        html: '&times;', // Simbolo "x" per cancellare
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
                        title: 'Pulisci' // Tooltip
                    });

                    // Aggiunge il campo di ricerca e il pulsante cancella nel wrapper
                    wrapper.append(searchField).append(clearBtn);
                    // Inserisce il wrapper sopra la lista delle categorie
                    $('#categorychecklist').before(wrapper);

                    // Mostra o nasconde il pulsante cancella in base al contenuto del campo di ricerca
                    searchField.on('input', function() {
                        clearBtn.toggle($(this).val().length > 0).trigger('keyup');
                    });

                    // Filtra le categorie durante la digitazione
                    searchField.on('keyup', function() {
                        var filter = $(this).val().toLowerCase();
                        $('#categorychecklist li').each(function() {
                            var label = $(this).text().toLowerCase();
                            $(this).toggle(label.indexOf(filter) > -1);
                        });
                    });

                    // Cancella il campo di ricerca e mostra tutte le categorie
                    clearBtn.on('click', function() {
                        searchField.val('').trigger('input');
                        $('#categorychecklist li').show();
                    });
                }

                // ===== 2. FILTRO NEL MENU A TENDINA CATEGORIA GENITORE =====
                // Controlla se esiste il menu a tendina per la categoria genitore
                if ($('#parent').length) {
                    // Crea un wrapper per il campo di ricerca
                    var parentWrap = $('<div>', {
                        css: {
                            position: 'relative',
                            marginBottom: '8px'
                        }
                    });

                    // Crea il campo di input per filtrare le categorie genitore
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

                    // Crea il pulsante per cancellare il testo del campo di ricerca
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

                    // Aggiunge il campo di ricerca e il pulsante cancella nel wrapper
                    parentWrap.append(parentSearch).append(clearParent);
                    // Inserisce il wrapper sopra il menu a tendina della categoria genitore
                    $('#parent').before(parentWrap);

                    // Mostra o nasconde il pulsante cancella in base al contenuto del campo di ricerca
                    parentSearch.on('input', function() {
                        clearParent.toggle($(this).val().length > 0);
                        var filter = $(this).val().toLowerCase();

                        // Filtra le opzioni del menu a tendina
                        $('#parent option').each(function() {
                            var text = $(this).text().toLowerCase();
                            if (filter === '' || text.indexOf(filter) > -1) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    });

                    // Cancella il campo di ricerca e mostra tutte le opzioni
                    clearParent.on('click', function() {
                        parentSearch.val('').trigger('input');
                        $('#parent option').show();
                    });
                }
            });
        ");
    }
});
