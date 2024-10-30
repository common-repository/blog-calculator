<?php
/*
  Plugin Name: Blog Calculator
  Plugin URI: http://www.blogcalculator.com
  Description: Blog Calculator Plugin
  Author: Callidus Technology Limited
  Version: 1.0
  Author URI: http://www.callidus-tech.com
  License: GPLv3
 */
/*
 * Description of Blog Calculator Plugin
 *
 * @author Callidus Technology Limited
 * 
 */
?>
<?php
session_start();
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
require_once( WP_LOAD_PATH . 'wp-load.php');
register_activation_hook(__FILE__, 'register_blog_function');
add_action('plugins_loaded', 'blog_calc_init');
register_deactivation_hook(__FILE__, 'deregister_blog_function');
add_action('admin_menu', 'blog_calc_func');
add_action('deleted_post', 'deleted_post_action');
add_action('save_post', 'save_post_action');
add_shortcode('custom_code', 'custom_func');

function custom_func()
{
    echo "hllo";
}

function save_post_action($post_id)
{
    calc_post_data('new');
}

function deleted_post_action($pid)
{
//    calc_post_data();
}

function register_blog_function()
{
    global $wpdb;
    $qry = "CREATE TABLE IF NOT EXISTS `" . $wpdb->get_blog_prefix() . "blog_calculator`(`id` int(11),`unique_design` int(11),`global_audience` int(11),`piller_articles` bigint,`unique_visitors` bigint,`avr_mnthly_incm` bigint,`avr_mnthly_out` bigint,`blog_value` double)";
    $result = $wpdb->query($qry);
    if ($result)
    {
        $count_id = $wpdb->get_var("select count(*) from `" . $wpdb->get_blog_prefix() . "blog_calculator` ");
        if ($count_id == 0)
        {
            $str3 = "INSERT INTO `" . $wpdb->get_blog_prefix() . "blog_calculator` VALUES('1' ,'1','1','0','0','0','0','0')";
            $wpdb->query($str3);
        }
    }
//    calc_post_data();
}

function deregister_blog_function()
{
    global $wpdb;
    $qry = "DROP TABLE `" . $wpdb->get_blog_prefix() . "blog_calculator`";
    $result = $wpdb->query($qry);
}

function blog_calc_func()
{
    add_menu_page('Blog Calculator', 'Blog Calculator', 10, 'blog_calculator', 'blog_calc_function');
}

function blog_calc_function()
{
    include 'blog_plugin_home.php';
}

function widget_blog_calc($args = 0)
{
    global $wpdb;
    $data = $wpdb->get_var("SELECT `blog_value` FROM `" . $wpdb->get_blog_prefix() . "blog_calculator` WHERE `id`='1'");
    $data=number_format(round($data, 2), 2);
    $_SESSION['new_val'] = $data;
    ?>    
    <script type="text/javascript">
        function createCookie(name,value,days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
            }
            else var expires = "";
            document.cookie = name+"="+value+expires+"; path=/";
        }
        function readCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        createCookie("data_val","<?= $data ?>",15);
        window.onload = startInterval;
        function startInterval() {
            setInterval("startTime();",10000);
        }
        function startTime() {
            var now = new Date();
            var ajaxRequest;  // The variable that makes Ajax possible!
                                                                                    	
            try{
                // Opera 8.0+, Firefox, Safari
                ajaxRequest = new XMLHttpRequest();
            } catch (e){
                // Internet Explorer Browsers
                try{
                    ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try{
                        ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (e){
                        // Something went wrong
                        alert('Your browser broke!');
                        return false;
                    }
                }
            }
            // Create a function that will receive data sent from the server
            ajaxRequest.onreadystatechange = function(){
                if(ajaxRequest.readyState == 4){
                    document.getElementById('data').innerHTML = ajaxRequest.responseText;
                }
            }
            ajaxRequest.open("GET", "<?php echo plugins_url() ?>/blogcalculator/ajax_request.php?val="+ readCookie("data_val"), true);
            ajaxRequest.send(null); 
        }
    </script>

    <div id="blog_calculator"><h2 class="widgettitle">Blog Calculator</h2>
        <div id="blog_content">
            <div class="widget">
                <div style="font-family:verdana; border: 1px solid #AAAAAA; background-color: white; width: 100%; text-align: center; padding: 2px 0 10px 0;">
                    <p style="margin: 0"><a href="http://www.blogcalculator.com/"><img src="<?= plugins_url() ?>/blogcalculator/images/1.jpg" style="border:0px;" border="0" height="71" width="110" alt="Blog Money Valuation"></a><br />
                        <span style="font-size: 12px; line-height: 14px;">My <a href="javascript:void(0);">blog</a> has been valued at...</span>
                        <span style="font-size: 16px;"><b>$<span id="data"><?php echo $data; ?></span></b></span>
                        <span style="font-size: 9px; line-height: 10px;"><br /><br /><a href="http://www.blogcalculator.com">Blog Valuation Tool from BlogCalculator.com</a></span></p></div>
            </div>            
        </div>
    </div>
    <?php
}

