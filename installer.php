<?php
if(isset($_POST['cid']) && isset($_POST['lk'])){
    $cid = $_POST['cid'];
    $lk = $_POST['lk'];
    $data = array(
        "customer_id" => $cid,
        "license_key" => $lk
    );
    file_put_contents("assets/config/auth.json", json_encode($data));
    header("Location:index.php");
}
?>

<form method="post">
    <input name="cid" placeholder="Customer - ID" required><br>
    <input name="lk" placeholder="License - KEY" required><br>
    <button type="submit">Login</button>
</form>
