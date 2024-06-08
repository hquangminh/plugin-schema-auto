<?php
/*
Plugin Name: WooCommerce Category Schema Integration
Description: Generate and insert custom JSON-LD schema for WooCommerce product categories.
Version: 1.0
Author: Quang Minh - Dev
*/

// Remove Yoast and Schema Pro schemas on WooCommerce category pages
add_action('template_redirect', 'devvn_remove_yoast_schema_on_category_page', 20);
function devvn_remove_yoast_schema_on_category_page()
{
    if (is_product_category()) {
        add_filter('wpseo_json_ld_output', '__return_false', 20); // Disable Yoast schema output
        remove_all_actions('wp_head', 10); // Disable Schema Pro actions
    }
}

// Hook to add custom schema on WooCommerce category pages
add_action('wp_head', 'devvn_add_custom_category_schema', 5);

function devvn_add_custom_category_schema() {
    if (is_product_category()) {
        global $wp_query;

        // Get current category
        $category = $wp_query->get_queried_object();
        $category_id = $category->term_id;
        $category_name = $category->name;
        $category_url = get_term_link($category);
        $category_description = get_category_meta_description();

        // Fetch products in the current category
        $products = get_products_in_category($category_id);

        // Create the new schema
        $custom_schema = [
            "@context" => "https://schema.org/",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => "https://viettienplastic.vn/#organization",
                    "url" => "https://viettienplastic.vn/",
                    "name" => ["Nhựa Việt Tiến", "Việt Tiến Plastic", "Công ty nhựa Việt Tiến"]
                ],
                [
                    "@type" => "Person",
                    "@id" => "https://viettienplastic.vn/author/viettien#person",
                    "url" => "https://viettienplastic.vn/author/viettien",
                    "name" => "BÙI THANH DUY"
                ],
                [
                    "@type" => "WebSite",
                    "name" => ["Nhựa Việt Tiến", "Việt Tiến Plastic", "Công ty nhựa Việt Tiến"],
                    "inLanguage" => "vi-VN",
                    "description" => "Nhựa Việt Tiến chuyên sản xuất và phân phối sản phẩm thùng nhựa công nghiệp, Khay nhựa, thùng rác nhựa mẫu mã đa dạng, đảm bảo chất lượng, giá cạnh tranh",
                    "@id" => "https://viettienplastic.vn/#website",
                    "url" => "https://viettienplastic.vn/",
                    "image" => "https://viettienplastic.vn/wp-content/uploads/2019/03/viettienplastic-logo.png",
                    "copyrightHolder" => [
                        "@id" => "https://viettienplastic.vn/#organization"
                    ],
                    "author" => [
                        "@id" => "https://viettienplastic.vn/author/viettien#person"
                    ],
                    "publisher" => [
                        "@id" => "https://viettienplastic.vn/#organization"
                    ]
                ],
                [
                    "@type" => "CollectionPage",
                    "@id" => $category_url . "#collectionpage",
                    "url" => $category_url,
                    "name" => get_image_alt_text($category_id),
                    "isPartOf" => [
                        "@id" => "https://viettienplastic.vn/#website"
                    ],
                    "inLanguage" => "vi-VN",
                    "primaryImageOfPage" => [
                        "@id" => $category_url . "#primaryimage"
                    ],
                    "description" => $category_description,
                    "thumbnailUrl" => get_thumbnail_url($category_id)
                ],
                [
                    "@type" => "ImageObject",
                    "keywords" => get_category_keywords($category_id),
                    "about" => [
                        "@id" => $category_url . "#thing"
                    ],
                    "name" => get_image_alt_text($category_id),
                    "contentUrl" => get_first_image_url($category_id),
                    "url" => get_first_image_url($category_id),
                    "@id" => $category_url . "#primaryimage",
                    "representativeOfPage" => "True",
                    "width" => 1200,
                    "height" => 628,
                    "encodingFormat" => ".webp",
                    "uploadDate" => get_image_upload_date($category_id),
                    "alternativeheadline" => get_image_alt_text($category_id),
                    "description" => $category_description,
                    "author" => [
                        "@id" => "https://viettienplastic.vn/author/viettien#person"
                    ],
                    "creator" => [
                        "@id" => "https://viettienplastic.vn/author/viettien#person"
                    ],
                    "producer" => [
                        "@id" => "https://viettienplastic.vn/"
                    ],
                    "copyrightHolder" => [
                        "@id" => "https://viettienplastic.vn/"
                    ]
                ],
                [
                    "@type" => "Product",
                    "name" => $category_name,
                    "alternateName" => get_category_alternate_names($category_id),
                    "url" => $category_url,
                    "@id" => $category_url . "#category",
                    "image" => get_all_image_urls_from_description($category_id),
                    "description" => $category_description,
                    "disambiguatingDescription" => get_disambiguating_description($category_id),
                    "brand" => [
                        "@type" => "Brand",
                        "name" => ["Nhựa Việt Tiến", "Việt Tiến Plastic", "Công ty nhựa Việt Tiến"]
                    ],
                    "sku" => generate_sku($category_name),
                    "mpn" => generate_mpn($category_id),
                    "material" => get_category_material($category_id),
                    "hasMerchantReturnPolicy" => [
                        "@type" => "MerchantReturnPolicy",
                        "inStoreReturnsOffered" => "True",
                        "merchantReturnDays" => 10,
                        "merchantReturnLink" => "https://viettienplastic.vn/chinh-sach-doi-tra/"
                    ],
                    "offers" => [
                        "@type" => "AggregateOffer",
                        "url" => $category_url,
                        "priceCurrency" => "VND",
                        "lowPrice" => get_low_price($products),
                        "highPrice" => get_high_price($products),
                        "offerCount" => count($products)
                    ],
                    "aggregateRating" => [
                        "@type" => "AggregateRating",
                        "ratingValue" => 5,
                        "bestRating" => 5,
                        "worstRating" => 4,
                        "ratingCount" => 96,
                        "reviewCount" => 19
                    ],
                    "audience" => [
                        "@type" => "Audience",
                        "audienceType" => "consumer",
                        "url" => "https://en.wikipedia.org/wiki/Consumer",
                        "name" => "Consumer",
                        "geographicArea" => [
                            "@type" => "AdministrativeArea",
                            "url" => "https://vi.wikipedia.org/wiki/Việt_Nam",
                            "@id" => "kg:/m/01crd5",
                            "name" => "Việt Nam"
                        ]
                    ],
                    "isSimilarTo" => get_similar_categories($category_id)
                ],
                get_random_review()
            ]
        ];

        // Output the schema
        echo '<script type="application/ld+json">' . json_encode($custom_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}

