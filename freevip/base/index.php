<?php 
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

$server    = 0;
$start     = 0;
$limit     = 0;
$paginator = '';
$servers   = '';

$server = getPageParam('server');
$number = getPageParam('page');
$limit = 7;
$start = getPageStartPosition($number, $limit);

if(!empty($server)) {
	$server = pdo()->query('SELECT id FROM servers WHERE id = ' . $server . ' LIMIT 1')->fetch(PDO::FETCH_ASSOC)['id'] ?? false;
}
 
if(!empty($server))
	$count = pdo()->query('SELECT COUNT(*) AS cnt FROM freevips WHERE server = ' . $server)->fetchColumn();
else 
	$count = pdo()->query('SELECT COUNT(*) AS cnt FROM freevips')->fetchColumn();

$page_name = '../freevip/?server=' . $server . '&';

resetIfPaginationIncorrect($number, $limit, $count, '../complaints/index');

$paginator = $tpl->paginator( 
	$number,
	$count,
	$limit,
	$page_name
);

$FW = new FreeVip($module, $pdo, $tpl);

$activeServers = $FW->getActiveServers();
$servers_options = '';

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
		$serverItem->id == $server,
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
$tpl->set("{title}", $tpl->result['title'], false);
$tpl->set("{site_name}", configs()->name);
$tpl->set("{image}", page()->image);
$tpl->set("{robots}", page()->robots);
$tpl->set("{type}", page()->kind);
$tpl->set("{description}", page()->description);
$tpl->set("{keywords}", page()->keywords);
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
	$PI->to_nav('freevip', 1, 0)
]; 

$nav = $tpl->get_nav($nav, 'elements/nav_li.tpl');

if(is_auth()) {
	include_once __DIR__ . '/../../../inc/authorized.php';
} else {
	include_once __DIR__ . '/../../../inc/not_authorized.php';
} 

$requests = $FW->getRequests($start, $server, $limit);

$tpl->load_template($module['tpl_dir'] . 'index.tpl');
$tpl->set("{template}", configs()->template);
$tpl->set("{servers}", $tpl->result['categories'], false);
$tpl->set("{requests}", $requests);
$tpl->set("{servers_options}", $servers_options);
$tpl->set("{pagination}", $paginator);
$tpl->compile('content');
$tpl->clear();