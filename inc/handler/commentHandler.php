<?php

include_once dirname(dirname(__FILE__)) . '/autoload.php';

if (isset($_POST['type']) && $_POST['type'] == "new_status") {

	// Infomation result for responsive
	$result = ['status' => 200];

	// New connect and call API
	$user = new UserController();
    $info = $user->GetUser(str_replace('%40', '@', $_POST['username']));

    // Valid user comment
    if ((strcmp(gettype($info), 'string') == 0) && strcmp('Tài khoản không hợp lệ', $info) == 0) {
    	$result['status'] = 404;
    	$result['respText'] = $info;
    } else {

    	// Write new comment on status
    	$comment = $user->NewComment($_POST['id_status'], $info['user_email'], $_POST['content_comment']);

	    // Return info user for Client
	    $name = !empty($info['user_displayname']) ? $info['user_displayname'] : $info['user_email'];
	    $src = !empty($info['user_avatar']) ? 'data:image;base64,'.$info['user_avatar'] : "asset/img/non-avatar.png";

    	$result['respText'] = $comment;
	    $result['name'] = $name;
	    $result['avatar'] = $src;    	
	    $result['id_user'] = $info['user_id'];
	    $result['content'] = $_POST['content_comment'];
    }

	echo json_encode($result);
} else {
	echo "Are your kidding me?";
}