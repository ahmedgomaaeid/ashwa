<?php
session_start();
//$_SESSION = array();
//echo 'test';
include 'simple-php-captcha.php';
$_SESSION['captcha'] = simple_php_captcha();
// +------------------------------------------------------------------------+
// | @author Ercan Agkaya (Themerig)
// | @author_url 1: https://www.themerig.com
// | @author_url 2: https://codecanyon.net/user/themerig
// | @author_email: support@themerig.com
// +------------------------------------------------------------------------+
// | Craigs Cms - Directory Listing Theme
// | Copyright (c) 2018 Directory & Listings CMS. All rights reserved.
// +------------------------------------------------------------------------+

include 'includes/head.php';
include 'includes/header.php';

if (!isset($_SESSION['like_action'])) {
    $_SESSION['like_action'] = '';
}

echo '<img src="/logo/amartil.png" alt="AMARTIL logo" style=" display: none; ">
    <link href="https://cdn.jsdelivr.net/npm/vanilla-calendar-pro/build/vanilla-calendar.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vanilla-calendar-pro/build/vanilla-calendar.min.js" defer></script>
    <style>
        #calendar2
        {
            margin:auto;
            width:100%;
            border: #ccc 1px solid;
        }
        .vanilla-calendar-column
        {
            min-width:300px;

        }





        .vanilla-calendar-day__btn_disabled
        {
            text-decoration: line-through !important;

        }




        .vanilla-calendar-day__btn {
            font-size: 1.6rem !important;
            line-height: 4rem !important;
        }
        .vanilla-calendar-week__day {
            font-size: 1.4rem !important;
            line-height: 3rem !important;
        }
        .vanilla-calendar-grid {
            display: flex;
            flex-grow: 1;
            justify-content: space-evenly;
            gap: 1.75rem;
        }
            #like_count,#dislike_count{
              margin:0 auto;
              display:block;
              text-align:center;
              width:100%;
              border-radius:20px;
            }
            #like_count{
                color:#0ABF00;
            }
            #dislike_count{
                color:#F20000;
            }
            #like_btn,#dislike_btn{
                padding:0;
                cursor:pointer;
                margin:0 auto;
                display:block;
                width:36px;
            }
            #like_btn img,#dislike_btn img{
             /*float:left;
             display:block;
             width:36px;
             height:44px;
             cursor:pointer;*/
            }
            section h2{

            margin-bottom:10px;
            }
            .text-holder{
                border: #ccc 1px solid;
background: #ffffff;
padding: 10px;
font-size: 16px;
color: black;
font-weight: bold;
            }
            .like_btn{
              display:block;
              width:36px;
              margin:0 auto;
            }
            .like_count{

            }
            .like_wrapper{
               float:left;
               position:relative;
               display:block;
               width:50%;
               padding:3px;

            }
            .time_unit{
                background:#000000;
                text-align:center;
                font-weight:normal;
                width:80px;
                display:block;
                float:left;
                font-size:20px;
                line-height:36px;
                padding:5px 5px;
                color:#ffffff;
            }

 .embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; }
 .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }

          </style>';

echo '<style>.img{
padding-left:15px;
}
.block section{
    margin-bottom:2rem !important;
}

.tag {
 background: none repeat scroll 0 0 #EE7407 !important;
 border-radius: 2px;
 color: white;
 cursor: default;
 display: inline-block;
 position: relative;
 white-space: nowrap;
 padding: 7px 20px 7px 0px ;
 margin: 5px 10px 0 0;
}

.tag span {
 background: none repeat scroll 0 0 #D66806;
 border-radius: 2px 0 0 2px;
 margin-right: 5px;
 padding: 5px 10px 5px;
}
h4.location {
    display: inline-block;
    margin-right: 25px;
}

</style>
<script src="assets/js/jquery-3.2.1.min.js"></script>';

$visits = 0;

if (!empty($_GET['id'])) {
    $items = $db->query('SELECT * FROM items WHERE id = ' . $_GET['id'])->fetch();
    //var_dump($items);
    $view = $db->prepare('update items set views = views +1 where id = ? ');
    $view->execute([$items['id']]);
    $counter = $db->query('SELECT COUNT(*) as cont FROM user_hits WHERE item_id = ' . $_GET['id'])->fetch();

    if (!isset($_SESSION['visited'])) {
        $_SESSION['visited'] = 1;
        $visit_count = $db->prepare('update items set visit_count = visit_count +1 where id = ? ');
        $visit_count->execute([$items['id']]);

        $counter_ip = $_SERVER['REMOTE_ADDR'];
        $counter_time = time();
        $counter = $db->query('SELECT COUNT(*) as cont FROM user_hits')->fetch();
        $c = $counter['cont'];
        ++$c;

        $count = $db->prepare('INSERT INTO user_hits VALUES(?,?,?,?)');
        $count->execute([$c, $_GET['id'], time(), '']);

        $counter = $db->query('SELECT COUNT(*) as cont FROM user_hits WHERE item_id = ' . $_GET['id'])->fetch();
        $visits = $counter['cont'];
    } else {
        $counter = $db->query('SELECT COUNT(*) as cont FROM user_hits WHERE item_id = ' . $_GET['id'])->fetch();
        $visits = $counter['cont'];
    }
} else {
    header('Location: index.php');
}
/*
 $adsns = $db -> query("SELECT * FROM adsense")->fetch();

 if (!empty($_SESSION['session'])) {

  if ($users['st'] != "1") {
   if ($items['user_id'] != $users['id']) {
    if ($items['permit'] != "1") {
     header("Location: index.php");
    }

    if ($items['sale_status'] != "0") {
     header("Location: index.php");
    }
   }
  }

 } else {

  if ($items['permit'] != "1") {
   header("Location: index.php");
  }

  if ($items['sale_status'] != "0") {
   header("Location: index.php");
  }

 }*/

