<?php

require_once __DIR__.'/helpers/db.php';
if(!isset($_SESSION['user_id'])){header('Location: login.php');exit;}
if ((int)$_SESSION['user_role'] === 1) { http_response_code(403); exit('Доступ запрещён.'); }
if($_SERVER['REQUEST_METHOD']!=='POST'){header('Location: dashboard.php');exit;}
$requestId=(int)filter_var($_GET['request_id'] ?? '', FILTER_VALIDATE_INT);
$page=max(1,(int)($_POST['p']??1));
$comment=trim($_POST['comment']??'');
$back='dashboard.php?p='.$page.'#request-'.$requestId;
if($comment==='' || mb_strlen($comment)>3000){
 $_SESSION['review_error']='Напишите отзыв до 3000 символов.';header('Location: '.$back);exit;
}
try{
 $pdo->beginTransaction();
 $s=$pdo->prepare('SELECT id_request FROM requests WHERE id_request=? AND user_id=? AND status_id=3 AND review_id IS NULL FOR UPDATE');
 $s->execute([$requestId,$_SESSION['user_id']]);
 if(!$s->fetch()){$pdo->rollBack();http_response_code(403);exit('Отзыв доступен владельцу завершённой заявки один раз.');}
 $s=$pdo->prepare('INSERT INTO reviews(comment) VALUES(?)');$s->execute([$comment]);
 $s=$pdo->prepare('UPDATE requests SET review_id=? WHERE id_request=?');$s->execute([$pdo->lastInsertId(),$requestId]);
 $pdo->commit();$_SESSION['flash']='Спасибо за отзыв!';
}catch(PDOException $ex){
 if($pdo->inTransaction())$pdo->rollBack();
 $_SESSION['review_error']='Не удалось сохранить отзыв. Попробуйте ещё раз.';
}
header('Location: '.$back);exit;
