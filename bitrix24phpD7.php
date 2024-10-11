CModule::IncludeModule("socialnetwork");
CModule::IncludeModule("tasks");

//Рабочие группы пользователей
	$groups = CSocNetGroup::GetList([],["NAME"=>"59rost.ru"]);
	if($gr = $groups->fetch()){
		$group_id=$gr['ID'];
		var_dump($gr);
	}

//Задачи пользователей
	$res = CTasks::GetList(Array(), Array("GROUP_ID" => $group_id));

	while ($arTask = $res->GetNext()){
		echo "Task name: ".$arTask["TITLE"];
		var_dump($arTask);
}

// лог в корень
logbp($inputString);
  function logbp($ar){
  file_put_contents($_SERVER['DOCUMENT_ROOT']."/logbp.txt",print_r($ar,true));
}
