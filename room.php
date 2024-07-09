<?php
$page_name = 'Chambres';

if (!isset($_GET['room'])) {
  http_response_code(404);
  header('location: 404.php');
  die;
}
include_once('includes/sql_rooms.php');
include_once('includes/sql_user.php');

$room_id = (int)htmlentities(trim($_GET['room']));
$room = getRoom($room_id);

if (!$room) {
  http_response_code(404);
  header('location: 404.php');
  die;
}

// recuperation de la reservation de l'utilisateur,
// s'il est connecte et a une reservation sur la chambre actuelle
if (isset($_SESSION['auth']['id']) && $_SESSION['auth']['id']) {
  $bookings = getBookingsOfUser($_SESSION['auth']['id']);
  if ($bookings) {
    foreach ($bookings as $bk) {
      if ($bk['room_id'] == $room['id']) {
        $checkout = new DateTime($bk['checkout']);
        $today = new DateTime();
        if ($checkout >= $today) {
          $book = $bk;
          $book['user'] = getUser($book['user_id']);
        } else {
        }
      }
    }
  }
}

$rooms = getThreeLastRooms();

$old_checkin = date('d/m/Y', strtotime('today'));
// dd($old_checkin);
$old_checkout = '';
$old_adults = '1';
$old_children = '0';

if (isset($_SESSION['auth']['email'])) {
  $old_email = $_SESSION['auth']['email'];
}

if (isset($_POST) && count($_POST)) {
  $error = [];
  $p = $_POST;

  if (isset($p['annuler_booking']) && $p['annuler_booking'] && isset($book)) {
    $id = (int)htmlentities(trim($p['annuler_booking']));
    if ($id) {
      $res = annulerBooking($id);
      if ($res) {
        header('location: room.php?room=' . $room['id']);
        die;
      }
    }
  }

  if (isset($p['retablir_booking']) && $p['retablir_booking'] && isset($book)) {
    $id = (int)htmlentities(trim($p['retablir_booking']));
    if ($id) {
      $res = retablirBooking($id);
      if ($res) {
        header('location: room.php?room=' . $room['id']);
        die;
      }
    }
  }

  if (isset($p['reservation']) && $p['reservation'] && !isset($book)) {

    // Traitement de checkin
    if (isset($_POST['checkin']) && strlen($_POST['checkin'])) {
      $checkin = htmlentities(trim($_POST['checkin']));

      if (preg_match(DATE_HTML_INPUT_REGEX, $checkin)) {
        $old_checkin = $checkin;
      } else {
        $old_checkin = $checkin;
        $error['checkin'] = 'Veuillez insérer une date valide';
      }
    } else {
      $error['checkin'] = 'La date de debut est requise';
    }

    // Traitement de checkout
    if (isset($_POST['checkout']) && strlen($_POST['checkout'])) {
      $checkout = htmlentities(trim($_POST['checkout']));

      if (preg_match(DATE_HTML_INPUT_REGEX, $checkout)) {
        $old_checkout = $checkout;
      } else {
        $old_checkout = $checkout;
        $error['checkout'] = 'Veuillez insérer une date valide';
      }
    } else {
      $error['checkout'] = 'La date de fin est requise';
    }

    // Traitement de adults
    if (isset($_POST['adults']) && strlen($_POST['adults'])) {
      $adults = (int)htmlentities(trim($_POST['adults']));

      if ($adults >= 0 && $adults <= 3) {
        $old_adults = $adults;
      } else {
        $error['adults'] = 'Veuillez insérer une donnée valide';
      }
    } else {
      $error['adults'] = 'Il doit y avoir au moins un adulte dans une chambre.';
    }

    // Traitement de children
    if (isset($_POST['children']) && strlen($_POST['children'])) {
      $children = (int)htmlentities(trim($_POST['children']));

      if ($children >= 0 && $children <= 3) {
        $old_children = $children;
      } else {
        $error['children'] = 'Veuillez insérer une donnée valide';
      }
    } else {
      $old_children = 0;
      $children = 0;
    }

    if (isset($_SESSION['auth']['email']) && $_SESSION['auth']['email']) {
      $user = getUser($_SESSION['auth']['email']);
      if ($user) {

        if (!$error) {
          $intervalle = getIntervalBetweenTwoDates($checkin, $checkout);

          for ($i = 1; $i < 5; $i++) {
            $nonValidref = genRef($checkin, $checkout, $user['id'], $room_id);
            if (getBooking($nonValidref, true)) {
              $errorRef = true;
            } else {
              $validRef = $nonValidref;
              $errorRef = false;
              $i = 5;
            }
          }
          if (isset($validRef) && $validRef || !$errorRef) {
            $booking = [
              'user_id' => $user['id'],
              'room_id' => $room_id,
              'checkin' => dmyToymd($checkin),
              'checkout' => dmyToymd($checkout),
              'adults' => $adults,
              'total' => $room['price'] * $intervalle,
              'statut' => 'ok',
              'children' => $children,
              'ref' => $validRef,
              'created_at' => date('Y-m-d H:i:s'),
            ];

            $theBooking = addBooking($booking);
            if ($theBooking) {

              setNotif('success', 'Félicitations. Votre réservation a été effectuée.', 'N\'oubliez pas de conserver votre CODE de réservation. Pour une vérification ultérieure.', 'booking_notif');

              // $book = getBooking($theBooking);
              // if (isset($_SESSION['booking'])) {
              //   $_SESSION['booking'][] = $book;
              // } else {
              //   $_SESSION['booking'][0] = $book;
              // }
            }
          } else {
            if ($errorRef) {
              setNotif('warning', 'Oups! Erreur: CODE réservation unique', "Désolés, nous n'avons pas pu vous attribuer un numéro de référence (CODE réservation unique).<br>Veuillez réessayer encore. <hr>Si le problème persiste, Veuillez contacter l'administrateur du site à travers la page de contact et sélectionnez le sujet \"<b>Signaler un problème avec le site</b>\".", 'booking_fail_notif');
            }
          }
        }
      } else {
        setNotif('warning', 'Quelque chose c\'est mal passé', 'Deconnectez-vous puis reconnectez-vous pour reparer le probleme');
      }
    }
  }
}

