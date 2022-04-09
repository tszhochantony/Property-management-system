<?php 
    if(!isset($_SESSION['lang'])){
        $_SESSION['lang'] == 'zh';
        require_once('../lang/chinese.php');
    }else if($_SESSION['lang'] == 'zh'){
        require_once('../lang/chinese.php');
    }else if($_SESSION['lang'] == 'en'){
        require_once('../lang/english.php');
    }
?>