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
?>
<!DOCTYPE html>
<html>
<head>
    <?require("common/head.php");?>
    <script>
    $(document).ready(function() {
        $('#department_table').DataTable();
    });
    </script>
</head>

<body>
    <?include('common/menuNew.php');?>
    <header>
        <h1><?=$department_management_title?></h1>
    </header>
    <p><a class="abutton" href="create_department.php"><?=$department_management_create?></a></p>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <table border="1" id="department_table" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th><?=$department_management_code?></th>
                            <th><?=$department_management_zhName?></th>
                            <th><?=$department_management_enName?></th>
                            <th><?=$position_management_manage?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $sql = $conn->prepare('SELECT * FROM department');
                        $sql->execute();
                        $result = $sql->get_result();
                        while ($record = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?=$record['department_id']?></td>
                                <td><?=$record['department_chi_name']?></td>
                                <td><?=$record['department_eng_name']?></td>
                                <td>
                                <a href="edit_department.php?department_id=<?=$record['department_id']?>"><button><?=$department_management_modify?></button></a>
                                <a href="delete_department.php?department_id=<?=$record['department_id']?>"><button onclick="return confirm('<?=$department_management_confirm?>?');"><?=$department_management_delete?></button></a>
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
    ?>
</script>
<script src="../common/js/classie.js"></script>
<script src="../common/js/gnmenu.js"></script>
<script>
new gnMenu( document.getElementById( 'gn-menu' ) );
</script>
</html>
<?$conn->close();?>