function calc_post_data($status = '')
{
    global $wpdb;
    require_once 'pagerank.php';
    $unique_post_value = 0;
    $unique_post = $wpdb->get_results("SELECT count(ID) as post_count from {$wpdb->posts} WHERE post_status = 'publish' GROUP BY  MONTH(`post_date`)", ARRAY_N);
    for ($i = 0; $i < count($unique_post); $i++)
    {
        $unique_post_value = $unique_post_value + intval($unique_post[$i][0]);
    }
    $unique_post_value = ceil($unique_post_value / count($unique_post));
    $plugin_data = $wpdb->get_row("SELECT * FROM `" . $wpdb->get_blog_prefix() . "blog_calculator` WHERE `id`='1'", ARRAY_A);
    if ($status == 'new')
        $args = array('numberposts' => 1, 'order' => 'DESC', 'orderby' => 'id');
    else
        $args = array('numberposts' => -1, 'order' => 'ASC', 'orderby' => 'title');
    $myposts = get_posts($args);
    $totalvalue = 0;
    $temp = 0;
    $udesign = preg_replace("/[^0-9]/", "", $plugin_data['unique_design']);
    $pilart = preg_replace("/[^0-9]/", "", $plugin_data['piller_articles']);
    $income = preg_replace("/[^0-9]/", "", $plugin_data['avr_mnthly_incm']);
    $outcome = preg_replace("/[^0-9]/", "", $plugin_data['avr_mnthly_out']);
    $globeapp = preg_replace("/[^0-9]/", "", $plugin_data['global_audience']);
//  $blogage = preg_replace("/[^0-9]/", "", $plugin_data['years']);
    $blogage = 0;
    $visits = preg_replace("/[^0-9]/", "", $plugin_data['unique_visitors']);
    foreach ($myposts as $post)
    {
        $temp++;
        $post_data = get_object_vars($post);
        $url = $post_data['guid'];
//        $url = 'http://ptindia.org/wp101';
        $testurl = $url;
        $word_count = sizeof(explode(' ', strip_tags($post_data['post_content'])));
        $new_content = $word_count;
        $postnum = preg_replace("/[^0-9]/", '', $unique_post_value);
        $postlength = preg_replace("/[^0-9]/", '', $word_count);
        $debug_vars = true;
//        echo "<br/>";
//        echo "<br/>";
//        echo "<br/>";
//        echo "url" . $url . "<br/>";
//        echo "unique deisng:" . $udesign . "<br/>";
//        echo "piller_articles:" . $pilart . "<br/>";
//        echo "income:" . $income . "<br/>";
//        echo "outcome:" . $outcome . "<br/>";
//        echo "global audience:" . $globeapp . "<br/>";
//        echo "unique visitors:" . $visits . "<br/>";
//        echo "unique post:" . $postnum . "<br/>";
//        echo "post length:" . $postlength . "<br/>";
//        echo "word count:" . $word_count . "<br/>";
        $subs = '';
        $domainName = '';
        $tld = '';

        $gTlds = explode(',', str_replace(' ', '', "aero, biz, com, coop, info,
jobs, museum, name, net, org, pro, travel, gov, edu, mil, int"));

        $cTlds = explode(',', str_replace(' ', '', "ac, ad, ae, af, ag, ai, al,
am, an, ao, aq, ar, as, at, au, aw, az, ax, ba, bb, bd, be, bf, bg, bh,
bi, bj, bm, bn, bo, br, bs, bt, bv, bw, by, bz, ca, cc, cd, cf, cg, ch,
ci, ck, cl, cm, cn, co, cr, cs, cu, cv, cx, cy, cz, de, dj, dk, dm, do,
dz, ec, ee, eg, eh, er, es, et, eu, fi, fj, fk, fm, fo, fr, ga, gb, gd,
ge, gf, gg, gh, gi, gl, gm, gn, gp, gq, gr, gs, gt, gu, gw, gy, hk, hm,
hn, hr, ht, hu, id, ie, il, im, in, io, iq, ir, is, it, je, jm, jo, jp,
ke, kg, kh, ki, km, kn, kp, kr, kw, ky, kz, la, lb, lc, li, lk, lr, ls,
lt, lu, lv, ly, ma, mc, md, mg, mh, mk, ml, mm, mn, mo, mp, mq, mr, ms,
mt, mu, mv, mw, mx, my, mz, na, nc, ne, nf, ng, ni, nl, no, np, nr, nu,
nz, om, pa, pe, pf, pg, ph, pk, pl, pm, pn, pr, ps, pt, pw, py, qa, re,
ro, ru, rw, sa, sb, sc, sd, se, sg, sh, si, sj, sk, sl, sm, sn, so, sr,
st, sv, sy, sz, tc, td, tf, tg, th, tj, tk, tl, tm, tn, to, tp, tr, tt,
tv, tw, tz, ua, ug, uk, um, us, uy, uz, va,
vc, ve, vg, vi, vn, vu, wf, ws, ye, yt, yu, za, zm, zw"));
        $tldarray = array_merge($gTlds, $cTlds);
        if (!strstr($testurl, 'http://'))
        {
            $testurl = "http://$testurl";
        }
        $testurlParsed = parse_url($testurl);
        $testurlHost = $testurlParsed['host'];
        $domainarray = explode('.', $testurlHost);
        $top = count($domainarray);

        for ($i = 0; $i < $top; $i++)
        {
            $_domainPart = array_pop($domainarray);

            if (!$tld_isReady)
            {
                if (in_array($_domainPart, $tldarray))
                {
                    $tld = ".$_domainPart" . $tld;
                } else
                {
                    $domainName = $_domainPart;
                    $tld_isReady = 1;
                }
            } else
            {
                $subs = ".$_domainPart" . $subs;
            }
        }
        $subdom = substr($subs, 1);
//echo "Subdomain = ".$subdom."<br />";
//echo "Domain = ".$domainName."<br />";
//echo "Extension = ".$tld."<br />";

        $selfhosted = 0;
        if (($subdom == "www") or ($subdom == ""))
        {
            $selfhosted = 1;
        }
//echo "Selfhosted = ".$selfhosted."<br />";
//echo "Listed in DMOZ ? ".dmoz_listed($domainName)."<br />";
        ?> 
        <?php
        $runningtotal = 0;

// Pagerank valuation
        $pagerank = getpagerank($url);
        if ($pagerank == 0)
            $pagerankvalue = 0;
        if ($pagerank == 1)
            $pagerankvalue = 10;
        if ($pagerank == 2)
            $pagerankvalue = 25;
        if ($pagerank == 3)
            $pagerankvalue = 50;
        if ($pagerank == 4)
            $pagerankvalue = 1300;
        if ($pagerank == 5)
            $pagerankvalue = 12000;
        if ($pagerank == 6)
            $pagerankvalue = 117000;
        if ($pagerank == 7)
            $pagerankvalue = 510000;
        if ($pagerank == 8)
            $pagerankvalue = 1500000;
        if ($pagerank == 9)
            $pagerankvalue = 6000000;
        if ($pagerank == 10)
            $pagerankvalue = 422000000;

//        echo "Google pagerank = " . $pagerankvalue . "<br>";
// alexa valuation

        $alexarank = get_alexa_popularity($url);

        if ($alexarank < 1000)
            $alexarankvalue = 3885000;
        if ($alexarank >= 1000 && $alexarank < 10000)
            $alexarankvalue = 885000;
        if ($alexarank >= 10000 && $alexarank < 25000)
            $alexarankvalue = 140000;
        if ($alexarank >= 25000 && $alexarank < 50000)
            $alexarankvalue = 46000;
        if ($alexarank >= 50000 && $alexarank < 75000)
            $alexarankvalue = 18000;
        if ($alexarank >= 75000 && $alexarank < 100000)
            $alexarankvalue = 8000;
        if ($alexarank >= 100000 && $alexarank < 175000)
            $alexarankvalue = 5000;
        if ($alexarank >= 175000 && $alexarank < 250000)
            $alexarankvalue = 1000;
        if ($alexarank >= 250000 && $alexarank < 500000)
            $alexarankvalue = 430;
        if ($alexarank >= 500000 && $alexarank < 1000000)
            $alexarankvalue = 170;
        if ($alexarank >= 1000000 && $alexarank < 2000000)
            $alexarankvalue = 50;
        if ($alexarank >= 2000000 && $alexarank < 3000000)
            $alexarankvalue = 25;
        if ($alexarank >= 3000000)
            $alexarankvalue = 1;
        if (($alexarank == 0) or ($alexarank == ""))
            $alexarankvalue = 1;

//        echo "Alexa pagerank = " . $alexarankvalue . "<br>";
//Pillar Article Valuation

        $pilartvalue = ($pilart * 3.58);

//        echo "Pillar Articles = " . $pilartvalue . "<br>";
//Monthly Income vs Outgoings
        if (($income != 0) or ($outcome != 0))
        {
            $monthprofit = ($income - $outcome);
            $monthprofit = ($monthprofit * 4.25);
        }
//        echo "Profit Monthly = " . $monthprofit . "<br>";

        if ($monthprofit == 0)
        {
            $monthprofit = ($visits / 1000);
            $monthprofit = ($monthprofit * 5.00);
            $monthprofit = ($monthprofit * 4.25);
        }
//        echo "Profit Monthly (Estimated) = " . $monthprofit . "<br>";
//Running total so far
        if ($selfhosted == 1)
        {
            $runningtotal = $pagerankvalue + $alexarankvalue + $pilartvalue + $monthprofit;
        }
        if ($selfhosted == 0)
        {
            $runningtotal = $pilartvalue + $monthprofit;
        }
//        echo "Running total = " . $runningtotal . "<br>";

        if ($runningtotal > 0)
        {
//Google index multiplier
            $googleindex = google_indexed($url);
            //remove comma from google index
            $googleindex = ereg_replace("[^A-Za-z0-9]", "", $googleindex);

            if ($googleindex < 50)
                $runningtotal = ($runningtotal * 0.95);
            if ($googleindex >= 50 && $googleindex < 100)
                $runningtotal = ($runningtotal * 1.02);
            if ($googleindex >= 100 && $googleindex < 200)
                $runningtotal = ($runningtotal * 1.05);
            if ($googleindex >= 200 && $googleindex < 500)
                $runningtotal = ($runningtotal * 1.07);
            if ($googleindex >= 500 && $googleindex < 1000)
                $runningtotal = ($runningtotal * 1.15);
            if ($googleindex >= 1000 && $googleindex < 10000)
                $runningtotal = ($runningtotal * 1.2);
            if ($googleindex >= 10000)
                $runningtotal = ($runningtotal * 1.32);

//            echo "Running total2 = " . $runningtotal . "<br>";
// Domain multiplier
            if ($selfhosted == 1)
            {
                if ($tld == ".com")
                {
                    $runningtotal = ($runningtotal * 1.1);
                }
                if ($tld == ".net")
                {
                    $runningtotal = ($runningtotal * 1.05);
                }
//                echo "Running total3 = " . $runningtotal . "<br>";
            }
            if ($selfhosted == 0)
            {
                $runningtotal = ($runningtotal * 0.8);
//                echo "Running total3 = " . $runningtotal . "<br>";
            }

//domain length

            $dmlength = strlen($domainName);
            if ($dmlength < 6)
                $runningtotal = ($runningtotal * 1.1);
            if ($dmlength >= 6 && $dmlength < 10)
                $runningtotal = ($runningtotal * 1.05);
            if ($dmlength >= 10 && $dmlength < 14)
                $runningtotal = ($runningtotal * 1.03);
            if ($dmlength >= 14 && $dmlength < 17)
                $runningtotal = ($runningtotal * 1.01);
            if ($dmlength >= 17)
                $runningtotal = ($runningtotal * 0.95);
//            echo "Running total4 = " . $runningtotal . "<br>";
//global appeal

            if ($globeapp == 1)
            {
                $runningtotal = ($runningtotal * 1.1);
            }
//            echo "Running total5 = " . $runningtotal . "<br>";
// blog age

            if ($blogage >= 1 && $blogage < 2)
                $runningtotal = ($runningtotal * 1.05);
            if ($blogage >= 2 && $blogage < 3)
                $runningtotal = ($runningtotal * 1.08);
            if ($blogage >= 3 && $blogage < 5)
                $runningtotal = ($runningtotal * 1.11);
            if ($blogage >= 5)
                $runningtotal = ($runningtotal * 1.17);
//            echo "Running total6 = " . $runningtotal . "<br>";
// Dmoz listed

            if (dmoz_listed($domainName) == 1)
                $runningtotal = ($runningtotal * 1.05);
//            echo "Running total7 = " . $runningtotal . "<br>";
// Posts Numbers

            if ($postnum < 6)
                $runningtotal = ($runningtotal * 0.9);
            if ($postnum >= 16)
                $runningtotal = ($runningtotal * 1.06);
//            echo "Running total8 = " . $runningtotal . "<br>";
//            $runningtotal . "<br>";
// Post Lengths

            if ($postlength < 100)
                $runningtotal = ($runningtotal * 0.92);
            if ($postlength >= 100 && $postlength < 200)
                $runningtotal = ($runningtotal * 0.96);
            if ($postlength >= 200 && $postlength < 300)
                $runningtotal = ($runningtotal * 0.97);
            if ($postlength >= 300 && $postlength < 400)
                $runningtotal = ($runningtotal * 1.01);
            if ($postlength >= 400 && $postlength < 500)
                $runningtotal = ($runningtotal * 1.02);
            if ($postlength >= 500 && $postlength < 1000)
                $runningtotal = ($runningtotal * 1.03);
            if ($postlength >= 1000 && $postlength < 2000)
                $runningtotal = ($runningtotal * 1.01);
            if ($postlength >= 2000)
                $runningtotal = ($runningtotal * 0.99);
        }
//        echo "Running total9 = " . $runningtotal . "<br>";
//unique design
        if ($udesign == 1)
        {
            if ($runningtotal < 400)
                $runningtotal = ($runningtotal + 50);
            if ($runningtotal >= 400)
                $runningtotal = ($runningtotal * 1.001);
        }
//        echo "TOTAL = " . $runningtotal . "<br>";
        $totalvalue = $runningtotal + $totalvalue;
    }
    $totalvalue = $totalvalue / $temp;
//    echo "TOTAL = " . $totalvalue . "<br>";
//    $totalvalue = number_format(round($runningtotal, 2), 2);
//    $totalvalue = at(round($runningtotal, 2), 2);
//    echo "Total:".$totalvalue;
    $_SESSION['new_val'] = number_format(round($totalvalue, 2), 2);
    if ($status == 'new')
        $wpdb->query("UPDATE `" . $wpdb->get_blog_prefix() . "blog_calculator` SET `blog_value`=(`blog_value`+" . $totalvalue . ")/2");
    else
        $wpdb->query("UPDATE `" . $wpdb->get_blog_prefix() . "blog_calculator` SET `blog_value`='" . $totalvalue . "'");
}

function blog_calc_init()
{
    register_sidebar_widget('Blog Calculator', 'widget_blog_calc');
}
?>
