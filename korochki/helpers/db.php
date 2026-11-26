<?php
require_once __DIR__.'/security.php';
final class Database {
 public static function connect():PDO {
  $c=require dirname(__DIR__).'/config.php';
  return new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4",$c['username'],$c['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false,PDO::MYSQL_ATTR_FOUND_ROWS=>true]);
 }
}
try{$pdo=Database::connect();}catch(PDOException $ex){http_response_code(503);exit('База данных недоступна. Запустите MySQL в XAMPP и проверьте config.php.');}
if(isset($_SESSION['user_id'])){
 $s=$pdo->prepare('SELECT role_id,fio FROM users WHERE id_user=?');$s->execute([$_SESSION['user_id']]);$current=$s->fetch();
 if(!$current){unset($_SESSION['user_id'],$_SESSION['user_role'],$_SESSION['user_name']);}
 else{$_SESSION['user_role']=$current['role_id'];$_SESSION['user_name']=$current['fio'];}
}

const REQUEST_STATUSES = [1 => 'Новая', 2 => 'В обработке', 3 => 'Выполнена'];
