<?php
    require_once("../../config.php");

    if(isset($_GET['id'])){
        $query = utility::query("DELETE FROM products WHERE product_id = ".utility::escape_string($_GET['id'])."");
        utility::confirm($query);

        utility::set_message("Product Deleted.");
        utility::redirect("../../../public/admin/index.php?products");
    } else {
        utility::redirect("../../../public/admin/index.php?products");
    }
?>