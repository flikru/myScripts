<?php
ini_set('max_execution_time', '600');
$up=isset($_GET['up'])?true:false;
$db = new Corrector($up);
var_dump($db->correctWord(['карыто','затвижка','водаотводн']));
//$db->getWordsFile();
class Corrector
{

    public $conn;
    public $table='word_list';

    public function __construct($updateTable=false)
    {
        $this->conn = new mysqli("localhost", "tsk-gk", "oVU4GwGn7iAwu2D4", "tsk-gk");
		/*if(!$this->checkTable()){
            $this->addWords();
        }else if($updateTable){
            $this->deleteWords();
            $this->addWords();
        }else{
            echo "Таблица существует";
        }*/
    }
    public function addWords()
    {
        $sql="CREATE TABLE ".$this->table."(
		    ru_words varchar(255),
		    translit varchar(255)
		)";
		if($res = $this->conn->query($sql)){
			echo "Создана таблица";
		}
    }
    public function deleteWords()
    {	
        $sql="DROP TABLE ".$this->table;
		if($res = $this->conn->query($sql)){
			echo "Удалена таблица";
		}
    }
    public function checkTable(){
		
		$res = $this->conn->query("SHOW TABLES LIKE '".$this->table."';");
		return ($res->fetch_assoc()) != NULL?true:false;
    }
    public function getWordsFile(){
    	$txt="REVERSE.TXT";
    	$handle = fopen($txt, "r");
		if ($handle) {
		    while (($buffer = fgets($handle, 4096)) !== false) {
		    	$str=htmlspecialchars(addslashes(trim($buffer)));
		    	/*$data[]=[
		    		"ru"=>$str,
		    		"en"=>$this->translitIt($str),
		    	];*/
				//$data[]=$str;
				$data[$str]=$this->translitIt($str);
		    }
		    if (!feof($handle)) {
		        echo "Error: unexpected fgets() fail\n";
		    }
		    fclose($handle);
		}
		return $data;
		//$this->insertWorldToTable($data);
    }
    public function insertWorldToTable($arWords)
    {
    	$i=0;
    	foreach($arWords as $key=>$word){
    		$sql="INSERT INTO ".$this->table." (ru_words, translit)
    		VALUES ('".$word['ru']."','".$word['en']."')";

			if($this->conn->query($sql)){
    			echo $i++;
			}
    	}
    	echo "Добавлено: $i<br>";
    }

    public function insertWorldToTableBIG($arWords)
    {
    	$i=0;
    	$sql="INSERT INTO ".$this->table." (ru_words, translit) VALUES ";
    	foreach($arWords as $key=>$word){
    		$ru=$word['ru'];
    		$en=$word['en'];
    		$sql.="('$ru','$en')";
    		$i++;
    	}
    	$sql.=";";
		if($this->conn->query($sql)){
    		echo "Добавлено: $i<br>";
		}
    }

    public function translitIt($str)
    {
        $tr = array(
                "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
                "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
                "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
                "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
                "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
                "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
                "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
                "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
                "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
                "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
                "ы"=>"yi","ь"=>"'","э"=>"e","ю"=>"yu","я"=>"ya"
            );
            return strtr($str,$tr);
    }

    public function correctWord($words)
    {
		$this->conn->query("SET NAMES utf8");
		$this->conn->query("SET CHARACTER SET utf8");
		
		$this->conn->query("SET charset utf8");
		$this->conn->query("SET character_set_client = utf8");
		$this->conn->query("SET character_set_connection = utf8");
		$this->conn->query("SET character_set_results = utf8");
		$this->conn->query("SET collation_connection = utf8_general_ci");	

		//Запрос для получения словаря
		$query = "SELECT ru_words, translit FROM word_list";

		//Получение словаря
		$word_list = array();

		if($stmt = $this->conn->prepare($query))
		{
				$stmt->execute();
				$stmt->bind_result($ru_word, $translit);
				while($stmt->fetch())
				{
					$word_translit[$ru_word] = $translit;
				}
		}
		$word_translit=$this->getWordsFile();
		$word_list = $word_translit;
        //Перебираем массив введенных слов и записываем результаты в новый массив
        $num = 0;
        while($num < count($words))
        {
            $myWord = $words[$num];
			$num++;

            if(isset($word_list[$myWord]))
            {
                $correct[] .= $myWord;
            }
            else
            {
				$enteredWord = $this->translitIt($myWord);

		$possibleWord = NULL;

		foreach($word_translit as $n=>$k)
		{
			if(levenshtein(metaphone($enteredWord), metaphone($k)) < (mb_strlen(metaphone($enteredWord))/2)+1)
			{
				if(levenshtein($enteredWord, $k) < mb_strlen($enteredWord)/2+1)
				{
					$possibleWord[$n] = $k;
				}
			}
		}

		$similarity = 0;
		$meta_similarity = 0;
		$min_levenshtein = 1000;
		$meta_min_levenshtein = 1000;

                //Считаем минимальное расстояние Левенштейна
				
                if(count($possibleWord))
                {
					foreach($possibleWord as $n)
					{
						$min_levenshtein = min($min_levenshtein, levenshtein($n, $enteredWord));
					}

                    //Считаем максимальное значение подобности слов
                    foreach($possibleWord as $n)
                    {
                        if(levenshtein($k, $enteredWord) == $min_levenshtein)
                        {
							$similarity = max($similarity, similar_text($n, $enteredWord));
                        }
                    }

					$result = NULL;
					
                    //Проверка всего слова
                    foreach($possibleWord as $n=>$k)
                    {
                        if(levenshtein($k, $enteredWord) <= $min_levenshtein)
						{
							if(similar_text($k, $enteredWord) >= $similarity)
							{
								$result[$n] = $k;
							}
						}
					}

					foreach($result as $n)
					{
						$meta_min_levenshtein = min($meta_min_levenshtein, levenshtein(metaphone($n), metaphone($enteredWord)));
					}
					
                    //Считаем максимальное значение подобности слов
                    foreach($result as $n)
                    {
                        if(levenshtein($k, $enteredWord) == $meta_min_levenshtein)
                        {
							$meta_similarity = max($meta_similarity, similar_text(metaphone($n), metaphone($enteredWord)));
                        }
                    }
					
					$meta_result = NULL;
					
                    //Проверка через метафон
					foreach($result as $n=>$k)
					{
						if(levenshtein(metaphone($k), metaphone($enteredWord)) <= $meta_min_levenshtein)
						{
								if(similar_text(metaphone($k), metaphone($enteredWord)) >= $meta_similarity)
								{
									$meta_result[$n] = $k;
								}
						}
					}
                    
					
					$correct[] .= key($meta_result);

                }
                else
                {
                    $correct[] .= $myWord;
                }
            }
        }
        return $correct;
    }

}
?>
