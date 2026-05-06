## WooCommerce AJAX Product Filter

**Shortcode:** `[wc_product_filter]`

### Core Features
*   **AJAX Filtering**: Update product grids instantly without page reloads for a seamless user experience.
*   **Category & Attributes**: Automatically pulls WooCommerce categories and product attributes (like size or color) into the sidebar.
*   **Elementor Loop Support**: Specifically designed to render products using Elementor's Loop Builder templates.
*   **Pagination & Sorting**: Built-in AJAX-based pagination and sorting options for Name (A-Z/Z-A).

---

### Important: Template Configuration
To display your products correctly, you must link the plugin to your specific **Elementor Loop Template ID**:

1.  Open the file `wc-ajax-product-filter.php`.
2.  Go to **Line 764** (inside the `wc_get_filtered_products` function).
3.  Locate this line:
    `$output .= $elementor->frontend->get_builder_content_for_display(176, true);`
4.  Replace **`176`** with your own Elementor Template ID. 
