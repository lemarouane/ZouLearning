        $(document).ready(function() {
            // Toggle sidebar
            $('.menu-toggle').click(function() {
                $('.sidebar').toggleClass('active');
            });

            // Auto-close sidebar on link click
            $('.sidebar-nav a').click(function() {
                $('.sidebar').removeClass('active');
            });

            // Toggle sub-menu visibility for collapsible parents
            $('.parent-item').click(function(e) {
                e.stopPropagation();
                const $parent = $(this);
                const $subMenu = $parent.next('.sub-menu');
                const $chevron = $parent.find('.chevron');

                // Toggle sub-menu
                $('.sub-menu').not($subMenu).removeClass('open');
                $subMenu.toggleClass('open');
                // Toggle chevron rotation
                $('.chevron').not($chevron).removeClass('open');
                $chevron.toggleClass('open');
            });

            // Auto-expand sub-menu for active page
            $('.sub-menu a.active').each(function() {
                const $subMenu = $(this).closest('.sub-menu');
                const $chevron = $subMenu.prev('.parent-item').find('.chevron');
                $subMenu.addClass('open');
                $chevron.addClass('open');
            });
        });
 