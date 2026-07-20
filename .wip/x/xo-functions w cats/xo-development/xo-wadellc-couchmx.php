<?php
/**
 * Category Authorization
 * Allow for common username and password pairs 
 * Applicable to Categories of Posts
 */
/**
 * NEW EXTENSION
 * To be added to Extend Dir
 * Build Settings page for Exo Functions 1st and add a tab for User Extensions
 * 
 * Category Authorization
 * Allow for common username and password pairs 
 * Applicable to Categories of Posts
 */

// Add fields to user profile
add_action('show_user_profile', 'add_user_cat_restriction_fields');
add_action('edit_user_profile', 'add_user_cat_restriction_fields');

function add_user_cat_restriction_fields($user) {
    $categories = get_categories(array('hide_empty' => 0));
    $user_allowed_cats = get_user_meta($user->ID, 'allowed_category_ids', true);
    if (!is_array($user_allowed_cats)) $user_allowed_cats = array();
    ?>
    <h3>Category Access Control</h3>
    <table class="form-table">
        <tr>
            <th><label>Allowed Categories</label></th>
            <td>
                <?php foreach($categories as $cat) : ?>
                    <label>
                        <input type="checkbox" name="allowed_category_ids[]" value="<?php echo $cat->term_id; ?>" <?php checked(in_array($cat->term_id, $user_allowed_cats)); ?>>
                        <?php echo $cat->name; ?>
                    </label><br>
                <?php endforeach; ?>
                <p class="description">If checked, this user can view these categories. If none are checked, they follow global site rules.</p>
            </td>
        </tr>
    </table>
    <?php
}

// Save the fields
add_action('personal_options_update', 'save_user_cat_restriction_fields');
add_action('edit_user_profile_update', 'save_user_cat_restriction_fields');

function save_user_cat_restriction_fields($user_id) {
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, 'allowed_category_ids', $_POST['allowed_category_ids']);
    }
}