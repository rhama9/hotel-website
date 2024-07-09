<?php session_start();

include 'includes/config.php';
$path = DOMAIN ?>
<!DOCTYPE HTML>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <title>
    <?= isset($_SESSION['sys']['site_name']) ? $_SESSION['sys']['site_name'] : '404' ?>
  </title>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="favicon.ico">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?= isset($path) ? $path : '/' ?>css/animate.css">
  <link rel="stylesheet" href="<?= isset($path) ? $path : '/' ?>css/bootstrap.css">
  <link rel="stylesheet" href="<?= isset($path) ? $path : '/' ?>css/font-awesome.min.css">
  <link rel="stylesheet" href="<?= isset($path) ? $path : '/' ?>css/colors/orange.css">
  <link rel="stylesheet" href="<?= isset($path) ? $path : '/' ?>css/theme.css">
  <link rel="stylesheet" href="<?= isset($path) ? $path : '/' ?>css/responsive.css">

  <!-- Javascripts -->
  <script type="text/javascript" src="<?= isset($path) ? $path : '/' ?>js/jquery-1.11.0.min.js"></script>
  <script type="text/javascript" src="<?= isset($path) ? $path : '/' ?>js/jquery.parallax-1.1.3.js"></script>
  <script type="text/javascript" src="<?= isset($path) ? $path : '/' ?>js/waypoints.min.js"></script>
  <script type="text/javascript" src="<?= isset($path) ? $path : '/' ?>js/custom.js"></script>
</head>

<body>

  <!-- Header -->
  <header>
    <!-- Navigation -->
    <div class="navbar yamm navbar-default" id="sticky">
      <div class="container">
        <div class="navbar-header" style="height: 100%;">
          <button type="button" data-toggle="collapse" data-target="#navbar-collapse-grid" class="navbar-toggle">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <style>
            .nav-persoo {
              margin-top: 1.3rem !important;
            }

            .shrink .nav-persoo {
              margin-top: 1.3rem !important;
              transition: .5s;
            }

            .nav-persoo div#logo span span {
              font-size: 2rem !important;
              font-weight: 600;
            }

            @media (max-width: 767px) {
              .navbar-default .nav-persoo {
                margin-top: .7rem !important;
              }

              .shrink .nav-persoo {
                margin-top: .7rem !important;
                transition: .2s;
              }
            }
          </style>
          <a href="<?= isset($path) ? $path : '/' ?>index.php" class="navbar-brand nav-persoo">
            <!-- Logo -->
            <div id="logo">
              <span style="display: flex; justify-content: center; align-items: center;">
                <span>
                  <?= isset($_SESSION['sys']['site_name']) ? $_SESSION['sys']['site_name'] : '' ?>
                </span>
              </span>
            </div>
          </a>
        </div>
        <div id="navbar-collapse-grid" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown ">
              <a href="<?= isset($path) ? $path : '/' ?>index.php">Accueil</a>
            </li>
            <li class="">
              <a href="<?= isset($path) ? $path : '/' ?>room-list.php">Chambres</a>
            </li>

            <li class="">
              <a href="<?= isset($path) ? $path : '/' ?>gallery.php">Galerie</a>
            </li>
            <li class="">
              <a href="<?= isset($path) ? $path : '/' ?>contact.php">Contact</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header><!-- Parallax Effect -->
  <script type="text/javascript">
    $(document).ready(function() {
      $('#parallax-pagetitle').parallax("50%", -0.55);
    });
  </script>

  <section class="parallax-effect">
    <div id="parallax-pagetitle" style="background-image: url(./images/site/10.jpg);">
      <div class="color-overlay">
        <!-- Page title -->
        <div class="container">
          <div class="row">
            <div class="col-sm-12">
              <ol class="breadcrumb">
                <li><a href="<?= isset($path) ? $path : '/' ?>index.php">Accueil</a></li>
                <li class="active">404</li>
              </ol>
              <h1>404 Page introuvable</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 404 -->
  <section class="error-404 mb-5" style="margin-bottom: 100px;">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h2 class="fadeIn appear">404</h2>
          <h3 class="fadeIn appear" data-start="700">
            Eh bien, c'est embarrassant... on ne retrouve pas votre page.
          </h3>
          <a style="border: 2px solid #5c5c5c; color: #5c5c5c;" href="<?= isset($path) ? $path : '/' ?>index.php" class="btn btn-lg btn-secondary mt30 fadeIn appear" data-start="1000" href="<?= isset($path) ? $path : '/' ?>index.php">
            <i class="fa fa-home"></i> Retourner à l'accueil
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer style="margin-top: 0px;">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-sm-6">
          <h4>À-propos de <?= isset($_SESSION['sys']['site_name']) ? $_SESSION['sys']['site_name'] : '' ?></h4>
          <pre style="background-color:unset; color:unset;border:unset;padding: unset; margin:unset"><?= isset($_SESSION['sys']['about']) ? $_SESSION['sys']['about'] : '' ?></pre>
        </div>
        <div class="col-md-6 col-sm-6">
          <h4>Adresse</h4>
          <address>
            <pre style="background-color:unset; color:unset;border:unset;padding: unset; margin:unset">location            </pre><br>
            <abbr title="Notre Téléphone">Téléphone:</abbr> <a href="tel:<?= isset($_SESSION['sys']['phone']) ? $_SESSION['sys']['phone'] : '' ?>"><?= isset($_SESSION['sys']['phone']) ? $_SESSION['sys']['phone'] : '' ?></a><br>
            <abbr title="Notre Email">E-mail:</abbr> <a href="mailto:<?= isset($_SESSION['sys']['email']) ? $_SESSION['sys']['email'] : '' ?>"><?= isset($_SESSION['sys']['email']) ? $_SESSION['sys']['email'] : '' ?></a><br>
            <abbr title="Site web">Site web:</abbr> <a href="<?= isset($_SESSION['sys']['web_adress']) ? $_SESSION['sys']['web_adress'] : '500' ?>"><?= isset($_SESSION['sys']['web_adress']) ? $_SESSION['sys']['web_adress'] : '500' ?></a><br>
          </address>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">
        <div class="row">
          <div class="col-xs-6"> &copy; 2023 - SITE NAME - All Rights Reserved.</div>
          <div class="col-xs-6 text-right">
            <!-- NAVIGATION -->
            <ul>
              <li>
                <a href="<?= isset($path) ? $path : '/' ?>index.php">Accueil</a>
              </li>
              <li>
                <a href="<?= isset($path) ? $path : '/' ?>room-list.php">Chambres</a>
              </li>
              <li>
                <a href="<?= isset($path) ? $path : '/' ?>gallery.php">Galerie</a>
              </li>
              <li>
                <a href="<?= isset($path) ? $path : '/' ?>contact.php">Contact</a>
              </li>

              <li>
                <a href="<?= isset($path) ? $path : '/' ?>admin/">ADMIN</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Go-top Button -->
  <div id="go-top"><i class="fa fa-angle-up fa-2x"></i></div>

</body>

</html>