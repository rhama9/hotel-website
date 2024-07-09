<?php

include_once 'includes/sql_sys.php';
setSysInfo();

include_once 'includes/sql_rooms.php';

$page_name = 'Accueil';

$rooms = getThreeLastRooms();
$images = getImages();

include 'layouts/header.php';
?>
<!-- Revolution Slider -->
<section class="revolution-slider">
    <div class="bannercontainer">
        <div class="banner">
            <ul>
                <?php foreach (PHRASE_ACCUEIL as $key => $value) : ?>
                    <!-- Slide  -->
                    <li data-transition="fade" data-slotamount="7" data-masterspeed="1500">
                        <!-- Main Image -->
                        <img src="<?= $value['image_path'] ?>" style="opacity:0;" alt="slidebg1" data-bgfit="cover" data-bgposition="left bottom" data-bgrepeat="no-repeat">
                        <!-- Layers -->
                        <!-- Layer 1 -->
                        <div class="caption sft revolution-starhotel bigtext_" data-x="0" data-y="50" style="background-color: transparent; color: #fff; font-size: 3rem; font-weight: 700; text-align: center; min-width: 950px; display: flex; justify-content: center; align-items: center; text-shadow: 2px 2px 2px rgba(0, 0, 0, .5);" data-speed="700" data-start="1700" data-easing="easeOutBack">
                            <span>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                            </span>
                            <span style="text-align: center;  padding-inline: 2rem;">
                                <?= $value['phrase'] ?>
                            </span>
                            <span>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                            </span>
                        </div>
                        <!-- Layer 2 -->
                        <div class="caption sft revolution-starhotel smalltext" data-x="0" data-y="150" style="background-color: transparent; color: #fff; font-size: 2.5rem; font-weight: 400; text-align: center; min-width: 950px; display: flex; justify-content: center; align-items: center; text-shadow: 2px 2px 2px rgba(0, 0, 0, .5);" data-speed="800" data-start="1600" data-easing="easeOutBack">
                            <span><?= $value['cta'] ?></span>
                            <!-- <span>Découvrez le confort ultime!</span> -->
                        </div>
                        <!-- Layer 3 -->
                        <div class="caption sft" data-x="0" data-y="200" style="background-color: transparent; color: #fff; font-size: 3.5rem; font-weight: 400; text-align: center; min-width: 950px; display: flex; justify-content: center; align-items: center; text-shadow: 2px 2px 2px rgba(0, 0, 0, .5);" data-speed="1000" data-start="1500" data-easing="easeOutBack">
                            <a href="room-list" class="button btn btn-purple btn-md">Voir Les Chambres</a>
                        </div>
                    </li>
                <?php endforeach; ?>

                <!-- Slide 2 -->
                <!-- <li data-transition="boxfade" data-slotamount="7" data-masterspeed="1000"> -->
                <!-- Main Image -->
                <!-- images -->
                <?php if (0 && $rooms && count($rooms[0]['images'])) : ?>
                    <!-- <img src="<?= $rooms[0]['images'][0]['path'] ?>" alt="darkblurbg" data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat"> -->
                <?php else : ?>
                    <!-- <img src="images/slides/2.gif" alt="darkblurbg" data-bgfit="cover" data-bgposition="left top" data-bgrepeat="no-repeat"> -->
                <?php endif; ?>
                <!-- Layers -->
                <!-- Layer 1 -->
                <!-- <div class="caption sft revolution-starhotel bigtext" data-x="585" data-y="30" data-speed="700" data-start="1700" data-easing="easeOutBack">
                        <span>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </span>
                        <?= count($rooms) ? $rooms[0]['name'] : '' ?>
                        <span>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                            <i class="fa fa-star-o"></i>
                        </span>
                    </div> -->
                <!-- Layer 2 -->
                <!-- <div class="caption sft revolution-starhotel smalltext" data-x="682" data-y="105" data-speed="800" data-start="1700" data-easing="easeOutBack">
                        <span>Offrez-vous des vacances de rêve.</span>
                    </div> -->
                <!-- Layer 3 -->
                <!-- <div class="caption sft" data-x="785" data-y="175" data-speed="1000" data-start="1900" data-easing="easeOutBack">
                        <a href="<?= count($rooms) ? "room.php?room=" . $rooms[0]['id']  : 'room-list.php' ?>" class="button btn btn-purple btn-lg"><?= isset($rooms[0]['id']) ? "Réservez cette chambre" : 'Voir Les Chambres' ?></a>
                    </div> -->
                <!-- </li> -->
            </ul>
        </div>
    </div>
</section>

