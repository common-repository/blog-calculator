<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
session_start();
if (!defined('WP_LOAD_PATH'))
{
    $classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    if (file_exists($classic_root . 'wp-load.php'))
        define('WP_LOAD_PATH', $classic_root);
    else
    if (file_exists($path . 'wp-load.php'))
        define('WP_LOAD_PATH', $path);
    else
        exit("Could not find wp-load.php");
}
require_once( WP_LOAD_PATH . 'wp-load.php');
if (isset($_REQUEST['val']))
{
    if ($_REQUEST['val'] == $_SESSION['new_val'])
    {
        echo $_REQUEST['val'];
    } else
    {
        global $wpdb;
        $data = $wpdb->get_var("SELECT `blog_value` FROM `" . $wpdb->get_blog_prefix() . "blog_calculator` WHERE `id`='1'");
        $data=number_format(round($data, 2), 2);
        echo $data;
    }
}
?>