<?php

include "config.php";
require_once 'lib/socialauth/autoload.php';

session_start();

$adapterConfigs = array(
    'vk' => array(
        'client_id'     => '4173904',
        'client_secret' => 'uTAnmELEX3UaKdk8dWR9',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=vk'
    )
);

/*
$adapterConfigs = array(
    'vk' => array(
        'client_id'     => '4173904',
        'client_secret' => 'uTAnmELEX3UaKdk8dWR9',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=vk'
    ),
    'odnoklassniki' => array(
        'client_id'     => '',
        'client_secret' => '',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=odnoklassniki',
        'public_key'    => ''
    ),
    'mailru' => array(
        'client_id'     => '',
        'client_secret' => '',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=mailru'
    ),
    'yandex' => array(
        'client_id'     => '6cea9d0a15e043cea4d0cdd8e8fb510e',
        'client_secret' => '3dd69e991de349df90556e77969f7bdb',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=yandex'
    ),
    'google' => array(
        'client_id'     => '810847243195.apps.googleusercontent.com',
        'client_secret' => 'rhb6ANWlYyIpsSjjT2uRLu-D',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=google'
    ),
    'facebook' => array(
        'client_id'     => '205733702967948',
        'client_secret' => '1c4405d2171d71a6b87c3ec6bd50ea5b',
        'redirect_uri'  => 'http://expert03.ru/auth2.php?provider=facebook'
    )
);
*/

$adapters = array();
foreach ($adapterConfigs as $adapter => $settings) {
    $class = 'socialauth\adapter\\' . ucfirst($adapter);
    $adapters[$adapter] = new $class($settings);
}

if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $adapters) && !isset($_SESSION['user'])) {
    $auther = new socialauth\socialauth($adapters[$_GET['provider']]);

    if ($auther->authenticate()) {

        $result = mysql_query(
            "SELECT *  FROM `users` WHERE `provider` = '{$auther->getProvider()}' AND `social_id` = '{$auther->getSocialId()}' LIMIT 1"
        );

        $record = mysql_fetch_array($result);
        if (!$record) {
            $values = array(
                $auther->getProvider(),
                $auther->getSocialId(),
                $auther->getName(),
                $auther->getEmail(),
                $auther->getSocialPage(),
                $auther->getSex(),
                date('Y-m-d', strtotime($auther->getBirthday())),
                $auther->getAvatar()
            );

            $query = "INSERT INTO `users` (`provider`, `social_id`, `userfio`, `email`, `social_page`, `sex`, `birthday`, `photoname`) VALUES ('";
            $query .= implode("', '", $values) . "')";
            $result = mysql_query($query);
        } else {
            $userFromDb = new stdClass();
            $userFromDb->provider   = $record['provider'];
            $userFromDb->socialId   = $record['social_id'];
            $userFromDb->name       = $record['userfio'];
            $userFromDb->email      = $record['email'];
            $userFromDb->socialPage = $record['social_page'];
            $userFromDb->sex        = $record['sex'];
            $userFromDb->birthday   = date('m.d.Y', strtotime($record['birthday']));
            $userFromDb->avatar     = $record['photoname'];
        }

        $user = new stdClass();
        $user->provider   = $auther->getProvider();
        $user->socialId   = $auther->getSocialId();
        $user->name       = $auther->getName();
        $user->email      = $auther->getEmail();
        $user->socialPage = $auther->getSocialPage();
        $user->sex        = $auther->getSex();
        $user->birthday   = $auther->getBirthday();
        $user->avatar     = $auther->getAvatar();

        if (isset($userFromDb) && $userFromDb != $user) {
            $idToUpdate = $record['id'];
            $birthday = date('Y-m-d', strtotime($user->birthday));

            mysql_query(
                "UPDATE `users` SET " .
                "`social_id` = '{$user->socialId}', `userfio` = '{$user->name}', `email` = '{$user->email}', " .
                "`social_page` = '{$user->socialPage}', `sex` = '{$user->sex}', " .
                "`birthday` = '{$birthday}', `photoname` = '$user->avatar' " .
                "WHERE `id`='{$idToUpdate}'"
            );
        }

        $_SESSION['user'] = $user;
    }

    header("location:index.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title></title>
</head>
<body>

<?php
if (isset($_SESSION['user'])) {
    echo '<p><a href="info.php">Скрытый контент</a></p>';
} else if (!isset($_GET['code']) && !isset($_SESSION['user'])) {
    foreach ($adapters as $title => $adapter) {
        echo '<p><a href="' . $adapter->getAuthUrl() . '">Аутентификация через ' . ucfirst($title) . '</a></p>';
    }
}
?>

</body>
</html>
