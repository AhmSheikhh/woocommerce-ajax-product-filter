<?php
/*
Plugin Name: WooCommerce AJAX Product Filter
Description: Advanced WooCommerce filter with AJAX.
Author: Sheikh Muhammad Ahmad
Version: 1.0
*/

/**
 * WooCommerce Advanced Product Filter with AJAX
 * Shortcode: [wc_product_filter]
 */

// Enqueue scripts and styles
function wc_filter_enqueue_assets() {
    if (!is_admin()) {
        wp_enqueue_script('jquery');
        
        // Inline CSS
        add_action('wp_footer', 'wc_filter_inline_styles', 5);
        
        // Inline JS
        add_action('wp_footer', 'wc_filter_inline_scripts', 10);
    }
}
add_action('wp_enqueue_scripts', 'wc_filter_enqueue_assets');

// Inline Styles
function wc_filter_inline_styles() {
    ?>
    <style>
        .wc-filter-wrapper {
            display: flex;
            gap: 30px;
            margin: 40px 0;
            font-family: 'Manrope', sans-serif;
        }
        
        .wc-filter-sidebar {
            flex: 0 0 250px;
            width: 250px;
        }
        
        .wc-filter-content {
            flex: 1;
            min-width: 0;
        }
        
        .wc-filter-section {
            margin-bottom: 30px;
        }
        
        .wc-filter-section h3 {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            font-size: 18px;
            line-height: 175%;
            letter-spacing: 0%;
            color: #C61E61;
            margin: 0 0 15px 0;
            text-transform: capitalize;
        }
        
        .wc-filter-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .wc-filter-option {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-family: 'Manrope', sans-serif;
            font-weight: 500;
            font-size: 15px;
            line-height: 175%;
            color: #474B57;
            transition: all 0.3s ease;
        }
        
        .wc-filter-option:hover {
            color: #C61E61;
        }
        
        .wc-filter-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #C61E61;
        }
        
        .wc-filter-option label {
            cursor: pointer;
            margin: 0;
            user-select: none;
        }
        
        .wc-applied-filters {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        
        .wc-applied-filters-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 700;
            font-size: 15px;
            color: #474B57;
            margin: 0;
        }
        
        .wc-filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 6px 12px;
            font-family: 'Manrope', sans-serif;
            font-weight: 500;
            font-size: 14px;
            color: #474B57;
        }
        
        .wc-filter-tag-remove {
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            color: #C61E61;
            transition: color 0.2s ease;
        }
        
        .wc-filter-tag-remove:hover {
            color: #a01850;
        }
        
        .wc-results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .wc-results-count {
            font-family: 'Manrope', sans-serif;
            font-weight: 500;
            font-size: 15px;
            color: #474B57;
        }
        
        .wc-sort-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .wc-sort-label {
            font-family: 'Manrope', sans-serif;
            font-weight: 500;
            font-size: 15px;
            color: #474B57;
        }
        
        .wc-sort-select {
            padding: 8px 30px 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-family: 'Manrope', sans-serif;
            font-weight: 500;
            font-size: 14px;
            color: #474B57;
            background: white;
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='%23474B57' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        
        .wc-products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .wc-products-grid .elementor-loop-container {
            display: contents;
        }
        
        .wc-filter-loading {
            position: relative;
            opacity: 0.5;
            pointer-events: none;
        }
        
        .wc-filter-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #C61E61;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .wc-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
            font-family: 'Manrope', sans-serif;
        }
        
        .wc-pagination a,
        .wc-pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-weight: 500;
            font-size: 14px;
            color: #474B57;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .wc-pagination a:hover {
            background: #C61E61;
            border-color: #C61E61;
            color: white;
        }
        
        .wc-pagination span.current {
            background: #C61E61;
            border-color: #C61E61;
            color: white;
        }
        
        .wc-no-products {
            text-align: center;
            padding: 60px 20px;
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
            color: #474B57;
        }
        
        /* Tablet Styles */
        @media (max-width: 1024px) {
            .wc-filter-section h3 {
                font-size: 14px;
            }
            
            .wc-filter-option {
                font-size: 14px;
            }
            
            .wc-applied-filters-title {
                font-size: 14px;
            }
            
            .wc-filter-tag {
                font-size: 13px;
            }
            
            .wc-results-count,
            .wc-sort-label {
                font-size: 14px;
            }
            
            .wc-sort-select {
                font-size: 13px;
            }
            
            .wc-products-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
            
            .wc-pagination a,
            .wc-pagination span {
                font-size: 13px;
            }
        }
        
        /* Mobile Styles */
        @media (max-width: 768px) {
            .wc-filter-wrapper {
                flex-direction: column;
                gap: 20px;
            }
            
            .wc-filter-sidebar {
                flex: 1;
                width: 100%;
            }
            
            .wc-filter-section h3 {
                font-size: 13px;
            }
            
            .wc-filter-option {
                font-size: 13px;
            }
            
            .wc-applied-filters-title {
                font-size: 13px;
            }
            
            .wc-filter-tag {
                font-size: 12px;
            }
            
            .wc-results-count,
            .wc-sort-label {
                font-size: 13px;
            }
            
            .wc-sort-select {
                font-size: 12px;
            }
            
            .wc-products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .wc-results-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .wc-pagination a,
            .wc-pagination span {
                min-width: 35px;
                height: 35px;
                font-size: 12px;
            }
        }
    </style>
    <?php
}