// recuperation de la reservation de l'utilisateur,
// s'il est connecte et a une reservation sur la chambre actuelle
if (isset($_SESSION['auth']['id']) && $_SESSION['auth']['id']) {
  $bookings = getBookingsOfUser($_SESSION['auth']['id']);
  if ($bookings) {
    foreach ($bookings as $bk) {
      if ($bk['room_id'] == $room['id']) {
        $checkout = new DateTime($bk['checkout']);
        $today = new DateTime();
        if ($checkout >= $today) {
          $book = $bk;
          $book['user'] = getUser($book['user_id']);
        }
      }
    }
  }
}

if (isset($book) && count($book) > 0) {
  $loadJs = [
    'html2canvas'
  ];
}
include 'layouts/header.php';
?>
<!-- Parallax Effect -->
<script type="text/javascript">
  $(document).ready(function() {
    $('#parallax-pagetitle').parallax("50%", -2);
  });
</script>

<section class="parallax-effect">
  <?php if (count($room['images'])) : ?>
    <div id="parallax-pagetitle" style="background-image: url(./<?= $room['images'][0]['path'] ?>);">
    <?php else : ?>
      <div id="parallax-pagetitle" style="background-image: url(./<?= PHRASE_ACCUEIL[rand(0, count(PHRASE_ACCUEIL) - 1)]['image_path']; ?>);">
      <?php endif; ?>
      <div class="color-overlay">
        <!-- Page title -->
        <div class="container">
          <div class="row">
            <div class="col-sm-12">
              <ol class="breadcrumb">
                <li><a href="index.php">Accuiel</a></li>
                <li><a href="room-list.php">Chambres</a></li>
                <li class="active"><?= $room['name'] ?></li>
              </ol>
              <h1><?= $room['name'] ?></h1>
            </div>
          </div>
        </div>
      </div>
      </div>
</section>

