--
-- Table structure for table `tblproduct`
--

CREATE TABLE `tblproduct` (
  `id` int(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `image` text NOT NULL,
  `price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblproduct`
--

INSERT INTO `tblproduct` (`id`, `name`, `code`, `image`, `price`) VALUES
(1, 'Camera', 'ABC', 'product-images/camera.jpg', 1000.00),
(2, 'Hard Drive', 'XYZ', 'product-images/external-hard-drive.jpg', 300.00),
(3, 'Watch', 'PQR', 'product-images/watch.jpg', 400.00),
(4, 'Laptop', 'DEF', 'product-images/laptop.jpg', 1200.00);

--
-- Indexes for table `tblproduct`
--
ALTER TABLE `tblproduct`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblproduct`
--
ALTER TABLE `tblproduct`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;


-- ***********************************************************************
-- New queries


CREATE TABLE `Order` (
  `id` int(8) NOT NULL,
  `p_id` varchar(255),
  PRIMARY KEY (`id`, `p_id`),
  FOREIGN KEY (`p_id`) REFERENCES `tblproduct`(`code`),
  `p_quantity` varchar(255) NOT NULL,
  `p_price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

alter table `Order` drop column `p_price`;

set @total_orders = 0;

CREATE TABLE `shipping` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  FOREIGN KEY (`order_id`) REFERENCES `Order`(`id`),
  `address` VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `payment` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  FOREIGN KEY (`order_id`) REFERENCES `Order`(`id`),
  `total_price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `tblproduct`
  ADD COLUMN `quantity` int default 1000;

SET @total_debit = 0;

delimiter //

  CREATE PROCEDURE reduce_item(IN pcode VARCHAR(255), IN pquantity INT)
            BEGIN
            UPDATE `tblproduct` SET `quantity` = `quantity` - pquantity WHERE `code`=pcode;
            END//



  CREATE PROCEDURE insert_order(IN ocode INT, IN pcode VARCHAR(255), IN pquantity INT)
            BEGIN
            INSERT INTO `Order` VALUES(ocode,pcode,pquantity);
            END//

  CREATE PROCEDURE insert_shipping(IN ocode INT, IN addres VARCHAR(255))
            BEGIN
            INSERT INTO `shipping`(`order_id`, `address`) VALUES(ocode,addres);
            END//

  CREATE PROCEDURE insert_payment(IN ocode INT, IN total_pquantity INT)
            BEGIN
            INSERT INTO `payment`(`order_id`, `total_price`) VALUES(ocode,total_pquantity);
            END//

delimiter ;

CREATE TRIGGER total_debit_trigger 
AFTER INSERT 
ON `payment` 
FOR EACH ROW 
SET @total_debit = @total_debit + new.total_price;