// Function to get category meta description
function get_category_meta_description() {
    return get_post_meta(get_queried_object_id(), '_yoast_wpseo_metadesc', true);
}

// Function to get products in a category
function get_products_in_category($category_id) {
    $args = [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category_id,
            ],
        ],
    ];
    $query = new WP_Query($args);
    $products = $query->posts;
    wp_reset_postdata();
    return $products;
}

// Function to get the low price of products
function get_low_price($products) {
    $prices = array_map(function($product) {
        $product_obj = wc_get_product($product->ID);
        return $product_obj ? $product_obj->get_price() : 0;
    }, $products);
    $prices = array_filter($prices, function($price) {
        return $price !== '';
    });
    return empty($prices) ? 0 : min($prices);
}

// Function to get the high price of products
function get_high_price($products) {
    $prices = array_map(function($product) {
        $product_obj = wc_get_product($product->ID);
        return $product_obj ? $product_obj->get_price() : 0;
    }, $products);
    $prices = array_filter($prices, function($price) {
        return $price !== '';
    });
    return empty($prices) ? 0 : max($prices);
}

// Function to get the thumbnail URL of the category
function get_thumbnail_url($category_id) {
    $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
    if ($thumbnail_id) {
        $image = wp_get_attachment_url($thumbnail_id);
        return $image ? $image : '';
    }
    return '';
}

// Function to get category keywords
function get_category_keywords($category_id) {
    return get_term_meta($category_id, 'category_keywords', true) ?: [];
}

// Function to get image alt text
function get_image_alt_text($category_id) {
    $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
    return $thumbnail_id ? get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) : '';
}

// Function to get the first image URL of the category
function get_first_image_url($category_id) {
    $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
    return $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
}

// Function to get image upload date
function get_image_upload_date($category_id) {
    $thumbnail_id = get_term_meta($category_id, 'thumbnail_id', true);
    $post = get_post($thumbnail_id);
    return $post ? $post->post_date : '';
}

// Function to get image description
function get_image_description($category_id) {
    return get_term_meta($category_id, 'image_description', true) ?: '';
}

// Function to get category alternate names
function get_category_alternate_names($category_id) {
    return get_term_meta($category_id, 'category_alternate_names', true) ?: [];
}

// Function to get all image URLs of the category
function get_all_image_urls_from_description($category_id) {
    $description = term_description($category_id);
    preg_match_all('/<img[^>]+src="([^">]+)"/', $description, $matches);
    return $matches[1] ?: [];
}

