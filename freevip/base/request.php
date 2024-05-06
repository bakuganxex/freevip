<?php 
require_once __DIR__ . '/config.php';

$requestId = getPageParam('id');
 
if(empty($requestId)) {
    show_error_page('not_settings');
}

$FW = new FreeVip($module, $pdo, $tpl);

$request = $FW->findRequestById($requestId);

if(empty($request)) {
    show_error_page('not_settings');
}

$activeServers = $FW->getActiveServers();
$servers_options = '';

// exit(var_dump($_SESSION));

function isAccessed($module) {
	if(in_array((int) $_SESSION['rights'], $module['group_access'])) {
		return true;
	}

	return false;
}

tpl()->compileCategory(
	'Все',
	'../freevip/',
	empty($server) ? 1 : 0,
	'freevip'
);

foreach($activeServers as $serverItem) {
	tpl()->compileCategory(
		$serverItem->name,
		'../freevip/?server=' . $serverItem->id,
		0,
		'freevip'
	);

	$servers_options .= '<option value="' . $serverItem->id . '">' . $serverItem->name . '</option>';
}

$tpl->load_template('elements/title.tpl');
$tpl->set("{title}", page()->title);
$tpl->set("{name}", configs()->name);
$tpl->compile('title');
$tpl->clear();

$tpl->load_template('head.tpl');
$tpl->set("{title}", $PI->compile_str($tpl->result['title'], $request->game_name));
$tpl->set("{site_name}", configs()->name);
$tpl->set("{image}", page()->image); 
$tpl->set("{robots}", page()->robots);
$tpl->set("{type}", page()->kind);
$tpl->set("{description}", $PI->compile_str(page()->description, $request->game_name));
$tpl->set("{keywords}", $PI->compile_str(page()->keywords, $request->game_name));
$tpl->set("{url}", page()->full_url);
$tpl->set("{other}", $module['to_head']);
$tpl->set("{token}", token());
$tpl->set("{cache}", configs()->cache);
$tpl->set("{template}", configs()->template);
$tpl->compile('content');
$tpl->clear();

$menu = $tpl->get_menu();

$nav = [
	$PI->to_nav('main', 0, 0),
    $PI->to_nav('freevip', 0, 0),
	$PI->to_nav('freevip_request', 1, 0, $PI->compile_str(page()->title, $request->game_name))
]; 

$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(is_auth()) {
	include_once __DIR__ . '/../../../inc/authorized.php';
} else {
	include_once __DIR__ . '/../../../inc/not_authorized.php';
} 

if($request->status == 0) {
    $status = "Не рассмотрена";
    $color = "warning";
}
if($request->status == 1) {
    $status = "Одобрена";
    $color = "success";
}
if($request->status == 2) {
    $status = "Отклонена";
    $color = "danger";
}

$signa = '<a class="thumbnail signaThumbnail" data-lightbox="1" href="../' . $request->img . '"><img class="thumbnail-img" src="../' . $request->img . '"></img></a>';

$serverObj = $FW->findServerById($request->server);

$game_time = expand_seconds2($request->game_time);

if($game_time == "Навсегда") {
    $game_time = "0 часов 0 минут 0 секунд";
} 

$soc_vk = $request->soc_vk;

if(!filter_var($soc_vk, FILTER_VALIDATE_URL)) {
    $soc_vk = 'https://' . $soc_vk;
}

$tpl->load_template($module['tpl_dir'] . 'request.tpl');
$tpl->set("{template}", configs()->template);
$tpl->set("{servers}", $tpl->result['categories'], false);
$tpl->set("{signa}", $signa); 
$tpl->set("{color}", $color);
$tpl->set("{status}", $status);
$tpl->set("{request_id}", $request->id);
$tpl->set("{author}", $request->author);
$tpl->set("{avatar}", $request->avatar);
$tpl->set("{login}", $request->login);
$tpl->set("{real_name}", $request->real_name);
$tpl->set("{game_name}", $request->game_name);
$tpl->set("{game_time}", $game_time);
$tpl->set("{soc_vk}", $request->soc_vk);
$tpl->set("{soc_vk_link}", $soc_vk);
$tpl->set("{server_name}", $serverObj['name']);
$tpl->set("{real_age}", $FW->num_word($request->real_age, ['год', 'лет', 'лет']));
$tpl->set("{created_at}", date('d.m.Y H:i', strtotime($request->date)));
$tpl->compile('content');
$tpl->clear();
