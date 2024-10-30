<?php
session_start();
require_once("pagerank.php");
?>
<?php

$url = strip_tags($_REQUEST['url']);
if (($url == "http://") or ($url == ""))
{
    header('Location: '.  get_site_url().'/wp-admin/admin.php?page=blog_calculator');
}
$testurl = $url;
$udesign = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['uniquedesign']));
$pilart = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['pilart']));
$income = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['income']));
$outcome = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['outcome']));
$globeapp = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['global']));
$blogage = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['years']));
$postnum = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['posts']));
$postlength = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['postlength']));
$visits = preg_replace("/[^0-9]/", "", strip_tags($_REQUEST['visits']));


//echo "Google pagerank = ".getpagerank($url)."<br />";
//echo "Alexa popularity = ".get_alexa_popularity($url)."<br />";
//echo "Google indexed = ".google_indexed($url)."<br />";

$debug_vars = true;

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

//echo "Google pagerank = ".$pagerankvalue."<br>";
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

//echo "Alexa pagerank = ".$alexarankvalue."<br>";
//Pillar Article Valuation

$pilartvalue = ($pilart * 3.58);

//echo "Pillar Articles = ".$pilartvalue."<br>";
//Monthly Income vs Outgoings
if (($income != 0) or ($outcome != 0))
{
    $monthprofit = ($income - $outcome);
    $monthprofit = ($monthprofit * 4.25);
}
//echo "Profit Monthly = ".$monthprofit."<br>";

if ($monthprofit == 0)
{
    $monthprofit = ($visits / 1000);
    $monthprofit = ($monthprofit * 5.00);
    $monthprofit = ($monthprofit * 4.25);
}
//echo "Profit Monthly (Estimated) = ".$monthprofit."<br>";
//Running total so far
if ($selfhosted == 1)
{
    $runningtotal = $pagerankvalue + $alexarankvalue + $pilartvalue + $monthprofit;
}
if ($selfhosted == 0)
{
    $runningtotal = $pilartvalue + $monthprofit;
}
//echo "Running total = ".$runningtotal."<br>";

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

