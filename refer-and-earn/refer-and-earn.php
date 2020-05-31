<?php
/*
Plugin Name: Refer and Earn
Description: A highly documented plugin that demonstrates how to create custom admin list-tables from database using WP_List_Table class.
Version:     1.0
Author:      Shailaja Bhagat
Author URI:  https://github.com/shailaja-bhagat/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * $cltd_example_db_version - holds current database version
 * and used on plugin update to sync database tables
 */
global $cltd_example_db_version;
$cltd_example_db_version = '1.1'; // version changed from 1.0 to 1.1

/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */
function cltd_example_install()
{
    global $wpdb;
    global $cltd_example_db_version;

    $table_name = $wpdb->prefix . 'feelya_refer_and_earn'; // do not forget about tables prefix

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('cltd_example_db_version', $cltd_example_db_version);

    $installed_ver = get_option('cltd_example_db_version');
    if ($installed_ver != $cltd_example_db_version) {
        //Manually create table

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('cltd_example_db_version', $cltd_example_db_version);
    }
}

register_activation_hook(__FILE__, 'cltd_example_install');

/**
 * Trick to update plugin database, see docs
 */
function cltd_example_update_db_check()
{
    global $cltd_example_db_version;
    if (get_site_option('cltd_example_db_version') != $cltd_example_db_version) {
        cltd_example_install();
    }
}

add_action('plugins_loaded', 'cltd_example_update_db_check');

/**
 * PART 2. Defining Custom Table List
 * ============================================================================
 *
 * In this part you are going to define custom table list class,
 * that will display your database records in nice looking table
 *
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Custom_Table_Example_List_Table class that will display our custom table
 * records in nice table
 */
class Custom_Table_Example_List_Table extends WP_List_Table
{
    /**
     * [REQUIRED] You must declare constructor and give some basic params
     */
    function __construct()
    {
        global $status, $page;

        parent::__construct(array(
            'singular' => 'refer and earn',
            'plural' => 'refer and earns',
        ));
    }

    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * [OPTIONAL] this is example, how to render specific column
     *
     * method name must be like this: "column_[column_name]"
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_commission_earned($item)
    {
        return '<em>' . $item['commission_earned'] . '</em>';
    }

    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_ref_therapist_full_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &refer_and_earn=2
        $actions = array(
            'edit' => sprintf('<a href="?page=refer_and_earns_form&id=%s">%s</a>', $item['id'], __('Edit', 'cltd_example')),
        );

        return sprintf('%s %s',
            $item['ref_therapist_full_name'],
            $this->row_actions($actions)
        );
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns()
    {
        $columns = array(
            'ref_therapist_full_name'   => __('Referer Therapist Name', 'cltd_example'),
            'therapist_full_name'       => __('Therapist Name', 'cltd_example'),
            'invitation_date'           => __('Invitation Date', 'cltd_example'),
            'sign_up_date' 			    => __( 'Sign Up Date', 're' ),
			'first_session_date' 	    => __( 'First Session Date', 're' ),
			'date_of_expire' 		    => __( 'Expiry Date', 're' ),
			'intro_fee_pending' 	    => __( 'Intro Fee Pending', 're' ),
			'intro_fee_paid' 		    => __( 'Intro Fee Paid', 're' ),
			'number_of_sessions' 	    => __( 'Number of Sessions', 're' ),
			'commission_earned' 	    => __( 'Commission Earned', 're' ),
        );
        return $columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = array(
            'ref_therapist_full_name'   => array('ref_therapist_full_name', true),
            'therapist_full_name'       => array('therapist_full_name', false),
            'invitation_date'           => array('invitation_date', false),
        );
        return $sortable_columns;
    }

    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'feelya_refer_and_earn'; // do not forget about tables prefix

        $per_page = 10; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $per_page) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'ref_therapist_full_name';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}

/**
 * PART 3. Admin page
 * ============================================================================
 *
 * In this part you are going to add admin page for custom table
 *
 * http://codex.wordpress.org/Administration_Menus
 */

/**
 * admin_menu hook implementation, will add pages to list refer_and_earns and to add new one
 */
function cltd_example_admin_menu()
{
    add_menu_page(__('Refer and Earns', 'cltd_example'), __('Refer and Earns', 'cltd_example'), 'activate_plugins', 'refer_and_earns', 'cltd_example_refer_and_earns_page_handler');
    add_submenu_page('refer_and_earns', __('Refer and Earns', 'cltd_example'), __('Refer and Earns', 'cltd_example'), 'activate_plugins', 'refer_and_earns', 'cltd_example_refer_and_earns_page_handler');
    // add new will be described in next part
    add_submenu_page('refer_and_earns', __('', 'cltd_example'), __('', 'cltd_example'), 'activate_plugins', 'refer_and_earns_form', 'cltd_example_refer_and_earns_form_page_handler');
}

add_action('admin_menu', 'cltd_example_admin_menu');

/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
function cltd_example_refer_and_earns_page_handler()
{
    global $wpdb;

    $table = new Custom_Table_Example_List_Table();
    $table->prepare_items();

    $message = '';
    if ('delete' === $table->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'cltd_example'), count($_REQUEST['id'])) . '</p></div>';
    }
    ?>
<div class="wrap">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Refer and Earns', 'cltd_example')?>
    </h2>
    <?php echo $message; ?>

    <form id="refer_and_earns-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $table->display() ?>
    </form>

</div>
<?php
}

