<?php

include_once 'includes/sql_rooms.php';

$page_name = 'Chambres';

$rooms = getRooms();
$room_types = getRoomFTypesFoExistingRooms();

include 'layouts/header.php';
?>
<!-- Parallax Effect -->
<script type="text/javascript">
  $(document).ready(function() {
    $('#parallax-pagetitle').parallax("50%", -2);
  });
</script>

<section class="parallax-effect">
  <div id="parallax-pagetitle" style="background-image: url(./<?= PHRASE_ACCUEIL[rand(0, count(PHRASE_ACCUEIL) - 1)]['image_path']; ?>); background-position: center;">
    <div class="color-overlay">
      <!-- Page title -->
      <div class="container">
        <div class="row">
          <div class="col-sm-12">
            <ol class="breadcrumb">
              <li><a href="index.php">Accueil</a></li>
              <li class="active">Les <?= isset($page_name) ? $page_name : '' ?></li>
            </ol>
            <h1>Liste des Chambres</h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?= showNotif(); ?>
<!-- Filter -->
<div class="container">
  <div class="row">
    <div class="col-sm-12">
      <ul class="nav nav-pills" id="filters">
        <li class="active"><a href="#" data-filter="*">All</a></li>
        <?php foreach ($room_types as $room_type) : ?>
          <li><a href="#" data-filter=".<?= $room_type['filter'] ?>"><?= $room_type['name'] ?></a></li>
        <?php endforeach; ?>
        <!-- <li><a href="#" data-filter=".single">#_Single Room</a></li>
                <li><a href="#" data-filter=".double">#_Double Room</a></li>
                <li><a href="#" data-filter=".executive">#_Executive Room</a></li>
                <li><a href="#" data-filter=".apartment">#_Apartment</a></li> -->
      </ul>
    </div>
  </div>
</div>
<!-- Rooms -->
<section class="rooms mt100">
  <div class="container">
    <div class="row room-list fadeIn appear">
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
  </div>
</section>

<section class="parallax-effect mt100">
  <div id="parallax-image" style="background-image: url(./<?= PHRASE_ACCUEIL[rand(0, count(PHRASE_ACCUEIL) - 1)]['image_path']; ?>);">
    <div class="color-overlay fadeIn appear" data-start="600">
      <div class="container">
        <div class="content">
          <h3 class="text-center"><?= $site_name ?></h3>
          <p class="text-center">Faites nous parvenir vos pensés
            <br>
            <a href="contact.php" class="btn btn-default btn-lg mt30">Contactez-nous</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'layouts/footer.php'; ?>