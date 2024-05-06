<?php 
$module = [
    'name'          => 'freevip',
    'to_head_admin' => "<script src=\"../modules_extra/freevip/ajax/ajax.js?v={cache}\"></script><link rel=\"stylesheet\" href=\"../modules_extra/freevip/templates/admin/css/primary.css?v={cache}\">",
    'to_head'       => "<script src=\"../modules_extra/freevip/ajax/ajax.js?v={cache}\"></script><link rel=\"stylesheet\" href=\"../modules_extra/freevip/templates/admin/css/primary.css?v={cache}\">",
    'tpl_dir'       => "../../../modules_extra/freevip/templates/" . configs()->template . "/tpl/",
    'tpl_dir_admin' => "../../../modules_extra/freevip/templates/admin/tpl/",
	'group_access' => [1, 12],	// основатель, команды проекта 
	'images_dir' => 'freevip_imgs',
];

// ini_set('display_errors', '1'); 
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL); 

function new_request_created($id) {
	return "Оставлена новая заявка на бесплатный VIP для девушек <a href='../freevip/request?id=" . $id . "'>#" . $id . "</a>";
}

function close_request_noty($id) {
	return "Ваша заявка на бесплатный VIP для девушек <a href='../freevip/request?id=" . $id . "'>#" . $id . "</a> рассмотрена";
}
 
class FreeVip { 
	private $pdo;
	private $tpl;
	private $module;

	function __construct($module, $pdo = null, $tpl = null) {
		$this->module = $module;
		if(isset($pdo)) {
			$this->pdo = $pdo;
		} 
		if(isset($tpl)) {
			$this->tpl = $tpl;
		}
	}

	public function loadComments($id) {

	}

	public function findRequestById($id) {
		$STH = $this->pdo->query('SELECT freevips.*, users.login, users.avatar, users.game_time FROM freevips LEFT JOIN users ON freevips.author = users.id WHERE freevips.id = ' . (int) $id . ' LIMIT 1');

		return $STH->fetch(PDO::FETCH_OBJ); 
	}

	public function findServerById($id) {
		$STH = $this->pdo->query('SELECT id, name, address FROM servers WHERE id = ' . (int) $id . ' LIMIT 1');

		$result = $STH->fetch(PDO::FETCH_ASSOC);

		if(!empty($result)) {
			return $result;
		}

		return false;
	}

	public function createRequest($author, $server, $img, $date, $real_name, $game_name, $real_age, $have_mic, $soc_vk) {
		$STH = $this->pdo->prepare('INSERT INTO freevips (author, server, img, date, real_name, game_name, real_age, have_mic, soc_vk) VALUES (:author, :server, :img, :date, :real_name, :game_name, :real_age, :have_mic, :soc_vk)');
		if($STH->execute([
			':author' => $author,
			':server' => $server,
			':img' => $img,
			':date' => $date,
			':real_name' => $real_name,
			':game_name' => $game_name,
			':real_age' => $real_age,
			':have_mic' => $have_mic,
			':soc_vk' => $soc_vk
		])) {
			return $this->pdo->lastInsertId();
		} 

		return false;
	}

	public function getActiveServers() {
		$STH = $this->pdo->query('SELECT id, name, address FROM servers WHERE servers.show = 1'); 

		return $STH->fetchAll(PDO::FETCH_OBJ);
	}

	public function num_word($value, $words, $show = true) 
	{
		$num = $value % 100;
		if ($num > 19) { 
			$num = $num % 10; 
		}
		
		$out = ($show) ?  $value . ' ' : '';
		switch ($num) {
			case 1:  $out .= $words[0]; break;
			case 2: 
			case 3: 
			case 4:  $out .= $words[1]; break;
			default: $out .= $words[2]; break;
		}
		
		return $out;
	}

    public function getRequests($start, $server, $limit = 10) {
		$start = checkStart($start, "int");
		$server = check($server, "int");
		$limit = check($limit, "int");

		if(empty($start)) {
			$start = 0;
		}
		if(empty($limit)) {
			$limit = 10;
		}

		// exit(var_dump($server));
		// exit(var_dump())

		if(!empty($server))
			$STH = $this->pdo->query("SELECT freevips.id, freevips.status, freevips.date, freevips.author, freevips.game_name, users.login, users.avatar FROM freevips LEFT JOIN users ON freevips.author = users.id WHERE freevips.server = $server 	ORDER BY date DESC LIMIT $start, $limit");
		else 
			$STH = $this->pdo->query("SELECT freevips.id, freevips.status, freevips.date, freevips.author, freevips.game_name, users.login, users.avatar FROM freevips LEFT JOIN users ON freevips.author = users.id ORDER BY date DESC LIMIT $start, $limit");
		
		$STH->setFetchMode(PDO::FETCH_OBJ);

		$this->tpl->result['local_content'] = '';

		while($row = $STH->fetch()) {
			if($row->status == 0) {
				$status = "Не рассмотрена";
				$color = "warning";
			}
			if($row->status == 1) {
				$status = "Одобрена";
				$color = "success";
			}
			if($row->status == 2) {
				$status = "Отклонена";
				$color = "danger";
			}

			$this->tpl->load_template($this->module['tpl_dir'] . '/elements/request.tpl');
			$this->tpl->set("{color}", $color);
			$this->tpl->set("{status}", $status);
			$this->tpl->set("{id}", $row->id);
			$this->tpl->set("{author}", $row->author);
			$this->tpl->set("{login}", $row->login);
			$this->tpl->set("{avatar}", $row->avatar);
			$this->tpl->set("{name}", $row->game_name);
			$this->tpl->set("{date}", expand_date($row->date, 7));
			$this->tpl->compile('local_content');
			$this->tpl->clear();
		}

		if($this->tpl->result['local_content'] == '') {
			$this->tpl->result['local_content'] = '<tr><td colspan="10">Заявок нет</td></tr>';
		}

		return $this->tpl->result['local_content'];
	}
}
