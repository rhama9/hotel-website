<?php

if (!isset($_SESSION)) {
    session_start();
}
$page_name = 'mon_compte';

if (isset($_SESSION['auth']) && $_SESSION['auth']) {
    include_once 'includes/sql_user.php';
    $user = getUser($_SESSION['auth']['id']);
    if ($user) {
        $old_fullname = $user['fullname'];
        $old_email = $user['email'];
        $old_phone = $user['phone'];
        $old_password = '';
        $old_anc_password = '';
    } else {
        unset($_SESSION['auth']);
        setNotif('warning', 'Quelque chose s\'est mal passé', "Veuiller vous reconnecteret");
        header('location: connection.php');
        die;
    }
} else {
    $_SESSION['notif'] = [
        'type' => 'warning',
        'title' => 'Veuillez vous connecter.',
        'message' => 'Vous devriez être connecter pour accéder à page du compte utilisateur.'
    ];
    header('location: connection.php');
    die;
}

if (isset($_POST) && count($_POST)) {
    $p = $_POST;
    $error = [];

    if (isset($p['info']) && $p['info']) {
        // dd($p);

        // traitement du site name
        if (isset($p['fullname'])  && strlen(trim($p['fullname']))) {
            $fullname = htmlentities(trim($p['fullname']));
            // on garde la valeur au cas ou le formulaire echou par la suite
            $old_fullname = $fullname;
        } else {
            $error['fullname'] = 'Ce champ est requis!';
        }

        // traitement de l'email
        if (isset($p['email'])  && strlen(trim($p['email']))) {

            $email = filter_var(trim($p['email']), FILTER_VALIDATE_EMAIL);
            if ($email) {
                $emailUser = getUser($email);
                if ($emailUser) {
                    if ($emailUser['id'] !== $_SESSION['auth']['id']) {
                        $error['email'] = 'Cette addresse mail est deja prise!';
                    }
                }
            } else {
                $error['email'] = 'Cet email n\'est pas valide!';
            }
            // on garde la valeur au cas ou le formulaire echou par la suite
            $old_email = $email;
        } else {
            $error['email'] = 'Ce champ est requis!';
        }

        // traitement du phone
        if (isset($p['phone'])  && strlen(trim($p['phone']))) {
            $phone = htmlentities(trim($p['phone']));
            // on garde la valeur au cas ou le formulaire echou par la suite
            $old_phone = $phone;
        } else {
            $error['phone'] = 'Ce champ est requis!';
        }

        if (!$error) {

            $Table = [
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
            ];

            $res = updateUserInfo($user['id'], $Table);

            if ($res) {
                logUser($res);
                setNotif(
                    'success',
                    'Opération réuissite!!!',
                    'Les informations ont bien été mise à jour.'
                );
                header('location: my-account.php');
                die;
            } else {
                setNotif(
                    'danger',
                    'Oups!!!, Opération échouée!!!',
                    'Quelque chose c\'est mal passé, veuillez réessayer.'
                );
            }
        }
    }


    if (isset($p['password']) && $p['password']) {
        // dd($p);

        // $regexPass = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,32}$/';
        $regexPass = PASSWORD_REGEX;

        // Traitement de l'ancien mot de passe
        if (isset($_POST['anc_password']) && strlen(trim($_POST['anc_password']))) {
            $anc_password = trim($_POST['anc_password']);

            // verification que le mot de passe est conforme au norme de securite
            if (preg_match($regexPass, $anc_password)) {
                $old_anc_password = htmlentities($anc_password);

                if (!veifyUser_password($user, $anc_password)) {
                    $error['anc_password'] = 'L\'ancien mot de passe est incorrecte';
                }
            } else {
                $error['anc_password'] = 'Le mot de passe n\'est pas valide:<br>
                    min: 1 chiffre (0-9),<br>
                    min: 1 lettre majiscule (A-Z),<br>
                    min: 1 lettre minuscule (a-z),<br>
                    min: 1 caractères spécial (@, $, %, #...etc),<br>
                    et 8-32 caractères sans espace
                    ';
            }
        } else {
            $error['anc_password'] = 'L\'ancien mot de passe est requis pour la modification';
        }

        // Traitement du nouveau mot de passe
        if (isset($_POST['new_password'])  && strlen(trim($_POST['new_password']))) {
            $new_password = trim($_POST['new_password']);

            // verification que le mot de passe est conforme au norme de securite
            if (preg_match($regexPass, $new_password)) {
                $old_new_password = htmlentities($new_password);

                if ($new_password !== trim($_POST['conf_password'])) {
                    $error['new_password'] = 'L\'ancien mot de passe et le nouveau ne corespondent pas!';
                    $error['conf_password'] = 'L\'ancien mot de passe et le nouveau ne corespondent pas!';
                }
            } else {
                $error['new_password'] = 'Le mot de passe n\'est pas valide:<br>
                    min: 1 chiffre (0-9),<br>
                    min: 1 lettre majiscule (A-Z),<br>
                    min: 1 lettre minuscule (a-z),<br>
                    min: 1 caractères spécial (@, $, %, #...etc),<br>
                    et 8-32 caractères sans espace
                    ';
                $error['conf_password'] = '';
            }
        } else {
            $error['new_password'] = 'Le nouveau mot de passe est requis pour la modification';
            $error['conf_password'] = '';
        }
        // traitement
        if (!$error) {

            $Table = [
                'password' => $new_password
            ];

            $res = updateUserPasword($user['id'], $Table);

            if ($res) {
                logUser($res);
                setNotif(
                    'success',
                    'Opération réuissite!!!',
                    'Mot de passe mis à bien été mis à jour.',
                    'password_notif'
                );
                header('location: my-account.php');
                die;
            } else {
                setNotif(
                    'danger',
                    'Oups!!!, Opération échouée!!!',
                    'Quelque chose c\'est mal passé, veuillez réessayer.'
                );
            }
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
                            <li class="active">Mon Compte</li>
                        </ol>
                        <h1>Mon Compte</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="container" style="margin-bottom: 100px;">
    <div class="row">
        <!-- Contact form -->
        <section id="contact-form" class="mt20">
            <div class="col-md-8">
                <?php $lanotifdereservation = showNotif(); ?>
                <div class="col-12" id="notif_container" style="position: relative; z-index: 999999; <?= isset($was_notif2) && $was_notif2 ? 'height: 100px;'  : 'height: 0px;' ?> ">
                    <?= $lanotifdereservation ?>
                </div>

                <form method="post" class="clearfix mt20" role="form">
                    <h4 class="lined-heading" style="margin-bottom: 20px;"><span>Information du compte</span></h4>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="fullname"><span class="required">*</span> Nom et Prénom:</label>
                                <input type="text" id="fullname" name="fullname" class="form-control <?= isset($error['fullname']) ? 'is-invalid' : '' ?>" placeholder="Nom et Prénom:" value="<?= $old_fullname ?>">
                                <?php if (isset($error['fullname'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['fullname'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group has-validation">
                                <label for="phone"><span class="required">*</span> Numero de téléphone (Whatsapp):</label>
                                <input type="text" id="phone" name="phone" class="form-control <?= isset($error['phone']) ? 'is-invalid' : '' ?>" placeholder="Ex: +212 710-577489" value="<?= $old_phone ?>">
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
                                <label for="email"><span class="required">*</span> Email:</label>
                                <input type="text" id="email" name="email" class="form-control <?= isset($error['email']) ? 'is-invalid' : '' ?>" placeholder="Ex: contact@<?= isset($old_fullname)  ? str_replace(' ', '-', strtolower($old_fullname)) : 'gmail' ?>.com" value="<?= $old_email ?>">
                                <?php if (isset($error['email'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['email'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>


                    </div>
                    <div class="form-group">
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-5 col-md-4">
                            <button type="submit" name="info" value="true" class="btn btn-primary px-5" style="display: inline-block; width: 100%;">Mettre à jour</button>
                        </div>
                        <div class="col-12 col-sm-3 d-none col-md-3">
                        </div>
                        <div class="col-12 col-sm-4 col-md-5 " style="display: flex; justify-content: end;">
                        </div>
                    </div>
                </form>
                <hr>
                <form method="post" class="clearfix mt20" role="form">

                    <h4 class="lined-heading" style="margin-bottom: 20px;"><span>Chamger de Mot de passe</span></h4>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group has-validation">
                                <label for="anc_password"><span class="required">*</span> Ancien Mot de passe:</label>
                                <input type="password" id="anc_password" name="anc_password" class="form-control <?= isset($error['anc_password']) ? 'is-invalid' : '' ?>" placeholder="Ancien Mot de passe" value="<?= $old_anc_password ?>">
                                <?php if (isset($error['anc_password'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['anc_password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group has-validation">
                                <label for="new_password"><span class="required">*</span> Nouveau mot de passe:</label>
                                <input type="password" id="new_password" name="new_password" class="form-control <?= isset($error['new_password']) ? 'is-invalid' : '' ?>" placeholder="Nouveau Mot de passe">
                                <?php if (isset($error['new_password'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['new_password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group has-validation">
                                <label for="conf_password"><span class="required">*</span> Confirmation</label>
                                <input type="password" id="conf_password" name="conf_password" class="form-control <?= isset($error['conf_password']) ? 'is-invalid' : '' ?>" placeholder="Confirmation nouveau mot de passe">
                                <?php if (isset($error['conf_password'])) : ?>
                                    <div class="invalid-feedback">
                                        <?= $error['conf_password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-5 col-md-6 mt20">
                            <button class="btn btn-primary px-5" type="submit" name="password" value="true" style="display: inline-block; width: 100%;" title="Mettre à jour le mot de passe">Mettre à jour le mot de passe</button>
                        </div>
                        <div class="col-12 col-sm-3 d-none col-md-1">
                        </div>
                        <div class="col-12 col-sm-4 col-md-5 mt20" style="display: flex; flex-direction: column; justify-content: end;">
                            <?php if ($_SESSION['auth']['is_admin']) : ?>
                                <style>
                                    span.btn.btn-default:hover,
                                    span.btn.btn-default:active {
                                        -webkit-box-shadow: unset;
                                        box-shadow: unset;
                                    }
                                </style>
                                <span class="btn btn-default disabled text-muted" id="deleteAdminBtnDisabled" style="pointer-events: unset; opacity: .4; display: inline-block; width: 100%;" title="Vous ne vouver pas suprimer votre compte car vous êtes ADMIN">
                                    Suprimer mon compte <i class="fas fa-trash">
                                    </i>
                                </span>
                                <p class="text-muted d-none text-xs" id="deleteAdminBtnDisabledMessage">
                                    Vous ne vouver pas suprimer votre compte car vous êtes ADMIN
                                </p>
                            <?php else : ?>
                                <button class="btn btn-danger delete_user_btn" style="display: inline-block; width: 100%;" title="Suprimer mon compte" data-user-id="<?= $user['id']  ?>" data-user-name="<?= $user['fullname'] ?>" type="button">
                                    Suprimer mon compte <i class="fas fa-trash">
                                    </i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        <?php include 'admin/confirmation_element.php' ?>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#deleteAdminBtnDisabled').click(function() {
            $("#deleteAdminBtnDisabledMessage").toggle();
        })
        $('.delete_user_btn').click(function(e) {
            el = e.currentTarget
            user_id = el.dataset.userId;
            user_name = el.dataset.userName;
            confirmation('Supprimer mon compte', "<h3>Voulez-vous vraiment supprimer votre compte ?</h3> <br> En supprimant votre compte, vous perdrez l'accès aux <b>réservations</b> et toutes vos informations personnelles, y compris votre <b>profil</b> et <b>l'historique de réservations</b>, seront définitivement supprimées.<br><br> <b><u>NB:</u> Veuillez noter que cette action est irréversible et toutes les données associées seront perdues définitivement.</b> <br><br>Si vous êtes sûr de vouloir supprimer votre compte, cliquez sur le bouton <b>\"OUI\"</b> ci-dessous.  ", true).done(function(result) {

                let req = $.post('delete-user.php', {
                    user_id
                }, 'json');
                req.done((result) => {
                    if (result.success == true) {
                        window.location = 'index.php';
                        // console.log(result)
                    } else {
                        window.location.reload()
                    }
                    console.log(result)

                }).fail((failed) => {
                    window.location.reload()
                    // console.log(failed.responseText)
                })

            }).fail((failed) => {
                window.location.reload()
                // console.log(failed);

            })
        })


    })
</script>
<?php include 'layouts/footer.php'; ?>