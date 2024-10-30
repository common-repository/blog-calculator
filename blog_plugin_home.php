<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!defined('WP_LOAD_PATH'))
{
    /** classic root path if wp-content and plugins is below wp-config.php */
    $classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/';
    if (file_exists($classic_root . 'wp-load.php'))
        define('WP_LOAD_PATH', $classic_root);
    else
    if (file_exists($path . 'wp-load.php'))
        define('WP_LOAD_PATH', $path);
    else
        exit("Could not find wp-load.php");
}
// let's load WordPress
require_once( WP_LOAD_PATH . 'wp-load.php');
global $wpdb;
if (isset($_POST['uniquedesign']))
{
    $result = $wpdb->query("UPDATE `" . $wpdb->get_blog_prefix() . "blog_calculator` SET `unique_design`='" . preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['uniquedesign'])) . "',`global_audience`='" . preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['global'])) . "',`piller_articles`='" . preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['pilart'])) . "',`unique_visitors`='" . preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['visits'])) . "',`avr_mnthly_incm`='" . preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['income'])) . "',`avr_mnthly_out`='" . preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['outcome'])) . "' WHERE `id`='1'");
    if ($result)
        echo '<div style=\'margin-top: 16px;font-size: 18px;color: green;margin-bottom: -13px;\'>Data Saved Successfully</div>';
//    else
//        echo '<div style=\'margin-top: 16px;font-size: 18px;color: red;margin-bottom: -13px;\'>Unable To Save Data Successfully</div>';
    calc_post_data();
    $plugin_data = $wpdb->get_row("SELECT * FROM `" . $wpdb->get_blog_prefix() . "blog_calculator` WHERE `id`='1'", ARRAY_A);
} else
{
    $plugin_data = $wpdb->get_row("SELECT * FROM `" . $wpdb->get_blog_prefix() . "blog_calculator` WHERE `id`='1'", ARRAY_A);
}
?>
<link rel="stylesheet" href="<?= plugins_url() ?>/blogcalculator/css/blog_plugin.css"/>
<br/><?php $post_count=  get_object_vars(wp_count_posts());
?>
<br/>
<div class="mainform">
    <form id="form1" name="form1" method="post" action="#" onsubmit="if(<?= $post_count['publish']  ?> > 5){return confirm('Blog Calculation may take several minutes if you have large numbers of posts. Do you want to contiue?');}">
        <dl class="formstyle">
            <!--<dt><label for="url">Blog URL <font color="#FF0000">*</font></label></dt>-->
            <!--<input name="url" type="hidden" id="url" value="http://" />-->
            <dt><label for="unique">Does your blog have a unique design</label></dt>
            <dd><input name="uniquedesign" type="checkbox" id="unique" <?php echo ($plugin_data['unique_design'] == 0) ? "" : "checked=checked"; ?> value="1" /><div class="fieldinfo">(A design which is only seen on your blog and is NOT a template)</div></dd>
            <dt><label for="global">Does the site appeal to a global audience</label></dt>
            <dd><input name="global" type="checkbox" id="global"  <?php echo ($plugin_data['global_audience'] == 0) ? "" : "checked=checked"; ?> value="1" /><div class="fieldinfo">(Would it appeal to people from multiple countries)</div></dd>
            <dt><label for="pilart">Number of pillar articles <font color="#FF0000">*</font></label></dt>
            <dd><input name="pilart" type="text" id="pilart" value="<?= $plugin_data['piller_articles'] ?>" /><div class="fieldinfo">
                    (Articles which are more than 3 months old and still bring in 100 visitors per month)</div></dd>
            <dt><label for="visits">Unique vistors per month <font color="#FF0000">* </font></label></dt>
            <dd><input name="visits" type="text" id="visits" value="<?= $plugin_data['unique_visitors'] ?>" /><div class="fieldinfo">(Use the total unique vistors from last month)</div></dd>
            <fieldset>
                <div class="fieldsetinfo">
                    The monthly income and outgoings below are not required if you don't want to use them, instead an average blog income will be worked out from the monthly unique visitors that you placed above at a rate of $5 income from every 1000 visitors.
                </div>
                <dt><label for="income">Average monthly income ($)</label></dt>
                <dd><input name="income" type="text" id="income" value="<?= $plugin_data['avr_mnthly_incm'] ?>" /><div class="fieldinfo">(Such as advert revenue, affiliate earning etc)</div></dd>

                <dt><label for="outcome">Average monthly outgoings ($)</label></dt>
                <dd><input name="outcome" type="text" id="outcome" value="<?= $plugin_data['avr_mnthly_out'] ?>" /><div class="fieldinfo">(Such as hosting costs, advert purchases etc)</div></dd>
            </fieldset>
            <!--            <dt><label for="years">Blog age in years</label></dt>
                        <dd><input name="years" type="text" id="years" value="" /><div class="fieldinfo">(Complete years only so for 3 months put 0.)</div></dd>
                        <dt><label for="posts">Unique blog posts per month</label></dt>
                        <dd><input name="posts" type="text" id="posts" value="" /><div class="fieldinfo">(An approximate amount of blog posts you post per month)</div></dd>
                        <dt><label for="length">Words per blog post</label></dt>
                        <dd><input name="postlength" type="text" id="length" value="" /><div class="fieldinfo">(The number of words of an average blog post on your blog)</div></dd><br>-->
            <center>
                <input type="submit" name="Submit" value="Calculate Now" />
            </center>
        </dl>
    </form>
    <br>
</div>