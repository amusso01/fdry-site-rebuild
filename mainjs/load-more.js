jQuery(function ($) {
    const container = $('#work-posts-container');
    
    if (!container.length) {
        return;
    }
    
    const grid = container.find('.work-grid'); 
    const spinner = container.find('.infinite-scroll-spinner'); 

    let currentPage = parseInt(container.attr('data-current-page'));
    let maxPages = parseInt(container.attr('data-max-pages'));
    let isLoading = false;

    const observer = lozad();
    observer.observe();

    function loadMorePosts() {
        if (isLoading || currentPage >= maxPages) {
            return; 
        }

        isLoading = true;
        spinner.show();

        $.ajax({
            url: my_ajax_params.ajax_url, 
            type: 'POST',
            data: {
                action: 'load_more_works', 
                page: currentPage,
            },
            success: function (response) {
                if (response) {
                    container.attr('data-current-page', currentPage + 1);
                    currentPage++;
                    grid.append(response);
                    observer.observe();

                } else {
                    currentPage = maxPages;
                }
                
                spinner.hide();
                isLoading = false;
            },
            error: function () {
                console.error('Error al cargar posts.');
                spinner.hide();
                isLoading = false;
            },
        });
    }

    // El detector de scroll
    $(window).on('scroll', function () {
        if (isLoading || currentPage >= maxPages) {
            return;
        }

        let viewportBottom = $(window).scrollTop() + $(window).height();

        let containerBottom = container.offset().top + container.outerHeight();

        let threshold = 300; 

        if (viewportBottom >= (containerBottom - threshold)) {
            loadMorePosts();
        }
    });
});