<!-- First ection -->
<div class="container mt50">
  <h2 class="lined-heading mt20"><span><?= $room['name'] ?></span></h2>

  <?= showNotif(); ?>
  <?= showNotif('booking_fail_notif'); ?>

  <div class="row">
    <!-- Slider -->
    <section class="standard-slider room-slider">
      <div class="col-sm-12 col-md-8">
        <div id="owl-standard" class="owl-carousel">
          <?php if (count($room['images'])) :  foreach ($room['images'] as $image) : ?>
              <div class="item" style="aspect-ratio: 750 / 481; overflow: hidden;">
                <a href="<?= $image['path'] ?>" data-rel="prettyPhoto[gallery1]" style="">
                  <img src="<?= $image['path'] ?>" alt="<?= $image['name'] ?>" class="img-responsive" style="object-fit: cover; aspect-ratio: 750 / 481; width: 100%;">
                </a>
              </div>
            <?php endforeach;
          else : ?>
            <div class="item">
              <a href="images/rooms/defaul_room_image.jpeg" data-rel="prettyPhoto[gallery1]">
                <img src="images/rooms/defaul_room_image.jpeg" alt="Image Par defaut." class="img-responsive" style="min-width: 100%;">
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Reservation form -->
    <section id="reservation-form" class="mt50 clearfix">
      <?php if (isset($book) && count($book) > 0) : ?>

        <div style="position: relative;" class="col-sm-12 col-md-4">
          <div id="revervationHtml" style=" position: relative; z-index: 0;" class="reservation-vertical clearfix">
            <h2 class="lined-heading"><span>Réservation</span></h2>
            <?php $lanotifdereservation = showNotif('booking_notif'); ?>
            <div class="row do_not_dawnload">
              <div id="notif_container" class="col-12" style="position: relative; <?= isset($was_booking_notif) && $was_booking_notif ? 'height: 130px;'  : 'height: 0px;' ?> ">
                <?= $lanotifdereservation ?>
              </div>
            </div>

            <div class="form-group">
              <label for="">Code de Réservation:</label>
              <p class="form-control" style="color: #222; font-size: 1.3rem; font-weight:600; padding-bottom: .4rem;">
                <?= $book['ref'] ?>
              </p>
            </div>
            <p>Utilisateur: <span style="font-weight:600;"><?= $book['user']['fullname'] ?></span></p>
            <p>Email: <span style="font-weight:600;"><?= $book['user']['email'] ?></span></p>
            <p>Date du debut: <span style="font-weight:600;"><?= dbDatetoTexte($book['checkin'], 'Y-m-d', 'd M, Y') ?></span></p>
            <p>Date de fin: <span style="font-weight:600;"><?= dbDatetoTexte($book['checkout'], 'Y-m-d', 'd M, Y')  ?></span></p>
            <p>Chambre: <span style="font-weight:600;"><?= $book['room']['name'] ?></span></p>
            <p>Nombre de jour: <span style="font-weight:600;"><?= getIntervalBetweenTwoDates($book['checkin'], $book['checkout'], 'Y-m-d') ?></span></p>
            <p>Adultes: <span style="font-weight:600;"><?= $book['adults'] ?></span></p>
            <p>Enfants: <span style="font-weight:600;"><?= $book['children'] ?></span></p>
            <div class="form-group">
              <label for="">Total: <code style="padding: .5rem; font-size: 1.5rem;"><?= $book['room']['formated_price'] . ' x ' . getIntervalBetweenTwoDates($book['checkin'], $book['checkout'], 'Y-m-d') ?></code></label>
              <p class="form-control" style="color: #222; font-size: 1.3rem; font-weight:600; padding-bottom: .4rem;"><?= $book['formated_price'] ?></p>
            </div>
            <hr>
            <div class="text-muted">
              <p>
                Pour modifier la réservation, veuillez nous envoyer un message
                en accédant à la page de <b>contact</b> en spécifiant
                le sujet "<b>Mettre ma réservation à jour</b>".
                N'oubliez pas d'insérer le code de votre réservation
                (<code><?= $book['ref'] ?></code>) et les données de la modification souhaitée.
              </p>
              <p>
                NB : Conservez votre code de réservation (Copiez-le ou télécharger la réservation ). <br> Ne le Partagez pas avec personne.
              </p>
            </div>

            <div class="do_not_dawnload" style="display: flex; justify-content: space-between; align-items: center;">
              <div class="col-3" style="display: table-cell_;">
                <?php if ($book['statut'] == 'ok') : ?>
                  <form method="post">
                    <input type="hidden" name="booking" value="<?= $book['id'] ?>">
                    <button title="Annuler la réservation" data-booking-id="<?= $book['id'] ?>" type="sybmit" name="annuler_booking" value="<?= $book['id'] ?>" class="btn btn-danger btn-sm delete_booking_btn">
                      Annuler
                    </button>
                  </form>
                <?php elseif ($book['statut'] == 'annuler') : ?>
                  <form method="post">
                    <input title="Rétablir la réservation" type="hidden" name="booking" value="<?= $book['id'] ?>">
                    <button data-booking-id="<?= $book['id'] ?>" type="sybmit" name="retablir_booking" value="<?= $book['id'] ?>" class="btn btn-success btn-md delete_booking_btn">
                      Retablir
                    </button>
                  </form>
                <?php endif; ?>
              </div>
              <div class="col-3" style="display: table-cell_;">
                <button type="button" class="btn btn-success btn-md dawnload_booking_btn" title="Télécharger ma reservation  (JPG)">
                  Télécharger
                </button>
              </div>
            </div>
            <div id="waterMark" style="margin-bottom: -2.3rem; font-size: 1.1rem; margin-left: -2.2rem; display: none;"><?= $_SESSION['sys']['site_name'] . ' - ' . $_SESSION['sys']['email'] . ' - ' . $_SESSION['sys']['web_adress'] ?></div>
          </div>
        </div>
      <?php else : ?>
        <div class="col-sm-12 col-md-4">
          <form class="reservation-vertical clearfix" role="form" method="post" name="reservationform" id="reservationformb">
            <h2 class="lined-heading"><span>Réservation</span></h2>
            <div class="price">
              <h3><?= $room['name']  ?></h3>
              <?= $room['formated_price']  ?><span>La nuit</span>
            </div>
            <?php if (!isset($_SESSION['auth'])) : ?>

              <p>Veuillez vous connecter ou vous inscrire pour réserver une chambre.</p>
              <a href="inscription.php" class="btn btn-primary btn-block" style="margin-bottom: 20px;">S'inscrire</a>
              <a href="connection.php" class="btn btn-primary btn-block">Se Connecter</a>

            <?php else : ?>
              <!-- Error message display -->
              <div class="form-group">
                <label for="email" accesskey="E">E-mail</label>
                <p class="form-control"><?= $old_email ?></p>
                <!-- <input name="email" type="text" id="email" value="<?= $old_email ?>" class="form-control" placeholder="Votre E-mail" /> -->
              </div>

              <div class="form-group has-validation">
                <label for="checkin">Date début</label>
                <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="Le début commence à 11:00">
                  <i class="fa fa-info-circle fa-lg"> </i>
                </div>
                <i class="fa fa-calendar infield"></i>
                <input name="checkin" type="text" id="checkin" value="<?= $old_checkin ?>" class="form-control <?= isset($error['checkin']) ? 'is-invalid' : '' ?>" placeholder="Date début" />
                <?php if (isset($error['checkin'])) : ?>
                  <div class="invalid-feedback">
                    <?= $error['checkin'] ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="form-group has-validation">
                <label for="checkout">Date fin</label>
                <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="La fin est à 12:00">
                  <i class="fa fa-info-circle fa-lg"> </i>
                </div>
                <i class="fa fa-calendar infield"></i>
                <input name="checkout" type="text" id="checkout" value="<?= $old_checkout ?>" class="form-control <?= isset($error['checkout']) ? 'is-invalid' : '' ?>" placeholder="Date fin" />
                <?php if (isset($error['checkout'])) : ?>
                  <div class="invalid-feedback">
                    <?= $error['checkout'] ?>
                  </div>
                <?php endif; ?>
              </div>
              <div class="form-group has-validation">
                <div class="guests-select ">
                  <label>Personnes</label>
                  <i class="fa fa-user infield"></i>
                  <div class="total form-control <?= isset($error['children']) || isset($error['adults']) ? 'is-invalid' : '' ?>" id="test"><?= is_integer($old_adults) && is_integer($old_children) ? $old_adults + $old_children : 1; ?></div>
                  <div class="guests">
                    <div class="form-group adults has-validation">
                      <label for="adults">Adultes</label>
                      <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="+18 years"> <i class="fa fa-info-circle fa-lg"> </i> </div>
                      <select name="adults" id="adults" class="form-control <?= isset($error['adults']) ? 'is-invalid' : '' ?>">
                        <?php foreach (ADULTES_POUR_CHAMBRE as $key => $text) :
                          if (is_integer($old_adults)) {
                            $selected =  ($old_adults === $key) ? 'selected' : '';
                          } else {
                            $selected = '';
                          }
                        ?>
                          <option value="<?= $key ?>" <?= $selected  ?>><?= $text ?></option>

                        <?php
                        endforeach; ?>
                      </select>
                      <?php if (isset($error['adults'])) : ?>
                        <div class="invalid-feedback">
                          <?= $error['adults'] ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <div class="form-group children has-validation">
                      <label for="children">Enfants</label>
                      <div class="popover-icon" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="0 till 18 years"> <i class="fa fa-info-circle fa-lg"> </i> </div>
                      <select name="children" id="children" class="form-control <?= isset($error['children']) ? 'is-invalid' : '' ?>">
                        <?php foreach (ENFANTS_POUR_CHAMBRE as $key => $text) :
                          if (is_integer($old_children)) {
                            $selected =  ($old_children === $key) ? 'selected' : '';
                          } else {
                            $selected = '';
                          }
                        ?>
                          <option value="<?= $key ?>" <?= $selected  ?>><?= $text ?></option>

                        <?php
                        endforeach; ?>
                      </select>
                      <?php if (isset($error['children'])) : ?>
                        <div class="invalid-feedback">
                          <?= $error['children'] ?>
                        </div>
                      <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-default button-save btn-block">Save</button>
                  </div>
                </div>
                <?php if (isset($error['children']) || isset($error['adults'])) : ?>
                  <div class="invalid-feedback">
                    <?= isset($error['children']) ? $error['children'] : '' ?>
                    <?= isset($error['adults']) ? $error['adults'] : '' ?>
                  </div>
                <?php endif; ?>
              </div>
              <button type="submit" name="reservation" value="<?= $_SESSION['auth']['email'] ?>" class="btn btn-primary btn-block">Réservez maintenant</button>
            <?php endif; ?>

          </form>
        </div>
      <?php endif; ?>

    </section>

    <!-- Room Content -->
    <section>
      <div class="container">
        <div class="row">
          <div class="col-sm-12 mt50">
            <h2 class="lined-heading"><span>Room Details</span></h2>
            <table class="table table-striped mt30">
              <tbody>
                <!-- Affichages des caracteristiques de la chambre -->
                <?php
                $r_crts = $room['caracteristics'];
                $ulEls = count($r_crts);
                $nbrElPerBlock = 3;
                $nbrBloc = ceil($ulEls / $nbrElPerBlock);
                $el = 0;
                $html = '';
                for ($i = 1; $i <=  $nbrBloc; $i++) {
                  $html .= '<tr>';

                  for ($y = 0; $y < $nbrElPerBlock; $y++) {
                    if (isset($r_crts[$el]['name'])) {
                      $html .= '<td><i class="fa fa-check-circle"></i>' . $r_crts[$el]['name'] . '</td>';
                      $el++;
                    }
                  }
                  $html .= '</tr>';
                }
                echo $html;
                ?>
              </tbody>
            </table>
            <h3 class="mt50"><?= $room['room_desc_title'] ?></h3>
            <p class="">
              <?= $room['room_desc'] ?></p>
          </div>

        </div>
      </div>
    </section>
  </div>