// Inline JavaScript
function wc_filter_inline_scripts() {
    ?>
    <script>
    jQuery(document).ready(function($) {
		
		// Handle specialty card clicks
$(document).on('click', '.wc-specialty-card', function(e) {
    e.preventDefault();
    var categoryId = $(this).data('category-id');
    
    // Uncheck all category filters first
    $('.wc-filter-option input[data-type="category"]').prop('checked', false);
    
    // Check the clicked specialty category
    $('input[data-type="category"][value="' + categoryId + '"]').prop('checked', true);
    
    // Trigger filter
    loadFilteredProducts(1);
    
    // Scroll to products
    $('html, body').animate({
        scrollTop: $('.wc-filter-content').offset().top - 100
    }, 500);
}); 

        // Handle filter changes
        $('.wc-filter-option input[type="checkbox"], .wc-sort-select').on('change', function() {
            loadFilteredProducts(1);
        });
        
        // Handle applied filter removal
        $(document).on('click', '.wc-filter-tag-remove', function() {
            var filterType = $(this).data('type');
            var filterValue = $(this).data('value');
            
            $('input[type="checkbox"][data-type="' + filterType + '"][value="' + filterValue + '"]').prop('checked', false);
            loadFilteredProducts(1);
        });
        
        // Handle pagination
        $(document).on('click', '.wc-pagination a', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            loadFilteredProducts(page);
            
            // Scroll to top of products
            $('html, body').animate({
                scrollTop: $('.wc-filter-content').offset().top - 100
            }, 500);
        });
        
        function loadFilteredProducts(page) {
            var categories = [];
            var attributes = [];
            var sortBy = $('.wc-sort-select').val();
            
            // Get selected categories
            $('.wc-filter-option input[data-type="category"]:checked').each(function() {
                categories.push($(this).val());
            });
            
            // Get selected attributes
            $('.wc-filter-option input[data-type="attribute"]:checked').each(function() {
                attributes.push($(this).val());
            });
            
            // Show loading state
            $('.wc-filter-content').addClass('wc-filter-loading');
            
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'wc_filter_products',
                    categories: categories,
                    attributes: attributes,
                    sort_by: sortBy,
                    page: page
                },
                success: function(response) {
                    if (response.success) {
                        $('.wc-applied-filters').html(response.data.applied_filters);
                        $('.wc-results-count').html(response.data.results_count);
                        $('.wc-products-grid').html(response.data.products);
                        $('.wc-pagination').html(response.data.pagination);
                    }
                    $('.wc-filter-content').removeClass('wc-filter-loading');
                },
                error: function() {
                    $('.wc-filter-content').removeClass('wc-filter-loading');
                }
            });
        }
    });
    </script>
    <?php
}

