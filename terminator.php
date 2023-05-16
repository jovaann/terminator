<?php
/**
 * Plugin Name: Terminator
 * Description: A plugin that clears autoload data from the options table
 * Version: 1.0.0
 * Author: Jovan Kitanovic
 * License: GPL2
 */

// Enqueue required scripts and styles
function terminator_enqueue_scripts() {
    wp_enqueue_script( 'terminator-script', plugin_dir_url( __FILE__ ) . 'js/terminator.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'terminator_enqueue_scripts' );

// Add a submenu page to WordPress admin
function terminator_submenu_page() {
    add_submenu_page( 'tools.php', 'Terminator', 'Terminator', 'manage_options', 'terminator', 'terminator_submenu_page_callback' );
}
add_action( 'admin_menu', 'terminator_submenu_page' );

// Callback function for the submenu page
function terminator_submenu_page_callback() {
    ?>
    <div class="wrap">
        <h1>Terminator</h1>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Option Name</th>
                    <th>Autoload Size (KB)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="terminator-options-list">
                <?php terminator_display_options(); ?>
            </tbody>
        </table>
        <p>
            <button id="terminator-clear-all" class="button">Clear Autoload for All Rows</button>
        </p>
    </div>
    <?php
}

// Display the top 10 options with the largest autoload size
function terminator_display_options() {
    global $wpdb;
    $options = $wpdb->get_results( "SELECT option_name, LENGTH(option_value) as autoload_size FROM {$wpdb->options} WHERE autoload = 'yes' ORDER BY autoload_size DESC LIMIT 10" );

    foreach ( $options as $option ) {
        $option_name = esc_attr( $option->option_name );
        $autoload_size = round( $option->autoload_size / 1024, 2 );
        ?>
        <tr data-option-name="<?php echo $option_name; ?>">
            <td><?php echo $option_name; ?></td>
            <td><?php echo $autoload_size; ?></td>
            <td><button class="terminator-clear button" data-option-name="<?php echo $option_name; ?>">Clear Autoload</button></td>
        </tr>
        <?php
    }
}

// AJAX callback function to clear autoload for a single row
function terminator_ajax_clear_autoload() {
    global $wpdb;
    $option_name = $_POST['option_name'];
    $wpdb->update( $wpdb->options, array( 'autoload' => 'no' ), array( 'option_name' => $option_name ) );
    wp_die();
}
add_action( 'wp_ajax_terminator_clear_autoload', 'terminator_ajax_clear_autoload' );

// AJAX callback function to clear autoload for all rows
function terminator_ajax_clear_all_autoload() {
    global $wpdb;
    $wpdb->query( "UPDATE {$wpdb->options} SET autoload = 'no' WHERE autoload = 'yes'" );
    wp_die();
}
add_action( 'wp_ajax_terminator_clear_all_autoload', 'terminator_ajax_clear_all_autoload' );
