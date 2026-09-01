<style type="text/css">
    .tabs.workpost{margin-top: 0; margin-bottom: 0;}
    .tab-pane {
        display: none;
    }
    .tab-pane.active {
        display: block;
    }
    .tabs img{max-width: 100%!important;max-height: initial;}

    .load-more-wrapper {
        text-align: center;
        padding: 40px 0;
    }
    .load-more-button {
        background: #333;
        color: #fff;
        padding: 12px 24px;
        text-decoration: none;
        display: inline-block;
        font-weight: bold;
    }
    .load-more-button.loading {
        background: #999;
        pointer-events: none;
        content: 'Loading...'; 
    }
</style>

<section <?php post_class('container-fluid'); ?> id="post-<?php the_ID(); ?>">
    <div class="tabs workpost" >
        <div class="tab-content">
            <div id="all" class="tab-pane active">
                <?php echo get_all_posts(); ?>
            </div>
            <div id="design" class="tab-pane">
                <?php echo get_posts_by_category('design'); ?>
            </div>
            <div id="website" class="tab-pane">
                <?php echo get_posts_by_category('website'); ?>
            </div>
            <div id="ecommerce" class="tab-pane">
                <?php echo get_posts_by_category('ecommerce'); ?>
            </div>
            <div id="growth" class="tab-pane">
                <?php echo get_posts_by_category('growth'); ?>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
(function($) {
    
    // ✅ VALIDADOR: Set de IDs de posts mostrados por tab
    var loadedPostsPerTab = {};
    
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.tab-nav a');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabLinks.forEach((link, index) => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                tabLinks.forEach(link => link.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                this.classList.add('active');
                const targetPane = document.querySelector(this.getAttribute('href'));
                targetPane.classList.add('active');
                
                // IMPORTANTE: Resetear el contador de página al cambiar de tab
                const container = targetPane.querySelector('.work-grid-container');
                if (container) {
                    container.dataset.page = '0';
                    
                    // Inicializar Set de posts para este tab si no existe
                    const category = container.dataset.category;
                    if (!loadedPostsPerTab[category]) {
                        loadedPostsPerTab[category] = new Set();
                        // Cargar IDs iniciales del grid actual
                        const grid = container.querySelector('.work-grid');
                        grid.querySelectorAll('a.ajax-call').forEach(link => {
                            const postElement = link.closest('article');
                            if (postElement) {
                                // Extraer ID del href o usar data-post-id
                                const postId = link.getAttribute('data-post-id') || extractPostIdFromHref(link.getAttribute('href'));
                                if (postId) {
                                    loadedPostsPerTab[category].add(postId);
                                }
                            }
                        });
                    }
                    
                    console.log('Tab:', category, '| Posts cargados:', loadedPostsPerTab[category].size);
                }
            });
        });
        
        // Inicializar el primer tab (all)
        const firstContainer = document.querySelector('.work-grid-container');
        if (firstContainer) {
            const category = firstContainer.dataset.category;
            if (!loadedPostsPerTab[category]) {
                loadedPostsPerTab[category] = new Set();
                const grid = firstContainer.querySelector('.work-grid');
                grid.querySelectorAll('a.ajax-call').forEach(link => {
                    const postId = link.getAttribute('data-post-id') || extractPostIdFromHref(link.getAttribute('href'));
                    if (postId) {
                        loadedPostsPerTab[category].add(postId);
                    }
                });
            }
            console.log('Init - Tab:', category, '| Posts cargados:', loadedPostsPerTab[category].size);
        }
    });

    // ✅ Helper: Extrae ID del post desde la URL
    function extractPostIdFromHref(href) {
        const match = href.match(/\/(\d+)\/?$/);
        return match ? match[1] : null;
    }

    $(document).ready(function() {
        
        $('.tab-content').on('click', '.load-more-button', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $container = $button.closest('.work-grid-container'); 
            var $grid = $container.find('.work-grid');
            var category = $container.data('category');
            
            // Obtener la página actual del data-page
            var currentPage = parseInt($container.data('page')) || 0;
            var nextPage = currentPage + 1;
			
			console.log('Página actual:', currentPage);
			console.log('Página a cargar:', nextPage);
			console.log('Categoría:', category);
			console.log('Posts en DOM (antes):', $grid.find('a.ajax-call').length);

            $button.addClass('loading').text('Loading...'); 

            $.ajax({
                url: my_ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_posts',
                    nonce: my_ajax_object.nonce,
                    page: nextPage,
                    category: category
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        
                        // ✅ VALIDADOR: Filtrar posts duplicados antes de agregar
                        var filteredHtml = filterDuplicatePosts(response.data.html, category);
                        
                        if (filteredHtml.trim() !== '') {
                            $grid.append(filteredHtml);
                            console.log('Posts agregados después de validación');
                        } else {
                            console.log('⚠️ No se agregaron posts (todos eran duplicados)');
                        }

                        // Actualiza el contador para la próxima carga
                        $container.data('page', nextPage);

                        if (response.data.more_posts === false) {
                            $button.remove();
                        } else {
                            $button.removeClass('loading').text('Load More');
                        }

                        if (typeof lozad === 'function') {
                            lozad().observe();
                        }
                        
                        console.log('Posts en DOM (después):', $grid.find('a.ajax-call').length);
                        console.log('Posts únicos en Set:', loadedPostsPerTab[category].size);

                    } else {
                        $button.remove();
                    }
                },
                error: function() {
                    console.log('Error al cargar los posts.');
                    $button.removeClass('loading').text('Error. Intenta de nuevo.');
                }
            });
        });
    });

    // ✅ VALIDADOR: Filtra posts duplicados del HTML recibido
    function filterDuplicatePosts(html, category) {
        var $temp = $('<div>').html(html);
        var $links = $temp.find('a.ajax-call');
        var newHtml = '';
        var addedCount = 0;
        
        // Asegurar que el Set existe para esta categoría
        if (!loadedPostsPerTab[category]) {
            loadedPostsPerTab[category] = new Set();
        }
        
        $links.each(function() {
            var $link = $(this);
            var postId = $link.attr('data-post-id') || extractPostIdFromHref($link.attr('href'));
            
            if (postId) {
                // Si el post ya existe, no lo incluyas
                if (loadedPostsPerTab[category].has(postId)) {
                    console.log('⛔ Post duplicado detectado (ID: ' + postId + ')');
                    return; // Skip this iteration
                }
                
                // Agregar post al Set y al HTML
                loadedPostsPerTab[category].add(postId);
                newHtml += $link.prop('outerHTML');
                addedCount++;
            }
        });
        
        console.log('Posts procesados: ' + $links.length + ' | Posts únicos: ' + addedCount);
        return newHtml;
    }

})(jQuery);
</script>