// Function to get disambiguating description
function get_disambiguating_description($category_id) {
    $content = term_description($category_id);
    $content = strip_tags($content); // Remove HTML tags
    return mb_substr($content, 0, 200) . (mb_strlen($content) > 200 ? '...' : '');
}

// Function to get materials of the category
function get_category_material($category_id) {
    return get_term_meta($category_id, 'category_material', true) ?: [];
}

// Function to get similar categories
function get_similar_categories($category_id) {
    // Get child categories
    $child_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'child_of' => $category_id,
        'hide_empty' => false,
    ));

    $isSimilarTo = [];

    foreach ($child_categories as $child) {
        $child_url = get_term_link($child);
        $child_name = single_cat_title('', false); // Fetch the H1 title
        $isSimilarTo[] = [
            "@type" => "Thing",
            "name" => $child_name,
            "url" => $child_url,
            "@id" => $child_url . "#category"
        ];
    }

    return $isSimilarTo;
}

// Function to generate a SKU
function generate_sku($category_name) {
    $sku = strtoupper(mb_substr($category_name, 0, 2)); // Take first two characters
    return $sku + rand(1000, 9999);
}

// Function to generate a MPN
function generate_mpn($category_id) {
    $product_count = count(get_products_in_category($category_id));
    return $product_count . "TR";
}

// Function to get a random review
function get_random_review() {
    $reviews_data = [
        ["LyLy", "Hàng tốt giá rẻ nên mua", "5"],
        ["Nam", "Chất lượng sản phẩm tốt", "5"],
        ["Trang", "Giao hàng nhanh, đóng gói kỹ", "5"],
        ["Minh", "Sản phẩm đúng mô tả", "4"],
        ["Phương", "Giá cả hợp lý", "4"]
    ];

    $random_reviews = [];
    for ($i = 0; $i < 5; $i++) {
        $random_review = $reviews_data[array_rand($reviews_data)];
        $random_reviews[] = [
            "@type" => "Review",
            "name" => $random_review[0],
            "reviewBody" => $random_review[1],
            "reviewRating" => [
                "@type" => "Rating",
                "ratingValue" => $random_review[2],
                "bestRating" => "5",
                "worstRating" => "4"
            ],
            "datePublished" => date("Y-m-d"),
            "author" => [
                "@type" => "Person",
                "name" => $random_review[0]
            ]
        ];
    }
    return $random_reviews;
}

// Add fields to category edit form
add_action('product_cat_edit_form_fields', 'add_category_custom_fields', 10, 2);
function add_category_custom_fields($term) {
    // Add form fields for keywords, alternate names, materials, and image description
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="category_keywords"><?php _e('Category Keywords'); ?></label></th>
        <td>
            <input type="text" name="category_keywords" id="category_keywords" value="<?php echo esc_attr(get_term_meta($term->term_id, 'category_keywords', true)); ?>" />
            <p class="description"><?php _e('Enter the keywords for the category, separated by commas.'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="category_alternate_names"><?php _e('Category Alternate Names'); ?></label></th>
        <td>
            <input type="text" name="category_alternate_names" id="category_alternate_names" value="<?php echo esc_attr(get_term_meta($term->term_id, 'category_alternate_names', true)); ?>" />
            <p class="description"><?php _e('Enter the alternate names for the category, separated by commas.'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="category_material"><?php _e('Category Material'); ?></label></th>
        <td>
            <input type="text" name="category_material" id="category_material" value="<?php echo esc_attr(get_term_meta($term->term_id, 'category_material', true)); ?>" />
            <p class="description"><?php _e('Enter the materials for the category, separated by commas.'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="image_description"><?php _e('Image Description'); ?></label></th>
        <td>
            <textarea name="image_description" id="image_description" rows="5" cols="50"><?php echo esc_textarea(get_term_meta($term->term_id, 'image_description', true)); ?></textarea>
            <p class="description"><?php _e('Enter the description for the image.'); ?></p>
        </td>
    </tr>
    <?php
}

// Save category custom fields
add_action('edited_product_cat', 'save_category_custom_fields', 10, 2);
function save_category_custom_fields($term_id) {
    if (isset($_POST['category_keywords'])) {
        update_term_meta($term_id, 'category_keywords', sanitize_text_field($_POST['category_keywords']));
    }
    if (isset($_POST['category_alternate_names'])) {
        update_term_meta($term_id, 'category_alternate_names', sanitize_text_field($_POST['category_alternate_names']));
    }
    if (isset($_POST['category_material'])) {
        update_term_meta($term_id, 'category_material', sanitize_text_field($_POST['category_material']));
    }
    if (isset($_POST['image_description'])) {
        update_term_meta($term_id, 'image_description', sanitize_textarea_field($_POST['image_description']));
    }
}
?>