if (empty($items['id'] == $_GET['id'])) {
    header('Location: index.php');
}
/*
 if (empty(seo($items['title']) == $_GET['title'])) {
  header("Location: index.php");
 }
 */
$query = $db->prepare("SELECT COUNT(*) FROM items WHERE user_id = '" . $items['user_id'] . "' AND permit = 1 AND sale_status NOT IN('1','2')");
$query->execute();
$users_count = $query->fetchColumn();
//echo $users_count;

$usr = $db->query('SELECT * FROM users WHERE id = ' . $items['user_id'])->fetch();
//	echo $items['user_id'];
//	print_r($usr);
echo '<input type="hidden" id="item_id" value="' . $_GET['id'] . '" />';

echo '<div class="page-title">
                    <div class="container clearfix">
                        <div class="float-left float-xs-none">
                            <h1><title style="  display: initial; text-transform:uppercase; ">';
if (!empty($items['title'])) {
    echo $items['title'];
}
if (!empty($items['type'])) {
    echo '<span class="tag">' . $items['type'] . '</span>';
}
echo '</title></h1><br>
                            <h4 class="location">';
$item_category = $db->query("SELECT * FROM category WHERE `id` = '{$items['category']}'")->fetch();
if (!empty($item_category)) {
    echo '<a> ' . $item_category['ctg_name'] . '</a>';
}

echo '</h4>';
echo '<h4 class="location">';
if (!empty($items['address'])) {
    echo '<a> ' . $items['address'] . '</a>';
}

echo '</h4><br>';

echo '

            <br>


                        <a class="btn btn-primary" class="keya" style="padding: 1.4rem 0rem; border-radius: 2px;box-shadow: 0 1px 6px rgba(0, 0, 0, 0);text-overflow: ellipsis;white-space: nowrap;display: inline-block;border: 0;background-color: #fff;">Le Numéro D&#039;AMartil®</a>




  <style>


@keyframes ani {
  0% {
    transform: translate(0, 0);
  }
  10% {
    transform: translate(-3px, 0);
  }
  20% {
    transform: translate(0px, 0);
  }
  30% {
    transform: translate(0, 0);
  }
  40% {
    transform: translate(0, 0);
  }
  50% {
    transform: translate(0, 0);
  }
  60% {
    transform: translate(0px, 0);
  }
  70% {
    transform: translate(-3px, 0);
  }
  80% {
    transform: translate(3px, 0);
  }
  90% {
    transform: translate(-3px, 0);
  }
  100% {
    transform: translate(0, 0);
  }
}
  </style>


<button style="	animation: ani 3s ease-in infinite;" id="ani">


 <a href="#call" class="btn btn-primary text-caps btn-framed" style="border-color: #f68f33; font-size: initial;"> <div id="resultc"></div>+2126 إضغط هنا لإظهار الرقم</a>

 </button>



                        <div class="float-right float-xs-none price">';
if (!empty($items['price'])) {
    //echo '<div class="number" style="text-align: center;">'.number_format($items['price'],"0","",".").' '.$settings['currency'].'</div>';
    echo '<div class="number" style="text-align: center;">' . $items['price'] . '<sup style="top: -8px;font-size: 21px;color: darkorange;"> DH</sup><a href="https://www.amartil.com/terms/" target="_blank" style="font-size: initial;color: crimson;">+ عمولة أمرتيل</a></div>';
}
if (!empty($items['price_appendix'])) {
    echo '<div class="number" style="text-align: center;color: #00000070;font-size: x-large;"><strike>' . $items['price_appendix'] . '</strike> ' . $settings['currency'] . '<a style="color: black;font-size: large;"> :السعر القديم</a></div>';
}
if (!empty($items['price_appmois'])) {
    echo '<div class="number" style="text-align: center;">' . $items['price_appmois'] . ' <a style="font-size: x-large;color: black;">:الكراء الشهري</a></div>';
}
if (!empty($items['id'])) {
    echo '<div class="txt-animate"><div class="id opacity-100" style="right: 1.2rem;background:#ff8c00;color:#ffffff;padding:5px 10px;border-radius:20px !important;text-align: center;">
         <strong>CODE: </strong>' .
        $items['id'] .
        ' Martil
        </div></div><br><br>';
}
echo '




    <div id="call" class="overlay1">
 <div class="call">
 <p>



<a class="close" href="#" style="
    opacity: 1.5;
">×</a>



<div dir="rtl" style="text-align: right;" trbidi="on">
<div style="text-align: right;">
<span style="font-size: x-large;">مرحبا! <span style="font-size: large;">المرجو إعطائنا هذا الكود:</span></span></div><div style="text-align: center;"><span style="font-size: x-large;"><span style="font-size: large;"><span style="color: yellow;"><span style="background: rgb(73, 184, 255) none repeat scroll 0% 0%; border-radius: 20px; color: white; padding: 1px 17px;"><b>CODE: </b>' .
    $items['id'] .
    ' Martil</span></span> </span></span></div>
<div class="content">

</div></div>
<br>
<section id="hire">

    <form>
<div dir="rtl" style="text-align: right;" trbidi="on">
<div style="text-align: right;">
<span style="font-size: large;"><b>للإتصال بنا عبر الهاتف: </span>



