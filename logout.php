<?php

if (isset($_COOKIE['login'])) {
	setcookie('login', '', time() - 3600);
} 
if(isset($_COOKIE['realname'])){
	setcookie('realname', '', time() - 3600);
}
if(isset($_COOKIE['message_friend_id'])){
	setcookie('message_friend_id', '', time() - 3600);
}
header('Location: index.php');
?>