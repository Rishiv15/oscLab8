
<?php
   session_start();
   
   // To include file containing connection to database code
   require_once("dbcontroller.php");
   
   // Create an instance of DBController to query data from the database
   $db_handle = new DBController();

   // If 'Add to cart', '+', '-', empty cart or remove button is clicked
   if(!empty($_GET["action"])) {

   // check which button was clicked
	switch($_GET["action"]) {

      // if add to cart or + button was clicked
      case "add":	
         // find the product in the table in the database	
         $productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");

         // create an array containing the product
         $itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>(int)$productByCode[0]["code"]+1, 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));
         
         // if cart is not empty
         if(!empty($_SESSION["cart_item"])) {
            // if product is in cart already
            if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
               foreach($_SESSION["cart_item"] as $k => $v) {
                     // increment the quantity by 1
                     if($productByCode[0]["code"] == $k) {
                        if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                           $_SESSION["cart_item"][$k]["quantity"] = 0;
                        }
                        $_SESSION["cart_item"][$k]["quantity"] += 1;
                     }
               }
            } else { // if product is not in cart
               $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
            }
         } else { // if cart is empty
            $_SESSION["cart_item"] = $itemArray;
         }
      break;
      
      // if remove button was clicked
		case "remove":
			if(!empty($_SESSION["cart_item"])) {
				foreach($_SESSION["cart_item"] as $k => $v) {
                  // remove the item that user wanted to
						if($_GET["code"] == $k)
                     unset($_SESSION["cart_item"][$k]);
                  // if in case cart becomes empty, unset the cart session variable				
						if(empty($_SESSION["cart_item"]))
							unset($_SESSION["cart_item"]);
				}
			}
      break;

      // if - button was clicked
      case "decrement":
         // find the product
         $productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
         foreach($_SESSION["cart_item"] as $k => $v) {
            if($productByCode[0]["code"] == $k) {
               // decrement the quantity by 1
               $_SESSION["cart_item"][$k]["quantity"] -= 1;

               // if quantity becomes <= 0 then remove from the cart
               if($_SESSION["cart_item"][$k]["quantity"] <= 0) {
                  unset($_SESSION["cart_item"][$k]);
               }

               // if in case cart becomes empty, unset the cart session variable
               if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
            }
         }
      break;

      // if empty cart button was clicked then empty the cart by unsetting the cart session variable
		case "empty":
			unset($_SESSION["cart_item"]);
      break;	
      
      case "pay":
         
      break;
	}
   }
?>
<HTML>
   <HEAD>
      <TITLE>Shopping Cart</TITLE>
      <link href="style.css" type="text/css" rel="stylesheet" />
   </HEAD>
   <BODY>
      <div id="shopping-cart">
         <div class="txt-heading bg-success text-center">Shopping Cart</div>
         <a id="btnEmpty" href="index.php?action=empty">Empty Cart</a>
         <?php
            if(isset($_SESSION["cart_item"])){
                $total_quantity = 0;
                $total_price = 0;
            ?>	
         <table class="tbl-cart" cellpadding="10" cellspacing="1">
            <tbody>
               <tr class="bg-primary">
                  <th style="text-align:left;">Name</th>
                  <th style="text-align:left;">Code</th>
                  <th style="text-align:right;" width="8%">Quantity</th>
                  <th style="text-align:right;" width="10%">Unit Price</th>
                  <th style="text-align:right;" width="10%">Price</th>
                  <th style="text-align:center;" width="5%">Remove</th>
               </tr>
               <?php		
                  foreach ($_SESSION["cart_item"] as $item){
                      $item_price = $item["quantity"]*$item["price"];
                  ?>
               <tr>
                  <td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
                  <td><?php echo $item["code"]; ?></td>
                  <td style="text-align:right;">
                     <table>
                        <tr>
                           <form method="post" action="index.php?action=add&code=<?php echo $item["code"]; ?>">
                              <input type="submit" class="bg-success" value="+">
                           </form>
                        </tr>
                        <tr>
                           <?php echo $item["quantity"]; ?> 
                        </tr>
                        <tr>
                           <form method="post" action="index.php?action=decrement&code=<?php echo $item["code"]; ?>">
                              <input type="submit" class="bg-danger" value="-">
                           </form>
                        </tr>
                     </table>
                  </td>
                  <td  style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
                  <td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
                  <td style="text-align:center;"><a href="index.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
               </tr>
               <?php
                  $total_quantity += $item["quantity"];
                  $total_price += ($item["price"]*$item["quantity"]);
                  }
                  ?>
               <tr>
                  <td colspan="2" align="right">Total:</td>
                  <td align="right"><?php echo $total_quantity; ?></td>
                  <td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
                  <td></td>
               </tr>
            </tbody>
         </table>
         <?php
            } else {
            ?>
         <div class="no-records">Cart is Empty. Add some products.</div>
         <?php 
            }
            ?>
      </div>

      <?php
         if(!empty($_SESSION["cart_item"])) {
            ?>
            <div class="container">
            <form method="post" action="index.php?action=pay">
               <input type="submit" class="bg-primary" value="Pay">
            </form>
            </div>

            <?php
               } 
            ?>
   

      <div id="product-grid">
         <div class="txt-heading bg-success text-center">Products</div>
         <?php
            $product_array = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
            if (!empty($product_array)) { 
            	foreach($product_array as $key=>$value){
            ?>
         <div class="product-item bg-primary">
            <form method="post" action="index.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
               <div class="product-image"><img style="max-width: 180; max-height: 150;" src="<?php echo $product_array[$key]["image"]; ?>"></div>
               <div class="product-tile-footer">
                  <div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
                  <div class="product-price"><?php echo "$".$product_array[$key]["price"]; ?></div>
                  <div class="cart-action"><input type="submit" value="Add to Cart" class="btnAddAction bg-success" /></div>
               </div>
            </form>
         </div>
         <?php
            }
            }
            ?>
      </div>
   </BODY>
</HTML>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">