<?php
/**
 * Category Authorization
 * Allow for common username and password pairs 
 * Applicable to Categories of Posts
 */

// Register the settings page
add_action('admin_menu', 'cat_restrict_create_menu');
function cat_restrict_create_menu() {
    add_options_page(
        'Category Access Settings', 
        'Category Access', 
        'manage_options', 
        'category-access-settings', 
        'cat_restrict_settings_page'
    );
}

// Register the settings with WordPress
add_action('admin_init', 'cat_restrict_register_settings');
function cat_restrict_register_settings() {
    register_setting('cat-restrict-group', 'restricted_category_ids');
}

// Display the settings page HTML
function cat_restrict_settings_page() {
?>
    <div class="wrap">
        <h1>Manage Category Access</h1>
        <form method="post" action="options.php">
            <?php settings_fields('cat-restrict-group'); ?>
            <?php do_settings_sections('cat-restrict-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Categories to Restrict:</th>
                    <td>
                        <?php 
                        $categories = get_categories(array('hide_empty' => 0));
                        $saved_ids = get_option('restricted_category_ids', array());
                        if (!is_array($saved_ids)) $saved_ids = array();

                        foreach($categories as $category) {
                            $checked = in_array($category->term_id, $saved_ids) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="restricted_category_ids[]" value="' . $category->term_id . '" ' . $checked . '> ' . $category->name . '</label><br>';
                        }
                        ?>
                        <p class="description">Only logged-in users can view posts in these categories.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}