<!-- Rooms -->
<section class="rooms mt50">
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <?php $lanotifdereservation2 = showNotif('deleteAccountNotif'); ?>
                <div class="col-12" id="notif_container" style="position: relative; z-index: 999999; <?= isset($was_notif2) && $was_notif2 ? 'height: 100px;'  : 'height: 0px;' ?> ">
                    <?= $lanotifdereservation2 ?>
                </div>
                <?php $lanotifdereservation = showNotif(); ?>
                <div class="col-12" id="notif_container" style="position: relative; z-index: 999999; <?= isset($was_notif2) && $was_notif2 ? 'height: 100px;'  : 'height: 0px;' ?> ">
                    <?= $lanotifdereservation ?>
                </div>
                <h2 class="lined-heading">
                    <span>Nos dernières chambres</span>
                </h2>
            </div>
            <!-- Room -->
            <?php
            if ($rooms) :
                foreach ($rooms as $room) : ?>
                    <?= roomToHtml($room); ?>
                <?php endforeach;
            else :
                ?>
                <div class="container text-center">
                    PAS DE CHAMBRE
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- USP's -->
<section class="usp mt100">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 class="lined-heading"><span>Vivez l'expérience tout compris</span></h2>
            </div>
            <div class="col-sm-3 bounceIn appear" data-start="0">
                <div class="box-icon">
                    <div class="circle"><i class="fa fa-glass fa-lg"></i></div>
                    <h3>Beverages inclus</h3>
                    <p>
                        Détendez-vous et profitez de vos boissons préférées, incluses dans votre séjour !
                    </p>
                    <!-- <a href="#">Read more<i class="fa fa-angle-right"></i></a> -->
                </div>
            </div>
            <div class="col-sm-3 bounceIn appear" data-start="400">
                <div class="box-icon">
                    <div class="circle"><i class="fa fa-credit-card fa-lg"></i></div>
                    <h3>Séjournez d'abord, payez après !</h3>
                    <p>
                        Détendez-vous dès votre arrivée et réglez votre séjour ultérieurement. Simplifiez votre expérience de réservation.
                    </p>
                    <!-- <a href="#">Read more<i class="fa fa-angle-right"></i></a> -->
                </div>
            </div>
            <div class="col-sm-3 bounceIn appear" data-start="800">
                <div class="box-icon">
                    <div class="circle"><i class="fa fa-cutlery fa-lg"></i></div>
                    <h3>Restaurant ouvert 24h/24</h3>
                    <p>
                        N'ayez jamais faim grâce à notre restaurant ouvert jour et nuit pour satisfaire toutes vos envies.
                    </p>
                    <!-- <a href="#">Read more<i class="fa fa-angle-right"></i></a> -->
                </div>
            </div>
            <div class="col-sm-3 bounceIn appear" data-start="1200">
                <div class="box-icon">
                    <div class="circle"><i class="fa fa-tint fa-lg"></i></div>
                    <h3>Spa inclus!</h3>
                    <p>
                        Offrez-vous une expérience de détente ultime avec notre spa inclus, pour un séjour revitalisant
                    </p>
                    <!-- <a href="#">Read more<i class="fa fa-angle-right"></i></a> -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Parallax Effect -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#parallax-image').parallax("80%", -2);
    });
</script>

<section class="parallax-effect mt100">
    <div id="parallax-image" style="background-image: url(./images/rooms/site/P10.jpg);">
        <div class="color-overlay fadeIn appear" data-start="600">
            <div class="container">
                <div class="content">
                    <h3 class="text-center"><?= $site_name ?></h3>
                    <p class="text-center">Une Experience Exeptionnelle!
                        <br>
                        <a href="room-list.php" class="btn btn-default btn-lg mt30">Voir les Chambres</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery -->
<section class="gallery-slider mt100">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="lined-heading"><span>Gallery</span></h2>
            </div>
        </div>
    </div>

    <!-- <style>
        .owl-wrapper-outer .owl-wrapper .owl-item {
            height: 100% !important;
            /* display: inline-block; */
        }

        .owl-wrapper-outer .owl-wrapper .owl-item .item {
            height: 100% !important;
            /* display: inline-block; */
        }

        .owl-wrapper-outer .owl-wrapper .owl-item .item a {
            height: 100% !important;
            /* display: inline-block; */
        }

        .owl-wrapper-outer .owl-wrapper .owl-item .item a img {
            object-fit: cover;
            height: 100% !important;
            /* display: inline-block; */
        }
    </style> -->
    <div id="owl-gallery" class="owl-carousel">
        <?php
        if (count($images)) :
            foreach ($images as $image) :
        ?>
                <div class="item" data-room-id="<?= $image['room_id'] ?>" style="">
                    <a href="<?= $image['path'] ?>" data-rel="prettyPhoto[gallery1]">
                        <img src="<?= $image['path'] ?>" alt="<?= $image['name'] ?>">
                        <i class="fa fa-search"></i>
                    </a>
                </div>
            <?php
            endforeach;
        else :
            foreach (PHRASE_ACCUEIL as $key => $value) :
            ?>
                <!-- Item <?= $key ?> -->
                <div class="item" style="aspect-ratio: 16/9; overflow: hidden; ;">
                    <a href="<?= $value['image_path'] ?>" data-rel="prettyPhoto[gallery1]">
                        <img src="<?= $value['image_path'] ?>" alt="<?= $value['phrase'] ?>" style="height: 100%; weight: 100%; object-fit: cover;">
                        <i class="fa fa-search"></i>
                    </a>
                </div>
        <?php endforeach;
        endif;  ?>
    </div>
</section>

<!-- Call To Action -->
<section id="call-to-action" class="mt100" style="margin-bottom: 0px;">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-12">
                <h2>Découvrez le confort ultime en réservant votre séjour dès aujourd'hui.</h2>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <a href="room-list.php" class="btn btn-default btn-lg pull-right">Réservez maintenant</a>
            </div>
        </div>
    </div>
</section>

<?php include 'layouts/footer.php' ?>