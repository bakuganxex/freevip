<?php 
require_once __DIR__ . '/../../../inc/start.php';
require_once __DIR__ . '/../../../inc/protect.php';
include_once __DIR__ . '/../base/config.php';

$AjaxResponse = new AjaxResponse();

$FW = new FreeVip($module, $pdo);

if(!isPostRequest() || !isRightToken()) {
	$AjaxResponse->status(false)->alert('Ошибка')->send();
}

if(isset($_POST['send_request_comment'])) {
    $req_id = check($_POST['req_id'], 'int');

    $request = $FW->findRequestById($req_id);

    if(empty($request)) {
        exit(json_encode(['status' => 2, 'error' => 'Заявка не найдена']));
    }

    $text = HTMLPurifier()->purify($_POST['text']);
	$text = find_img_mp3($text, $req_id, 1);

	if (empty($text)) {
		exit (json_encode(array('status' => '2', 'input' => 'text', 'reply' => 'Заполните!')));
	} 

	if (mb_strlen($text, 'UTF-8') > 10000) {
		exit (json_encode(array('status' => '2', 'input' => 'text', 'reply' => 'Слишком длинный контент.')));
	}

	$date = date("Y-m-d H:i:s");

	$STH = $pdo->prepare("INSERT INTO `freevips__comments` (`user_id`, `freevip_id`, `text`, `date`) values (:user_id, :freevip_id, :text, :date)");
	$STH->execute(array( 'user_id' => $_SESSION['id'], 'freevip_id' => $req_id, 'text' => $text, 'date' => $date ));

	exit (json_encode(array('status' => '1')));
}

if(isset($_POST['dell_request_comment'])) {
    if(!in_array($_SESSION['rights'], $module['group_access'])) {
        exit(json_encode(['status' => 2, 'error' => 'Недостаточно прав']));
    } 

    $comm_id = check($_POST['comm_id'], 'int');

    $pdo->query('DELETE FROM freevips__comments WHERE id = ' . $comm_id . ' limit 1');

    exit(json_encode(['status' => 1]));
}

if(isset($_POST['load_request_comments'])) {
    $req_id = check($_POST['req_id'], 'int');

    $request = $FW->findRequestById($req_id);

    if(empty($request)) {
        exit(json_encode(['status' => 2, 'error' => 'Заявка не найдена']));
    }

    $tpl                    = new Template;


    $STH = $pdo->query('SELECT freevips__comments.*, users.login, users.avatar, users.rights FROM freevips__comments LEFT JOIN users ON freevips__comments.user_id = users.id WHERE freevips__comments.freevip_id = ' . $req_id . ' ORDER BY id DESC');
    $STH->setFetchMode(PDO::FETCH_OBJ);

    $tpl->result['content'] = '';

    $i = 0;

    while($row = $STH->fetch()) {
        $date = expand_date($row->date, 8);

        if(is_worthy("q")) {
            $dell = '<span onclick="dell_request_comment('.$row->id.');" tooltip="yes" data-placement="left" title="Удалить" class="m-icon icon-trash dell_message"></span>';
        } else {
            $dell = '';
        }
        $i++;
        
        $gp = $users_groups[$row->rights];
        $tpl->load_template($module['tpl_dir'] . 'elements/comment.tpl');
        $tpl->set("{id}", $row->id);
        $tpl->set("{user_id}", $row->user_id);
        $tpl->set("{login}", $row->login);
        $tpl->set("{avatar}", $row->avatar);
        $tpl->set("{text}", $row->text);
        $tpl->set("{dell}", $dell);
        $tpl->set("{date_full}", $date['full']);
        $tpl->set("{date_short}", $date['short']);
        $tpl->set("{gp_color}", $gp['color']);
        $tpl->set("{gp_name}", $gp['name']);
        $tpl->compile('content');
        $tpl->clear();
    }

    if($i == 0) {
		echo '<span class="empty-element">Комментариев нет</span>';
	} else {
		$tpl->show($tpl->result['content']);
		$tpl->global_clear();
	}

    exit();
}

if(isset($_POST['change_status'])) {
    if(!in_array($_SESSION['rights'], $module['group_access'])) {
        exit(json_encode(['status' => 2, 'error' => 'Недостаточно прав']));
    } 
    
    $req_id = check($_POST['req_id'], 'int');
    $status = check($_POST['status'], 'int');

    $request = $FW->findRequestById($req_id);

    if(empty($request)) {
        exit(json_encode(['status' => 2, 'error' => 'Заявка не найдена']));
    }
    
    if(!in_array($status, [1, 2])) {
        exit(json_encode(['status' => 2, 'error' => 'Неверный статус заявки']));
    }

    $STH = $pdo->query('UPDATE freevips SET status = ' . $status . ' WHERE id = ' . $req_id . ' LIMIT 1');

    incNotifications();
    
    $noty = close_request_noty($request->id);

    send_noty($pdo, $noty, $request->author, 2);

    exit(json_encode([
        'status' => 1,
    ]));
}

