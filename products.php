<?php
  date_default_timezone_set("Asia/Kathmandu");
  include_once("connection.php");
  if(isset($_GET['action']) && $_GET['action'] == "api") {
    $sql = "SELECT * FROM `products` WHERE status='published' ORDER BY id DESC LIMIT ".(isset($_GET['skip'])?$_GET['skip']:'0').", 20";
      $json_array = array();
      $result = $connect->query($sql);
      while($rows = $result->FETCH_ASSOC()){
          $row_array['id'] = $rows['id'];
          $row_array['name'] = $rows['name'];
          $row_array['price'] = $rows['price'];
          $row_array['quantity'] = $rows['quantity'];
          $row_array['description'] = $rows['description'];
          $row_array['modified'] = $rows['modified'];
          $row_array['created'] = $rows['created'];

          array_push($json_array,$row_array);
      }
      $json_result['res'] = 'success';
      $json_result['data'] = $json_array;
      print_r(json_encode($json_result));
    exit;
  }
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Department Store</title>
<link href="/favicon.ico" type="image/x-icon" rel="shortcut icon" />
</head>
<body>
<div align="center">
<?php
  date_default_timezone_set("Asia/Kathmandu");
  include_once("connection.php");
  if(isset($_GET['action'])) {
    $action = $_GET['action'];
    $name = "";
    $price= "";
    $quantity= "";
    $description = "";
    $status="published";
    $edit = "false";
    $id = null;

    if($action == "edit_product"){
      $sql = "SELECT * FROM products WHERE id='".$_GET['id']."'";
      $result = $connect->query($sql);
      if($result->num_rows > 0){
        $rows = $result->FETCH_ASSOC();
        $name  = $rows['name'];
        $price = $rows['price'];
        $quantity= $rows['quantity'];
        $status = $rows['status'];
        $description=$rows['description'];
        $edit = "true";
        $id = $rows['id'];
        echo '<h1>Edit product - "'.$rows['name'].'"</h1>';
      }
      else{
        echo "<h2>Error id not found, click <a href='products.php'>here<a/> to goto list</h2>";
        exit;
      }
    }
    else if($action == "delete_product"){
          $sql = "DELETE FROM products WHERE id=".$_GET['id'];
          $result = $connect->query($sql) or die("<h1>Delete error</h1>");
          echo "<h2>Data delete success click <a href='products.php'>here<a/> to goto list</h2>";
          exit;
    }
    else{
      echo "<h1>Add new product </h1>";
    }
?>

<form name="htmlform" method="post" action="products.php">
<table width="450px">
<tr>
 <td valign="top">
  <label for="product_name">Product name </label>
 </td>
 <td valign="top">
  <input  type="text" name="name" maxlength="200" size="30" value="<?php echo $name;?>">
 </td>
</tr>
 <tr>
 <td valign="top">
  <label for="product_name">Description </label>
 </td>
 <td valign="top">
  <input  type="text" name="description" maxlength="200" size="30" value="<?php echo $description;?>">
 </td>
</tr>

<tr>
 <td valign="top"">
  <label for="price">Price </label>
 </td>
 <td valign="top">
  <input  type="text" name="price" maxlength="200" size="30" value="<?php echo $price;?>">
 </td>
</tr>
<tr>
 <td valign="top">
  <label for="quantity">Quantity</label>
 </td>
 <td valign="top">
  <input  type="text" name="quantity" maxlength="20" size="30" value="<?php echo $quantity;?>">
 </td>

</tr>
<tr>
 <td valign="top">
  <label for="status">status</label>
 </td>
 <td valign="top">
  <select name="status">
    <option value="draft">Draft</option>
    <option value="published" <?php if($status == "published"){echo 'selected="selected"';}?> >Published</option>
  </select>
 </td>
</tr>
<tr>
 <td colspan="2" style="text-align:center">
  <input type="hidden" value="<?php echo $edit;?>" name="edit"/>
  <input type="hidden" value="<?php echo $id;?>" name="id"/>
  <input type="submit" value="Submit" name="submit_product">
 </td>
</tr>
</table>
</form>
<h2>Click <a href='products.php'>here<a/> to goto list</h2>
<?php
}
else if(isset($_POST['submit_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $status = $_POST['status'];
    $description=$_POST['description'];
    $created = date("Y-m-d H:i:s");
    $modified = date("Y-m-d H:i:s");

    if(isset($_POST['id']) && ($_POST['edit'] == "true")){
     $sql = "UPDATE products SET name='{$name}', price='{$price}', quantity='{$quantity}', status='{$status}', description='{$description}', modified='{$modified}' WHERE id=".$_POST['id'];
    }
    else{
     $sql = "INSERT INTO products (name, price, quantity, status,description, created, modified) VALUES ('{$name}', '{$price}', '{$quantity}', '{$status}','{$description}', '{$created}', '{$modified}')";
    }
    $result = $connect->query($sql) or die("<h1>Data insert/update error</h1>");
    echo "<h2>Data saved/updated click <a href='products.php'>here<a/> to goto list</h2>";
    exit;
}
else{
    $sql = "SELECT * FROM products ORDER BY id DESC  LIMIT ".(isset($_GET['skip'])?$_GET['skip']:'0').",20";
    $result = $connect->query($sql);
    echo "<h1>Department Store</h1>";
    echo "<h3><a href='products.php?action=new_product'>Add product</a> <a href='products.php?action=api'>View API</a></h3>";
    if($result->num_rows > 0){
?>
      <table style="min-width:500px;width:90%" border="1">
    <tr>
      <th>SN</th><th>product</th><th>price</th><th>Status</th><th>description</th><th>quantity</th><th>Created</th><th>Modified</th><th>Action</th>
    </tr>
<?php
      $sn=0;
      while($rows = $result->FETCH_ASSOC()){
    $sn++;
    echo "
        <tr style='text-align:center;'>
      <td>{$sn}</td>
      <td>{$rows['name']}</td>
      <td>{$rows['price']}</td>
      <td>{$rows['status']}</td>
      <td>{$rows['description']}</td>
      <td>{$rows['quantity']}</td>
      <td>{$rows['created']}</td>
      <td>{$rows['modified']}</td>
      <td><a href='products.php?action=edit_product&id={$rows['id']}'>edit</a> <a href='products.php?action=delete_product&id={$rows['id']}' onclick='return confirm(\"Are You sure to delete?\")'>delete</a></td>
    </tr>
    ";
    }
      echo "</table>";

    }
    else{
      echo "There are no any products";
    }
}
?>
</div>