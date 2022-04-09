<?
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require '../lib/PHPMailer/src/Exception.php';
    require '../lib/PHPMailer/src/PHPMailer.php';
    require '../lib/PHPMailer/src/SMTP.php';
    session_start();
    require_once('../lang/lang_conn.php');

    
    if (!isSet($_SESSION['user'])) {
        header('Location: index.php');
    } else if ($_SESSION['user']['type'] != 'staff') {
        header('Location: index.php');
    }
    function getCategoryName($category_id) {
      if($_SESSION['lang'] == 'zh'){
        $sql = $GLOBALS['conn']->prepare('SELECT category_chi_name FROM feedback_category WHERE category_id=?');
        $sql->bind_param('s', $category_id);
        $sql->execute();
        $result = $sql->get_result();
        if ($record = $result -> fetch_assoc()) {
          return $record['category_chi_name'];
        }
      } else {
        $sql = $GLOBALS['conn']->prepare('SELECT category_eng_name FROM feedback_category WHERE category_id=?');
        $sql->bind_param('s', $category_id);
        $sql->execute();
        $result = $sql->get_result();
        if ($record = $result -> fetch_assoc()) {
          return $record['category_eng_name'];
      }
    }
  }   
      function getStatus($status_id) {
        switch ($status_id) {
          case 0:  
            if($_SESSION['lang'] == 'zh'){
              return '未處理';
          } else return 'Waiting for process';
          case 1:   
            if($_SESSION['lang'] == 'zh'){
              return '待處理';
          } else return 'Waiting for process';
          case 1.5: 
            if($_SESSION['lang'] == 'zh'){
              return '處理中';
          } else return 'Processing';
          case 2:  
            if($_SESSION['lang'] == 'zh'){
              return '已處理';
          } else return 'Processed';
        }
      }
?>