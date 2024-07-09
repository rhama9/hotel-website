<?php

include_once 'includes/sql_rooms.php';

$page_name = 'Galerie';

$images = getImages();
$image_types = getTmageTypesofExistingImages();

include 'layouts/header.php';
?>
<!-- Parallax Effect -->
<script type="text/javascript">
    $(document).ready(function() {
        $('#parallax-pagetitle').parallax("50%", -2);
    });
</script>

<section class="parallax-effect">
    <div id="parallax-pagetitle" style="background-image: url(./<?= PHRASE_ACCUEIL[rand(0, count(PHRASE_ACCUEIL) - 1)]['image_path']; ?>);">
        <div class="color-overlay">
            <!-- Page title -->
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <ol class="breadcrumb">
                            <li><a href="index.php">Accueil</a></li>
                            <li class="active"><?= isset($page_name) ? $page_name : '' ?></li>
                        </ol>
                        <h1><?= isset($page_name) ? $page_name : '' ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= showNotif() ?>

<!-- Filter -->
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-pills" id="filters">
                <li class="active"><a href="#" data-filter="*">All</a></li>
                <?php foreach ($image_types as $image_type) : ?>
                    <li><a href="#" data-filter=".<?= $image_type['name'] . '_' . $image_type['id'] ?>"><?= $image_type['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Gallery -->
<section id="gallery" class="mt50">
    <div class="container">
        <div class="row gallery">
            <!-- Images -->
            <?php if ($images) : foreach ($images as $image) : ?>
                    <div class="col-sm-3 <?= $image['image_type']['name'] . '_' . $image['image_type']['id'] ?> fadeIn appear">
                        <a href="<?= $image['path'] ?>" data-rel="prettyPhoto[gallery1]"><img src="<?= $image['path'] ?>" alt="<?= $image['name'] ?>" class="img-responsive zoom-img" />
                            <i class="fa fa-search"></i>
                        </a>
                    </div>
                <?php endforeach;
            else : ?>
                <div class="container text-center">
                    LA GALERIE EST VIDE
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<section class="parallax-effect mt100">
    <div id="parallax-image" style="background-image: url(./<?= PHRASE_ACCUEIL[rand(0, count(PHRASE_ACCUEIL) - 1)]['image_path']; ?>);">
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


<?php include 'layouts/footer.php'; ?>