<b style="
    font-size: x-large;
    background: #ffb9b961;
    padding: 2px 35px;
">

<SCRIPT LANGUAGE="JAVASCRIPT">
<!--

var r_text = new Array ();
r_text[0] = "<a href=tel:+212644409300>212644409300</a>";
r_text[1] = "<a href=tel:+212630009590>212630009590</a>";


var i = Math.floor(r_text.length * Math.random());

document.write("" +
r_text[i]  );

document.body.style.background=bgcolorlist[Math.floor(Math.random()*bgcolorlist.length)];

</script>




</span></b>



</span><br>
<li>
 يمكنك الضغط على الرقم للإتصال مباشرة
</li>
<span style="font-size: large;"><b></b></span>
<span style="font-size: large;"><b style="color: #6a2121; font-weight: initial;">وقت الإتصال من: 09:00 إلى 22:00 كل أيام الاسبوع </b></span>

</div>
<div style="text-align: right;">
<span style="font-size: large;">

</span></div>
<div style="text-align: right;">
<div style="text-align: right;">
<br>
<ul style="text-align: right;">
<li><span style="font-size: large;"><b>للتواصل عبر Whatsapp إضغط على أيقونة الواتساب:</b></span></li>
</ul>
<div dir="rtl" style="text-align: right;" trbidi="on">
<div dir="rtl" style="text-align: right;" trbidi="on">
<ul style="text-align: right;">
<a href="https://api.whatsapp.com/send?phone=212644409300&amp;text=سلام AMARTIL.com كنتواصل معاكم حول هاد العقار:  CODE: ' .
    $items['id'] .
    ' Martil" imageanchor="1" style="clear: right; float: right; margin-bottom: 1em; margin-left: 1em;" target="_blank"><img border="0" data-original-height="512" data-original-width="512" height="80" src="https://1.bp.blogspot.com/-Muena6HPmCs/YEViDCCsPwI/AAAAAAAAATY/pDHN0JjdKy0j_pk5kiQHrIlmyZVCc5JfgCLcBGAsYHQ/s320/whatsapp.png" width="80"></a>
<li style="color: #6a2121;">

إذا كان الرقم لايجيب قم بالتواصل معنا في أي وقت عبر واتساب أو SMS </li>
</ul>
<div style="text-align: right;">
<span style="color: #444444;font-size: small;"><b style="
    color: #5b7096;
">- المرجو الإطلاع على صفحة <a href="https://www.amartil.com/terms/" target="_blank" style="
    color: #001fff !important;
">رسوم الخدمة</a> قبل الإتصال بنا, فإنه بإتصالك توافق على كل شروطنا</b></span><br>
<span style="color: #444444; font-size: x-small;"><b style="color: teal; font-size: medium;">نسعد بتواصلك</b></span></div>

</form>
</section></div></div></div></div>

    <div class="background"></div>
            </div>


        </header>';

echo '<section class="content">
            <section class="block">
                <div class="container">';