if(isset($_POST['delete_request'])) {
    if(!in_array($_SESSION['rights'], $module['group_access'])) {
        exit(json_encode(['status' => 2, 'error' => 'Недостаточно прав']));
    }

    $req_id = check($_POST['req_id'], 'int');

    $request = $FW->findRequestById($req_id);

    if(empty($request)) {
        exit(json_encode(['status' => 2, 'error' => 'Заявка не найдена']));
    }
    
    $img_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $request->img;

    if(file_exists($img_path)) {
        unlink($img_path);
    }

    $STH = $pdo->query('DELETE FROM freevips WHERE id = ' . $req_id . ' LIMIT 1');
    $STH = $pdo->query('DELETE FROM freevips__comments WHERE freevip_id = ' . $req_id);

    exit(json_encode([
        'status' => 1,
    ]));
}

if(isset($_POST['upload_image'])) {
    if (empty($_FILES['image']['name'])) {
		exit(json_encode(['status' => 2, 'content' => 'Выберите изображение!']));
    }
  
    $path = 'files/' . $module['images_dir'] . '/';
    $name = time() . rand(0, 9);

    if (if_img($_FILES['image']['name'])) {
        $image = $path . $name . '.jpg';
            
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' . $image)) {
            exit(json_encode(['status' => 2, 'content' => 'Ошибка загрузки файла!']));
        }
    } else { 
        exit(json_encode(['status' => 2, 'content' => 'Изображение должено быть в формате JPG,GIF,BMP или PNG']));
    }

    exit(json_encode(['status' => 1, 'image' => $image]));
}

if(isset($_POST['create_request'])) {
    $server = check($_POST['server'], 'int');
    $real_name = check($_POST['real_name'], null);
    $real_age = check($_POST['real_age'], 'int');
    $game_name = check($_POST['game_name'], null);
    $soc_vk = check($_POST['soc_vk'], null);
    $have_mic = check($_POST['have_mic'], 'int');
    $signa = check($_POST['signa'], null);

    if(empty($server)) {
        exit(json_encode(['status' => 2, 'error' => 'Выбран неизвестный сервер.']));
    }

    if(mb_strlen($real_name) < 3) {
        exit(json_encode(['status' => 2, 'error' => 'Слишком короткое имя.']));
    }

    if(mb_strlen($real_name) > 16) {
        exit(json_encode(['status' => 2, 'error' => 'Слишком длинное имя.']));
    }

    if(mb_strlen($game_name) < 3) {
        exit(json_encode(['status' => 2, 'error' => 'Слишком короткий ник.']));
    }

    if(mb_strlen($game_name) > 24) {
        exit(json_encode(['status' => 2, 'error' => 'Слишком длинный ник.']));
    }

    if($real_age < 16) {
        exit(json_encode(['status' => 2, 'error' => 'Минимальный возраст 16 лет.']));
    }

    if(strpos($soc_vk, 'vk.com') === false) {
        exit(json_encode(['status' => 2, 'error' => 'Укажите верную ссылку на страницу VK.']));
    }

    if(empty($have_mic)) {
        exit(json_encode(['status' => 2, 'error' => 'Для отправления заявки необходим микрофон.']));
    }

    if(empty($signa)) {
        exit(json_encode(['status' => 2, 'error' => 'Для отправления заявки необходимо сделать сигну.']));
    }

    if(empty($FW->findServerById($server))) {
        exit(json_encode(['status' => 2, 'error' => 'Выбран неизвестный сервер']));
    }

    $img_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $signa;

    if(!file_exists($img_path)) {
        exit(json_encode(['status' => 2, 'error' => 'Загруженная сигна не найдена.']));
    }

    $createRequest = $FW->createRequest($_SESSION['id'], $server, $signa, date('Y-m-d H:i:s', time()), $real_name, $game_name, $real_age, $have_mic, $soc_vk);

    if($createRequest) {
        incNotifications();

        $noty = new_request_created($createRequest);

        send_noty($pdo, $noty, 0, 1);

        exit(json_encode(['status' => 1, 'content' => '<script>
            document.location.href = "/freevip/request?id=' . $createRequest . '";
        </script>']));
    }

    exit(json_encode(['status' => 2, 'error' => 'При создании заявки произошла ошибка, обратитесь к администрации проекта.']));
}
