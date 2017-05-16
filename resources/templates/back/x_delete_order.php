<?php
    require_once("../../config.php");

    if(isset($_GET['id'])){
        $query = utility::query("DELETE FROM orders WHERE order_id = ".utility::escape_string($_GET['id'])."");
        utility::confirm($query);

        utility::set_message("Order Deleted.");
        utility::redirect("../../../public/admin/index.php?orders");
    } else {
        utility::redirect("../../../public/admin/index.php?orders");
    }
?>