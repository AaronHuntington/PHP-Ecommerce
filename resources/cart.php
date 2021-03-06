<?php 
    require_once("config.php");
?>

<?php
    
    if(isset($_GET['add'])){
        
        $query = utility::query("SELECT * FROM products WHERE product_id=".utility::escape_string($_GET['add'])."");
        utility::confirm($query);

        while($row = utility::fetch_array($query)){
            if($row['product_quantity'] != $_SESSION['product_'.$_GET['add']]){
                $_SESSION['product_'.$_GET['add']]+=1;
                utility::redirect("../public/checkout.php");
            } else {
                utility::set_message("We only have ". $row['product_quantity']." ". "{$row['product_title']}" . "Available");
                utility::redirect('../public/checkout.php');
            }
        }
    }

    if(isset($_GET["remove"])){
        $_SESSION['product_' . $_GET['remove']]--;

        if($_SESSION['product_' . $_GET['remove']] < 1){
            unset($_SESSION['item_total']);
            unset($_SESSION['item_quantity']);
            
            utility::redirect("../public/checkout.php");
        } else {
            utility::redirect("../public/checkout.php");
        }
    }

    if(isset($_GET['delete'])){
        $_SESSION['product_'.$_GET['delete']] = '0';
        unset($_SESSION['item_total']);
        unset($_SESSION['item_quantity']);

        utility::redirect("../public/checkout.php");
    }

    function cart(){
        $total          = 0;
        $item_quantity  = 0;
        $item_name      = 1;
        $item_number    = 1;
        $amount         = 1;
        $quantity       = 1;

        foreach($_SESSION as $name => $value){

            if($value > 0){
                if(substr($name, 0, 8) == "product_"){

                    $length = strlen($name - 8);

                    $id = substr($name, 8, $length);

                    $query = utility::query("SELECT * FROM products WHERE product_id = ".utility::escape_string($id)."");
                    utility::confirm($query);

                    while($row = utility::fetch_array($query)){

                        $sub_productTotal = $row['product_price']*$value;
                        $item_quantity += $value;

                        $product_image = utility::display_image($row['product_image']);

$product = <<<DELIMETER
<tr>
    <td>
        {$row['product_title']}<br>
        <img src="../resources/{$product_image}" width="100">
    </td>
    <td>&#36;{$row['product_price']}</td>
    <td>{$value}</td>
    <td>&#36;{$sub_productTotal}</td>
    <td>
        <a class="btn btn-warning" href="../resources/cart.php?remove={$row['product_id']}"><span class="glyphicon glyphicon-minus"></span></a>
        <a class="btn btn-success" href="../resources/cart.php?add={$row['product_id']}"><span class="glyphicon glyphicon-plus"></span></a>    
        <a class="btn btn-danger" href="../resources/cart.php?delete={$row['product_id']}"><span class="glyphicon glyphicon-remove"></span></a>
    </td>
</tr>

<input type="hidden" name="item_name_{$item_name}" value="{$row['product_title']}">
<input type="hidden" name="item_number_{$item_number}" value="{$row['product_id']}">
<input type="hidden" name="amount_{$amount}" value="{$row['product_price']}">
<input type="hidden" name="quantity_{$quantity}" value="{$value}">
DELIMETER;
                        echo $product;

                        $item_name++;
                        $item_number++;
                        $amount++;
                        $quantity++;

                        $_SESSION["item_total"] = $total += $sub_productTotal;
                        $_SESSION["item_quantity"] = $item_quantity;
                    }

                    // $_SESSION["item_total"] = $total += $sub_productTotal;
                    // $_SESSION["item_quantity"] += $value;
                }
            }
        }
    }

    function show_paypal_button(){

        if(isset($_SESSION['item_quantity']) && $_SESSION['item_quantity'] != 0){
$paypal_button = <<<DELIMETER

<input type="image" name="upload"
    src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
    alt="PayPal - The safer, easier way to pay online">

DELIMETER;
            
            return $paypal_button;
        }

    }

    function process_transaction(){

        if(isset($_GET['tx'])){
            $amount = $_GET['amt'];
            $currency = $_GET['cc'];
            $transaction = $_GET['tx'];
            $status = $_GET['st'];
            $total          = 0;
            $item_quantity  = 0;

            foreach($_SESSION as $name => $value){

                if($value > 0){
                    if(substr($name, 0, 8) == "product_"){

                        $length = strlen($name - 8);

                        $id = substr($name, 8, $length);

                        $send_order = query("INSERT INTO orders (order_amount, order_transaction, order_status, order_currency)
                            VALUES ('{$amount}','{$status}','{$transaction}','{$currency}')");
                        $last_id = last_id();
                        confirm($send_order);

                        $query = utility::query("SELECT * FROM products WHERE product_id = ".utility::escape_string($id)."");
                        utility::confirm($query);

                        while($row = utility::fetch_array($query)){

                            $product_price      = $row['product_price'];
                            $product_title      = $row['product_title'];
                            $sub_productTotal = $row['product_price']*$value;
                            $item_quantity += $value;

                            $total += $sub_productTotal;
                            $item_quantity;

                            $insert_report = utility::query("INSERT INTO reports (product_id, order_id, product_title, product_price, product_quantity)
                                VALUES ('{$id}','{$last_id}','{$product_title}','{$product_price}','{$value}')");

                            utility::confirm($insert_report);
                        }

                        // $_SESSION["item_total"] = $total += $sub_productTotal;
                        // $_SESSION["item_quantity"] += $value;
                    }
                }
            }
            session_destroy();
        } else {
            utility::redirect("index.php");
        }
    }
?>