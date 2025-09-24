/**
 * JavaScript admin para La Musa Core
 */

(function($) {
    'use strict';

    const LaMusaAdmin = {
        
        init: function() {
            this.initMenuManagement();
            this.initAllergenManagement();
            this.initDateValidation();
            this.initQuickActions();
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('change', '[data-name="restaurant"] select', this.handleRestaurantChange);
            $(document).on('change', '[data-name="start_date"] input, [data-name="end_date"] input', this.handleDateChange);
            $(document).on('click', '.lamusa-quick-action', this.handleQuickAction);
            $(document).on('submit', '#edittag', this.handleAllergenSubmit);
        },

        initMenuManagement: function() {
            // Auto-guardar drafts cada 30 segundos
            if ($('body').hasClass('post-type-weekly_menu')) {
                this.initAutosave();
            }
            
            // Previsualización del menú
            this.initMenuPreview();
            
            // Validación de fechas en tiempo real
            this.initDateValidation();
        },

        initAutosave: function() {
            let lastSaved = Date.now();
            
            setInterval(() => {
                if (Date.now() - lastSaved > 30000) { // 30 segundos
                    this.autosaveMenu();
                    lastSaved = Date.now();
                }
            }, 5000); // Verificar cada 5 segundos
        },

        autosaveMenu: function() {
            const $form = $('#post');
            if (!$form.length || !this.hasUnsavedChanges()) {
                return;
            }
            
            const formData = new FormData($form[0]);
            formData.append('action', 'lamusa_autosave_menu');
            formData.append('nonce', lamusaAdmin.nonce);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('.lamusa-autosave-status').text('Guardado automáticamente a las ' + new Date().toLocaleTimeString());
                    }
                }
            });
        },

        hasUnsavedChanges: function() {
            // Verificar si hay cambios sin guardar
            return $('input, textarea, select').filter(function() {
                return $(this).data('original-value') !== $(this).val();
            }).length > 0;
        },

        initMenuPreview: function() {
            const $previewBtn = $('<button type="button" class="button lamusa-preview-menu">Previsualizar Menú</button>');
            
            $('#publishing-action').before($previewBtn);
            
            $previewBtn.on('click', this.showMenuPreview);
        },

        showMenuPreview: function(e) {
            e.preventDefault();
            
            const menuData = LaMusaAdmin.collectMenuData();
            
            // Crear modal de previsualización
            const $modal = $('<div class="lamusa-preview-modal">' +
                '<div class="lamusa-preview-content">' +
                '<div class="lamusa-preview-header">' +
                '<h2>Previsualización del Menú</h2>' +
                '<button class="lamusa-preview-close">&times;</button>' +
                '</div>' +
                '<div class="lamusa-preview-body"></div>' +
                '</div>' +
                '</div>');
            
            $('body').append($modal);
            
            // Generar HTML de previsualización
            LaMusaAdmin.generateMenuPreviewHTML(menuData, $modal.find('.lamusa-preview-body'));
            
            // Eventos del modal
            $modal.on('click', '.lamusa-preview-close, .lamusa-preview-modal', function(e) {
                if (e.target === this) {
                    $modal.remove();
                }
            });
        },

        collectMenuData: function() {
            return {
                title: $('#title').val(),
                restaurant: $('[data-name="restaurant"] select').val(),
                startDate: $('[data-name="start_date"] input').val(),
                endDate: $('[data-name="end_date"] input').val(),
                price: $('[data-name="menu_price"] input').val(),
                introduction: $('[data-name="menu_introduction"] textarea').val(),
                days: this.collectMenuDays(),
                allergens: this.collectAllergenData()
            };
        },

        collectMenuDays: function() {
            const days = [];
            
            $('[data-name="menu_days"] .acf-row').each(function() {
                const $row = $(this);
                days.push({
                    name: $row.find('[data-name="day_name"] select').val(),
                    date: $row.find('[data-name="day_date"] input').val(),
                    active: $row.find('[data-name="day_active"] input').is(':checked'),
                    content: $row.find('[data-name="menu_content"] textarea').val(),
                    specialNote: $row.find('[data-name="special_note"] input').val()
                });
            });
            
            return days;
        },

        collectAllergenData: function() {
            const groups = [];
            
            $('[data-name="allergen_groups"] .acf-row').each(function() {
                const $row = $(this);
                const dishes = [];
                
                $row.find('[data-name="dishes"] .acf-row').each(function() {
                    const $dishRow = $(this);
                    dishes.push({
                        name: $dishRow.find('[data-name="dish_name"] input').val(),
                        description: $dishRow.find('[data-name="dish_description"] textarea').val(),
                        allergensContains: $dishRow.find('[data-name="allergens_contains"] input:checked').map(function() {
                            return $(this).next('label').text();
                        }).get(),
                        allergensTraces: $dishRow.find('[data-name="allergens_traces"] input:checked').map(function() {
                            return $(this).next('label').text();
                        }).get()
                    });
                });
                
                groups.push({
                    title: $row.find('[data-name="group_title"] input').val(),
                    order: $row.find('[data-name="group_order"] input').val(),
                    dishes: dishes
                });
            });
            
            return groups;
        },

        generateMenuPreviewHTML: function(data, $container) {
            let html = '<div class="menu-preview">';
            
            if (data.title) {
                html += '<h2 class="menu-title">' + data.title + '</h2>';
            }
            
            if (data.price) {
                html += '<div class="menu-price">' + data.price + '</div>';
            }
            
            if (data.startDate && data.endDate) {
                html += '<div class="menu-dates">Del ' + 
                       this.formatDate(data.startDate) + ' al ' + 
                       this.formatDate(data.endDate) + '</div>';
            }
            
            if (data.introduction) {
                html += '<div class="menu-introduction">' + data.introduction + '</div>';
            }
            
            // Días del menú
            if (data.days && data.days.length > 0) {
                html += '<div class="menu-days">';
                data.days.forEach(day => {
                    if (day.active) {
                        html += '<div class="menu-day-item">';
                        html += '<h3 class="menu-day-title">' + day.name + '</h3>';
                        if (day.date) {
                            html += '<div class="menu-day-date">' + this.formatDate(day.date) + '</div>';
                        }
                        if (day.specialNote) {
                            html += '<div class="menu-special-note">' + day.specialNote + '</div>';
                        } else if (day.content) {
                            html += '<div class="menu-content">' + day.content + '</div>';
                        }
                        html += '</div>';
                    }
                });
                html += '</div>';
            }
            
            html += '</div>';
            
            $container.html(html);
        },

        formatDate: function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES');
        },

        initAllergenManagement: function() {
            if ($('body').hasClass('taxonomy-allergen')) {
                this.initColorPicker();
                this.initIconSelector();
                this.initAllergenPreview();
            }
        },

        initColorPicker: function() {
            $('input[name="allergen_color"]').wpColorPicker({
                change: function(event, ui) {
                    LaMusaAdmin.updateAllergenPreview();
                }
            });
        },

        initIconSelector: function() {
            const $iconField = $('input[name="allergen_icon"]');
            const $iconBtn = $('<button type="button" class="button lamusa-icon-selector">Seleccionar Icono</button>');
            
            $iconField.after($iconBtn);
            
            $iconBtn.on('click', function() {
                LaMusaAdmin.showIconSelector($iconField);
            });
            
            $iconField.on('input', this.updateAllergenPreview);
        },

        showIconSelector: function($field) {
            const icons = [
                'dashicons-warning',
                'dashicons-info',
                'dashicons-dismiss',
                'dashicons-yes',
                'dashicons-no',
                'dashicons-food',
                'dashicons-carrot'
            ];
            
            let html = '<div class="lamusa-icon-selector-modal">';
            html += '<div class="lamusa-icon-selector-content">';
            html += '<h3>Seleccionar Icono</h3>';
            html += '<div class="lamusa-icon-grid">';
            
            icons.forEach(icon => {
                html += '<div class="lamusa-icon-option" data-icon="' + icon + '">';
                html += '<span class="dashicons ' + icon + '"></span>';
                html += '<span class="icon-name">' + icon + '</span>';
                html += '</div>';
            });
            
            html += '</div>';
            html += '<button class="button lamusa-icon-cancel">Cancelar</button>';
            html += '</div></div>';
            
            const $modal = $(html);
            $('body').append($modal);
            
            $modal.on('click', '.lamusa-icon-option', function() {
                const icon = $(this).data('icon');
                $field.val(icon);
                LaMusaAdmin.updateAllergenPreview();
                $modal.remove();
            });
            
            $modal.on('click', '.lamusa-icon-cancel, .lamusa-icon-selector-modal', function(e) {
                if (e.target === this) {
                    $modal.remove();
                }
            });
        },

        updateAllergenPreview: function() {
            const name = $('input[name="tag-name"]').val() || 'Alérgeno';
            const icon = $('input[name="allergen_icon"]').val();
            const color = $('input[name="allergen_color"]').val();
            
            let $preview = $('.lamusa-allergen-preview');
            if (!$preview.length) {
                $preview = $('<div class="lamusa-allergen-preview"><h4>Previsualización:</h4><div class="preview-content"></div></div>');
                $('.form-table').after($preview);
            }
            
            let html = '<span class="allergen-item" style="color: ' + color + ';">';
            if (icon) {
                html += '<i class="dashicons ' + icon + '"></i> ';
            }
            html += name + '</span>';
            
            $preview.find('.preview-content').html(html);
        },

        initDateValidation: function() {
            const $startDate = $('[data-name="start_date"] input');
            const $endDate = $('[data-name="end_date"] input');
            
            $startDate.add($endDate).on('change', this.validateMenuDates);
        },

        validateMenuDates: function() {
            const $startDate = $('[data-name="start_date"] input');
            const $endDate = $('[data-name="end_date"] input');
            const startDate = $startDate.val();
            const endDate = $endDate.val();
            
            if (!startDate || !endDate) {
                return;
            }
            
            // Validar que la fecha de fin sea posterior a la de inicio
            if (new Date(endDate) <= new Date(startDate)) {
                LaMusaAdmin.showValidationError('La fecha de fin debe ser posterior a la fecha de inicio.');
                return;
            }
            
            // Verificar solapamientos con otros menús
            const restaurantId = $('[data-name="restaurant"] select').val();
            const postId = $('#post_ID').val();
            
            if (restaurantId) {
                LaMusaAdmin.checkDateOverlap(restaurantId, startDate, endDate, postId);
            }
        },

        checkDateOverlap: function(restaurantId, startDate, endDate, postId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_check_date_overlap',
                    restaurant_id: restaurantId,
                    start_date: startDate,
                    end_date: endDate,
                    post_id: postId || 0,
                    nonce: lamusaAdmin.nonce
                },
                success: function(response) {
                    if (response.success && response.data.conflict) {
                        const conflict = response.data.conflict;
                        LaMusaAdmin.showValidationWarning(
                            'Las fechas se solapan con el menú "' + conflict.menu_title + 
                            '" (' + conflict.start_date + ' - ' + conflict.end_date + ')'
                        );
                    } else {
                        LaMusaAdmin.hideValidationMessages();
                    }
                }
            });
        },

        showValidationError: function(message) {
            this.showValidationMessage(message, 'error');
        },

        showValidationWarning: function(message) {
            this.showValidationMessage(message, 'warning');
        },

        showValidationMessage: function(message, type) {
            let $notice = $('.lamusa-validation-notice');
            
            if (!$notice.length) {
                $notice = $('<div class="notice lamusa-validation-notice"><p></p></div>');
                $('#post').prepend($notice);
            }
            
            $notice.removeClass('notice-error notice-warning notice-success')
                   .addClass('notice-' + type)
                   .find('p').text(message);
        },

        hideValidationMessages: function() {
            $('.lamusa-validation-notice').remove();
        },

        handleRestaurantChange: function() {
            const restaurantId = $(this).val();
            
            if (restaurantId) {
                // Pre-rellenar algunos campos basados en el restaurante
                LaMusaAdmin.loadRestaurantDefaults(restaurantId);
            }
        },

        loadRestaurantDefaults: function(restaurantId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_get_restaurant_defaults',
                    restaurant_id: restaurantId,
                    nonce: lamusaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Pre-rellenar título si está vacío
                        const $title = $('#title');
                        if (!$title.val() && data.name) {
                            $title.val('Menú Semanal - ' + data.name);
                        }
                    }
                }
            });
        },

        handleDateChange: function() {
            // Debounce la validación de fechas
            clearTimeout(LaMusaAdmin.dateValidationTimeout);
            LaMusaAdmin.dateValidationTimeout = setTimeout(function() {
                LaMusaAdmin.validateMenuDates();
            }, 500);
        },

        initQuickActions: function() {
            // Añadir botones de acción rápida
            if ($('body').hasClass('edit-php') && $('.post-type-weekly_menu').length) {
                this.addBulkActions();
            }
        },

        addBulkActions: function() {
            const $bulkActions = $('#bulk-action-selector-top');
            
            $bulkActions.append('<option value="activate_menus">Activar Menús</option>');
            $bulkActions.append('<option value="deactivate_menus">Desactivar Menús</option>');
            
            // Manejar acciones bulk personalizadas
            $(document).on('click', '#doaction, #doaction2', function(e) {
                const action = $(this).siblings('select').val();
                
                if (action === 'activate_menus' || action === 'deactivate_menus') {
                    e.preventDefault();
                    LaMusaAdmin.handleBulkMenuAction(action);
                }
            });
        },

        handleBulkMenuAction: function(action) {
            const selectedPosts = [];
            $('input[name="post[]"]:checked').each(function() {
                selectedPosts.push($(this).val());
            });
            
            if (selectedPosts.length === 0) {
                alert('Por favor selecciona al menos un menú.');
                return;
            }
            
            const activate = action === 'activate_menus';
            const confirmMessage = activate ? 
                '¿Activar los menús seleccionados?' : 
                '¿Desactivar los menús seleccionados?';
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_bulk_menu_action',
                    posts: selectedPosts,
                    bulk_action: action,
                    nonce: lamusaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        },

        handleQuickAction: function(e) {
            e.preventDefault();
            
            const action = $(this).data('action');
            const postId = $(this).data('post-id');
            
            switch (action) {
                case 'duplicate_menu':
                    LaMusaAdmin.duplicateMenu(postId);
                    break;
                case 'toggle_menu_status':
                    LaMusaAdmin.toggleMenuStatus(postId);
                    break;
            }
        },

        duplicateMenu: function(postId) {
            if (!confirm('¿Duplicar este menú?')) {
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_duplicate_menu',
                    post_id: postId,
                    nonce: lamusaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.data.edit_url;
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        },

        toggleMenuStatus: function(postId) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'lamusa_toggle_menu_status',
                    post_id: postId,
                    nonce: lamusaAdmin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        },

        handleAllergenSubmit: function(e) {
            // Validar datos del alérgeno antes de enviar
            const name = $('input[name="tag-name"]').val();
            const icon = $('input[name="allergen_icon"]').val();
            
            if (!name.trim()) {
                alert('El nombre del alérgeno es obligatorio.');
                e.preventDefault();
                return false;
            }
            
            if (icon && !icon.startsWith('dashicons-') && !icon.startsWith('fa-')) {
                alert('El icono debe tener un formato válido (dashicons- o fa-).');
                e.preventDefault();
                return false;
            }
            
            return true;
        }
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        LaMusaAdmin.init();
        
        // Almacenar valores originales para detectar cambios
        $('input, textarea, select').each(function() {
            $(this).data('original-value', $(this).val());
        });
    });

    // Exponer objeto global
    window.LaMusaAdmin = LaMusaAdmin;

})(jQuery);