/**
 * PART 4. Form for adding andor editing row
 * ============================================================================
 *
 * In this part you are going to add admin page for adding andor editing items
 * You cant put all form into this function, but in this example form will
 * be placed into meta box, and if you want you can split your form into
 * as many meta boxes as you want
 *
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
 */

/**
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function cltd_example_refer_and_earns_form_page_handler()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'feelya_refer_and_earn'; // do not forget about tables prefix

    $message = '';
    $notice = '';

    // this is default $item which will be used for new records
    $default = array(
        'id'                        => 0,
        'ref_therapist_full_name'   => '',
        'therapist_full_name'       => '',
        'intro_fee_paid'            => '',
    );
    

    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);

        $row_id = $item['id'];

        $ref_info = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE id = $row_id" );
    
        $ref_old_commision = $ref_info[0]->commission_earned;
        $ref_new_commision = $item['intro_fee_paid'];

        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = cltd_example_validate_refer_and_earn($item);
        if ($item_valid === true) {
            if ($item['id'] != 0) {
                // $result = $wpdb->update($table_name, 30, array('id' => $item['id']));
                $result = $wpdb->update( $table_name, [
                    // 'commission_earned'		=> $ref_new_commision + $ref_old_commision,
                    'intro_fee_pending'		=> 'no',
                    'intro_fee_paid'		=> 30
                ], [
                    'id' => $item['id']
                ], [
                    '%s',
                    '%d'
                ], [
                    '%d'
                ] );
                
                if ($result) {
                    $message = __('Intro fee updated successfully', 'cltd_example');
                } else {
                    $notice = __('There was an error while updating item', 'cltd_example');
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    }
    else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'cltd_example');
            }
        }
    }

    // here we adding our custom meta box
    add_meta_box('refer_and_earns_form_meta_box', 'Refer and Earn data', 'cltd_example_refer_and_earns_form_meta_box_handler', 'refer_and_earn', 'normal', 'default');

    ?>
<div class="wrap">
    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h2><?php _e('Refer and Earn', 'cltd_example')?> <a class="add-new-h2"
                                href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=refer_and_earns');?>"><?php _e('back to list', 'cltd_example')?></a>
    </h2>

    <?php if (!empty($notice)): ?>
    <div id="notice" class="error"><p><?php echo $notice ?></p></div>
    <?php endif;?>
    <?php if (!empty($message)): ?>
    <div id="message" class="updated"><p><?php echo $message ?></p></div>
    <?php endif;?>

    <form id="form" method="POST">
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
        <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
        <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

        <div class="metabox-holder" id="poststuff">
            <div id="post-body">
                <div id="post-body-content">
                    <?php /* And here we call our custom meta box */ ?>
                    <?php do_meta_boxes('refer_and_earn', 'normal', $item); ?>
                    <input type="submit" value="<?php _e('Save', 'cltd_example')?>" id="submit" class="button-primary" name="submit">
                </div>
            </div>
        </div>
    </form>
</div>
<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function cltd_example_refer_and_earns_form_meta_box_handler($item)
{
    global $wpdb;

    $row_id = $item['id'];

    $tbl        = $wpdb->prefix . 'feelya_refer_and_earn';
    $ref_info   = $wpdb->get_results( "SELECT * FROM {$tbl} WHERE id = $row_id" );

    $ref_intro_fee      = $ref_info[0]->intro_fee_paid;
        
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="ref_therapist_full_name"><?php _e('Referer Therapist Name', 'cltd_example')?></label>
        </th>
        <td>
            <input id="ref_therapist_full_name" name="ref_therapist_full_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['ref_therapist_full_name'])?>"
                   size="50" class="code" placeholder="<?php _e('Referer Therapist Name', 'cltd_example')?>" readonly>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="therapist_full_name"><?php _e('Therapist Name', 'cltd_example')?></label>
        </th>
        <td>
            <input id="therapist_full_name" name="therapist_full_name" type="text" style="width: 95%" value="<?php echo esc_attr($item['therapist_full_name'])?>"
                   size="50" class="code" placeholder="<?php _e('Therapist Name', 'cltd_example')?>" readonly>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="intro_fee_paid"><?php _e('Intro Fee', 'cltd_example')?></label>
        </th>
        <td>
            <input id="intro_fee_paid" name="intro_fee_paid" type="checkbox" value="30"
                   class="code" <?php if($ref_intro_fee > 0 ){?>  disabled checked<?php } ?>> 30<br>
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function cltd_example_validate_refer_and_earn($item)
{
    $messages = array();

    if (empty($item['intro_fee_paid'])) $messages[] = __('Please check the box', 'cltd_example');
    //...
    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

/**
 * Do not forget about translating your plugin, use __('english string', 'your_uniq_plugin_name') to retrieve translated string
 * and _e('english string', 'your_uniq_plugin_name') to echo it
 * in this example plugin your_uniq_plugin_name == cltd_example
 *
 * to create translation file, use poedit FileNew catalog...
 * Fill name of project, add "." to path (ENSURE that it was added - must be in list)
 * and on last tab add "__" and "_e"
 *
 * Name your file like this: [my_plugin]-[ru_RU].po
 *
 * http://codex.wordpress.org/Writing_a_Plugin#Internationalizing_Your_Plugin
 * http://codex.wordpress.org/I18n_for_WordPress_Developers
 */
function cltd_example_languages()
{
    load_plugin_textdomain('cltd_example', false, dirname(plugin_basename(__FILE__)));
}

add_action('init', 'cltd_example_languages');
