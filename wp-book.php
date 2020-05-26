<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              therakeshpurohit.wordpress.com
 * @since             1.0.0
 * @package           Wp_Book
 *
 * @wordpress-plugin
 * Plugin Name:       WP Book
 * Plugin URI:        https://github.com/TheRakeshPurohit/WP-Book
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            TheRakeshPurohit
 * Author URI:        therakeshpurohit.wordpress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-book
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_BOOK_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-book-activator.php
 */
function activate_wp_book() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-book-activator.php';
	Wp_Book_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-book-deactivator.php
 */
function deactivate_wp_book() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-book-deactivator.php';
	Wp_Book_Deactivator::deactivate();
}

if ( ! function_exists( 'wpbook_setup_post_type' ) ) {
	/**
	 * Register the book custom post type.
	 */
	function wpbook_setup_post_type() {
		register_post_type(
			'book',
			array(
				'labels'      => array(
					'name'          => __( 'Books' ),
					'singular_name' => __( 'Book' ),
				),
				'public'      => true,
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'Books' ),
			)
		);
	}
	add_action( 'init', 'wpbook_setup_post_type' );
}

// hook into the init action and call create_book_category_taxonomies when it fires.
add_action( 'init', 'book_categories_hierarchical_taxonomy', 0 );

// hook into the init action and call create_book_category_taxonomies when it fires.
add_action( 'init', 'book_tags_nonhierarchical_taxonomy', 0 );

/**
 * Create a custom taxonomy name it topics for your posts.
 */
function book_categories_hierarchical_taxonomy() {

	// Add new taxonomy, make it hierarchical like categories.
	// first do the translations part for GUI.

	$labels = array(
		'name'              => _x( 'Book Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Book Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Book Categories' ),
		'all_items'         => __( 'All Book Categories' ),
		'parent_item'       => __( 'Parent Book Category' ),
		'parent_item_colon' => __( 'Parent Book Category:' ),
		'edit_item'         => __( 'Edit Book Category' ),
		'update_item'       => __( 'Update Book Category' ),
		'add_new_item'      => __( 'Add New Book Category' ),
		'new_item_name'     => __( 'New Book Category Name' ),
		'menu_name'         => __( 'Book Category' ),
	);

	// Now register the taxonomy.

	register_taxonomy(
		'Book Categories',
		array( 'book' ),
		array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'book' ),
		)
	);

}

/**
 * Create a custom taxonomy name it book tags for your books.
 */
function book_tags_nonhierarchical_taxonomy() {

	// Add new taxonomy, make it non hierarchical like tags.
	// first do the translations part for GUI.

	$labels = array(
		'name'              => _x( 'Book Tags', 'taxonomy general name' ),
		'singular_name'     => _x( 'Book Tag', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Book Tags' ),
		'all_items'         => __( 'All Book Tags' ),
		'parent_item'       => __( 'Parent Book Tag' ),
		'parent_item_colon' => __( 'Parent Book Tag:' ),
		'edit_item'         => __( 'Edit Book Tag' ),
		'update_item'       => __( 'Update Book Tag' ),
		'add_new_item'      => __( 'Add New Book Tag' ),
		'new_item_name'     => __( 'New Book Tag Name' ),
		'menu_name'         => __( 'Book Tag' ),
	);

	// Now register the taxonomy.

	register_taxonomy(
		'Book Tags',
		array( 'book' ),
		array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'book' ),
		)
	);

}
/**
 *
 * Custom metabox book info
 *
 * @return void
 */
function wporg_add_custom_box() {
	$screens = array( 'post', 'wporg_cpt' );
	foreach ( $screens as $screen ) {
			add_meta_box(
				'wporg_box_id',           // Unique ID.
				'Custom Meta Box Title',  // Box title.
				'wporg_custom_box_html',  // Content callback, must be of type callable.
				$screen                   // Post type.
			);
	}
}
add_action( 'add_meta_boxes', 'wporg_add_custom_box' );

/**
 * Custom meta box type.
 *
 * @param [type] $post
 * @return void
 */
function wporg_custom_box_html( $post ) {
	$value = get_post_meta( $post->ID, '_wporg_meta_key', true );
	?>
	<label for="wporg_field">Description for this field</label>
	<select name="wporg_field" id="wporg_field" class="postbox">
	<option value="">Select something...</option>
	<option value="something" <?php selected( $value, 'something' ); ?>>Something</option>
	<option value="else" <?php selected( $value, 'else' ); ?>>Else</option>
	</select>
	<?php
}

/**
 * save post data
 *
 * @param [type] $post_id
 * @return void
 */
function wporg_save_postdata( $post_id ) {
	if ( array_key_exists( 'wporg_field', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_wporg_meta_key',
			$_POST['wporg_field']
		);
	}
}
add_action( 'save_post', 'wporg_save_postdata' );


register_activation_hook( __FILE__, 'activate_wp_book' );
register_deactivation_hook( __FILE__, 'deactivate_wp_book' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-book.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_book() {

	$plugin = new Wp_Book();
	$plugin->run();

}
run_wp_book();
