/**
 * JavaScript frontend para La Musa Core
 */

(function($) {
    'use strict';

    // Objeto principal del plugin
    const LaMusaCore = {
        
        init: function() {
            this.initWidgets();
            this.bindEvents();
            this.initAccessibility();
        },

        initWidgets: function() {
            this.initRestaurantInfo();
            this.initWeeklyMenu();
            this.initAllergenInfo();
            this.initRestaurantLinks();
        },

        bindEvents: function() {
            $(document).on('click', '.allergen-item', this.handleAllergenClick);
            $(document).on('click', '.menu-day-title', this.handleMenuDayToggle);
            $(window).on('resize', this.handleResize.bind(this));
        },

        initAccessibility: function() {
            // Añadir soporte para navegación por teclado
            $('.restaurant-link, .allergen-item').attr('tabindex', '0');
            
            // Eventos de teclado
            $(document).on('keydown', '.restaurant-link, .allergen-item', function(e) {
                if (e.which === 13 || e.which === 32) { // Enter o Espacio
                    e.preventDefault();
                    $(this).click();
                }
            });
        },

        initRestaurantInfo: function() {
            $('.restaurant-info').each(function() {
                const $container = $(this);
                const layout = $container.data('layout') || 'vertical';
                
                // Añadir clases CSS dinámicas según configuración
                $container.addClass('restaurant-info-' + layout);
                
                // Efectos de hover en modo tarjeta
                if (layout === 'card') {
                    $container.hover(
                        function() { $(this).addClass('restaurant-info-hover'); },
                        function() { $(this).removeClass('restaurant-info-hover'); }
                    );
                }
            });
        },

        initWeeklyMenu: function() {
            $('.weekly-menu-container').each(function() {
                const $container = $(this);
                
                // Funcionalidad de expansión/colapso para menús largos
                $container.find('.menu-content').each(function() {
                    const $content = $(this);
                    const maxHeight = 200;
                    
                    if ($content.height() > maxHeight) {
                        $content.css({
                            'max-height': maxHeight + 'px',
                            'overflow': 'hidden',
                            'position': 'relative'
                        });
                        
                        const $expandBtn = $('<button class="menu-expand-btn">' + 
                            LaMusaCore.strings.showMore + '</button>');
                        
                        $expandBtn.on('click', function() {
                            if ($content.hasClass('expanded')) {
                                $content.removeClass('expanded').css('max-height', maxHeight + 'px');
                                $(this).text(LaMusaCore.strings.showMore);
                            } else {
                                $content.addClass('expanded').css('max-height', 'none');
                                $(this).text(LaMusaCore.strings.showLess);
                            }
                        });
                        
                        $content.after($expandBtn);
                    }
                });
                
                // Destacar el día actual
                this.highlightCurrentDay($container);
            });
        },

        highlightCurrentDay: function($container) {
            const today = new Date();
            const dayNames = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
            const currentDay = dayNames[today.getDay()];
            
            $container.find('.menu-day-item').each(function() {
                const $dayItem = $(this);
                const dayTitle = $dayItem.find('.menu-day-title').text().toLowerCase();
                
                if (dayTitle.includes(currentDay)) {
                    $dayItem.addClass('menu-current-day');
                }
            });
        },

        initAllergenInfo: function() {
            $('.allergen-info-container').each(function() {
                const $container = $(this);
                
                // Tooltip para alérgenos
                $container.find('.allergen-item').each(function() {
                    const $item = $(this);
                    const allergenName = $item.text().trim();
                    
                    // Buscar descripción si existe
                    const description = $item.data('description');
                    if (description) {
                        $item.attr('title', description);
                    }
                });
                
                // Filtrado de alérgenos
                this.initAllergenFilter($container);
            });
        },

        initAllergenFilter: function($container) {
            // Añadir buscador si hay muchos alérgenos
            const $allergenItems = $container.find('.allergen-item');
            
            if ($allergenItems.length > 10) {
                const $searchBox = $('<div class="allergen-search">' +
                    '<input type="text" placeholder="' + LaMusaCore.strings.searchAllergens + '" class="allergen-search-input">' +
                    '</div>');
                
                $container.prepend($searchBox);
                
                $searchBox.find('input').on('input', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    
                    $allergenItems.each(function() {
                        const $item = $(this);
                        const allergenText = $item.text().toLowerCase();
                        
                        if (allergenText.includes(searchTerm)) {
                            $item.show();
                        } else {
                            $item.hide();
                        }
                    });
                });
            }
        },

        initRestaurantLinks: function() {
            $('.restaurant-links').each(function() {
                const $container = $(this);
                
                // Efectos de hover para botones
                $container.find('.restaurant-link-button').hover(
                    function() { $(this).addClass('restaurant-link-hover'); },
                    function() { $(this).removeClass('restaurant-link-hover'); }
                );
                
                // Track clicks para analytics (si está disponible)
                $container.find('.restaurant-link').on('click', function() {
                    const linkText = $(this).text().trim();
                    const href = $(this).attr('href');
                    
                    // Enviar evento a Google Analytics si está disponible
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'click', {
                            'event_category': 'Restaurant Link',
                            'event_label': linkText,
                            'value': href
                        });
                    }
                });
            });
        },

        handleAllergenClick: function(e) {
            const $item = $(this);
            
            // Toggle selección visual
            $item.toggleClass('allergen-selected');
            
            // Emitir evento personalizado
            $(document).trigger('lamusaAllergenToggle', {
                allergen: $item.text().trim(),
                selected: $item.hasClass('allergen-selected')
            });
        },

        handleMenuDayToggle: function(e) {
            const $title = $(this);
            const $dayItem = $title.closest('.menu-day-item');
            const $content = $dayItem.find('.menu-content');
            
            if ($content.length && $dayItem.hasClass('collapsible')) {
                e.preventDefault();
                
                $content.slideToggle();
                $dayItem.toggleClass('expanded');
                
                // Cambiar icono si existe
                const $icon = $title.find('.toggle-icon');
                if ($icon.length) {
                    $icon.toggleClass('rotated');
                }
            }
        },

        handleResize: function() {
            // Recalcular layouts responsivos
            this.adjustResponsiveLayouts();
        },

        adjustResponsiveLayouts: function() {
            const windowWidth = $(window).width();
            
            // Ajustar grid de alérgenos en móvil
            $('.allergen-grid').each(function() {
                const $grid = $(this);
                const originalColumns = $grid.data('columns') || 2;
                
                if (windowWidth < 768) {
                    $grid.removeClass('allergen-grid-' + originalColumns)
                          .addClass('allergen-grid-mobile');
                } else {
                    $grid.removeClass('allergen-grid-mobile')
                          .addClass('allergen-grid-' + originalColumns);
                }
            });
            
            // Ajustar enlaces de restaurante
            $('.restaurant-links-grid').each(function() {
                const $links = $(this);
                const originalColumns = $links.data('columns') || 2;
                
                if (windowWidth < 768) {
                    $links.removeClass('restaurant-links-grid-' + originalColumns)
                          .addClass('restaurant-links-grid-1');
                } else {
                    $links.removeClass('restaurant-links-grid-1')
                          .addClass('restaurant-links-grid-' + originalColumns);
                }
            });
        },

        // Utilidades
        utils: {
            debounce: function(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            formatDate: function(dateString, format) {
                const date = new Date(dateString);
                const options = {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                };
                
                if (format === 'long') {
                    options.weekday = 'long';
                    options.month = 'long';
                }
                
                return date.toLocaleDateString('es-ES', options);
            },

            isElementInViewport: function(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );
            }
        }
    };

    // Strings localizadas (serán sobrescritas por wp_localize_script)
    LaMusaCore.strings = {
        showMore: 'Ver más',
        showLess: 'Ver menos',
        searchAllergens: 'Buscar alérgenos...',
        loading: 'Cargando...',
        error: 'Error al cargar los datos'
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        LaMusaCore.init();
    });

    // Exponer objeto global para uso externo
    window.LaMusaCore = LaMusaCore;

})(jQuery);
