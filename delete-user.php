<?php
if (!isset($_SESSION)) {
    // Démarre la session
    session_start();
}
header("Content-Type: application/json");
// include_once 'includes/dbcon.php';
if (!isset($_SESSION['auth']) || !$_SESSION['auth']['id']) {

    $res = [
        'success' => false,
        'code' => '300',
        'message' => "Veuillez vous connecter pour accéder à cette fonctionalite du site.",
    ];
    setNotif(
        'danger',
        'Oups!!!',
        $res['message'],
    );
    echo json_encode($res);
    exit;
}

if (isset($_POST['user_id']) && $_POST['user_id']) {

    $user_id = (int)htmlentities(trim($_POST['user_id']));

    if (is_integer($user_id) && $user_id > 0) {

        include_once 'includes/sql_user.php';

        $dbUser = getUser($user_id);
        if ($dbUser) {
            // dd([$dbUser['id'], $_SESSION['auth']['id'], $dbUser['id'] === $_SESSION['auth']['id']]);
            if ($dbUser['id'] == $_SESSION['auth']['id']) {
                if (!$dbUser['is_admin']) {
                    $req = deleteUser($user_id);
                    if ($req) {
                        unset($_SESSION['auth']);
                        $res = [
                            'success' => true,
                            'code' => '200',
                            'user_id' => $user_id,
                            'message' => "Votre compte a été supprimé avec succès !",
                        ];
                        setNotif(
                            'success',
                            'Operation reuissite',
                            $res['message'],
                            'deleteAccountNotif'
                        );
                    } else {
                        $res = [
                            'success' => false,
                            'code' => '500',
                            'user_id' => $user_id,
                            'message' => "Une erreur côté serveur est intervenu!",
                        ];
                        setNotif(
                            'danger',
                            'Oups!!!',
                            $res['message'],
                        );
                    }
                } else {
                    $res = [
                        'success' => false,
                        'code' => '300',
                        'user_id' => $user_id,
                        'message' => "Vous ne vouver pas suprimer votre compte car vous êtes ADMIN !",
                    ];
                    setNotif(
                        'danger',
                        'Oups!!!',
                        $res['message'],
                    );
                }
            } else {
                $res = [
                    'success' => false,
                    'code' => '300',
                    'message' => "Désolé, vous ne pouvez pas supprimer le compte d'un autre utilisateur.!",
                ];
                setNotif(
                    'danger',
                    'Oups!!!',
                    $res['message'],
                );
            }
        } else {
            $res = [
                'success' => false,
                'code' => '300',
                'message' => "Désolé, aucun utilisateur n'a ete trouver!",
            ];
            setNotif(
                'danger',
                'Oups!!!',
                $res['message'],
            );
        }
    } else {
        $res = [
            'success' => false,
            'code' => '300',
            'message' => "les paramètres de la requête ne sont pas conformés aux attentes du serveur!!!",
        ];
        setNotif(
            'danger',
            'Oups!!!',
            $res['message'],
        );
    }


    echo json_encode($res);
    exit;
}

$res = [
    'success' => false,
    'code' => '404',
    'message' => "Pas de donnée envoye.",
];
setNotif(
    'danger',
    'Oups!!!',
    $res['message'],
);

echo json_encode($res);
exit;
