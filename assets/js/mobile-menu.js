/**
 * JavaScript para navegación mobile sin recarga
 */

(function($) {
    'use strict';

    const MobileMenuNavigator = {
        
        init: function() {
            this.bindEvents();
            this.handleInitialLoad();
        },
        
        bindEvents: function() {
            // Solo en mobile (< 768px)
            if (window.innerWidth <= 768) {
                this.initMobileNavigation();
            }
            
            // Re-inicializar en resize
            $(window).on('resize', this.handleResize.bind(this));
        },
        
        initMobileNavigation: function() {
            const self = this;
            
            // Interceptar clicks en días para mobile
            $(document).on('click', '.mobile-day-header[data-day-toggle]', function(e) {
                e.preventDefault();
                
                const dayKey = $(this).data('day-toggle');
                self.switchToDay(dayKey);
                
                // Actualizar URL sin recargar
                const newUrl = $(this).attr('href');
                if (history.pushState) {
                    history.pushState(null, null, newUrl);
                }
            });
        },
        
        switchToDay: function(dayKey) {
            // Actualizar estado visual de días
            $('.mobile-day-item').removeClass('active');
            $('.mobile-day-item[data-day="' + dayKey + '"]').addClass('active');
            
            // Mostrar/ocultar contenido
            $('.mobile-day-content').removeClass('active');
            $('.mobile-day-content[data-day-content="' + dayKey + '"]').addClass('active');
            
            // Animar flecha
            $('.mobile-day-arrow').css('transform', 'rotate(0deg)');
            $('.mobile-day-item.active .mobile-day-arrow').css('transform', 'rotate(180deg)');
            
            // Scroll mejorado con delay para evitar bloqueos
            this.scrollToDay(dayKey);
        },
        
        scrollToDay: function(dayKey) {
            const self = this;
            
            // Pequeño delay para asegurar que el DOM se ha actualizado
            setTimeout(function() {
                const $selectedDay = $('.mobile-day-item[data-day="' + dayKey + '"]');
                
                if ($selectedDay.length && window.innerWidth <= 768) {
                    // Detener cualquier animación de scroll en curso
                    $('html, body').stop(true, false);
                    
                    // Calcular posición con offset más conservador
                    const targetOffset = $selectedDay.offset().top - 80;
                    
                    // Scroll suave con callback para evitar conflictos
                    $('html, body').animate({
                        scrollTop: targetOffset
                    }, {
                        duration: 400,
                        easing: 'swing',
                        complete: function() {
                            // Scroll completado sin problemas
                        }
                    });
                }
            }, 100);
        },
        
        handleInitialLoad: function() {
            // Si hay un día activo al cargar la página, hacer scroll a él
            if (window.innerWidth <= 768) {
                const $activeDay = $('.mobile-day-item.active');
                if ($activeDay.length) {
                    const activeDayKey = $activeDay.data('day');
                    if (activeDayKey) {
                        // Delay más largo para carga inicial
                        setTimeout(() => {
                            this.scrollToDay(activeDayKey);
                        }, 500);
                    }
                }
            }
        },
        
        handleResize: function() {
            // Re-inicializar navegación mobile si cambia el tamaño
            if (window.innerWidth <= 768) {
                this.initMobileNavigation();
            }
        }
    };

    // Inicializar cuando el DOM esté listo
    $(document).ready(function() {
        MobileMenuNavigator.init();
    });

    // Exponer para uso externo
    window.MobileMenuNavigator = MobileMenuNavigator;

})(jQuery);