// Main shortcode function
function wc_product_filter_shortcode() {
    ob_start();
    
    // Get all product categories
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
    ));
    
    // Get all product attributes with pa_ prefix (WooCommerce attributes)
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    $attributes_data = array();
    
    foreach ($attribute_taxonomies as $tax) {
        $attribute_name = wc_attribute_taxonomy_name($tax->attribute_name);
        $terms = get_terms(array(
            'taxonomy' => $attribute_name,
            'hide_empty' => true,
        ));
        
        if (!empty($terms) && !is_wp_error($terms)) {
            $attributes_data[] = array(
                'label' => $tax->attribute_label,
                'name' => $attribute_name,
                'terms' => $terms
            );
        }
    }
    
    ?>
    <div class="wc-filter-wrapper">
        <!-- Sidebar Filters -->
        <div class="wc-filter-sidebar">
            <!-- Categories Section -->
            <?php if (!empty($categories) && !is_wp_error($categories)) : ?>
            <div class="wc-filter-section">
                <h3>Categories</h3>
                <div class="wc-filter-options">
                    <?php foreach ($categories as $category) : ?>
                    <div class="wc-filter-option">
                        <input type="checkbox" 
                               id="cat-<?php echo esc_attr($category->term_id); ?>" 
                               value="<?php echo esc_attr($category->term_id); ?>"
                               data-type="category"
                               data-name="<?php echo esc_attr($category->name); ?>">
                        <label for="cat-<?php echo esc_attr($category->term_id); ?>">
                            <?php echo esc_html($category->name); ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Attributes Sections -->
            <?php foreach ($attributes_data as $attribute) : ?>
            <div class="wc-filter-section">
                <h3><?php echo esc_html($attribute['label']); ?></h3>
                <div class="wc-filter-options">
                    <?php foreach ($attribute['terms'] as $term) : ?>
                    <div class="wc-filter-option">
                        <input type="checkbox" 
                               id="attr-<?php echo esc_attr($term->term_id); ?>" 
                               value="<?php echo esc_attr($term->term_id); ?>"
                               data-type="attribute"
                               data-taxonomy="<?php echo esc_attr($attribute['name']); ?>"
                               data-name="<?php echo esc_attr($term->name); ?>">
                        <label for="attr-<?php echo esc_attr($term->term_id); ?>">
                            <?php echo esc_html($term->name); ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Main Content Area -->
        <div class="wc-filter-content">
            <!-- Applied Filters -->
            <div class="wc-applied-filters">
                <span class="wc-applied-filters-title">Applied Filters:</span>
            </div>
            
            <!-- Results Header -->
            <div class="wc-results-header">
                <div class="wc-results-count">
                    <?php echo wc_get_products_count_text(); ?>
                </div>
                <div class="wc-sort-wrapper">
                    <span class="wc-sort-label">SORT BY</span>
                    <select class="wc-sort-select">
                        <option value="default">Default</option>
                    <!--    <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option> -->
                        <option value="name_asc">Name: A to Z</option>
                        <option value="name_desc">Name: Z to A</option>
                    </select>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="wc-products-grid">
                <?php echo wc_get_filtered_products(); ?>
            </div>
            
            <!-- Pagination -->
            <div class="wc-pagination">
                <?php echo wc_get_products_pagination(1); ?>
            </div>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('wc_product_filter', 'wc_product_filter_shortcode');

