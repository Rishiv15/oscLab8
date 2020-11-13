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
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `p_id` int,
  FOREIGN KEY (`p_id`) REFERENCES `tblproduct`(`id`),
  `p_quantity` varchar(255) NOT NULL,
  `p_price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `shipping` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  FOREIGN KEY (`order_id`) REFERENCES `Order`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `payment` (
  `id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `order_id` int,
  FOREIGN KEY (`order_id`) REFERENCES `Order`(`id`),
  `price` double(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `tblproduct`
  ADD COLUMN `quantity` int default 1000;
  


