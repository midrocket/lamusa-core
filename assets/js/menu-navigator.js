/**
 * JavaScript para el Widget Navegador de Menús
 */

(function($) {
    'use strict';

    class MenuNavigator {
        constructor($container) {
            this.$container = $container;
            this.currentRestaurant = $container.data('default-restaurant');
            this.currentDay = null;
            this.menuData = null;
            
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.loadInitialData();
        }
        
        bindEvents() {
            const self = this;
            
            // Navegación de restaurantes
            this.$container.on('click', '.nav-item', function(e) {
                e.preventDefault();
                const restaurantId = $(this).data('restaurant-id');
                if (restaurantId) {
                    self.switchRestaurant(restaurantId);
                }
            });
            
            // Navegación de días
            this.$container.on('click', '.day-item:not(.no-menu)', function(e) {
                e.preventDefault();
                const dayName = $(this).data('day');
                if (dayName) {
                    self.switchDay(dayName);
                }
            });
            
            // Soporte para teclado
            this.$container.on('keydown', '.nav-item, .day-item', function(e) {
                if (e.which === 13 || e.which === 32) { // Enter o Espacio
                    e.preventDefault();
                    $(this).click();
                }
            });
        }
        
        loadInitialData() {
            if (this.currentRestaurant) {
                this.switchRestaurant(this.currentRestaurant);
            }
        }
        
        switchRestaurant(restaurantId) {
            if (restaurantId === this.currentRestaurant && this.menuData) {
                return;
            }
            
            // Actualizar estado visual de navegación
            this.updateRestaurantNavigation(restaurantId);
            
            this.currentRestaurant = restaurantId;
            this.loadRestaurantData(restaurantId);
        }
        
        updateRestaurantNavigation(restaurantId) {
            this.$container.find('.nav-item').removeClass('active').attr('aria-selected', 'false');
            this.$container.find('.nav-item[data-restaurant-id="' + restaurantId + '"]')
                .addClass('active').attr('aria-selected', 'true');
        }
        
        switchDay(dayName) {
            if (dayName === this.currentDay) {
                return;
            }
            
            // Actualizar estado visual de días
            this.updateDayNavigation(dayName);
            
            this.currentDay = dayName;
            this.loadDayContent(dayName);
        }
        
        updateDayNavigation(dayName) {
            this.$container.find('.day-item').removeClass('active').attr('aria-selected', 'false');
            this.$container.find('.day-item[data-day="' + dayName + '"]')
                .addClass('active').attr('aria-selected', 'true');
        }
        
        loadRestaurantData(restaurantId) {
            const self = this;
            
            this.showLoading();
            
            $.ajax({
                url: lamusaNavigator.ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_get_restaurant_menu_data',
                    restaurant_id: restaurantId,
                    nonce: lamusaNavigator.nonce
                },
                success: function(response) {
                    self.hideLoading();
                    
                    if (response.success) {
                        self.menuData = response.data;
                        self.renderRestaurantData(response.data);
                    } else {
                        self.showError(response.data || lamusaNavigator.strings.error);
                    }
                },
                error: function(xhr, status, error) {
                    self.hideLoading();
                    self.showError(lamusaNavigator.strings.error + ': ' + error);
                }
            });
        }
        
        loadDayContent(dayName) {
            const self = this;
            
            // Mostrar loading solo en el contenido
            this.$container.find('.menu-content-display').addClass('loading');
            
            $.ajax({
                url: lamusaNavigator.ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_get_day_menu_content',
                    restaurant_id: this.currentRestaurant,
                    day_name: dayName,
                    nonce: lamusaNavigator.nonce
                },
                success: function(response) {
                    self.$container.find('.menu-content-display').removeClass('loading');
                    
                    if (response.success) {
                        self.renderDayContent(response.data);
                    } else {
                        self.showError(response.data || lamusaNavigator.strings.error);
                    }
                },
                error: function(xhr, status, error) {
                    self.$container.find('.menu-content-display').removeClass('loading');
                    self.showError(lamusaNavigator.strings.error + ': ' + error);
                }
            });
        }
        
        renderRestaurantData(data) {
            // Renderizar cabecera
            this.renderHeader(data.restaurant, data.menu);
            
            // Renderizar navegación de días
            this.renderDaysNavigation(data.days);
            
            // Cargar contenido del primer día disponible
            if (data.days && data.days.length > 0) {
                const firstActiveDay = data.days.find(day => day.active);
                if (firstActiveDay) {
                    this.switchDay(firstActiveDay.name);
                } else {
                    // Si no hay días activos, mostrar el primero
                    this.switchDay(data.days[0].name);
                }
            }
        }
        
        renderHeader(restaurant, menu) {
            let headerHtml = '<div class="menu-header">';
            
            // Título del restaurante
            headerHtml += '<div class="menu-header-title">';
            headerHtml += '<span class="restaurant-name">' + (restaurant.title || '') + '</span>';
            if (restaurant.title_secondary) {
                headerHtml += ' <span class="restaurant-name-secondary">' + restaurant.title_secondary + '</span>';
            }
            headerHtml += '</div>';
            
            // Subtítulos
            if (restaurant.subtitle) {
                headerHtml += '<div class="menu-header-subtitle">' + restaurant.subtitle + '</div>';
            }
            if (restaurant.subtitle_secondary) {
                headerHtml += '<div class="menu-header-subtitle-secondary">' + restaurant.subtitle_secondary + '</div>';
            }
            
            // Información del menú
            if (menu) {
                headerHtml += '<div class="menu-info-row">';
                
                // Precio
                if (menu.price) {
                    headerHtml += '<div class="menu-price">' + menu.price + '</div>';
                }
                
                // Fechas
                if (menu.dates) {
                    headerHtml += '<div class="menu-dates">' + menu.dates + '</div>';
                }
                
                headerHtml += '</div>';
                
                // Descripción general
                if (menu.introduction) {
                    headerHtml += '<div class="menu-introduction">' + menu.introduction + '</div>';
                }
            } else {
                headerHtml += '<div class="menu-not-available"><p>' + lamusaNavigator.strings.no_menu + '</p></div>';
            }
            
            headerHtml += '</div>';
            
            this.$container.find('.menu-header-section').html(headerHtml);
        }
        
        renderDaysNavigation(days) {
            let daysHtml = '';
            
            days.forEach(day => {
                const dayClass = 'day-item' + (day.active ? ' has-menu' : ' no-menu');
                daysHtml += '<div class="' + dayClass + '" data-day="' + day.name + '" tabindex="0" role="tab">';
                daysHtml += '<span class="day-name">' + day.display_name + '</span>';
                
                if (day.date) {
                    daysHtml += '<span class="day-date">' + day.date + '</span>';
                }
                
                if (!day.active) {
                    daysHtml += '<span class="day-status">' + lamusaNavigator.strings.no_menu + '</span>';
                }
                
                daysHtml += '</div>';
            });
            
            this.$container.find('.menu-nav-days').html(daysHtml);
        }
        
        renderDayContent(content) {
            this.$container.find('.menu-content-display').html(content);
            
            // Trigger evento personalizado
            this.$container.trigger('menuDayLoaded', {
                restaurant: this.currentRestaurant,
                day: this.currentDay
            });
        }
        
        showLoading() {
            this.$container.find('.menu-navigator-content').addClass('loading');
        }
        
        hideLoading() {
            this.$container.find('.menu-navigator-content').removeClass('loading');
        }
        
        showError(message) {
            const errorHtml = '<div class="menu-error">' + message + '</div>';
            this.$container.find('.menu-content-display').html(errorHtml);
        }
        
        // Métodos públicos para control externo
        goToRestaurant(restaurantId) {
            this.switchRestaurant(restaurantId);
        }
        
        goToDay(dayName) {
            this.switchDay(dayName);
        }
        
        refresh() {
            if (this.currentRestaurant) {
                this.loadRestaurantData(this.currentRestaurant);
            }
        }
    }

    // Auto-inicialización
    $(document).ready(function() {
        $('.lamusa-menu-navigator').each(function() {
            const navigator = new MenuNavigator($(this));
            
            // Guardar instancia para acceso externo
            $(this).data('menuNavigator', navigator);
        });
    });

    // Exponer para uso global
    window.LaMusaMenuNavigator = MenuNavigator;

})(jQuery);