// Get products count text
function wc_get_products_count_text($args = array()) {
    $query_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array('relation' => 'OR'),
    );
    
    if (!empty($args['categories'])) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $args['categories'],
        );
    }
    
    if (!empty($args['attributes'])) {
        foreach ($args['attributes'] as $attr_id) {
            $term = get_term($attr_id);
            if ($term && !is_wp_error($term)) {
                $query_args['tax_query'][] = array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $attr_id,
                );
            }
        }
    }
    
    $query = new WP_Query($query_args);
    $total = $query->found_posts;
    $start = 1;
    $end = min(16, $total);
    
    if (!empty($args['page'])) {
        $page = intval($args['page']);
        $start = (($page - 1) * 16) + 1;
        $end = min($page * 16, $total);
    }
    
    wp_reset_postdata();
    
    return "Showing {$start}-{$end} Of {$total} Results.";
}

// Hook to modify Elementor loop query
add_action('elementor/query/wc_custom_filter', function($query) {
    // Get filter parameters from session/cookie
    $categories = isset($_SESSION['wc_filter_cats']) ? $_SESSION['wc_filter_cats'] : array();
    $attributes = isset($_SESSION['wc_filter_attrs']) ? $_SESSION['wc_filter_attrs'] : array();
    $sort_by = isset($_SESSION['wc_filter_sort']) ? $_SESSION['wc_filter_sort'] : 'default';
    
    if (!empty($categories) || !empty($attributes)) {
        $tax_query = array('relation' => 'OR');
        
        if (!empty($categories)) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $categories,
            );
        }
        
        if (!empty($attributes)) {
            foreach ($attributes as $attr_id) {
                $term = get_term($attr_id);
                if ($term && !is_wp_error($term)) {
                    $tax_query[] = array(
                        'taxonomy' => $term->taxonomy,
                        'field' => 'term_id',
                        'terms' => $attr_id,
                    );
                }
            }
        }
        
        $query->set('tax_query', $tax_query);
    }
    
    // Handle sorting
    switch ($sort_by) {
        case 'price_asc':
            $query->set('meta_key', '_price');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'ASC');
            break;
        case 'price_desc':
            $query->set('meta_key', '_price');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC');
            break;
        case 'name_asc':
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
            break;
        case 'name_desc':
            $query->set('orderby', 'title');
            $query->set('order', 'DESC');
            break;
    }
});

// Get filtered products using Elementor template
function wc_get_filtered_products($categories = array(), $attributes = array(), $sort_by = 'default', $page = 1) {
    // Start session if not started
    if (!session_id()) {
        session_start();
    }
    
    // Store filter parameters in session
    $_SESSION['wc_filter_cats'] = $categories;
    $_SESSION['wc_filter_attrs'] = $attributes;
    $_SESSION['wc_filter_sort'] = $sort_by;
    
    $query_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 16,
        'paged' => $page,
        'tax_query' => array('relation' => 'OR'),
    );
    
    // Add category filter
    if (!empty($categories)) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $categories,
        );
    }
    
    // Add attribute filters
    if (!empty($attributes)) {
        foreach ($attributes as $attr_id) {
            $term = get_term($attr_id);
            if ($term && !is_wp_error($term)) {
                $query_args['tax_query'][] = array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $attr_id,
                );
            }
        }
    }
    
    // Add sorting
    switch ($sort_by) {
        case 'price_asc':
            $query_args['meta_key'] = '_price';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'ASC';
            break;
        case 'price_desc':
            $query_args['meta_key'] = '_price';
            $query_args['orderby'] = 'meta_value_num';
            $query_args['order'] = 'DESC';
            break;
        case 'name_asc':
            $query_args['orderby'] = 'title';
            $query_args['order'] = 'ASC';
            break;
        case 'name_desc':
            $query_args['orderby'] = 'title';
            $query_args['order'] = 'DESC';
            break;
    }
    
    $query = new WP_Query($query_args);
    
    $output = '';
    
   if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        
        // Render using Elementor's proper method
        if (class_exists('\Elementor\Plugin')) {
            $elementor = \Elementor\Plugin::instance();
            $output .= $elementor->frontend->get_builder_content_for_display(176, true);
        }
    }
    wp_reset_postdata();
} else {
        $output = '<div class="wc-no-products">No products found matching your filters.</div>';
    }
    
    return $output;
}

