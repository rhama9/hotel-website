<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['auth']) && $_SESSION['auth']) {
    $_SESSION['notif'] = [
        'type' => 'success',
        'title' => 'Pas besoin de vous inscrire de nouveau.',
        'message' => 'Vous êtes déjà connecté.'
    ];
    header('location: index.php');
    die;
}

$page_name = 'Inscription';


// System de sauvegarde des ancienne valeurs tape par le user
$old_fullname = '';
$old_phone = '';
$old_email = '';
$old_password = '';

if (isset($_POST) && count($_POST)) {
    include 'includes/sql_user.php';

    $error = [];
    $regexNom = '/^[a-zA-Z0-9\s]+$/';
    $regexTel = '/^[1-9 0+\-]+$/';
    // verification que le mot de passe est conforme au norme de securite
    // $regexPass = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,32}$/';
    $regexPass = PASSWORD_REGEX;

    // Traitement de fullname
    if (isset($_POST['fullname']) && strlen($_POST['fullname'])) {
        $fullname = htmlentities(trim($_POST['fullname']));

        if (preg_match($regexNom, $fullname)) {
            $old_fullname = $fullname;
        } else {
            $error['fullname'] = 'Veuillez insérer un nom valide';
        }
    } else {
        $error['fullname'] = 'Le champ est requis';
    }

    // Traitement de phone
    if (isset($_POST['phone']) && strlen($_POST['phone'])) {
        $phone = htmlentities(trim($_POST['phone']));

        if (preg_match($regexTel, $phone)) {
            $old_phone = $phone;
        } else {
            $error['phone'] = 'Veuillez insérer un numero de telephone valide';
        }
    } else {
        $error['phone'] = 'Le champ est requis';
    }

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
            $old_password = htmlentities($password);

            if (preg_match($regexPass, $password)) {

                // Traitement de la confirmation du mot de passe
                if (isset($_POST['c_password']) && strlen($_POST['c_password'])) {
                    $c_password = trim($_POST['c_password']);
                    // vd([$password, $c_password, !preg_match($regexPass, $c_password), $password !== $c_password]);
                    if (!preg_match($regexPass, $c_password) or $password !== $c_password) {
                        $error['c_password'] = 'Les mots de passe ne correspondent pas.';
                    }
                } else {
                    $error['c_password'] = 'Veuillez confirmer le mot de passe';
                }
            } else {
                $error['password'] = 'Le mot de passe n\'est pas valide:<br>
                min: 1 chiffre (0-9),<br>
                min: 1 lettre majiscule (A-Z),<br>
                min: 1 lettre minuscule (a-z),<br>
                min: 1 caractères spécial (@, $, %, #...etc),<br>
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

        if ($dbConnected) {
            // on recuper l'email dans la base de donnee
            $user = getUser($email);

            if (!$user) {
                $NewUser = [
                    'fullname' => $fullname,
                    'email' => $email,
                    'phone' => $phone,
                    'image_path' => 'user_default_pic.png',
                    'password' => $password,
                ];

                $user = addUser($NewUser);
                if ($user) {
                    logUser($user);

                    setNotif(
                        'success',
                        'Vous êtes connecté',
                        'Bienvenue ' . strtoupper($user['fullname'])
                    );
                    header('location: index.php');
                } else {
                    setNotif(
                        'danger',
                        'Oups!!!',
                        'Quelque chose s\'est mal passe. Si le probleme persiste, contactez l\'administrateur du site.'
                    );
                }
            } else {
                $error['email'] = 'Cette adresse email est deja prise, <a href="connection.php?email=' . $email . '">connectez-vous plutot ici.</a>';

                setNotif(
                    'danger',
                    'Oups!!!',
                    $error['email']
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
                        <h1><?= isset($page_name) ? $page_name : '' ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container" style="margin-bottom: 100px;">
    <?= showNotif(); ?>
    <div class="row">
        <!-- Contact form -->
        <section id="contact-form" class="mt50">
            <div class="col-md-7">
                <h2 class="lined-heading"><span>Bienvenue</span></h2>
                <p>
                    VVeuillez entrer vos identifiants dans les champs ci-dessous pour créer un compte sur le site et vous connecter.
                    Si vous avez déjà un compte,
                    <a href="connection.php">
                        connectez-vous en cliquant ici.
                    </a>
                </p>
                <div id="message"></div>

                <form method="post" class="clearfix mt50" role="form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="fullname" accesskey="N"><span class="required">*</span> Nom et Prénom:</label>
                                <input name="fullname" type="text" id="fullname" class="form-control <?= isset($error['fullname']) ? 'is-invalid' : '' ?>" value="<?= $old_fullname ?>" placeholder="Votre Nom et prénon." required />
                                <?php if (isset($error['fullname'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['fullname'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="phone" accesskey="T"><span class="required">*</span> Telephone:</label>
                                <input name="phone" type="text" id="phone" class="form-control <?= isset($error['phone']) ? 'is-invalid' : '' ?>" value="<?= $old_phone ?>" placeholder="Votre numéro de télphone." required />
                                <?php if (isset($error['phone'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['phone'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group has-validation">
                                <label for="email" accesskey="E"><span class="required">*</span> E-mail:</label>
                                <input name="email" type="text" id="email" value="<?= $old_email ?>" class="form-control <?= isset($error['email']) ? 'is-invalid' : '' ?>" placeholder="Votre Email." required />
                                <?php if (isset($error['email'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="password" accesskey="M"><span class="required">*</span> Mot de passe</label>
                                <input name="password" type="password" id="password" value="<?= $old_password ?>" class="form-control <?= isset($error['password']) ? 'is-invalid' : '' ?>" placeholder="Votre Mot de passe." required />
                                <?php if (isset($error['password'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="c_password" accesskey="C"><span class="required">*</span> Confirmation du Mot de passe</label>
                                <input name="c_password" type="password" id="c_password" value="" class="form-control <?= isset($error['c_password']) ? 'is-invalid' : '' ?>" placeholder="Confirmation du Mot de passe." required />
                                <?php if (isset($error['c_password'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['c_password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-6 col-md-3">
                            <button type="submit" class="btn  btn-lg btn-primary" style="width: 100%">S'INSCRIRE</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?php include 'layouts/footer.php'; ?>