// echo $items['available_after'];
if ($items['available_after'] > 0) {
    $now = $items['create_date'];
    //echo $now.':'.date('j M Y G:i:s',$now).'<br/>';
    $days = $items['available_after'];
    $date = strtotime('+' . $days . ' days', $now);
    $avdate = date('j M Y G:i:s', $date);

    echo '<div class="row" id="available_block">
                           <div class="col-md-12" style="color:#000000;text-align:center;font-size:20px">Available after</div>
                           <div class="col-md-3"></div>
                           <div class="col-md-6">
                             <ul style="color:#000000;list-style:none;text-align:center;font-size:20px;padding:5px;border:#cccccc 0px solid;height:60px;width:350px;display:block;margin:0 auto !important;" id="countdown">
                               <li class="time_unit" id="days" style="margin-right:6px;"></li>
                               <li class="time_unit" id="hours" style="margin-right:6px;"></li>
                               <li class="time_unit" id="minutes" style="margin-right:6px;"></li>
                               <li class="time_unit" id="seconds"></li>
                             </ul>
                           </div>
                           <div class="col-md-3"></div>
                       </div>

<script>
    var countDownDate = new Date("' .
            $avdate.
            '").getTime();

            // Update the count down every 1 second
            var x = setInterval(function() {

                // Get todays date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                //document.getElementById("countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                $("#days").html("" + days + " d");
                $("#hours").html("" + hours + " h");
                $("#minutes").html("" + minutes + " m");
                $("#seconds").html("" + seconds + " s");

                if (distance < 0) {
                    clearInterval(x);
                    $("#days").html("");
                    $("#hours").html("");
                    $("#minutes").html("");
                    $("#seconds").html("");
                    $("#available_block").hide();

                }
            }, 1000);
</script>
';
}
echo '<div class="row">';
if (!empty($_SESSION['session'])) {
    $gallery = $db->prepare("SELECT * FROM gallery WHERE item_id = '" . $items['id'] . "' ORDER BY thumb_id DESC");
    $gallery->execute();
    if ($gallery->rowCount()) {
        echo '<div class="down-links">';
        foreach ($gallery as $row) {
            echo '<a href="' . $row['image'] . '" download="' . $row['image'] . '" style="display:none;" class="download_btns"></a>';
        }

        echo '</div>';
    }
}

echo '<div class="col-md-8">
                            <section>
';

$gallery = $db->prepare("SELECT * FROM gallery WHERE item_id = '" . $items['id'] . "' ORDER BY thumb_id DESC");
$gallery->execute();
if ($gallery->rowCount()) {
    echo '<div class="gallery-carousel owl-carousel">';
    foreach ($gallery as $row) {
        echo '<img src="' . $row['image'] . '" alt="' . $items['title'] . '" data-hash="' . $row['id'] . '">';
    }

    echo '</div>';
}

$gallery = $db->prepare("SELECT * FROM gallery WHERE item_id = '" . $items['id'] . "' ORDER BY thumb_id DESC");
$gallery->execute();
if ($gallery->rowCount()) {
    echo '<div class="gallery-carousel-thumbs owl-carousel">';
    foreach ($gallery as $row) {
        echo '<a href="#' .
            $row['id'] .
            '" class="owl-thumb background-image">
         <img src="' .
            $row['image'] .
            '" alt="' .
            $items['title'] .
            '">
                                </a>';
    }

    echo '</div>';
}

echo '<section style="margin-bottom:10px;display:none">
                                    <div class="box" style="padding: 5px;padding-top: 0;">
                                        <div class="row" style="padding:10px;padding-bottom:0;padding-top: 0;">
                                          <div class="col-md-12" style="text-align:center;background:#000;color:#ffffff;padding:10px;line-height:30px">Publish Date</div>
                                        </div>
    <div class="row" style="padding:10px;padding-top:0">
                                          <div class="col-md-12" style="text-align:center;font-size:14px;background:#ffffff;padding:10px;line-height:30px">' .
    date('j M Y G:i', $items['create_date']) .
    '</div>
                                        </div>

                                    </div></section>';

if ($items['expire_date'] !== null && $items['expire_date'] < time()) {
    echo '<br><section style="margin-bottom:10px;">
                                    <div class="box" style="padding: 5px;padding-top: 0;background:#FFD740;">

    <div class="row" style="padding:10px;padding-top:0;height: 100%;">

<div class="col-md-12" style="text-align:center;font-size:x-large;color:#000000;padding:10px;line-height:30px;">
</div><p style="text-align: center;width: 100%;font-size: xx-large;color: #ff3c00;">لقد نجحنا في بيع هذا العقار</p>
<p style="text-align: center;width: 100%;font-size: x-large;"> لكن, لاتقلق 😎 لدينا الكثير الكثير من العقارات مطروحة للبيع في موقعنا</p>




                                    </div></section>';
}

echo '<section>
                                <h2>' .
    $lang['product_description'] .
    '</h2>';
if (!empty($items['description'])) {
    echo '<p class="text-holder" style="text-align: right; direction:rtl;">' . str_ireplace('</div>', '', str_ireplace('<div>', '<br>', $items['description'])) . '</p>';
}
echo '</section>';

if (!empty($items['video_url'])) {
    echo '<section><div class="center">

                                                <p name="youtube" class="text-holder" style="font-size: 100%;" id="youtube">' .
        $items['video_url'] .
        '</p>

                                            </div>
                                            <div class="center">
                                            <div class="embed-container">
                                              <iframe id="videoObject" type="text/html" width="300" height="250" frameborder="0" allowfullscreen></iframe>
                                             <div>
                                            </div></section>';
}

$i_b_1 = $db->query("SELECT * FROM i_category_box_1 WHERE item_id = '" . $items['id'] . "'")->fetch(PDO::FETCH_ASSOC);
$i_b_2 = $db->query("SELECT * FROM i_category_box_2 WHERE item_id = '" . $items['id'] . "'")->fetch(PDO::FETCH_ASSOC);
$i_b_3 = $db->query("SELECT * FROM i_category_box_3 WHERE item_id = '" . $items['id'] . "'")->fetch(PDO::FETCH_ASSOC);

if ($i_b_1 or $i_b_2 or $i_b_3) {
}

$i_category_box_5 = $db->prepare("SELECT * FROM i_category_box_5 WHERE item_id = '" . $items['id'] . "'");
$i_category_box_5->execute();

if ($i_category_box_5->rowCount()) {
    echo '<section>

                                <h2>' .
        $lang['product_features'] .
        '</h2>
                                <ul class="features-checkboxes columns-4">';
    foreach ($i_category_box_5 as $row) {
        $category_box_5 = $db->query("SELECT * FROM category_box WHERE `id` = '{$row['ctg_bx_5_id']}'")->fetch();
        echo '<li>' . $category_box_5['ctg_bx_name'] . '</li>';
    }

    echo '</ul>



                            </section>';

    echo '<section>
         <h2>' .
        $lang['product_details'] .
        '</h2>
         <dl class="columns-2">';

    $i_category_box_1 = $db->prepare("SELECT * FROM i_category_box_1 WHERE item_id = '" . $items['id'] . "'");
    $i_category_box_1->execute();
    if ($i_category_box_1->rowCount()) {
        foreach ($i_category_box_1 as $row) {
            $category_box_1 = $db->query("SELECT * FROM category_box_1 WHERE `id` = '{$row['ctg_bx_1_id']}'")->fetch();
            $category_box = $db->query("SELECT * FROM category_box WHERE `id` = '{$category_box_1['category_box_id']}'")->fetch();
            echo '<dt>' .
                $category_box['ctg_bx_name'] .
                '</dt>
           <dd>' .
                $category_box_1['name'] .
                '</dd>';
        }
    }

    $i_category_box_2 = $db->prepare("SELECT * FROM i_category_box_2 WHERE item_id = '" . $items['id'] . "'");
    $i_category_box_2->execute();
    if ($i_category_box_2->rowCount()) {
        foreach ($i_category_box_2 as $row) {
            $category_box_2 = $db->query("SELECT * FROM category_box WHERE `id` = '{$row['ctg_bx_2_id']}'")->fetch();
            echo '<dt>' .
                $category_box_2['ctg_bx_name'] .
                '</dt>
           <dd>' .
                $row['ctg_bx_2_subj'] .
                '  ' .
                $category_box_2['text_val'] .
                '</dd>';
        }
    }

    $i_category_box_3 = $db->prepare("SELECT * FROM i_category_box_3 WHERE item_id = '" . $items['id'] . "'");
    $i_category_box_3->execute();
    if ($i_category_box_3->rowCount()) {
        foreach ($i_category_box_3 as $row) {
            $category_box_3 = $db->query("SELECT * FROM category_box WHERE `id` = '{$row['ctg_bx_3_id']}'")->fetch();
            echo '<dt>' .
                $category_box_3['ctg_bx_name'] .
                '</dt>
           <dd>' .
                $row['ctg_bx_3_subj'] .
                '</dd>';
        }
    }

    echo '</dl>
       </section>';
}

if ($items['purpose'] == 'louer') {
    echo '<h2>' .
        $lang['product_dispo'] .
        '</h2><section>
                                    <div id="calendar2"></div>';
    echo '</section>';
}

echo '<div class="mobileHide"><section>
                                <h2>' .
    $lang['product_location'] .
    '</h2>
                                <div class="map height-300px" id="map-small"></div>
                            </section></div>';

$i_category_box_5 = $db->prepare("SELECT * FROM i_category_box_5 WHERE item_id = '" . $items['id'] . "'");
$i_category_box_5->execute();

if ($i_category_box_5->rowCount()) {
    echo '<section>

                                                <h2>' .
        $lang['product_tags'] .
        '</h2>
                                                <div class="columns-4">';
    $tags_query = $db->query("SELECT * FROM items WHERE `id` = '{$items['id']}'")->fetch();
    $tags_arr = explode(',', $tags_query['tags']);
    foreach ($tags_arr as $tag) {
        echo '<div class="tag"><span>#</span>' . $tag . '</div>';
    }

    echo '</div><br></section>';

    echo '<hr>




                                   <script>
                                       var idcomments_acct = "5b5500ebe1eedb890e84d9388299d7f1";
                                       var idcomments_post_id;
                                       var idcomments_post_url;
                                   </script>
<span id="IDCommentsPostTitle" style="display:none"></span>
<script type="text/javascript" src="https://www.intensedebate.com/js/genericCommentWrapperV2.js"></script>





                              ';

    include 'share.php';
}

$item = $db->prepare("SELECT * FROM items WHERE user_id = '" . $items['user_id'] . "' AND permit = 1 AND sale_status NOT IN('1','2') AND id NOT IN('" . $items['id'] . "') ORDER BY rand() LIMIT 3");
$item->execute();
if ($item->rowCount()) {
    $query = $db->prepare("SELECT COUNT(*) FROM items WHERE user_id = '" . $items['user_id'] . "' AND permit = 1 AND sale_status NOT IN('1','2') AND id <> " . $items['id']);
    $query->execute();
    $item_count = $query->fetchColumn();
    echo '<section>
                                <h2><br>' .
        $lang['product_other_ads_this_person_has_shared'] .
        '</h2>

                                <div class="items list compact">';

    foreach ($item as $row) {
        $product_gallery = $db->query("SELECT * FROM gallery WHERE `item_id` = '{$row['id']}'")->fetch();
        $product_category = $db->query("SELECT * FROM category WHERE `id` = '{$row['category']}'")->fetch();
        $product_user = $db->query("SELECT * FROM users WHERE `id` = '{$row['user_id']}'")->fetch();

        $ttl = $row['title'];
        $limit = 28;
        $text = strlen($ttl);
        $title = substr($ttl, 0, $limit);

        $adr = $row['address'];
        $limit = 51;
        $text = strlen($adr);
        $address = substr($adr, 0, $limit);

        echo '<div class="item">';
        if ($row['featured'] == '1') {
            echo '<div class="ribbon-featured">' . $lang['product_featured'] . '</div>';
        }

        echo '<div class="wrapper">
                                            <div class="image">

                                                <h3>';
        if (!empty($product_category['ctg_name'])) {
            echo '<a class="tag category">' . $product_category['ctg_name'] . '</a>';
        }
        echo '<a href="product.php?id=' . $row['id'] . '" class="title">' . $title . '</a>';
        if (!empty($row['type'])) {
            echo '&nbsp;&nbsp;<span class="tag"> ' . $row['type'] . '</span>';
        }

        echo '</h3>

                                                <a href="product.php?id=' .
            $row['id'] .
            '" class="image-wrapper background-image">';
        if (!empty($product_gallery['image'])) {
            echo '<img src="' . $product_gallery['image'] . '" alt="' . $items['title'] . '">';
        }

        echo '</a>
                                            </div>

                                            <h4 class="location">';
        if ($row['address']) {
            echo '<a>' . $address . '</a>';
        }

        echo '</h4>';

        if (!empty($row['price'])) {
            echo '<div class="price">' . number_format($row['price'], '0', '', '.') . ' ' . $settings['currency'] . '</div>';
        }

        echo '<div class="meta">

                                                <figure>
                                                    <i class="fa fa-calendar-o"></i>' .
            timeConvert(date('d.m.Y H:i:s', $row['create_date'])) .
            '
                                                </figure>

                                                <figure>
                                                    <a href="profile_detail.php?users=' .
            $product_user['fullname'] .
            '">
                                                        <i class="fa fa-user"></i>' .
            $product_user['fullname'] .
            '
                                                    </a>
                                                </figure>

                                            </div>';

        if (!empty($row['description'])) {
            echo '<div class="description">
                                                <p>' .
                strip_tags($row['description']) .
                '</p>
                                            </div>';
        }

        echo '<div class="additional-info">
              <ul>

               <li>
                <figure><i class="fa fa-calendar-o">&nbsp;</i> ' .
            $lang['product_create_date'] .
            '</figure>
                <aside>' .
            date('d.m.Y H:i:s', $row['create_date']) .
            '</aside>
               </li>

              </ul>
             </div>';

        echo '<!--<a href="product.php?id=' .
            $row['id'] .
            '" class="detail text-caps underline" style="text-decoration:none !important;" >' .
            $lang['product_detail'] .
            '</a>-->
                                        </div>
                                    </div>';
    }

    if ($item_count >= '3') {
        echo '<div class="center">
                                        <a href="profile_detail.php?users=' .
            $product_user['username'] .
            '" class="btn btn-primary text-caps btn-framed" style="
    width: 130px;
">' .
            $lang['product_show_all'] .
            '</a>
                                    </div>





                                    ';
    }
    echo '</div>


                            </section>';
}

echo '</div>


                        <div class="col-md-4">

                            <aside class="sidebar">';

if (!empty($_SESSION['session'])) {
    echo '<button style="	animation: ani 9s ease-in infinite;     width: -webkit-fill-available;" id="imgsdownload" onclick="download_images()">



                                <a href="#download" class="btn btn-primary text-caps btn-framed" style="background: #ff3c00;border-color: #f68f33; font-size: initial; width: inherit;"> <div id="resultc"></div>تحميل الصور</a>
                            </button>';
}
$like_img = '';
$dislike_img = '';
//echo $_SESSION['like_action'];
if (!isset($_SESSION['like_action']) || $_SESSION['like_action'] == '') {
    $like_img = '<button id="like_btn" class="like_btn unliked" style="background:none;border:none;"><img src="images/like_inactive.png" /></button>';
    $dislike_img = '<button id="dislike_btn" class="like_btn undisliked" style="background:none;border:none;"><img src="images/dislike_inactive.png" /></button>';
} else {
    if (isset($_SESSION['like_action']) && $_SESSION['like_action'] == 'like') {
        $like_img = '<button id="like_btn" class="like_btn liked" style="background:none;border:none;"><img src="images/like.png" /></button>';
        $dislike_img = '<button id="dislike_btn" class="like_btn undisliked" disabled="disabled" style="background:none;border:none;"><img src="images/dislike_inactive.png" /></button>';
    } elseif (isset($_SESSION['like_action']) && $_SESSION['like_action'] == 'unlike') {
        $like_img = '<button id="like_btn" class="like_btn unliked" style="background:none;border:none;"><img src="images/like_inactive.png" /></button>';
        $dislike_img = '<button id="dislike_btn" class="like_btn undisliked" style="background:none;border:none;"><img src="images/dislike_inactive.png" /></button>';
    }
    if (isset($_SESSION['like_action']) && $_SESSION['like_action'] == 'dislike') {
        $like_img = '<button id="like_btn" class="like_btn unliked" disabled="disabled" style="background:none;border:none;"><img src="images/like_inactive.png" /></button>';
        $dislike_img = '<button id="dislike_btn" class="like_btn disliked" style="background:none;border:none;"><img src="images/dislike.png" /></button>';
    } elseif (isset($_SESSION['like_action']) && $_SESSION['like_action'] == 'undislike') {
        $like_img = '<button id="like_btn" class="like_btn unliked" style="background:none;border:none;"><img src="images/like_inactive.png" /></button>';
        $dislike_img = '<button id="dislike_btn" class="like_btn undisliked" style="background:none;border:none;"><img src="images/dislike_inactive.png" /></button>';
    }
}
if (isset($_SESSION['like_action'])) {
    echo '<input type="hidden" id="like_status" value="' . $_SESSION['like_action'] . '" />';
} else {
    echo '<input type="hidden" id="like_status" value="" />';
}
echo '<section style="margin-bottom:10px;">


       <div class="row">
       <div class="col-md-12" style="padding:0 !important;">
       <div class="like_wrapper">' .
    $like_img .
    '<div id="like_count" class="text-holder">' .
    $items['like_count'] .
    '</div></div>
       <div class="like_wrapper" style="margin-left:0px">' .
    $dislike_img .
    '<div id="dislike_count" class="text-holder">' .
    $items['dislike_count'] .
    '</div></div>
       </div>

       </div>

       </section>';

echo '<section style="margin-bottom:10px;">
                                    <div class="box" style="padding: 5px;padding-top: 0;">
                                        <div class="row" style="padding:10px;padding-bottom:0;padding-top: 0;padding-bottom:0">
                                          <div class="col-md-12" style="text-align:center;background:#000;color:#ffffff;line-height: 40px;font-size: 21px;">' .
    date('j M - G:i', $items['create_date']) .
    '</div>
                                        </div>
    <div class="row" style="padding:10px;padding-top:0;height: 49px;">
                                          <div class="col-md-12" style="text-align:center;font-size:30px;background:#ffffff;padding:inherit;">' .
    $visits .
    '<img src="visits.png" class="img" /></div>
                                        </div>

                                    </div></section>';

echo '

<div class="box" style="
    padding: 0rem;
">


 <style>
ul.techornate-follow-buttons a:hover{color:#f79a38}.techornate-follow-buttons.b-title li{text-align:center;background-color:#fff;width:32%;}.techornate-follow-buttons.b-title a{text-transform:uppercase;text-decoration:none;margin:20px auto 0;font-size:10px}.techornate-follow-buttons li{display:inline-block;padding:0 0 7px;margin-bottom:3px!important}.techornate-follow-buttons li:last-child{padding-bottom:7px!important}.techornate-follow-buttons.b-title i{margin:0 auto 10px;display:block}.techornate-follow-buttons.style-1 i{height:32px;width:32px;line-height:32px;font-size:20px;margin:10px auto 0;text-align:center;color:#333}
</style>


<ul class="techornate-follow-buttons style-1 b-title" style="
    padding-inline-start: 0px;
    margin-bottom: 0rem;
    width: 103%;
">


<li><a href="https://www.facebook.com/amartil.help" target="_blank"><i class="fa fa-facebook" style="
    font-size: xx-large;
"></i>50000+ Likes</a>

</li><li><a href="https://www.youtube.com/c/AMARTILOfficial" target="_blank"><i class="fa fa-youtube" aria-hidden="true" style="
    font-size: xx-large;
"></i>50000+ Sub</a></li>

<li><a href="https://www.instagram.com/amartilcom/" target="_blank"><i class="fa fa-instagram" style="
    font-size: xx-large;
"></i>100K+ Follow</a></li>




</ul>


<ul class="techornate-follow-buttons style-1 b-title" style="
    padding-inline-start: 0px;
    margin-bottom: 0rem;
    width: 102%;
">



<li><a href="https://www.facebook.com/groups/amartil/" target="_blank"><i class="fa fa-users" aria-hidden="true" style="
    font-size: xx-large;
"></i>FB Group</a></li>


<li style="
"><a href="https://twitter.com/amartilcom" target="_blank"><i class="fa fa-twitter" style="
    font-size: xx-large;
"></i>Twitter</a></li>


<li style="
"><a href="http://t.me/amartil" target="_blank"><i class="fa fa-telegram" aria-hidden="true" style="
    font-size: xx-large;
"></i>Telegram</a></li>


</ul>


 </div>
<br>

';

echo '
                                <section>

                                    <div class="box">
                                        <div class="author"><h3 style="
    text-align: center;
">أطلب زيارة العقار</h3>
                                            </div>
                                        <hr>
                                        <dl>';
/* if (!empty($usr['contact_phone'])) {
           echo'<dt>'.$lang['product_phone'].'</dt>
             <dd>'.$usr['contact_phone'].'</dd>';
           }

           if (!empty($usr['contact_email'])) {
            echo'<dt>'.$lang['product_email'].'</dt>
             <dd>'.$usr['contact_email'].'</dd>';

           }*/
/*
           if (!empty($usr['phone'])) {
            if ($usr['hide_phone'] == "1") {
           echo'<dt>'.$lang['product_phone'].'</dt>
             <dd>'.$lang['product_secret_phone_number'].'</dd>';
            } else {
           echo'<dt>'.$lang['product_phone'].'</dt>
             <dd>'.$usr['phone'].'</dd>';
            }
           }

           if (!empty($usr['email'])) {
            if ($usr['hide_email'] == "1") {
            echo'<dt>'.$lang['product_email'].'</dt>
             <dd>'.$lang['product_secret_email_address'].'</dd>';
            } else {
            echo'<dt>'.$lang['product_email'].'</dt>
             <dd>'.$usr['email'].'</dd>';
            }

           }*/

echo '</dl>

          <form class="form" onsubmit="return false" id="sentmessage">
          <div class="form-group">

                                                <input name="username" id="username" class="form-control" placeholder="الإسم - Name" type="text"/>
                                            </div>
                                            <div class="form-group">

                                                <input name="email" id="email" class="form-control" placeholder="بريدك الإلكتروني - Email" type="text" />
                                            </div>
                                            <div class="form-group">

                                                <input name="phone" id="phone" class="form-control" placeholder="رقمك الهاتفي - Phone" type="text" />
                                            </div>
                                            <div class="form-group" style="display: none;">

                                                <textarea name="message" id="message" class="form-control" rows="6"
                                                    placeholder="' .
    $lang['product_send_a_message_to'] .
    ' ' .
    $usr['fullname'] .
    '">' .
    $lang['product_hi'] .
    ' ' .
    $usr['fullname'] .
    '!
' .
    $lang['product_i_am_interested_in_your_offer_ID'] .
    '
' .
    $items['title'] .
    '.
' .
    $lang['product_please_give_me_more_details'] .
    '</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="captcha" class="col-form-label">Captcha</label>
                                                <img id="captcha_img" src="' .
    $_SESSION['captcha']['image_src'] .
    '" alt="CAPTCHA code">

                                            </div>
                                            <div class="form-group">
                                                <input name="captcha" id="captcha" class="form-control" placeholder="الكابتشا - Captcha" type="text" />
                                            </div>
           <input type="hidden" name="target_id" value="' .
    $usr['id'] .
    '">
           <input type="hidden" name="product_id" value="' .
    $items['id'] .
    '">

                                            <button type="submit" onclick="sent_message()" class="btn btn-primary"><div id="resultc"></div>' .
    $lang['product_send'] .
    '</button>

                                        </form>
          <br>
          <div id="result"></div>
          <div id="fav"></div>


          </div>

                                </section>




                            </aside>

                            <aside class="sidebar" style="position:sticky;top:0;">';

$featured_items = $db->prepare('SELECT * FROM items WHERE sale_status NOT IN("1","2") AND permit NOT IN("0") AND featured = "1" ORDER BY RAND() LIMIT 2');
$featured_items->execute();

if ($featured_items->rowCount()) {
    echo '<section>

                                    <h2>' .
        $lang['product_featured_ads'] .
        '</h2>

                                    <div class="items compact">';

    foreach ($featured_items as $row) {
        $ct = "SELECT * FROM category WHERE id = '" . $row['category'] . "'";
        $stmt = $db->query($ct);
        $category_p = $stmt->fetch(PDO::FETCH_ASSOC);

        $us = "SELECT * FROM users WHERE id = '" . $row['user_id'] . "'";
        $stmt = $db->query($us);
        $usr_p = $stmt->fetch(PDO::FETCH_ASSOC);

        $gl = "SELECT * FROM gallery WHERE item_id = '" . $row['id'] . "'";
        $stmt = $db->query($gl);
        $gallery_p = $stmt->fetch(PDO::FETCH_ASSOC);

        echo '<div class="item">';
        if ($row['featured'] == '1') {
            echo '<div class="ribbon-featured">' . $lang['product_featured'] . '</div>';
        }

        echo '<div class="wrapper">

                                                <div class="image">

                                                    <h3>';
        if (!empty($category_p['ctg_name'])) {
            echo '<a class="tag category" style="
    bottom: 0.8rem;
    left: -34px;
">' .
                $category_p['ctg_name'] .
                '</a>';
        }
        if (!empty($row['title'])) {
            echo '<a href="product.php?id=' .
                $row['id'] .
                '" class="title" style="
    color: #fff;
    transition: none;
    text-shadow: 0 0.5rem 0.8rem rgb(0 0 0);
    display: block;
    height: 365px;
">' .
                $row['title'] .
                '</a>';
        }
        if (!empty($row['type'])) {
            echo '<span class="tag">' . $row['type'] . '</span>';
        }
        echo '</h3>';
        if (!empty($gallery_p['image'])) {
            echo '<a href="product.php?id=' .
                $row['id'] .
                '" class="image-wrapper background-image">
                                                        <img src="' .
                $gallery_p['image'] .
                '" alt="">
                                                    </a>';
        }

        echo '</div>';

        if (!empty($row['address'])) {
            echo '<h4 class="location">
                                                    <a>' .
                $row['address'] .
                '</a>
                                                </h4>';
        }

        if (!empty($row['price'])) {
            echo '<div class="price">' . number_format($row['price'], '0', '', '.') . ' ' . $settings['currency'] . '</div>';
        }

        echo '<div class="meta">';

        if (!empty($row['create_date'])) {
            echo '<figure>
                                                        <i class="fa fa-calendar-o"></i>' .
                timeConvert(date('d.m.Y H:i:s', $row['create_date'])) .
                '
                                                    </figure>';
        }

        if (!empty($usr_p['fullname'])) {
            echo '<figure>
                                                        <a target="_blank" href="profile_detail.php?users=' .
                $usr_p['username'] .
                '">
                                                            <i class="fa fa-user"></i>' .
                $usr_p['fullname'] .
                '
                                                        </a>
                                                    </figure>';
        }

        echo '</div>


                                        </div>';
    }

    echo '</div>

                                </section>';
}

echo '</aside>';

if ($adsns['item_ads_statu'] == '1') {
    echo '<section>

                                    <h2>' .
        $lang['admin_adsense_google_adsense'] .
        '</h2>

          <div class="items compact"><div class="item">

           <div class="ribbon-featured">
            <div class="ribbon-start"></div>
            <div class="ribbon-content">' .
        $lang['admin_adsense_sponsored'] .
        '</div>
            <div class="ribbon-end">
             <figure class="ribbon-shadow"></figure>
            </div>
           </div>

           <div class="wrapper">

                                                <div class="image">';
    echo $adsns['item_ads'];
    echo '</div>

                                            </div>

                                        </div>


                                </section></div>';
}

echo '</div>




                    </div>
                </div>
            </section>
        </section>';

echo '<script>
    s_days = "' .
    $items['block_days'].
    '".split(",");


    $(document).ready(function() {
            ';
            if ($items['purpose'] == 'louer') {
                echo 'const calendar2 = new VanillaCalendar("#calendar2",{
                type: "multiple",
                    months: 2,
                    jumpMonths: 2,
                    settings: {
                        lang: "ar",
                        range: {
                            disabled: s_days,
                        },
                        //selection: false,
                        visibility: {
                            daysOutside: false,
                            weekend: false,
                        },
                    },

            }); calendar2.init();
        ';
    }
    echo 'validateYouTubeUrl();
    });

    function download_images() {
        const imageLinks = document.querySelectorAll(".download_btns");

        imageLinks.forEach((link, index) => {
            setTimeout(() => {
                link.click();
            }, index * 500); // 500 milliseconds delay between each download
        });
    }

    function validateYouTubeUrl() {
        var url = $("#youtube").html();
        if (url != undefined || url != "") {
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {

                $("#videoObject").attr("src", "https://www.youtube.com/embed/" + match[2] +
                "?autoplay=1&enablejsapi=1");
            } else {

                $("#videoObject").attr("src", "");

            }
        }
    }
</script>';

echo '

 <script>
     //<![CDATA[

     function nocontext(e) {
         var clickedTag = (e == null) ? event.srcElement.tagName : e.target.tagName;
         if (clickedTag == "IMG") {
             alert(alertMsg);
             return false;
         }
     }
     var alertMsg = "الصور عليها حقوق الإنتاج لشركة أمرتيل لذلك يمنع التحميل";
     document.oncontextmenu = nocontext;

     //]]>
 </script>


';

include 'includes/footer.php';