//echo "Running total2 = ".$runningtotal."<br>";
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
//echo "Running total3 = ".$runningtotal."<br>";
    }
    if ($selfhosted == 0)
    {
        $runningtotal = ($runningtotal * 0.8);
//echo "Running total3 = ".$runningtotal."<br>";
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
//echo "Running total4 = ".$runningtotal."<br>";
//global appeal

    if ($globeapp == 1)
    {
        $runningtotal = ($runningtotal * 1.1);
    }
//echo "Running total5 = ".$runningtotal."<br>";
// blog age

    if ($blogage >= 1 && $blogage < 2)
        $runningtotal = ($runningtotal * 1.05);
    if ($blogage >= 2 && $blogage < 3)
        $runningtotal = ($runningtotal * 1.08);
    if ($blogage >= 3 && $blogage < 5)
        $runningtotal = ($runningtotal * 1.11);
    if ($blogage >= 5)
        $runningtotal = ($runningtotal * 1.17);
//echo "Running total6 = ".$runningtotal."<br>";
// Dmoz listed

    if (dmoz_listed($domainName) == 1)
        $runningtotal = ($runningtotal * 1.05);
//echo "Running total7 = ".$runningtotal."<br>";
// Posts Numbers

    if ($postnum < 6)
        $runningtotal = ($runningtotal * 0.9);
    if ($postnum >= 16)
        $runningtotal = ($runningtotal * 1.06);
//echo "Running total9 = ".$runningtotal."<br>";$runningtotal."<br>";
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
//echo "Running total9 = ".$runningtotal."<br>";
//unique design
if ($udesign == 1)
{
    if ($runningtotal < 400)
        $runningtotal = ($runningtotal + 50);
    if ($runningtotal >= 400)
        $runningtotal = ($runningtotal * 1.001);
}
?>

<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
        <link rel="shortcut icon" href="http://www.blogcalculator.com/favicon.ico" type="image/x-icon" />
        <meta name="copyright" content="blogcalculator.com" />
        <meta name="description" content="How Much Is My Blog Worth? Fin out your Blog's Value. Valuation is for entertainment only." />
        <meta name="keywords" content="blog,blogging,blog value,site,value,blogging,blog value calculator,my blogs value,blog worth,blog most popular,blog application,blog host" />
        <meta name="robots" content="index,follow" />
        <link type="text/css" rel="stylesheet" media="all" href="style.css" />
        <script type="text/javascript"> </script>
        <base href="http://www.blogcalculator.com/" />
        <title>BlogCalculator.com Blog Valuation Results for <?php echo $url; ?></title>
    </head>

    <body class="fullwidth">
        <div id="container">
            <div id="header">
                <div id="header-in">
                    <img src="money3.jpg" width="105" height="75" alt="Blog Valuation" class="mainpic">
                        <a href="http://www.blogcalculator.com">
                            <img src="title.gif" width="560" height="52" alt="blogcalculator.com" class="titleimg" title="Find Out My Blogs Value"><br>
                                    </a>
                                    <h1>The Blog Valuation Tool - Find Out How Much Your Blog is Worth.</h1>
                                    </div>
                                    </div>
                                    <div id="content-wrap" class="clear lcol">
                                        <div class="column">
                                            <div class="column-in">
                                                <center>
                                                    <a href="http://www.jdoqocy.com/click-3758192-10376688" target="_top">
                                                        <img src="http://www.blogcalculator.com/bluehost4.gif" width="160" height="600" alt="Bluehost.com Web Hosting $6.95" border="0"/></a>
                                                </center>
                                                <br><br>
                                                        <div class="widgetfront">
                                                            <b>Other Great Sites</b><br>
                                                                <ul>
                                                                    <li><a href="http://webhostinggeeks.com">Web Hosting Guide</a><br>Reviews of web hosting providers.</li>
                                                                    <li><a href="http://www.121carhireaustria.com">Car Hire Austria</a><br>Compare car hire prices with one search</li>
                                                                    <li><a href="http://www.bomblogic.com/advertise">Buy website traffic</a><br>Cheap country targeted website visitors.</li>
                                                                    <li><a href="http://partners.mckremie.com/">Hosting Affiliate Program</a><br>Promote hosting and earn money.</li>
                                                                </ul>
                                                        </div>
                                                        </div>
                                                        </div>
                                                        <div class="content">
                                                            <div class="content-in">
                                                                <div class="result">
                                                                    <center>Your Blog is Worth...<center>-->
<?php
//echo "Running total10 = ".$runningtotal."<br>";
$totalvalue = number_format(round($runningtotal, 2), 2);
header('Location: '.  get_site_url().'/wp-admin/admin.php?page=blog_calculator');

?>
<!--                                                                            </div>

                                                                            <center>
                                                                                <a href="http://www.kqzyfj.com/click-3758192-10376701" target="_top">
                                                                                    <img src="http://blogcalculator.com/bluehost.gif" width="468" height="60" alt="Bluehost.com Web Hosting $6.95" border="0"/></a>
                                                                            </center>
                                                                            <br>
                                                                                <a class="a2a_dd" href="http://www.addtoany.com/share_save?linkname=Blog%20Calculator&amp;linkurl=http%3A%2F%2Fwww.blogcalculator.com"><img src="http://static.addtoany.com/buttons/share_save_120_16.gif" width="120" height="16" border="0" alt="Share/Save/Bookmark"/></a><script type="text/javascript">a2a_linkname="Blog Calculator";a2a_linkurl="http://www.blogcalculator.com";</script><script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>
                                                                                <br><br>

                                                                                        Do you want to display to the world how much your blog is worth? If you do we have these nice little boxes below for you. Just copy the code from the text area and paste it where you want your blogs value displayed.<br><br>
                                                                                                <div class="both">
                                                                                                    <div class="widget">
                                                                                                        <div style="font-family:verdana; border: 1px solid #AAAAAA; background-color: white; width: 120px; text-align: center; padding: 2px 0 10px 0;">
                                                                                                            <p style="margin: 0"><a href="http://www.blogcalculator.com/"><img src="http://www.blogcalculator.com/1.jpg" style="border:0;" height="71" width="110" alt="Blog Money Valuation"></a><br />
                                                                                                                <span style="font-size: 12px; line-height: 14px;">My <a href="<?php echo $url; ?>">blog</a> has been valued at...</span>
                                                                                                                <span style="font-size: 16px;"><b>$<?php echo $totalvalue; ?></b></span>
                                                                                                                <span style="font-size: 9px; line-height: 10px;"><br /><br /><a href="http://www.blogcalculator.com">Blog Valuation Tool from BlogCalculator.com</a></span></p></div>
                                                                                                    </div>
                                                                                                    <div class="widgetcode">
                                                                                                        <textarea>
<div style="font-family:verdana; border: 1px solid #AAAAAA; background-color: white; width: 120px; text-align: center; padding: 2px 0 10px 0;"><p style="margin: 0"><a href="http://www.blogcalculator.com/"><img src="http://www.blogcalculator.com/1.jpg" style="border:0;" height="71" width="110" alt="Blog Money Valuation"></a><br />
<span style="font-size: 12px; line-height: 14px;">My <a href="<?php echo $url; ?>">blog</a> has been valued at...</span>
<span style="font-size: 16px;"><b>$<?php echo $totalvalue; ?></b></span>
<span style="font-size: 9px; line-height: 10px;"><br /><br /><a href="http://www.blogcalculator.com">Blog Valuation Tool from BlogCalculator.com</a></span></p></div>
                                                                                                        </textarea>
                                                                                                        <br>
                                                                                                    </div>
                                                                                                </div>
-->



<?php
$_SESSION['blog_plug'] = "
<div style='font-family:verdana; border: 1px solid #AAAAAA; background-color: white; width: 100%; text-align: center; padding: 2px 0 10px 0;'><p style='margin: 0'><a href='http://www.blogcalculator.com/'><img src='http://www.blogcalculator.com/2.jpg' style='border:0px;' height='71' width='110' alt='Blog Money Valuation'></a><br />
<span style='font-size: 12px; line-height: 14px;'>My <a href='" . $url . "'>blog</a> has been valued at...</span>
<span style='font-size: 16px;'><b>$" . $totalvalue . "</b></span>
<span style='font-size: 9px; line-height: 10px;'><br /><br /><a href='http://www.blogcalculator.com'>Blog Valuation Tool from BlogCalculator.com</a></span></p></div>
                                                                                                        ";
echo $_SESSION['blog_plug'];
?>

<!--                                                                                                <div class="both">
                                                                                                    <div class="widget">
                                                                                                        <div style="font-family:verdana; border: 1px solid #AAAAAA; background-color: white; width: 120px; text-align: center; padding: 2px 0 10px 0;">
                                                                                                            <p style="margin: 0"><a href="http://www.blogcalculator.com/"><img src="http://www.blogcalculator.com/2.jpg" style="border:0px;" border="0" height="71" width="110" alt="Blog Money Valuation"></a><br />
                                                                                                                <span style="font-size: 12px; line-height: 14px;">My <a href="<?php echo $url; ?>">blog</a> has been valued at...</span>
                                                                                                                <span style="font-size: 16px;"><b>$<?php echo $totalvalue; ?></b></span>
                                                                                                                <span style="font-size: 9px; line-height: 10px;"><br /><br /><a href="http://www.blogcalculator.com">Blog Valuation Tool from BlogCalculator.com</a></span></p></div>
                                                                                                    </div>
                                                                                                    <div class="widgetcode">
                                                                                                        <textarea>
<div style="font-family:verdana; border: 1px solid #AAAAAA; background-color: white; width: 120px; text-align: center; padding: 2px 0 10px 0;"><p style="margin: 0"><a href="http://www.blogcalculator.com/"><img src="http://www.blogcalculator.com/2.jpg" style="border:0px;" height="71" width="110" alt="Blog Money Valuation"></a><br />
<span style="font-size: 12px; line-height: 14px;">My <a href="<?php echo $url; ?>">blog</a> has been valued at...</span>
<span style="font-size: 16px;"><b>$<?php echo $totalvalue; ?></b></span>
<span style="font-size: 9px; line-height: 10px;"><br /><br /><a href="http://www.blogcalculator.com">Blog Valuation Tool from BlogCalculator.com</a></span></p></div>
                                                                                                        </textarea>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="clear"></div>
                                                                                                Remember this site is just for fun and <b>A blog is only worth what someone is willing to pay you for it.</b><br><br>
                                                                                                        We hope you enjoyed BlogCalculator.com - Remember to bookmark us so you can see whether your blog increases in value.
                                                                                                        <br><br>
                                                                                                                <center>
                                                                                                                    <a href="http://www.dpbolvw.net/click-3758192-10376704" target="_top">
                                                                                                                        <img src="http://www.blogcalculator.com/bluehost3.gif" width="468" height="60" alt="Bluehost.com Web Hosting $6.95" border="0"/></a>
                                                                                                                </center>
                                                                                                                </div>
                                                                                                                </div>
                                                                                                                </div>
                                                                                                                <div class="clear"></div>
                                                                                                                <div id="footer">
                                                                                                                    <div id="footer-in">
                                                                                                                        <div class="privacy">
                                                                                                                            We respect your privacy and none of the information you submit is stored in anyway, it is only used to generate the valuation using our algorithm and we can't even see what you have entered. 
                                                                                                                        </div>
                                                                                                                        <div class="copy">
                                                                                                                            &copy; Copyright 2008 - 2010 - BlogCalculator.com - [ <a href="contactus.html">Contact Us</a> ]
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                </div>-->
<!-- Start of StatCounter Code -->
<script type="text/javascript">
    var sc_project=4118828; 
    var sc_invisible=1; 
    var sc_partition=49; 
    var sc_click_stat=1; 
    var sc_security="b9ded058"; 
</script>
<!--                                                                                                                <script type="text/javascript" src="http://www.statcounter.com/counter/counter.js"></script><noscript><div class="statcounter"><a title="web analytics" href="http://www.statcounter.com/" target="_blank"><img class="statcounter" src="http://c.statcounter.com/4118828/0/b9ded058/1/" alt="web analytics" ></a></div></noscript>
 End of StatCounter Code 

</body>
</html>-->