// Get pagination
function wc_get_products_pagination($current_page, $categories = array(), $attributes = array()) {
    $query_args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 16,
        'tax_query' => array('relation' => 'OR'),
    );
    
    if (!empty($categories)) {
        $query_args['tax_query'][] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $categories,
        );
    }
    
    if (!empty($attributes)) {
        foreach ($attributes as $attr_id) {
            $term = get_term($attr_id);
            if ($term && !is_wp_error($term)) {
                $query_args['tax_query'][] = array(
                    'taxonomy' => $term->taxonomy,
                    'field' => 'term_id',
                    'terms' => $attr_id,
                );
            }
        }
    }
    
    $query = new WP_Query($query_args);
    $total_pages = $query->max_num_pages;
    wp_reset_postdata();
    
    if ($total_pages <= 1) {
        return '';
    }
    
    $output = '';
    
    // Previous button
    if ($current_page > 1) {
        $output .= '<a href="#" data-page="' . ($current_page - 1) . '">&laquo;</a>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $output .= '<span class="current">' . $i . '</span>';
        } else {
            $output .= '<a href="#" data-page="' . $i . '">' . $i . '</a>';
        }
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $output .= '<a href="#" data-page="' . ($current_page + 1) . '">&raquo;</a>';
    }
    
    return $output;
}

// AJAX handler for filtering products
function wc_ajax_filter_products() {
    $categories = isset($_POST['categories']) ? array_map('intval', $_POST['categories']) : array();
    $attributes = isset($_POST['attributes']) ? array_map('intval', $_POST['attributes']) : array();
    $sort_by = isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : 'default';
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    
    // Get applied filters HTML
    $applied_filters_html = '<span class="wc-applied-filters-title">Applied Filters:</span>';
    
    if (!empty($categories)) {
        foreach ($categories as $cat_id) {
            $term = get_term($cat_id);
            if ($term && !is_wp_error($term)) {
                $applied_filters_html .= '<span class="wc-filter-tag">';
                $applied_filters_html .= esc_html($term->name);
                $applied_filters_html .= '<span class="wc-filter-tag-remove" data-type="category" data-value="' . esc_attr($cat_id) . '">×</span>';
                $applied_filters_html .= '</span>';
            }
        }
    }
    
    if (!empty($attributes)) {
        foreach ($attributes as $attr_id) {
            $term = get_term($attr_id);
            if ($term && !is_wp_error($term)) {
                $applied_filters_html .= '<span class="wc-filter-tag">';
                $applied_filters_html .= esc_html($term->name);
                $applied_filters_html .= '<span class="wc-filter-tag-remove" data-type="attribute" data-value="' . esc_attr($attr_id) . '">×</span>';
                $applied_filters_html .= '</span>';
            }
        }
    }
    
    // Get products
    $products_html = wc_get_filtered_products($categories, $attributes, $sort_by, $page);
    
    // Get results count
    $results_count = wc_get_products_count_text(array(
        'categories' => $categories,
        'attributes' => $attributes,
        'page' => $page
    ));
    
    // Get pagination
    $pagination_html = wc_get_products_pagination($page, $categories, $attributes);
    
    wp_send_json_success(array(
        'applied_filters' => $applied_filters_html,
        'products' => $products_html,
        'results_count' => $results_count,
        'pagination' => $pagination_html
    ));
}
add_action('wp_ajax_wc_filter_products', 'wc_ajax_filter_products');
add_action('wp_ajax_nopriv_wc_filter_products', 'wc_ajax_filter_products');
