<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['auth']) && $_SESSION['auth']) {
    $_SESSION['notif'] = [
        'type' => 'success',
        'title' => 'Pas besoin de vous connecter de nouveau.',
        'message' => 'Vous êtes déjà connecté.'
    ];
    header('location: index.php');
    die;
}
$page_name = 'Connexion';

// $old_email = 'cissoko@gmail.com';
// $old_password = 'Courage.1234';
$old_email = '';
$old_password = '';

if (isset($_GET['email']) && strlen(trim($_GET['email']))) {
    $old_email = htmlentities(trim($_GET['email']));
}

if (isset($_POST) && count($_POST)) {
    $error = [];
    // Traitement de l'email
    if (isset($_POST['email']) && strlen($_POST['email'])) {
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        if ($email) {
            $old_email = $email;
        } else {
            $error['email'] = 'Veuillez insérer une afresse mail valide';
        }
    } else {
        $error['email'] = 'Le champ email est requis';
    }

    // Traitement du mot de passe
    if (isset($_POST['password'])) {
        if (strlen($_POST['password']) >= 8) {
            $password = trim($_POST['password']);
            // verification que le mot de passe est conforme au norme de securite
            $patterm = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,32}$/';
            if (!preg_match($patterm, $password)) {
                $error['password'] = 'Le mot de passe n\'est pas valide:<br>
                min: 1 chiffre (0-9),<br>
                min: 1 lettre majiscule,<br>
                min: 1 lettre minuscule,<br>
                min: 1 caractères spécial,<br>
                et 8-32 caractères sans espace
                ';
            }
        } else {
            $error['password'] = 'Le mot de passe doit être au min 8 caractères!';
        }
    } else {
        $error['password'] = 'Le mot de passe est requis';
    }

    // traitement ici
    if (!$error) {
        include_once 'includes/sql_user.php';
        // include_once 'includes/sql_rooms.php';

        if ($dbConnected) {
            // on recuper l'email dans la base de donnee
            $user = getUser($email);

            if ($user) {

                logUser($user);

                setNotif(
                    'success',
                    'Vous êtes connecté',
                    'Bienvenue ' . strtoupper($res['fullname'])
                );
                header('location: index.php');
            } else {
                setNotif(
                    'danger',
                    'Oups!!!',
                    'Connection echoué, email ou mot de passe incorece!'
                );
            }
        } else {
            setNotif(
                'danger',
                'Oups!!!',
                'Une erreur est survenue côté serveur!'
            );
        }
    }
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
                        <h1>Se connecter</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container" style="margin-bottom: 100px;">

    <?= showNotif(); ?>
    <div class="row">

        <div class="col-md-2"></div>
        <!-- Contact form -->
        <section id="contact-form" class="mt50">
            <div class="col-md-8">
                <h2 class="lined-heading"><span>Bienvenue</span></h2>
                <p>
                    Veuillez entrer vos identifiants dans les champs ci-dessous pour vous connecter sur le site.
                    Ou
                    <a href="inscription.php">
                        inscrivez-vous en cliquant ici.
                    </a>
                </p>
                <div id="message"></div>
                <!-- Error message display -->
                <form method="post" class="clearfix mt50" role="form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="email" accesskey="E"><span class="required">*</span> Votre E-mail:</label>
                                <input name="email" type="email" id="email" value="<?= $old_email ?>" placeholder="Veuillez sasir votre Email." class="form-control <?= isset($error['email']) ? 'is-invalid' : '' ?>" required />
                                <?php if (isset($error['email'])) : ?>
                                    <div class="invalid-feedback" style="display: unset; color: red;">
                                        <?= $error['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" accesskey="M"><span class="required">*</span> Mot de passe</label>
                                    <input name="password" type="password" id="password" value="<?= $old_password ?>" class="form-control" placeholder="Votre Mot de passe." required />
                                    <?php if (isset($error['password'])) : ?>
                                        <div class="invalid-feedback" style="display: unset; color: red;">
                                            <?= $error['password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn  btn-lg btn-primary" style="width: 100%;">CONNEXTION</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </section>
        <div class="col-md-2"></div>

    </div>
</div>


<?php include 'layouts/footer.php'; ?>