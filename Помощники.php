<?
//Путь до класса
$class = new \ReflectionClass('RGT');
var_dump($class->getFileName());
$class = new \ReflectionClass('CNext');
var_dump(get_class_methods($class));

?>
