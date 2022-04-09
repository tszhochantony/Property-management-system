<?
require('../common/conn.php');

session_start();
require_once('../lang/lang_conn.php');

$alert_msg = '';

if (!isSet($_SESSION['user'])) {
    header('Location: index.php');
} else if ($_SESSION['user']['type'] != 'staff') {
    header('Location: index.php');
}

// function deletePosition(){
//     if($confirm_msg = "確認刪除部門?"){
//         $alert_msg = "hi";
//     }
// }
?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script>
    $(document).ready(function() {
        $('#staff_position_table').DataTable();
    });
    </script>
</head>

<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$position_management_title?></h1>
    </header>
    <p><a class="abutton" href="create_staff_position.php"><?=$position_management_position?></a></p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <table border="1" id="staff_position_table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th><?=$position_management_positionCode?></th>
                            <th><?=$position_management_zhName?></th>
                            <th><?=$position_management_enName?></th>
                            <th><?=$position_management_department?></th>
                            <th><?=$position_management_manage?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $sql = $conn->prepare('SELECT * FROM staff_position');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?=$record['position_id']?></td>
                                <td><?=$record['position_chi_name']?></td>
                                <td><?=$record['position_eng_name']?></td>
                                <td><?=$record['department_id']?></td>
                                <td>
                                <a href="edit_staff_position.php?position_id=<?=$record['position_id']?>"><button><?=$position_management_modify?></button></a>
                                <a href="delete_staff_position.php?position_id=<?=$record['position_id']?>"><button onclick="return confirm('<?=$position_management_confirm?>?');"><?=$position_management_delete?></button></a>
                                </td>
                                </tr>
                            <? } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
    <script type="text/javascript">
    <?php
    if ($alert_msg <> '') echo "alert('$alert_msg');";
    if ($confirm_msg <> '') echo "confirm('$confirm_msg');";
    ?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