</div>

<!-- Autres Chambres -->
<section class="rooms mt50">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <h2 class="lined-heading"><span>Autres Chambres</span></h2>
      </div>
      <!-- Room -->
      <?php foreach ($rooms as $room) : ?>
        <?= roomToHtml($room); ?>
      <?php endforeach; ?>
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
<?php if (isset($book) && count($book) > 0) : ?>
  <script>
    $(document).ready(function() {
      $(".dawnload_booking_btn").click(function() {

        <?= 'const bookRef = "' . $book['ref'] . '";' ?>

        let height = $("#revervationHtml").parent('div').css('height')
        // // on cache ce que l'on ne veut pas dans la photo
        $(".do_not_dawnload").each(function() {
          $(this).hide()
        })
        $("#revervationHtml").parent('div').css('height', height)

        // On agrandit le html pour une meilleure resolution a l'image
        let width = $("#revervationHtml").css('width')
        $("#waterMark").show()
        $("#revervationHtml").css({
          'width': '420px',
          'margin': '10px',
          'transform': ' scale(5, 5) translate(1000px, -500px) '
        })

        // On genere l'image
        html2canvas($("#revervationHtml")[0]).then((canvas) => {
          const image = canvas.toDataURL("image/jpg")
          // on cree le lien de telechargement
          const a = document.createElement('a');
          a.setAttribute('href', image)
          a.setAttribute('download', bookRef + '.jpg')
          // on clique sur le lien cree et on suprime le lien
          a.click()
          a.remove()
        }).then(() => {
          // on reaffiche ce que l'on ne veut pas dans la photo
          $("#revervationHtml").parent('div').css('height', height)
          $("#revervationHtml").css({
            'width': width,
            'margin': '0px',
            'transform': ' scale(1, 1) translate(0px, 0px)'
          })

          // Pour que le responsive continue de fonctionner
          // nous devons retire nos css de l'element pour
          // ne plus avoir la priorite sur le rendue html par le css
          $("#revervationHtml").attr('style', '')
          $("#revervationHtml").css('z-index', '0')
          $("#waterMark").hide()
          $(".do_not_dawnload").each(function() {
            $(this).show()
          })
        })
      })
    })
  </script>

<?php endif;
include 'layouts/footer.php';
?>