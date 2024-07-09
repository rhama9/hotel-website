<?php

include_once 'includes/sql_user.php';

$page_name = 'Contact';


$old_subject = 'reservation';
$old_content = '';

if (isset($_POST) and count($_POST)) {
    $p = $_POST;

    $error = [];
    // dd($p);
    if (isset($p['subject'])  && $p['subject']) {
        $subject = (int)htmlentities(trim($p['subject']));
        if ($subject >= 0 && SUJET_DE_CONTACT[$subject]) {
            // on garde la valeur au cas ou le formulaire echou par la suite
            $old_subject = $subject;
        } else {
            $error['subject'] = ['Sujet non valide.'];
        }
    } else {
        $error['subject'] = ['Vous devez sélectionner un sujet.'];
    }

    if (isset($p['content'])  && strlen($p['content'])) {
        $content = htmlentities(trim($p['content']));
        if (strlen($content) >= 25) {
            // on garde la valeur au cas ou le formulaire echou par la suite
        } else {
            $error['content'] = 'Pour soumettre le message, il doit comporter au minimum 25 caractères.';
        }
        $old_content = $content;
    } else {
        $error['content'] = 'Pour soumettre le message, vous devriez écrire le message ici.';
    }
    if (!$error) {
        if (isset($_SESSION['auth']) && $_SESSION['auth']) {
            $contactTable = [
                'user_id' => $_SESSION['auth']['id'],
                'subject' => $subject,
                'content' => $content
            ];

            $contact = newMessage($contactTable);
            if ($contact) {
                setNotif('success', 'Votre message a bien été envoyé', "<b>Merci beaucoup</b>.<br>Vos retours et critiques nous permettent d'améliorer nos services.", 'notif2');
                header('location: ' . DOMAIN);
            }
        } else {
            setNotif('warning', 'Connection requise!', 'Vous devez être connecté pour envoyer un message.');
            header('location: connection.php');
            die;
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

<div class="container">
    <div class="row">
        <?= showNotif() ?>

        <!-- Contact form -->
        <section id="contact-form" class="mt50">
            <div class="col-md-7">
                <h2 class="lined-heading"><span>Faites nous parvenir vos pensés</span></h2>
                <?php if (isset($_SESSION['auth'])) : ?>
                    <div class="col-12 col-sm-12" style="margin-bottom: 20px;">
                        <p>
                            Veuillez remplir les champs ci-dessous. votre Nom et Prenom, Email et téléphone sont associés avec ce message pour facilité une prise de contact future via Email, ou Whatsapp (votre numero de téléphone).
                        </p>
                    </div>
                    <?php $lanotifdereservation = showNotif('notif2'); ?>
                    <div class="col-12" id="notif_container" style="position: relative; <?= isset($was_notif2) && $was_notif2 ? 'height: 100px;'  : 'height: 0px;' ?> ">
                        <?= $lanotifdereservation ?>
                    </div>
                    <!-- Error message display -->
                    <form class="clearfix mt0" role="form" method="post" id="contactformc">
                        <div class="col-12 col-md-12">
                            <div class="form-group">
                                <label for="">Nom et Prénom</label>
                                <div class="popover-icon" data-content="Votre non et prénom sont rempli automatique et ne peuvent pas être modifier" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right">
                                    <i class="fa fa-info-circle fa-lg"> </i>
                                </div>
                                <p class="form-control"><?= $_SESSION['auth']['fullname'] ?></p>
                            </div>
                            <div class="form-group">
                                <label for="">Téléphone</label>
                                <div class="popover-icon" data-content="Votre numero de téléphone est rempli automatique et ne peut pas être modifier" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right">
                                    <i class="fa fa-info-circle fa-lg"> </i>
                                </div>
                                <p class="form-control"><?= $_SESSION['auth']['phone'] ?></p>
                            </div>
                            <div class="form-group">
                                <label for="">Email</label>
                                <div class="popover-icon" data-content="Votre email est rempli automatique et ne peut pas être modifier" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="right">
                                    <i class="fa fa-info-circle fa-lg"> </i>
                                </div>
                                <p class="form-control"><?= $_SESSION['auth']['email'] ?></p>
                            </div>
                            <div class="form-group">
                                <label for="subject" accesskey="S">Subjet</label>
                                <select name="subject" id="subject" class="form-control <?= isset($error['subject']) ? 'is-invalid' : '' ?>" style="color: #222; font-size: 1.3rem; font-weight:600; padding-bottom: .4rem;">
                                    <?php foreach (SUJET_DE_CONTACT as $key => $text) :
                                        if (is_integer($old_subject)) {
                                            $selected =  ($old_subject === $key) ? 'selected' : '';
                                        } else {
                                            $selected = '';
                                        }
                                    ?>
                                        <option value="<?= $key ?>" <?= $selected  ?>><?= $text ?></option>

                                    <?php
                                    endforeach; ?>
                                    <?php if (isset($error['subject'])) : ?>
                                        <div class="invalid-feedback" style="display: unset; color: red;">
                                            <?= $error['subject'] ?>
                                        </div>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group has-validation">
                                <label for="content" accesskey="M"><span class="required">*</span> Votre message</label>
                                <textarea name="content" rows="9" id="content" class="form-control <?= isset($error['content']) ? 'is-invalid' : '' ?>" style="color: #222; font-size: 1.3rem; font-weight:600; padding-bottom: .4rem;"><?= $old_content ?></textarea>
                                <?php if (isset($error['content'])) : ?>
                                    <div class="invalid-feedback" style="display: unset; color: red;">
                                        <?= $error['content'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-12 col-md-5" style="margin-bottom: 50px">
                            <button type="submit" class="btn  btn-lg btn-primary" style="width: 100%;">Envoyer</button>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="col-12" style="margin-bottom: 50px">
                        <p>Veuillez vous connecter ou vous inscrire ou réserver une chambre.</p>
                        <a href="inscription.php" class="btn btn-primary btn-block" style="margin-bottom: 20px">S'inscrire</a>
                        <a href="connection.php" class="btn btn-primary btn-block">Se Connecter</a>
                    </div>
                <?php endif; ?>

            </div>
        </section>
        <!-- Contact details -->
        <section class="contact-details ">
            <div class="col-md-5">
                <h2 class="lined-heading  "><span>Address</span></h2>
                <!-- Panel -->
                <div class="panel panel-default text-center">
                    <div class="panel-heading">
                        <div class="panel-title"><i class="fa fa-star"></i> <strong><?= isset($site_name) ? $site_name : '' ?></strong></div>
                    </div>
                    <div class="panel-body">
                        <address>
                            <pre style="background-color:unset; color:unset;border:unset;padding: unset; margin:unset"><?= $site_location ?></pre><br>
                            <abbr title="Notre Téléphone">Téléphone:</abbr> <a href="tel:<?= $site_phone ?>"><?= $site_phone ?></a><br>
                            <abbr title="Notre Email">E-mail:</abbr> <a href="mailto:<?= $site_email ?>"><?= $site_email ?></a><br>
                            <abbr title="Site web">Site web:</abbr> <a href="<?= $site_web_adress ?>"><?= $site_web_adress ?></a><br>
                        </address>
                    </div>
                </div>
            </div>
        </section>


    </div>
</div>

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

<script>

</script>

<?php include 'layouts/footer.php'; ?>