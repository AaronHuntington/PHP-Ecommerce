<?php
    $orders = new orders;
?>
<div class="col-md-12">
    <div class="row">
        <h1 class="page-header">
            Manufacture Pages
        </h1>
        <h4 class="bg-success">
            <?php utility::display_message();?>
        </h4>
    </div>
    <div class="row">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>MFG</th> 
                    <th>Transaction</th>
                    <th>Currency</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $orders->display_orders();
                ?>
            </tbody>
        </table>
    </div>