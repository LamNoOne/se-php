-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2024 at 04:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `se-php`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `createCartFromOrder` (IN `p_orderId` INT, OUT `p_cartId` INT, OUT `p_errorMessage` VARCHAR(255))   create_cart_from_order:BEGIN
    DECLARE v_cartId INT;
    
    -- Declare variables for error handling
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Rollback the transaction on exception
        ROLLBACK;
        SET p_errorMessage = 'An error occurred during the cart population process';
    END;

    -- Start a transaction
    START TRANSACTION;

    -- Find the cartId for the given orderId
    SELECT C.id INTO v_cartId
    FROM `order` AS O
    JOIN `cart` AS C
    ON O.userId = C.userId
    WHERE O.id = p_orderId;

    -- Check if the orderId exists
    IF v_cartId IS NULL THEN
        -- Handle the case where orderId does not exist
        SET p_errorMessage = 'Order does not exist';
        ROLLBACK;
        LEAVE create_cart_from_order;
    END IF;

    -- Insert products from orderdetail into cartdetail
    INSERT INTO cartdetail (cartId, productId, quantity)
    SELECT v_cartId, productId, quantity
    FROM orderdetail
    WHERE orderId = p_orderId;

    -- Set output parameters
    SET p_cartId = v_cartId;
    SET p_errorMessage = NULL;

    -- Commit the transaction
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `createOrderDirectlyProduct` (IN `p_userId` INT, IN `p_productId` INT, IN `p_quantity` INT, IN `p_shipAddress` VARCHAR(200), IN `p_phoneNumber` VARCHAR(11), OUT `p_orderId` INT, OUT `p_errorMessage` VARCHAR(255))   create_order_directly_product:BEGIN
    -- Declare variables
    DECLARE productPrice FLOAT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Rollback the transaction on exception
        ROLLBACK;
        SET p_errorMessage = 'An error occurred during the checkout process';
    END;

    -- Start a transaction
    START TRANSACTION;

    -- Find the price of the selected product
    SELECT price INTO productPrice
    FROM `product`
    WHERE id = p_productId;

    -- Check if the product exists
    IF productPrice IS NULL THEN
        SET p_errorMessage = 'Selected product does not exist';
        ROLLBACK;
        LEAVE create_order_directly_product;
    END IF;

    -- Create a new order
    INSERT INTO `order` (orderStatusId, userId, shipAddress, phoneNumber)
    VALUES (1, p_userId, p_shipAddress, p_phoneNumber);

    -- Get the last inserted order ID
    SET p_orderId = LAST_INSERT_ID();

    -- Insert order details for the selected product
    INSERT INTO `orderdetail` (orderId, productId, quantity, price)
    VALUES (p_orderId, p_productId, p_quantity, productPrice);

    -- Commit the transaction
    COMMIT;

    -- Set error message to NULL as there was no error
    SET p_errorMessage = NULL;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `createOrderSelectedProductCart` (IN `p_userId` INT, IN `p_productIds` VARCHAR(255), IN `p_shipAddress` VARCHAR(200), IN `p_phoneNumber` VARCHAR(11), OUT `p_orderId` INT, OUT `p_errorMessage` VARCHAR(255))   create_order_selected_product_cart:BEGIN
    DECLARE cartId INT;
    DECLARE productId INT;

    -- Declare variables for error handling
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Rollback the transaction on exception
        ROLLBACK;
        SET p_errorMessage = 'An error occurred during the selected checkout process';
    END;

    -- Start a transaction
    START TRANSACTION;

    -- Find the cartId for the given userId
    -- The cartId in cartdetail check product in cart
    SELECT CD.cartId INTO cartId
    FROM user U JOIN cart C ON U.id = c.userId
    JOIN cartdetail CD ON C.id = CD.cartId 
    WHERE U.id= p_userId LIMIT 1;

    -- Exit if no cart is found for the given userId
    IF cartId IS NULL THEN
        -- Handle the case where no cart is found
        SET p_errorMessage = 'No product found in cart for the given user';
        ROLLBACK;
        LEAVE create_order_selected_product_cart;
    END IF;

    -- Create a new order
    INSERT INTO `order` (orderStatusId, userId, shipAddress, phoneNumber)
    VALUES (1, p_userId, p_shipAddress, p_phoneNumber);

    -- Get the last inserted order ID
    SET p_orderId = LAST_INSERT_ID();

    -- Iterate over the product IDs in the comma-separated list
    product_loop: LOOP
        -- Exit the loop if there are no more product IDs
        IF p_productIds IS NULL OR LENGTH(p_productIds) = 0 THEN
            LEAVE product_loop;
        END IF;

        -- Extract the next product ID
        SET productId = CAST(SUBSTRING_INDEX(p_productIds, ',', 1) AS UNSIGNED);

        -- Remove the extracted product ID from the list
        SET p_productIds = TRIM(BOTH ',' FROM SUBSTRING(p_productIds, LENGTH(productId) + 2));

        -- Copy selected cart detail to order detail
        INSERT INTO orderdetail (orderId, productId, quantity, price)
        SELECT p_orderId, cd.productId, cd.quantity, p.price
        FROM cartdetail cd JOIN product p ON cd.productId = p.id
        WHERE cd.cartId = cartId AND cd.productId = productId;

        -- Delete selected product from cart
        DELETE FROM cartdetail WHERE cartdetail.cartId = cartId AND cartdetail.productId = productId;
    END LOOP;

    -- Commit the transaction
    COMMIT;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `userId`, `createdAt`, `updatedAt`) VALUES
(1, 1, '2024-02-26 05:52:42', '2024-02-26 05:52:42'),
(2, 2, '2024-02-26 05:52:49', '2024-02-26 05:52:49'),
(19, 21, '2024-03-06 03:36:06', '2024-03-06 03:36:06'),
(20, 22, '2024-03-06 03:58:40', '2024-03-06 03:58:40'),
(41, 43, '2024-03-12 01:59:23', '2024-03-12 01:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `cartdetail`
--

CREATE TABLE `cartdetail` (
  `cartId` int(10) UNSIGNED NOT NULL,
  `productId` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `createdAt`, `updatedAt`) VALUES
(1, 'Smartphone', 'Smartphone', '2023-11-26 16:19:52', '2023-11-26 16:19:52'),
(2, 'Laptop', 'Laptop', '2023-11-26 16:20:15', '2023-11-26 16:20:15'),
(3, 'Accessory', 'Accessories', '2023-11-26 07:45:29', '2023-11-27 14:10:22'),
(4, 'Studio', 'Studio', '2023-11-26 07:45:42', '2023-11-26 07:45:42'),
(5, 'Camera', 'Camera', '2023-11-26 07:45:51', '2023-11-26 07:45:51'),
(6, 'PC', 'PC, Monitor', '2023-11-26 07:46:07', '2023-11-27 09:29:35'),
(7, 'TV', 'Television', '2023-11-26 07:46:22', '2023-11-30 18:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(10) UNSIGNED NOT NULL,
  `orderStatusId` tinyint(3) UNSIGNED NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `shipAddress` varchar(100) DEFAULT NULL,
  `phoneNumber` varchar(11) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `orderStatusId`, `userId`, `shipAddress`, `phoneNumber`, `createdAt`, `updatedAt`) VALUES
(1, 2, 2, '1234 ST', '0834480248', '2024-03-02 09:29:47', '2024-03-02 09:29:47'),
(2, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 10:11:32', '2024-03-02 10:11:32'),
(3, 1, 2, '123Tower', '0834480248', '2024-03-02 10:13:51', '2024-03-02 10:13:51'),
(4, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 10:16:57', '2024-03-02 10:16:57'),
(5, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 10:17:26', '2024-03-02 10:17:26'),
(6, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 10:18:40', '2024-03-02 10:18:40'),
(7, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 10:19:16', '2024-03-02 10:19:16'),
(8, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 10:19:38', '2024-03-02 10:19:38'),
(9, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 12:27:38', '2024-03-02 12:27:38'),
(10, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 12:48:27', '2024-03-02 12:48:27'),
(11, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 12:51:03', '2024-03-02 12:51:03'),
(12, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 12:58:40', '2024-03-02 12:58:40'),
(13, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 13:05:12', '2024-03-02 13:05:12'),
(14, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 13:05:45', '2024-03-02 13:05:45'),
(15, 1, 2, 'ABC Tower', '0832038769', '2024-03-02 13:08:07', '2024-03-02 13:08:07'),
(16, 1, 2, 'ABC Tower', '0832038769', '2024-03-03 01:28:44', '2024-03-03 01:28:44'),
(17, 1, 2, '1234 ST ADDRESS', '0932038767', '2024-03-03 03:51:21', '2024-03-03 03:51:21'),
(18, 2, 2, '1234 STATUAS', '0832038769', '2024-03-03 09:06:03', '2024-03-03 09:06:03'),
(19, 2, 2, 'ABC Tower', '0832038769', '2024-03-03 09:16:28', '2024-03-03 09:16:28'),
(20, 2, 2, 'ABC Tower', '0832038769', '2024-03-03 09:17:09', '2024-03-03 09:17:09'),
(21, 2, 2, 'ABC Tower', '0832038769', '2024-03-03 09:25:35', '2024-03-03 09:25:35'),
(22, 1, 2, 'SaiGon HomeTown', '0123456789', '2024-03-05 08:22:59', '2024-03-05 08:22:59'),
(23, 2, 2, 'ABC Tower', '0832038769', '2024-03-05 14:37:23', '2024-03-05 14:37:23'),
(24, 2, 2, 'ABC Tower', '0832038769', '2024-03-05 15:11:27', '2024-03-05 15:11:27'),
(25, 2, 2, 'ABC Tower', '0832038769', '2024-03-05 15:12:46', '2024-03-05 15:12:46'),
(26, 2, 2, 'ABC Tower', '0832038769', '2024-03-05 16:40:10', '2024-03-05 16:40:10'),
(30, 2, 2, 'ABC Tower', '0832038769', '2024-03-05 16:46:50', '2024-03-05 16:46:50'),
(31, 2, 21, 'BenTre City', '0834480248', '2024-03-06 03:37:09', '2024-03-06 03:37:09'),
(32, 2, 2, 'ABC Tower', '0832038769', '2024-03-06 03:57:57', '2024-03-06 03:57:57'),
(33, 2, 22, 'NEW BenTre City ', '0834480248', '2024-03-06 04:05:39', '2024-03-06 04:05:39'),
(34, 2, 22, 'NEW BENTRE', '0834480248', '2024-03-06 04:12:52', '2024-03-06 04:12:52'),
(35, 2, 22, '1234 NEW ST', '0834480248', '2024-03-06 04:13:32', '2024-03-06 04:13:32'),
(36, 2, 22, 'SAI GON TOWER', '0934581249', '2024-03-07 11:07:56', '2024-03-07 11:07:56'),
(37, 2, 22, 'SAI GON TOWER', '0934581249', '2024-03-09 14:30:03', '2024-03-09 14:30:03'),
(38, 2, 2, 'ABC Tower', '0832038769', '2024-03-10 03:43:25', '2024-03-10 03:43:25'),
(39, 2, 2, 'Ben Tre', '0832038769', '2024-03-11 08:29:34', '2024-03-11 08:29:34'),
(40, 2, 2, 'Ben Tre City', '0832038769', '2024-03-11 13:44:18', '2024-03-11 13:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `orderdetail`
--

CREATE TABLE `orderdetail` (
  `orderId` int(10) UNSIGNED NOT NULL,
  `productId` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price` bigint(20) UNSIGNED NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orderdetail`
--

INSERT INTO `orderdetail` (`orderId`, `productId`, `quantity`, `price`, `createdAt`, `updatedAt`) VALUES
(1, 2, 6, 198, '2024-03-02 09:29:47', '2024-03-02 09:29:47'),
(1, 7, 3, 121, '2024-03-02 09:29:47', '2024-03-02 09:29:47'),
(2, 13, 9, 1249, '2024-03-02 10:11:32', '2024-03-02 10:11:32'),
(2, 493, 3, 499, '2024-03-02 10:11:32', '2024-03-02 10:11:32'),
(2, 498, 1, 499, '2024-03-02 10:11:32', '2024-03-02 10:11:32'),
(3, 111, 5, 558, '2024-03-02 10:13:51', '2024-03-02 10:13:51'),
(4, 9, 6, 799, '2024-03-02 10:16:57', '2024-03-02 10:16:57'),
(5, 105, 3, 799, '2024-03-02 10:17:26', '2024-03-02 10:17:26'),
(6, 4, 2, 59, '2024-03-02 10:18:40', '2024-03-02 10:18:40'),
(7, 14, 1, 799, '2024-03-02 10:19:16', '2024-03-02 10:19:16'),
(8, 9, 1, 799, '2024-03-02 10:19:38', '2024-03-02 10:19:38'),
(9, 11, 1, 1002, '2024-03-02 12:27:38', '2024-03-02 12:27:38'),
(10, 482, 1, 999, '2024-03-02 12:48:27', '2024-03-02 12:48:27'),
(11, 2, 1, 198, '2024-03-02 12:51:03', '2024-03-02 12:51:03'),
(12, 6, 1, 321, '2024-03-02 12:58:40', '2024-03-02 12:58:40'),
(13, 2, 1, 198, '2024-03-02 13:05:12', '2024-03-02 13:05:12'),
(13, 102, 1, 102, '2024-03-02 13:05:12', '2024-03-02 13:05:12'),
(14, 105, 1, 799, '2024-03-02 13:05:45', '2024-03-02 13:05:45'),
(14, 481, 1, 999, '2024-03-02 13:05:45', '2024-03-02 13:05:45'),
(15, 29, 2, 756, '2024-03-02 13:08:07', '2024-03-02 13:08:07'),
(15, 107, 1, 855, '2024-03-02 13:08:07', '2024-03-02 13:08:07'),
(16, 15, 3, 189, '2024-03-03 01:28:44', '2024-03-03 01:28:44'),
(16, 105, 1, 799, '2024-03-03 01:28:44', '2024-03-03 01:28:44'),
(17, 8, 2, 499, '2024-03-03 03:51:21', '2024-03-03 03:51:21'),
(17, 13, 3, 1249, '2024-03-03 03:51:21', '2024-03-03 03:51:21'),
(18, 6, 2, 321, '2024-03-03 09:06:03', '2024-03-03 09:06:03'),
(19, 1, 1, 121, '2024-03-03 09:16:28', '2024-03-03 09:16:28'),
(20, 5, 1, 1999, '2024-03-03 09:17:09', '2024-03-03 09:17:09'),
(21, 16, 1, 145, '2024-03-03 09:25:35', '2024-03-03 09:25:35'),
(22, 105, 5, 799, '2024-03-05 08:22:59', '2024-03-05 08:22:59'),
(23, 13, 1, 1249, '2024-03-05 14:37:23', '2024-03-05 14:37:23'),
(23, 102, 1, 102, '2024-03-05 14:37:23', '2024-03-05 14:37:23'),
(24, 5, 5, 1999, '2024-03-05 15:11:27', '2024-03-05 15:11:27'),
(25, 1, 1, 121, '2024-03-05 15:12:46', '2024-03-05 15:12:46'),
(26, 5, 3, 1999, '2024-03-05 16:40:10', '2024-03-05 16:40:10'),
(26, 10, 1, 167, '2024-03-05 16:40:10', '2024-03-05 16:40:10'),
(30, 205, 1, 1999, '2024-03-05 16:46:50', '2024-03-05 16:46:50'),
(31, 7, 1, 121, '2024-03-06 03:37:09', '2024-03-06 03:37:09'),
(32, 3, 1, 111, '2024-03-06 03:57:57', '2024-03-06 03:57:57'),
(33, 14, 2, 799, '2024-03-06 04:05:39', '2024-03-06 04:05:39'),
(34, 10, 1, 167, '2024-03-06 04:12:52', '2024-03-06 04:12:52'),
(34, 591, 1, 1002, '2024-03-06 04:12:52', '2024-03-06 04:12:52'),
(35, 204, 2, 2999, '2024-03-06 04:13:32', '2024-03-06 04:13:32'),
(36, 1, 1, 121, '2024-03-07 11:07:56', '2024-03-07 11:07:56'),
(37, 2, 1, 198, '2024-03-09 14:30:03', '2024-03-09 14:30:03'),
(37, 29, 1, 756, '2024-03-09 14:30:03', '2024-03-09 14:30:03'),
(38, 3, 1, 111, '2024-03-10 03:43:25', '2024-03-10 03:43:25'),
(38, 5, 3, 1999, '2024-03-10 03:43:25', '2024-03-10 03:43:25'),
(38, 10, 1, 167, '2024-03-10 03:43:25', '2024-03-10 03:43:25'),
(38, 13, 1, 1249, '2024-03-10 03:43:25', '2024-03-10 03:43:25'),
(38, 102, 1, 102, '2024-03-10 03:43:25', '2024-03-10 03:43:25'),
(39, 5, 1, 1999, '2024-03-11 08:29:34', '2024-03-11 08:29:34'),
(39, 486, 1, 999, '2024-03-11 08:29:34', '2024-03-11 08:29:34'),
(40, 7, 1, 121, '2024-03-11 13:44:18', '2024-03-11 13:44:18'),
(40, 216, 1, 121, '2024-03-11 13:44:18', '2024-03-11 13:44:18'),
(40, 304, 1, 59, '2024-03-11 13:44:18', '2024-03-11 13:44:18'),
(40, 481, 1, 999, '2024-03-11 13:44:18', '2024-03-11 13:44:18'),
(40, 497, 2, 499, '2024-03-11 13:44:18', '2024-03-11 13:44:18'),
(40, 575, 1, 121, '2024-03-11 13:44:18', '2024-03-11 13:44:18'),
(40, 592, 1, 1002, '2024-03-11 13:44:18', '2024-03-11 13:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `orderstatus`
--

CREATE TABLE `orderstatus` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `orderstatus`
--

INSERT INTO `orderstatus` (`id`, `name`, `createdAt`, `updatedAt`) VALUES
(1, 'Pending', '2023-11-26 16:16:15', '2023-11-26 16:16:15'),
(2, 'Paid', '2023-11-26 16:16:24', '2023-11-26 16:16:24'),
(3, 'Delivering', '2023-11-26 16:16:34', '2023-11-26 16:16:34'),
(4, 'Delivered', '2023-11-26 16:16:44', '2023-11-26 16:16:44');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `id` int(10) NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `otpCode` varchar(20) NOT NULL,
  `otpStatus` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`id`, `userId`, `otpCode`, `otpStatus`, `createdAt`) VALUES
(1, 43, '915768', 1, '2024-03-12 01:59:23');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `invoice_id` varchar(50) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `payer_id` varchar(50) DEFAULT NULL,
  `payer_name` varchar(50) DEFAULT NULL,
  `payer_email` varchar(50) DEFAULT NULL,
  `payer_country` varchar(20) DEFAULT NULL,
  `merchant_id` varchar(255) DEFAULT NULL,
  `merchant_email` varchar(50) DEFAULT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `paid_amount_currency` varchar(10) NOT NULL,
  `payment_source` varchar(50) DEFAULT NULL,
  `payment_status` varchar(25) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `order_id`, `invoice_id`, `transaction_id`, `payer_id`, `payer_name`, `payer_email`, `payer_country`, `merchant_id`, `merchant_email`, `paid_amount`, `paid_amount_currency`, `payment_source`, `payment_status`, `createdAt`, `updatedAt`) VALUES
(2, 15, '9R895017MP774140Y', '9XX54883MM232261R', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 2367.00, 'USD', 'paypal', 'COMPLETED', '2024-03-02 09:25:53', '2024-03-03 01:37:10'),
(3, 16, '47D866831S457804R', '57A0544252937084U', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 1366.00, 'USD', 'paypal', 'COMPLETED', '2024-03-02 19:28:29', '2024-03-03 01:37:10'),
(4, 17, '72M719190J585454L', '65K870064R784691V', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 4745.00, 'USD', 'paypal', 'COMPLETED', '2024-03-02 21:51:05', '2024-03-03 03:51:56'),
(10, 19, '0P5887855H8152617', '4F369667TW0463947', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 121.00, 'USD', 'paypal', 'COMPLETED', '2024-03-03 03:16:13', '2024-03-03 09:16:41'),
(11, 20, '6KW40187PP030471B', '1CD94967TX304192J', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 1999.00, 'USD', 'paypal', 'COMPLETED', '2024-03-03 03:16:53', '2024-03-03 09:17:29'),
(12, 21, '4YL39325GP739221X', '84C33967FC572811V', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 145.00, 'USD', 'paypal', 'COMPLETED', '2024-03-03 03:25:19', '2024-03-03 09:25:49'),
(13, 23, '2HC921292H283613G', '0D934860J8344191V', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 1351.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 08:37:12', '2024-03-05 14:37:52'),
(14, 24, '8J522480JY0703601', '34636450XT380670C', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 9995.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 09:11:37', '2024-03-05 15:12:20'),
(15, 25, '81L69875W9649343H', '63A71823GD100341N', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 121.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 09:12:31', '2024-03-05 15:13:02'),
(16, 26, '69287245TX746060A', '8NM33111UH108364X', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 6164.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 10:39:58', '2024-03-05 16:40:34'),
(17, 30, '1CU54817SD206621M', '98173766KL863394F', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 1999.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 10:46:34', '2024-03-05 16:47:04'),
(18, 31, '3KJ51788F6508224C', '1VN04209RP4157819', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 121.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 21:36:53', '2024-03-06 03:37:31'),
(19, 32, '7BS11708YN7237835', '4K76015503461750F', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 111.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 21:57:40', '2024-03-06 03:58:17'),
(20, 33, '3W960534PG2593107', '7RG36120RP355143L', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 1598.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 22:05:21', '2024-03-06 04:05:51'),
(21, 34, '672392592T937853E', '1HV522526K424134N', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 1169.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 22:12:35', '2024-03-06 04:13:06'),
(22, 35, '4Y029083MT724263P', '62442555E6439534M', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 5998.00, 'USD', 'paypal', 'COMPLETED', '2024-03-05 22:13:14', '2024-03-06 04:13:44'),
(23, 36, '9DY595426E470171A', '2WX51441XW093350B', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 121.00, 'USD', 'paypal', 'COMPLETED', '2024-03-07 05:07:40', '2024-03-07 11:08:20'),
(24, 37, '8AV56119LY4511504', '3B361896FM190614G', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 954.00, 'USD', 'paypal', 'COMPLETED', '2024-03-09 08:29:44', '2024-03-09 14:35:42'),
(25, 38, '3UW59670GV5231806', '1LR93732UA045030C', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 7626.00, 'USD', 'paypal', 'COMPLETED', '2024-03-09 22:55:55', '2024-03-10 04:56:48'),
(26, 39, '251882601S821932J', '5B764681N8846674R', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 2998.00, 'USD', 'paypal', 'COMPLETED', '2024-03-11 02:29:15', '2024-03-11 08:29:55'),
(27, 40, '1AB80357CW6777622', '00547442CU969452V', 'VTQWDKYKKB68N', 'Lam Client', 'sb-lgptq29434958@personal.example.com', 'US', 'QJDPB4GGR6A4Q', 'sb-6bdrv29335663@business.example.com', 3421.00, 'USD', 'paypal', 'COMPLETED', '2024-03-11 07:44:12', '2024-03-11 13:45:02');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(10) UNSIGNED NOT NULL,
  `categoryId` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `imageUrl` varchar(200) NOT NULL,
  `screen` varchar(200) DEFAULT NULL,
  `operatingSystem` varchar(50) DEFAULT NULL,
  `processor` varchar(50) DEFAULT NULL,
  `ram` int(10) UNSIGNED DEFAULT NULL,
  `storageCapacity` int(10) UNSIGNED DEFAULT NULL,
  `weight` decimal(5,2) UNSIGNED DEFAULT NULL,
  `batteryCapacity` int(10) UNSIGNED DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` bigint(20) UNSIGNED NOT NULL,
  `stockQuantity` int(10) UNSIGNED NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `categoryId`, `name`, `description`, `imageUrl`, `screen`, `operatingSystem`, `processor`, `ram`, `storageCapacity`, `weight`, `batteryCapacity`, `color`, `price`, `stockQuantity`, `createdAt`, `updatedAt`) VALUES
(1, 1, 'Xiaomi Redmi Note 10', 'Xiaomi Redmi Note 10', 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:80/plain/https://cellphones.com.vn/media/catalog/product/v/n/vn_iphone_15_green_pdp_image_position-1a_green_color_1_4.jpg', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 121, 20, '2023-11-14 03:43:11', '2023-12-01 08:40:36'),
(2, 1, 'SamSung Galaxy Pro Max', 'SamSung Galaxy Pro Max', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962302/se-shop/products/mq10jwoiqkrrafp7aegc.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 198, 61, '2023-11-26 01:31:43', '2023-11-27 01:41:34'),
(3, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093762/se-shop/products/te3x4h8k7xwfci269vsa.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 111, 70, '2023-11-27 14:02:44', '2023-11-27 14:02:44'),
(4, 3, 'Logitech Lift Vertical', 'Logitech Lift Vertical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094927/se-shop/products/ez7h6hghdr4j3rjfyybv.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 59, 53, '2023-11-27 14:22:09', '2023-12-02 13:48:01'),
(5, 3, 'Logitech mouse v2 white', 'Logitech mouse v2 white', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095257/se-shop/products/pfbdaczdw9bofbm0niv6.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 1999, 69, '2023-11-27 14:27:39', '2023-11-29 16:01:28'),
(6, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962476/se-shop/products/h5xaxtznfslpetjszp58.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 321, 61, '2023-11-26 01:34:38', '2023-11-27 01:42:18'),
(7, 3, 'Apple airpods', 'Apple airpods', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095315/se-shop/products/yz64hlnrlbcosipp7ik9.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:28:37', '2023-11-27 14:28:37'),
(8, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099849/se-shop/products/iwc3cms1rubn5a90fwnx.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:11', '2023-11-27 15:44:11'),
(9, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100001/se-shop/products/i7rpfk0bklbp5bhsfmuh.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:46:44', '2023-11-27 15:46:44'),
(10, 1, 'IPhone Pro Max', 'IPhone Pro Max', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962221/se-shop/products/y71mwckji2fmw5hqeo6p.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 167, 61, '2023-11-26 01:30:22', '2023-11-27 01:41:27'),
(11, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100781/se-shop/products/qobrb0ngkjo4sbfoaqb1.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 76, '2023-11-27 15:59:43', '2023-11-30 02:32:00'),
(12, 1, 'SamSung Galaxy Pro Max', 'SamSung Galaxy Pro Max', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962302/se-shop/products/mq10jwoiqkrrafp7aegc.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 198, 61, '2023-11-26 01:31:43', '2023-11-27 01:41:34'),
(13, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100893/se-shop/products/wwtgsflrioqiw6jovf3f.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:01:35', '2023-11-27 16:01:35'),
(14, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101013/se-shop/products/uz3noovybiheldpxpat5.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:35', '2023-11-27 16:03:35'),
(15, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962444/se-shop/products/sn8qzltrpkcvtsso2r9n.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 189, 61, '2023-11-26 01:34:05', '2023-11-27 01:42:02'),
(16, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962446/se-shop/products/arufyeth0e8kpbpsurfp.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 145, 61, '2023-11-26 01:34:07', '2023-11-27 01:42:05'),
(17, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962452/se-shop/products/rxwslzofahaobk3vmwpo.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 89, 61, '2023-11-26 01:34:13', '2023-11-27 01:42:08'),
(18, 1, 'SamSung Galaxy Pro Max', 'SamSung Galaxy Pro Max', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962311/se-shop/products/w7byy4yfqmr8b3fapfyp.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 231, 61, '2023-11-26 01:31:52', '2023-11-27 01:41:38'),
(19, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962465/se-shop/products/sxbtrbncz5ledqhdwp5x.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 256, 61, '2023-11-26 01:34:26', '2023-11-27 01:42:13'),
(20, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962471/se-shop/products/wyadrpirga0b8x1j90o8.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 399, 61, '2023-11-26 01:34:32', '2023-11-27 01:42:16'),
(21, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962476/se-shop/products/h5xaxtznfslpetjszp58.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 299, 61, '2023-11-26 01:34:38', '2023-11-27 01:42:18'),
(22, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962482/se-shop/products/ujgxztfrsm5uzxqigw5j.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 123, 61, '2023-11-26 01:34:43', '2023-11-27 01:42:21'),
(23, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962487/se-shop/products/ipdu1psxfxi7kzgo4qv2.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 124, 61, '2023-11-26 01:34:48', '2023-11-27 01:42:23'),
(24, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962493/se-shop/products/rqkfxqjjvd9lqqutifqm.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 111, 61, '2023-11-26 01:34:54', '2023-11-27 01:42:26'),
(25, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962498/se-shop/products/hall7zjlr2fzuesukwuf.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 457, 61, '2023-11-26 01:34:59', '2023-11-27 01:42:29'),
(26, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962504/se-shop/products/ohitzzb5vt3astyo9o6j.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 127, 61, '2023-11-26 01:35:05', '2023-11-27 01:42:32'),
(27, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962511/se-shop/products/rafe6tcy6yvtatbwrji4.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 245, 61, '2023-11-26 01:35:12', '2023-11-27 01:42:35'),
(28, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962516/se-shop/products/ozblkblm2fctbkwovxy1.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 354, 61, '2023-11-26 01:35:17', '2023-11-27 01:42:38'),
(29, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962520/se-shop/products/sueckrioxkdewrdsfrwx.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 756, 61, '2023-11-26 01:35:21', '2023-11-27 01:42:41'),
(30, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962526/se-shop/products/ke50nzw9wtn6lk30iq1p.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 316, 61, '2023-11-26 01:35:27', '2023-11-27 01:42:45'),
(31, 1, 'SamSung Galaxy Z Fold', 'SamSung Galaxy Z Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1700962534/se-shop/products/qdwz7yse9x2gpmye6fto.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 541, 61, '2023-11-26 01:35:35', '2023-11-27 01:42:48'),
(32, 1, 'IPhone XS', 'IPhone XS', 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:80/plain/https://cellphones.com.vn/media/catalog/product/i/p/iphone-15-pro-max_3.png', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 200, 'Blue', 121, 61, '2023-11-26 12:37:11', '2023-11-27 07:23:00'),
(33, 1, 'SamSung X Fold', 'SamSung X Fold', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701078918/se-shop/products/yzktk1bjbiajn1wbwjal.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 121, 61, '2023-11-27 09:55:19', '2023-11-27 09:55:19'),
(34, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701078940/se-shop/products/nqrlttqiy2sfqj04hlki.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 121, 61, '2023-11-27 09:55:42', '2023-11-27 09:55:42'),
(35, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701078958/se-shop/products/yerb8jtc6vgf7zo2e7ms.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 121, 61, '2023-11-27 09:56:00', '2023-11-27 09:56:00'),
(36, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092559/se-shop/products/wnxcmpzlvf0lqvvnsj0a.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 230, 61, '2023-11-27 13:42:41', '2023-11-27 13:42:41'),
(37, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092569/se-shop/products/dgshb3rdlzchvrka62tv.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 121, 61, '2023-11-27 13:42:51', '2023-11-27 13:42:51'),
(38, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092574/se-shop/products/ixcrd2wkk2a55ru1z25j.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 786, 61, '2023-11-27 13:42:56', '2023-11-27 13:42:56'),
(39, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092583/se-shop/products/k3sigxx18urxookr4l3t.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 121, 61, '2023-11-27 13:43:05', '2023-11-27 13:43:05'),
(40, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092589/se-shop/products/lozir2qdfgez2ksizbsh.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 456, 61, '2023-11-27 13:43:11', '2023-11-27 13:43:11'),
(41, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092598/se-shop/products/irgql7rwb8yizeftv2a4.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 499, 61, '2023-11-27 13:43:20', '2023-11-27 13:43:20'),
(42, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092607/se-shop/products/htkgvmh2fxxnm4z4fagq.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 299, 61, '2023-11-27 13:43:29', '2023-11-27 13:43:29'),
(43, 1, 'IPhone XS', 'IPhone XS', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092619/se-shop/products/pw5rmpoydfsqa4t8y4nt.webp', 'FullHD | IPS', 'iOS', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 199, 61, '2023-11-27 13:43:41', '2023-11-27 13:43:41'),
(44, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092643/se-shop/products/sl1itjuxtoctrraawlx3.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 5000, 'Blue', 149, 61, '2023-11-27 13:44:05', '2023-11-27 13:44:05'),
(45, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092652/se-shop/products/kgv6s8l2qvqbbdexizd3.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 129, 61, '2023-11-27 13:44:14', '2023-11-27 13:44:14'),
(46, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092653/se-shop/products/r74zlrizxcwkyfskkiar.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 119, 61, '2023-11-27 13:44:15', '2023-11-27 13:44:15'),
(47, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092655/se-shop/products/kgegoq5b0moydxth1zib.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 109, 61, '2023-11-27 13:44:18', '2023-11-27 13:44:18'),
(48, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092658/se-shop/products/qnrixppuihrudqurnoag.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:20', '2023-11-27 13:44:20'),
(49, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092660/se-shop/products/qdvtga81waorvib0kmcv.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:22', '2023-11-27 13:44:22'),
(50, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092661/se-shop/products/rah23xvm45caxuvndwxn.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 129, 61, '2023-11-27 13:44:23', '2023-11-27 13:44:23'),
(51, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092663/se-shop/products/ouhrkdjj7ucwnm1w87ki.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:25', '2023-11-27 13:44:25'),
(52, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092665/se-shop/products/gop71r318ntnlzsktrln.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 179, 61, '2023-11-27 13:44:27', '2023-11-27 13:44:27'),
(53, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092667/se-shop/products/vvt64pfjbnyse22yayf7.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:29', '2023-11-27 13:44:29'),
(54, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092668/se-shop/products/sxn9mlhjnrvrjdj4p55e.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 189, 61, '2023-11-27 13:44:30', '2023-11-27 13:44:30'),
(55, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092670/se-shop/products/suiaooeidvdaxavcuuyw.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:32', '2023-11-27 13:44:32'),
(56, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092671/se-shop/products/lbnshy7brdzewdypoeni.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:33', '2023-11-27 13:44:33'),
(57, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092673/se-shop/products/phzduwtlf7cnqiuqe2oz.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 149, 61, '2023-11-27 13:44:35', '2023-11-27 13:44:35'),
(58, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092674/se-shop/products/ybjmxk6bmzcu8rbieztp.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:36', '2023-11-27 13:44:36'),
(59, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092676/se-shop/products/bsjcb4vedhos1icwucqt.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:37', '2023-11-27 13:44:37'),
(60, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092677/se-shop/products/lwgf5i9rgkmskunxruea.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 146, 61, '2023-11-27 13:44:39', '2023-11-27 13:44:39'),
(61, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092678/se-shop/products/udnxypy4p5ewqmgxaswo.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:41', '2023-11-27 13:44:41'),
(62, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092680/se-shop/products/s3lsm0ssfs8utyggo9pl.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 169, 61, '2023-11-27 13:44:42', '2023-11-27 13:44:42'),
(63, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092681/se-shop/products/li47fnf6b13fhf9xofad.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:43', '2023-11-27 13:44:43'),
(64, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092683/se-shop/products/nnw1pe804z53smcpzukw.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:45', '2023-11-27 13:44:45'),
(65, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092684/se-shop/products/rfkabzntiggvzmsfxyhy.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 269, 61, '2023-11-27 13:44:46', '2023-11-27 13:44:46'),
(66, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092686/se-shop/products/vqudm7m7ztrkncr9gqw5.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:48', '2023-11-27 13:44:48'),
(67, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092687/se-shop/products/kxggezwj0x6pnwxuyzka.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 233, 61, '2023-11-27 13:44:49', '2023-11-27 13:44:49'),
(68, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092689/se-shop/products/a1bdl52lcclruy6xpake.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:51', '2023-11-27 13:44:51'),
(69, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092690/se-shop/products/jxtyjcbhcb8yx34dun0r.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 249, 61, '2023-11-27 13:44:53', '2023-11-27 13:44:53'),
(70, 1, 'SamSung Galaxy S23', 'SamSung Galaxy S23', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092692/se-shop/products/rlitdgsld2cecyuwn643.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 9999, 'Blue', 121, 61, '2023-11-27 13:44:55', '2023-11-27 13:44:55'),
(71, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092714/se-shop/products/rysh4xyfhznwjtj4hjid.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 369, 61, '2023-11-27 13:45:16', '2023-11-27 13:45:16'),
(72, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092716/se-shop/products/ulharlchzvwnhcjwobvk.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:18', '2023-11-27 13:45:18'),
(73, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092717/se-shop/products/dyvqwihpmyq02tupuehl.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 369, 61, '2023-11-27 13:45:20', '2023-11-27 13:45:20'),
(74, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092719/se-shop/products/x87cfw09dtqsxpllxjia.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:21', '2023-11-27 13:45:21'),
(75, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092721/se-shop/products/oomi4qj0movsvwt2q69o.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:23', '2023-11-27 13:45:23'),
(76, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092723/se-shop/products/evitehmocxqdqllo3xlh.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 689, 61, '2023-11-27 13:45:24', '2023-11-27 13:45:24'),
(77, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092725/se-shop/products/ifzgmrye5rlikknbnshp.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:27', '2023-11-27 13:45:27'),
(78, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092726/se-shop/products/bz72gorskkxoqid0woou.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:28', '2023-11-27 13:45:28'),
(79, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092731/se-shop/products/vak9cwq1nqzpd4e5l1jo.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 632, 61, '2023-11-27 13:45:32', '2023-11-27 13:45:32'),
(80, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092735/se-shop/products/hyvlpixqc9rzzrshhxkx.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:37', '2023-11-27 13:45:37'),
(81, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092739/se-shop/products/rz0mwbyue9uyskf7axth.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 699, 61, '2023-11-27 13:45:41', '2023-11-27 13:45:41'),
(82, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092743/se-shop/products/cyvvc3axkldfkglrxnit.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:45', '2023-11-27 13:45:45'),
(83, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092746/se-shop/products/zw7ufgalj49hr5ibrmun.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:48', '2023-11-27 13:45:48'),
(84, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092749/se-shop/products/cn2vatjumaqmhkqgcfvp.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:45:51', '2023-11-27 13:45:51'),
(85, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092754/se-shop/products/laop9asxmlnqysj2idyu.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 789, 61, '2023-11-27 13:45:57', '2023-11-27 13:45:57'),
(86, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092758/se-shop/products/osbw3ksdzhapkqjgscgl.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:46:00', '2023-11-27 13:46:00'),
(87, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092760/se-shop/products/obkienvwxw24quoxc7an.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:46:02', '2023-11-27 13:46:02'),
(88, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092763/se-shop/products/yfuszuzffbwloa6ikmmw.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 799, 61, '2023-11-27 13:46:05', '2023-11-27 13:46:05'),
(89, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092768/se-shop/products/vfj2ose7fvzl43javzf6.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:46:10', '2023-11-27 13:46:10'),
(90, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092769/se-shop/products/zfg6upsewwlaivgiific.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 777, 61, '2023-11-27 13:46:11', '2023-11-27 13:46:11'),
(91, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092771/se-shop/products/otvwtflk5cpraatxkgub.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:46:13', '2023-11-27 13:46:13'),
(92, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092773/se-shop/products/ptagyvbl1bmfoigfsbla.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 785, 61, '2023-11-27 13:46:15', '2023-11-27 13:46:15'),
(93, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092775/se-shop/products/rke83g1tf93hzgoikjnx.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:46:19', '2023-11-27 13:46:19'),
(94, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092779/se-shop/products/m4lsqerseyym5yydffqe.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 753, 61, '2023-11-27 13:46:21', '2023-11-27 13:46:21'),
(95, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092781/se-shop/products/mz6exbnpctawh991pjjn.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 712, 61, '2023-11-27 13:46:23', '2023-11-27 13:46:23'),
(96, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092783/se-shop/products/lzjnvcosrowoqcj1suxt.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 785, 61, '2023-11-27 13:46:25', '2023-11-27 13:46:25'),
(97, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092785/se-shop/products/rfjlfhosuxu80ogsghoo.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 121, 61, '2023-11-27 13:46:28', '2023-11-27 13:46:28'),
(98, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092788/se-shop/products/q4aoiqrexyd271havfdz.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 456, 61, '2023-11-27 13:46:31', '2023-11-27 13:46:31'),
(99, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092791/se-shop/products/lic9snyxua2xb00rbztg.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 129, 61, '2023-11-27 13:46:33', '2023-11-27 13:46:33'),
(100, 1, 'SamSung Galaxy S24', 'SamSung Galaxy S24', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701092793/se-shop/products/h2jm3cgipfwung62hbsc.webp', 'FullHD | IPS', 'Android', 'Snap gragon', 4, 64, 1.20, 999, 'Blue', 475, 61, '2023-11-27 13:46:35', '2023-11-27 13:46:35'),
(101, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093762/se-shop/products/te3x4h8k7xwfci269vsa.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 111, 70, '2023-11-27 14:02:44', '2023-11-27 14:02:44'),
(102, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093770/se-shop/products/o8txhqq5eu9zlwtcihhv.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 102, 66, '2023-11-27 14:02:52', '2023-11-29 17:23:36'),
(103, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093773/se-shop/products/yduzlcjeml5j9gqevpme.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 99, 55, '2023-11-27 14:02:55', '2023-12-02 13:48:01'),
(104, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093776/se-shop/products/fano2i6dvhdhjkzfcyzb.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 599, 70, '2023-11-27 14:02:58', '2023-11-27 14:02:58'),
(105, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093791/se-shop/products/qnozvpwylo4dylrvy65h.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 799, 70, '2023-11-27 14:03:13', '2023-11-27 14:03:13'),
(106, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093797/se-shop/products/iajqjkwxphlfqfuun1xb.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:03:19', '2023-11-27 14:03:19'),
(107, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093803/se-shop/products/bje7mgpzwnre1mkenice.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 855, 66, '2023-11-27 14:03:25', '2023-12-02 13:48:43'),
(108, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093807/se-shop/products/a5gz7mstvt84ahr8uw2i.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 559, 70, '2023-11-27 14:03:30', '2023-11-27 14:03:30'),
(109, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093812/se-shop/products/i4t570e8jnmxvxay4bka.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 556, 70, '2023-11-27 14:03:34', '2023-11-27 14:03:34'),
(110, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093818/se-shop/products/o34xy9hyrmvekpqejhlc.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 557, 70, '2023-11-27 14:03:40', '2023-11-27 14:03:40'),
(111, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093823/se-shop/products/aeejsyf2b8ib4aiuu5if.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 558, 68, '2023-11-27 14:03:45', '2023-11-29 16:00:10'),
(112, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093829/se-shop/products/ewegzih02wehpjfg433v.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:03:51', '2023-11-27 14:03:51'),
(113, 2, 'MacBook Pro 16 inch M2 Max 2023', 'MacBook Pro 16 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093854/se-shop/products/jpbrszs17nqnlwaqtovr.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 999, 70, '2023-11-27 14:04:16', '2023-11-27 14:04:16'),
(114, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093864/se-shop/products/kcicfjyuk8fn9ncjjfrt.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:04:27', '2023-11-27 14:04:27'),
(115, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093871/se-shop/products/agvdjyu457hd8d3flopr.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 199, 70, '2023-11-27 14:04:34', '2023-11-27 14:04:34'),
(116, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093882/se-shop/products/ppkgnn78pdak9j9p7b7h.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:04:44', '2023-11-27 14:04:44'),
(117, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093884/se-shop/products/k8qbcgvh2xkdfgawqeop.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:04:46', '2023-11-27 14:04:46'),
(118, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093887/se-shop/products/hkaavjmmtu62i2pvhvnx.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 726, 70, '2023-11-27 14:04:49', '2023-11-27 14:04:49'),
(119, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093889/se-shop/products/lcx3wpjj7joaahhaakb0.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:04:51', '2023-11-27 14:04:51'),
(120, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093893/se-shop/products/mxjestenqaltwv9mr23g.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:04:55', '2023-11-27 14:04:55'),
(121, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093902/se-shop/products/iih59e0u865iehttcbhe.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 756, 70, '2023-11-27 14:05:05', '2023-11-27 14:05:05'),
(122, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093908/se-shop/products/g71w6a44s8xgvo8bjao7.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:11', '2023-11-27 14:05:11'),
(123, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093914/se-shop/products/y9oj1oqpvxejfjcmwizy.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 123, 70, '2023-11-27 14:05:16', '2023-11-27 14:05:16'),
(124, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093920/se-shop/products/gachtcv5umfuhykyk5yr.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 399, 70, '2023-11-27 14:05:22', '2023-11-27 14:05:22'),
(125, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093925/se-shop/products/jx0hotpkp7tieitergag.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:27', '2023-11-27 14:05:27'),
(126, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093928/se-shop/products/lw9bbp8glzqfypqslg7j.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 399, 70, '2023-11-27 14:05:30', '2023-11-27 14:05:30'),
(127, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093930/se-shop/products/lyuxtgqkgwttlhv6afqr.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:32', '2023-11-27 14:05:32'),
(128, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093932/se-shop/products/ptdlvj2oxijclhjszepj.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 756, 70, '2023-11-27 14:05:34', '2023-11-27 14:05:34'),
(129, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093934/se-shop/products/pirb8bip2k5idg9r6p7o.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:36', '2023-11-27 14:05:36'),
(130, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093936/se-shop/products/rrygknfma2mqu2c9s4qd.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:38', '2023-11-27 14:05:38'),
(131, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093938/se-shop/products/sburzgblbrveuboecfkh.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 752, 70, '2023-11-27 14:05:40', '2023-11-27 14:05:40'),
(132, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093939/se-shop/products/gosiykdciha4hkdbkuxg.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:42', '2023-11-27 14:05:42'),
(133, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093942/se-shop/products/akvwuwbe6hlzebr7qgwv.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 557, 70, '2023-11-27 14:05:44', '2023-11-27 14:05:44'),
(134, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093944/se-shop/products/wgnhtorehlmfqtubd6qp.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:46', '2023-11-27 14:05:46'),
(135, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093946/se-shop/products/mih7robwlie43v7menvi.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 758, 70, '2023-11-27 14:05:48', '2023-11-27 14:05:48'),
(136, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093948/se-shop/products/lxlfiqv7hyddcvioqt0t.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:50', '2023-11-27 14:05:50'),
(137, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093950/se-shop/products/bozhbkqlxil8ojs2okfu.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 568, 70, '2023-11-27 14:05:52', '2023-11-27 14:05:52'),
(138, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093952/se-shop/products/eoanc1ywg8iyzodvfoav.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 595, 70, '2023-11-27 14:05:55', '2023-11-27 14:05:55'),
(139, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093955/se-shop/products/vdyh596ziupcgo66t0px.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:05:57', '2023-11-27 14:05:57'),
(140, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093957/se-shop/products/gjsfhtj6vyfv5i5hoqn8.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 254, 70, '2023-11-27 14:05:59', '2023-11-27 14:05:59'),
(141, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093959/se-shop/products/duotdcacle82s45pvsuc.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:02', '2023-11-27 14:06:02'),
(142, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093961/se-shop/products/ge906uhy5pd7ozmri5ea.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 136, 70, '2023-11-27 14:06:04', '2023-11-27 14:06:04'),
(143, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093964/se-shop/products/qe4d3iia9cjhj9pdilyk.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:06', '2023-11-27 14:06:06'),
(144, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093966/se-shop/products/lscvp92lyrba673os8qp.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 764, 70, '2023-11-27 14:06:08', '2023-11-27 14:06:08'),
(145, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093969/se-shop/products/mzdfjqr4dkqu455gwj6l.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 124, 70, '2023-11-27 14:06:11', '2023-11-27 14:06:11'),
(146, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093970/se-shop/products/ccrcfbet3pe4h814xyco.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 129, 70, '2023-11-27 14:06:13', '2023-11-27 14:06:13'),
(147, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093973/se-shop/products/dlkllvlvc8le9uevbpat.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:15', '2023-11-27 14:06:15'),
(148, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093973/se-shop/products/jxkac7eaxrwr72vgjf3o.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 456, 70, '2023-11-27 14:06:15', '2023-11-27 14:06:15'),
(149, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093974/se-shop/products/kjxd3ogz0pchrtfrvxgf.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:16', '2023-11-27 14:06:16'),
(150, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093976/se-shop/products/h8xstlmwnckdhuvgktsh.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 237, 70, '2023-11-27 14:06:18', '2023-11-27 14:06:18'),
(151, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093986/se-shop/products/ydfxbloi8nx4ixcehpnj.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:28', '2023-11-27 14:06:28'),
(152, 2, 'MacBook Pro 14 inch M2 Max 2023', 'MacBook Pro 14 inch M2 Max 2023 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093987/se-shop/products/n79zbdwsysevjhlfouer.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:29', '2023-11-27 14:06:29'),
(153, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093993/se-shop/products/twtntssme000i9iggacv.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 555, 70, '2023-11-27 14:06:35', '2023-11-27 14:06:35'),
(154, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093994/se-shop/products/tbqd8ufd07jqxd2hc4nv.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 444, 70, '2023-11-27 14:06:36', '2023-11-27 14:06:36'),
(155, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093996/se-shop/products/ynqyysoeybix3qnu8xha.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:38', '2023-11-27 14:06:38'),
(156, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093998/se-shop/products/kkd1ip2mldxblwonkehs.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:40', '2023-11-27 14:06:40'),
(157, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701093999/se-shop/products/kgimj1fv3aswpuhvs8w1.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 777, 70, '2023-11-27 14:06:41', '2023-11-27 14:06:41'),
(158, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094001/se-shop/products/rijkhlrhq1kvurpbt90t.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 555, 70, '2023-11-27 14:06:43', '2023-11-27 14:06:43'),
(159, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094002/se-shop/products/atzcqsx1tmxgzk24o6mt.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:44', '2023-11-27 14:06:44'),
(160, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094004/se-shop/products/dognviitk7793mulyefg.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 666, 70, '2023-11-27 14:06:46', '2023-11-27 14:06:46'),
(161, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094005/se-shop/products/b2ad56rw5iiwxgvfwe4s.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:47', '2023-11-27 14:06:47'),
(162, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094007/se-shop/products/vb1zsq0gcidp7ny4rdp8.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:49', '2023-11-27 14:06:49'),
(163, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094008/se-shop/products/ft8bfvku0fn5rims8nng.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 554, 70, '2023-11-27 14:06:50', '2023-11-27 14:06:50'),
(164, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094009/se-shop/products/s82zti7040pc8lj9vfm8.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 444, 70, '2023-11-27 14:06:51', '2023-11-27 14:06:51');
INSERT INTO `product` (`id`, `categoryId`, `name`, `description`, `imageUrl`, `screen`, `operatingSystem`, `processor`, `ram`, `storageCapacity`, `weight`, `batteryCapacity`, `color`, `price`, `stockQuantity`, `createdAt`, `updatedAt`) VALUES
(165, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094011/se-shop/products/ppoteso5ol3jqsnnlaja.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 111, 70, '2023-11-27 14:06:54', '2023-11-27 14:06:54'),
(166, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094014/se-shop/products/fdojmdu7e9mohb5dp381.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 222, 70, '2023-11-27 14:06:56', '2023-11-27 14:06:56'),
(167, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094014/se-shop/products/vvr5xurd3z6j5cojsbdz.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 333, 70, '2023-11-27 14:06:56', '2023-11-27 14:06:56'),
(168, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094016/se-shop/products/ssdlwozfwqymdwnyiiww.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:06:57', '2023-11-27 14:06:57'),
(169, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094017/se-shop/products/abt2iqarran1oygkqlpn.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 444, 70, '2023-11-27 14:07:00', '2023-11-27 14:07:00'),
(170, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094019/se-shop/products/bmfthfu3jspmvrn3a14h.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 555, 70, '2023-11-27 14:07:01', '2023-11-27 14:07:01'),
(171, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094020/se-shop/products/dghklcv6p5ufsyj8xvaw.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 666, 70, '2023-11-27 14:07:02', '2023-11-27 14:07:02'),
(172, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094022/se-shop/products/fclhjfji0m7x4t4vprfg.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 777, 70, '2023-11-27 14:07:04', '2023-11-27 14:07:04'),
(173, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094028/se-shop/products/qr1mfgrlorwfsllk2nix.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 888, 70, '2023-11-27 14:07:10', '2023-11-27 14:07:10'),
(174, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094037/se-shop/products/ylwvnlojq2krmfuciymc.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:07:19', '2023-11-27 14:07:19'),
(175, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094040/se-shop/products/ntzphgtyo1bsrm0mjbpy.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 886, 70, '2023-11-27 14:07:22', '2023-11-27 14:07:22'),
(176, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094044/se-shop/products/geaaaf4mt0d3nv3wf8lr.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:07:26', '2023-11-27 14:07:26'),
(177, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094048/se-shop/products/utibewfgkdk7xh1g40h2.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 668, 70, '2023-11-27 14:07:30', '2023-11-27 14:07:30'),
(178, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094052/se-shop/products/erypeu2o5j9gykdsm29l.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:07:34', '2023-11-27 14:07:34'),
(179, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094055/se-shop/products/ahdaslsjpafd8oc4kyqx.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:07:37', '2023-11-27 14:07:37'),
(180, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094060/se-shop/products/xbmxpk92ptmggq4iezzj.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:07:42', '2023-11-27 14:07:42'),
(181, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094063/se-shop/products/qy5zsxbeovekoyaivze3.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 456, 70, '2023-11-27 14:07:46', '2023-11-27 14:07:46'),
(182, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094067/se-shop/products/d6q5babuztahste2ep5j.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 723, 70, '2023-11-27 14:07:49', '2023-11-27 14:07:49'),
(183, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094077/se-shop/products/gtgykgmmsku838xydynj.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 649, 70, '2023-11-27 14:07:59', '2023-11-27 14:07:59'),
(184, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094078/se-shop/products/wkbczhsvosp7kvanosok.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:08:02', '2023-11-27 14:08:02'),
(185, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094081/se-shop/products/gryro4zjlr3s0hrkzho2.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 452, 70, '2023-11-27 14:08:05', '2023-11-27 14:08:05'),
(186, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094084/se-shop/products/kp9hms15l3knuk2vs9jf.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:08:06', '2023-11-27 14:08:06'),
(187, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094086/se-shop/products/hyicf2nrsb1bcuxjyqzh.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1333, 70, '2023-11-27 14:08:08', '2023-11-27 14:08:08'),
(188, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094088/se-shop/products/h64wojncy39c8lgxoh82.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 2864, 70, '2023-11-27 14:08:10', '2023-11-27 14:08:10'),
(189, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094090/se-shop/products/oyetbqo19qdlciat3ggt.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 3275, 70, '2023-11-27 14:08:12', '2023-11-27 14:08:12'),
(190, 2, 'MacBook Pro 14 inch M2 Max 2021', 'MacBook Pro 14 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094092/se-shop/products/g8svhcbtoabbr4y1u960.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1721, 70, '2023-11-27 14:08:14', '2023-11-27 14:08:14'),
(191, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094108/se-shop/products/nbctaisiohywz4dazz4c.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 3578, 70, '2023-11-27 14:08:30', '2023-11-27 14:08:30'),
(192, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094117/se-shop/products/yrp8i3jiqthzk8fhczvr.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1281, 70, '2023-11-27 14:08:39', '2023-11-27 14:08:39'),
(193, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094119/se-shop/products/dzc8ckch114vnriaazyp.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1291, 70, '2023-11-27 14:08:41', '2023-11-27 14:08:41'),
(194, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094121/se-shop/products/e62tbz2vqg0dcptrfvx7.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1421, 70, '2023-11-27 14:08:43', '2023-11-27 14:08:43'),
(195, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094123/se-shop/products/ll7gl1q2zwohr7nevx3x.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1241, 70, '2023-11-27 14:08:45', '2023-11-27 14:08:45'),
(196, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094125/se-shop/products/xzdqubslhccob9oheno6.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1261, 70, '2023-11-27 14:08:47', '2023-11-27 14:08:47'),
(197, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094127/se-shop/products/uuskmdxfoo522ywkvghz.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:08:49', '2023-11-27 14:08:49'),
(198, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094130/se-shop/products/h0l0ztdjqvxkykeoyh6l.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 1721, 70, '2023-11-27 14:08:52', '2023-11-27 14:08:52'),
(199, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094132/se-shop/products/ufolahxefgjs378ojtab.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 6121, 70, '2023-11-27 14:08:54', '2023-11-27 14:08:54'),
(200, 2, 'MacBook Pro 15 inch M2 Max 2021', 'MacBook Pro 15 inch M2 Max 2021 (12 CPU - 38 GPU - 32GB - 1TB)', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094134/se-shop/products/am8rfdhnsryuaqy4r15s.webp', '4K | IPS', 'MacOS', 'Chip M2', 32, 1000, 1.20, 999, 'Blue', 5121, 70, '2023-11-27 14:08:56', '2023-11-27 14:08:56'),
(201, 3, 'Logitech Lift Vertical', 'Logitech Lift Vertical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094927/se-shop/products/ez7h6hghdr4j3rjfyybv.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 59, 53, '2023-11-27 14:22:09', '2023-12-02 13:48:01'),
(202, 3, 'Router Internet For Home', 'Router Internet For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701094979/se-shop/products/ad83trrdybkg0styvil9.webp', '4K | IPS', 'Cisco', 'Cisco', 1, 1, 1.20, 999, 'Blue', 6000, 70, '2023-11-27 14:23:01', '2023-11-27 14:23:01'),
(203, 3, 'Router Internet For Home', 'Router Internet For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095113/se-shop/products/hvwilujt71gnoa8cqakr.webp', '4K | IPS', 'Cisco', 'Cisco', 1, 1, 1.20, 999, 'Blue', 3000, 65, '2023-11-27 14:25:15', '2023-12-02 02:40:20'),
(204, 3, 'Samsung backup charger', 'Samsung backup charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095185/se-shop/products/ofqychrpwv8ibqwf0e5y.webp', '4K | IPS', 'Samsung', 'Samsung', 1, 1, 1.20, 999, 'Blue', 2999, 70, '2023-11-27 14:26:27', '2023-11-27 14:26:27'),
(205, 3, 'Logitech mouse v2 white', 'Logitech mouse v2 white', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095257/se-shop/products/pfbdaczdw9bofbm0niv6.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 1999, 69, '2023-11-27 14:27:39', '2023-11-29 16:01:28'),
(206, 3, 'Logitech mouse v2 white', 'Logitech mouse v2 white', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095270/se-shop/products/b4ilgmdwatag9myvmw4b.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 1999, 70, '2023-11-27 14:27:52', '2023-11-27 14:27:52'),
(207, 3, 'Apple airpods', 'Apple airpods', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095315/se-shop/products/yz64hlnrlbcosipp7ik9.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:28:37', '2023-11-27 14:28:37'),
(208, 3, 'Apple airpods', 'Apple airpods', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095320/se-shop/products/cppncedm0vhuhx7bnd6m.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 999, 'Blue', 1999, 70, '2023-11-27 14:28:42', '2023-11-27 14:28:42'),
(209, 3, 'Apple airpods', 'Apple airpods', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095323/se-shop/products/bhc0zmidv519yzaqkjqe.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 999, 'Blue', 1999, 70, '2023-11-27 14:28:45', '2023-11-27 14:28:45'),
(210, 3, 'Apple airpods', 'Apple airpods', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095327/se-shop/products/ehscjehcl5mva19zuds0.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 999, 'Blue', 1988, 70, '2023-11-27 14:28:49', '2023-11-27 14:28:49'),
(211, 3, 'Apple airpods', 'Apple airpods', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095329/se-shop/products/mvzf8gjn7bk6d6yyxaso.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 999, 'Blue', 1998, 70, '2023-11-27 14:28:51', '2023-11-27 14:28:51'),
(212, 3, 'Apple magic keyboard v1', 'Apple magic keyboard v1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095358/se-shop/products/okviumq9j7qypbsqe0cr.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 9999, 'Blue', 1299, 69, '2023-11-27 14:29:20', '2023-12-01 07:23:29'),
(213, 3, 'Apple magic keyboard v1', 'Apple magic keyboard v1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095362/se-shop/products/js7cntdcjhzgkcoipxmn.webp', '4K | IPS', 'Apple', 'Apple', 1, 1, 1.20, 1000, 'Blue', 1199, 70, '2023-11-27 14:29:24', '2023-11-27 14:29:24'),
(214, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095414/se-shop/products/izyt4rkk12zxtngkuspd.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:30:16', '2023-11-27 14:30:16'),
(215, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095416/se-shop/products/fnavrw7wbffma1jmow7m.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 1000, 'Blue', 121, 70, '2023-11-27 14:30:18', '2023-11-27 14:30:18'),
(216, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095418/se-shop/products/k8cdzqznuqn77xavvnpw.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 1000, 'Blue', 121, 70, '2023-11-27 14:30:20', '2023-11-27 14:30:20'),
(217, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095420/se-shop/products/kapatdhmk86ndq5izomt.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 1000, 'Blue', 754, 70, '2023-11-27 14:30:22', '2023-11-27 14:30:22'),
(218, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095423/se-shop/products/afm1lm9kesymkdnriqwu.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 1000, 'Blue', 121, 70, '2023-11-27 14:30:25', '2023-11-27 14:30:25'),
(219, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095424/se-shop/products/cjfphtt6oi74126z2vsm.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 1000, 'Blue', 233, 70, '2023-11-27 14:30:26', '2023-11-27 14:30:26'),
(220, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095434/se-shop/products/welua1xosfrioqykfd6x.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 1000, 'Blue', 121, 70, '2023-11-27 14:30:37', '2023-11-27 14:30:37'),
(221, 3, 'Logitech bussiness MX mechanical', 'Logitech bussiness MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095443/se-shop/products/shgdxyt6oeaw3luqm9bc.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:30:45', '2023-11-27 14:30:45'),
(222, 3, 'Logitech MX mechanical', 'Logitech MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095457/se-shop/products/j3nw8xsicv8nf0h7banl.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:30:59', '2023-11-27 14:30:59'),
(223, 3, 'Logitech MX mechanical', 'Logitech MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095460/se-shop/products/thil81gxrv9ng0h8g2ev.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 9991, 'Blue', 121, 70, '2023-11-27 14:31:03', '2023-11-27 14:31:03'),
(224, 3, 'Logitech MX mechanical', 'Logitech MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095462/se-shop/products/qdd2wwgc64oqzc7kbgej.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 9991, 'Blue', 121, 70, '2023-11-27 14:31:05', '2023-11-27 14:31:05'),
(225, 3, 'Logitech MX mechanical', 'Logitech MX mechanical', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095465/se-shop/products/ml5cpvontqqxsnjuasba.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 9991, 'Blue', 121, 70, '2023-11-27 14:31:07', '2023-11-27 14:31:07'),
(226, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095486/se-shop/products/ogijidbtpnlwuk8suzez.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 9991, 'Blue', 121, 70, '2023-11-27 14:31:28', '2023-11-27 14:31:28'),
(227, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095490/se-shop/products/bd9lvp4abcrq73ikxxvn.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:32', '2023-11-27 14:31:32'),
(228, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095492/se-shop/products/jwjebgm0ze8qe37c4vs0.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:34', '2023-11-27 14:31:34'),
(229, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095493/se-shop/products/jlmwyfcqu41mtgqma93y.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:36', '2023-11-27 14:31:36'),
(230, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095495/se-shop/products/ijro89ajd3yafg0ofl9l.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:37', '2023-11-27 14:31:37'),
(231, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095497/se-shop/products/bhehu80gtwlilezc1jqs.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:39', '2023-11-27 14:31:39'),
(232, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095498/se-shop/products/gfkyso8dftyrfj6s3wpl.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:40', '2023-11-27 14:31:40'),
(233, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095500/se-shop/products/ltrspsurt33lmp5qsfnj.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:42', '2023-11-27 14:31:42'),
(234, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095501/se-shop/products/u1tgyuxdcqiscncqetvk.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:43', '2023-11-27 14:31:43'),
(235, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095503/se-shop/products/xrd5ac0rrpd6hpjzjyyy.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:45', '2023-11-27 14:31:45'),
(236, 3, 'Logitech MX charger', 'Logitech MX charger', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095504/se-shop/products/l64m4el824qrwcotny6s.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:31:46', '2023-11-27 14:31:46'),
(237, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095536/se-shop/products/hnhx4ued5cvmyxylbjsp.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:18', '2023-11-27 14:32:18'),
(238, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095537/se-shop/products/tueowhwecle6ggk8ce0u.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:20', '2023-11-27 14:32:20'),
(239, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095539/se-shop/products/sntihev29kbqhvyrhlrh.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:21', '2023-11-27 14:32:21'),
(240, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095541/se-shop/products/yaeztpnkhkohczn600yn.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:24', '2023-11-27 14:32:24'),
(241, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095543/se-shop/products/kasgspeccz9flw6j4ciu.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:25', '2023-11-27 14:32:25'),
(242, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095545/se-shop/products/iy99gogtxlaq3j42ekag.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:28', '2023-11-27 14:32:28'),
(243, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095547/se-shop/products/hsjizq6puv2berd6ngho.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:29', '2023-11-27 14:32:29'),
(244, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095549/se-shop/products/cwyhmoilfc2mdz5shrfh.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:31', '2023-11-27 14:32:31'),
(245, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095550/se-shop/products/qpntl8movk18zbigbvbu.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:32', '2023-11-27 14:32:32'),
(246, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095552/se-shop/products/sdr141lqvhlt8cx6kyr5.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:34', '2023-11-27 14:32:34'),
(247, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095554/se-shop/products/fpdawaasdtgpru7e3nek.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:36', '2023-11-27 14:32:36'),
(248, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095556/se-shop/products/hf6pxuv0fymu0wx82fhr.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:38', '2023-11-27 14:32:38'),
(249, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095557/se-shop/products/kpeyk9yxzsp94abgxt7o.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:39', '2023-11-27 14:32:39'),
(250, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095559/se-shop/products/a44sr7zeky5vzi4ko1pf.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:41', '2023-11-27 14:32:41'),
(251, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095560/se-shop/products/mgmvnh3stvttqfb7c0yx.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:42', '2023-11-27 14:32:42'),
(252, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095562/se-shop/products/c5pjkelduexlsxum6r7p.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:44', '2023-11-27 14:32:44'),
(253, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095563/se-shop/products/e8171xpiwuaw6vgwluga.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:46', '2023-11-27 14:32:46'),
(254, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095565/se-shop/products/o88cdeifu2myht8ex333.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:47', '2023-11-27 14:32:47'),
(255, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095566/se-shop/products/ttwbhvrpimowlbsnqlr8.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:49', '2023-11-27 14:32:49'),
(256, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095569/se-shop/products/wv9yvffzghblqihfl8ci.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:51', '2023-11-27 14:32:51'),
(257, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095571/se-shop/products/znwsgj4yakvggfq8tr65.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:53', '2023-11-27 14:32:53'),
(258, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095573/se-shop/products/kvmdxkilyijzygl7gewc.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:55', '2023-11-27 14:32:55'),
(259, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095574/se-shop/products/b3m2d0ykujsirl5qjgb0.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:56', '2023-11-27 14:32:56'),
(260, 3, 'Apple Magic Key', 'Apple Magic Key', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095576/se-shop/products/bcagexnngp6dlmm7clnt.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:32:58', '2023-11-27 14:32:58'),
(261, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095610/se-shop/products/irw0xu04oynwg2vvebuu.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 100, 'Blue', 121, 70, '2023-11-27 14:33:32', '2023-11-27 14:33:32'),
(262, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095611/se-shop/products/l5nfb4ywqgsjn7stkvp9.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:33', '2023-11-27 14:33:33'),
(263, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095613/se-shop/products/o3atrip6qu1frkp9cmau.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:35', '2023-11-27 14:33:35'),
(264, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095614/se-shop/products/kpngeaijjlfbdjyko1je.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:37', '2023-11-27 14:33:37'),
(265, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095616/se-shop/products/rphrzk9dlctwzcflxb92.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:38', '2023-11-27 14:33:38'),
(266, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095617/se-shop/products/lscme2w28zwmeop6w3uc.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:39', '2023-11-27 14:33:39'),
(267, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095619/se-shop/products/q6dvhbzztzike5ui2vw4.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:41', '2023-11-27 14:33:41'),
(268, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095621/se-shop/products/qaohmn06l1ixswb0b0qd.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:44', '2023-11-27 14:33:44'),
(269, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095624/se-shop/products/n9jo2dbwunaqjwfwwk1d.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:46', '2023-11-27 14:33:46'),
(270, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095626/se-shop/products/fig4cyst8uuyijyfttxo.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:48', '2023-11-27 14:33:48'),
(271, 3, 'USB Receiver Logitech', 'USB Receiver Logitech', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095627/se-shop/products/nrduykriylhl9ty5cpez.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:33:49', '2023-11-27 14:33:49'),
(272, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095666/se-shop/products/shy3hktvyycmtuun1vf0.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 99, 'Blue', 121, 70, '2023-11-27 14:34:28', '2023-11-27 14:34:28'),
(273, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095668/se-shop/products/n7vyru5ltjxmnrcok44v.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:30', '2023-11-27 14:34:30'),
(274, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095670/se-shop/products/kbfdhka6vaz7tgrwr5iu.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:32', '2023-11-27 14:34:32'),
(275, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095671/se-shop/products/er1saovvyjqzdqzu2wwo.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:33', '2023-11-27 14:34:33'),
(276, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095673/se-shop/products/vfysoikahhbnl4zzogqi.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:35', '2023-11-27 14:34:35'),
(277, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095674/se-shop/products/olssaw4jcwbw05u6soaf.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:36', '2023-11-27 14:34:36'),
(278, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095675/se-shop/products/rlznuimd2x9ji0zwnzpn.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:38', '2023-11-27 14:34:38'),
(279, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095677/se-shop/products/fqhw0akwidsoaeribjre.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:39', '2023-11-27 14:34:39'),
(280, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095679/se-shop/products/urdpt2jxt2e5f3b51dd0.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:41', '2023-11-27 14:34:41'),
(281, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095680/se-shop/products/bu7xfonx2xipkxngf524.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:42', '2023-11-27 14:34:42'),
(282, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095682/se-shop/products/s7tzdz7mtkufhwjer27f.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:45', '2023-11-27 14:34:45'),
(283, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095684/se-shop/products/tmrnzedav1lbhyzc4bif.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:46', '2023-11-27 14:34:46'),
(284, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095686/se-shop/products/yg5ljcwcayyfenylazgg.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:48', '2023-11-27 14:34:48'),
(285, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095687/se-shop/products/sai5kpsqolibewnszlzs.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:49', '2023-11-27 14:34:49'),
(286, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095689/se-shop/products/wnffazbu03v8sv1kclun.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:51', '2023-11-27 14:34:51'),
(287, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095690/se-shop/products/q4meu49awgqrhxgpemgg.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:52', '2023-11-27 14:34:52'),
(288, 3, 'Logitech MX Bussiness Mouse', 'Logitech MX Bussiness Mouse', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095692/se-shop/products/nbdw5e4klwa5yabrphh4.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 999, 'Blue', 121, 70, '2023-11-27 14:34:54', '2023-11-27 14:34:54'),
(289, 3, 'Asus Moderm Network For Home', 'Asus Moderm Network For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095751/se-shop/products/wehzbumyqjqdj2rnvpjm.webp', '4K | IPS', 'Asus', 'Asus', 1, 1, 1.20, 999, 'Blue', 1211, 70, '2023-11-27 14:35:53', '2023-11-27 14:35:53'),
(290, 3, 'Asus Moderm Network For Home', 'Asus Moderm Network For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095755/se-shop/products/t6wxhf2e0fsdtrbjgojs.webp', '4K | IPS', 'Asus', 'Asus', 1, 1, 1.20, 777, 'Blue', 1921, 70, '2023-11-27 14:35:57', '2023-11-27 14:35:57'),
(291, 3, 'Asus Moderm Network For Home', 'Asus Moderm Network For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095756/se-shop/products/xih6ckobezw38ebc7ms6.webp', '4K | IPS', 'Asus', 'Asus', 1, 1, 1.20, 777, 'Blue', 1821, 70, '2023-11-27 14:35:58', '2023-11-27 14:35:58'),
(292, 3, 'Asus Moderm Network For Home', 'Asus Moderm Network For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095758/se-shop/products/pyi4w03abbeu9sefh2pq.webp', '4K | IPS', 'Asus', 'Asus', 1, 1, 1.20, 777, 'Blue', 1521, 70, '2023-11-27 14:36:00', '2023-11-27 14:36:00'),
(293, 3, 'Asus Moderm Network For Home', 'Asus Moderm Network For Home', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095759/se-shop/products/aekomvwl56hz7efwcpzl.webp', '4K | IPS', 'Asus', 'Asus', 1, 1, 1.20, 777, 'Blue', 1721, 70, '2023-11-27 14:36:01', '2023-11-27 14:36:01'),
(294, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095806/se-shop/products/ibjsejqloioktly4meoj.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:48', '2023-11-27 14:36:48'),
(295, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095807/se-shop/products/tgpj3m9zvbjiywkaabcm.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:49', '2023-11-27 14:36:49'),
(296, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095809/se-shop/products/vlmui6cw8htsmdlqlrim.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:51', '2023-11-27 14:36:51'),
(297, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095811/se-shop/products/tkioskuffalnies2dedb.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:53', '2023-11-27 14:36:53'),
(298, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095813/se-shop/products/j4kjbxjvep1a8gco7de8.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:55', '2023-11-27 14:36:55'),
(299, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095814/se-shop/products/amfyynj4ff8ew6tt5aqh.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:56', '2023-11-27 14:36:56'),
(300, 3, 'Logictech MX Mini Keyboard - Hot', 'Logictech MX Mini Keyboard - Hot', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701095816/se-shop/products/pqikmc68vdn7plhic29u.webp', '4K | IPS', 'Logitech', 'Logitech', 1, 1, 1.20, 777, 'Blue', 255, 70, '2023-11-27 14:36:58', '2023-11-27 14:36:58'),
(301, 3, 'JBL BoomBox 2 Mini For Music, Pinic', 'Powerfull Sound Deep Bass', 'https://image.made-in-china.com/2f0j00MBbfCMFrwvoy/Boombox-Portable-Wireless-Bluetooth-Speaker-Ipx7-Waterproof-Dynamics-Music-Outdoor-Loudspeaker.jpg', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 121, 76, '2023-11-27 14:55:15', '2023-11-30 02:34:13'),
(302, 4, 'JBL BoomBox 1 Mini For Music', 'Powerfull Sound Deep Bass', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701097561/se-shop/products/he6z5tkhes5gpe02uvl7.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 121, 80, '2023-11-27 15:06:03', '2023-11-27 15:06:03'),
(303, 4, 'JBL BoomBox 1 Mini For Music', 'Powerfull Sound Deep Bass', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701097622/se-shop/products/inq6d5sbqop5kxvjzbiq.jpg', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 121, 80, '2023-11-27 15:07:06', '2023-11-27 15:07:06'),
(304, 4, 'Head phone 1', 'Head phone 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098183/se-shop/products/vjxswnyd44xixgfhoz5f.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 59, 75, '2023-11-27 15:16:25', '2023-12-02 13:25:01'),
(305, 4, 'Head phone 1', 'Head phone 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098207/se-shop/products/ftb1u2vb52btmmi2arsw.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 59, 79, '2023-11-27 15:16:49', '2023-12-02 10:13:49'),
(306, 4, 'Head phone 1', 'Head phone 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098211/se-shop/products/jasgylc5kihtim4mhose.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 59, 80, '2023-11-27 15:16:54', '2023-11-27 15:16:54'),
(307, 4, 'Head phone 1', 'Head phone 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098220/se-shop/products/nhzuvehexhxx7c4dioqa.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 59, 80, '2023-11-27 15:17:02', '2023-11-27 15:17:02'),
(308, 4, 'Head phone 1', 'Head phone 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098226/se-shop/products/do4gnnj8miifdc2lljsr.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 777, 'Blue', 59, 80, '2023-11-27 15:17:08', '2023-11-27 15:17:08'),
(309, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098245/se-shop/products/ohlw1mbumzbb47nwhoug.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 76, '2023-11-27 15:17:28', '2023-11-29 16:00:10'),
(310, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098248/se-shop/products/wbmkottzdddczrym0uym.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:31', '2023-11-27 15:17:31'),
(311, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098255/se-shop/products/lmpzug66hg1p9enfixza.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:37', '2023-11-27 15:17:37'),
(312, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098261/se-shop/products/sjsvsmjqyk43oadrbpma.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:43', '2023-11-27 15:17:43'),
(313, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098266/se-shop/products/hglmd0rkksvviim9j5t4.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:48', '2023-11-27 15:17:48'),
(314, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098270/se-shop/products/rj0yoizxszawxrgkmrrz.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:52', '2023-11-27 15:17:52'),
(315, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098272/se-shop/products/zmwu7fjsjxgzwhc3ka75.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:54', '2023-11-27 15:17:54'),
(316, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098274/se-shop/products/hvt0wq42qn2zaved5fpg.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:56', '2023-11-27 15:17:56'),
(317, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098275/se-shop/products/ouuvucdadqks9ooumb5q.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:57', '2023-11-27 15:17:57'),
(318, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098277/se-shop/products/yiwxpkdn5nyucoxhi232.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:17:59', '2023-11-27 15:17:59'),
(319, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098278/se-shop/products/bzgtar6pyopubfn2nazt.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:00', '2023-11-27 15:18:00'),
(320, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098279/se-shop/products/hl2f9euxvqojxpmoef2d.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:02', '2023-11-27 15:18:02'),
(321, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098281/se-shop/products/pn4bssv5x2vj6htivqss.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:03', '2023-11-27 15:18:03'),
(322, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098282/se-shop/products/mxzdgzlolrpuho71sgpf.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:05', '2023-11-27 15:18:05'),
(323, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098284/se-shop/products/pbe9igp8vtoxvhcw1yo5.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:06', '2023-11-27 15:18:06'),
(324, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098286/se-shop/products/b1nvbfx2acw1b9n1u7ee.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:08', '2023-11-27 15:18:08'),
(325, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098288/se-shop/products/jwbiftpjffndglilutn7.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:11', '2023-11-27 15:18:11'),
(326, 4, 'Head phone 2', 'Head phone 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098290/se-shop/products/nz02zhvt1i0vjfsx2jyl.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:13', '2023-11-27 15:18:13'),
(327, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098303/se-shop/products/qx1s3cc1ysarxgbhqsda.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 666, 'Blue', 59, 80, '2023-11-27 15:18:25', '2023-11-27 15:18:25'),
(328, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098307/se-shop/products/tnw04h2yrwta9dz9cnlt.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 59, 80, '2023-11-27 15:18:29', '2023-11-27 15:18:29'),
(329, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098312/se-shop/products/gebpwbrz0i8h4kcxrs0a.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 59, 80, '2023-11-27 15:18:34', '2023-11-27 15:18:34'),
(330, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098316/se-shop/products/fm9s9q1idjhqzjhwxsy9.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 59, 80, '2023-11-27 15:18:39', '2023-11-27 15:18:39'),
(331, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098318/se-shop/products/getej33h9xyyin7aeimw.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 59, 80, '2023-11-27 15:18:40', '2023-11-27 15:18:40'),
(332, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098320/se-shop/products/z7r7sghjhqcf2nn3reza.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 59, 80, '2023-11-27 15:18:42', '2023-11-27 15:18:42'),
(333, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098321/se-shop/products/jdoboeqfyrnrzsxqfivy.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 59, 80, '2023-11-27 15:18:44', '2023-11-27 15:18:44'),
(334, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098323/se-shop/products/l5wyulypwqf8itjswt3h.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 99, 80, '2023-11-27 15:18:45', '2023-11-27 15:18:45'),
(335, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098328/se-shop/products/cvhhqyylwggfrwuwbhek.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:18:50', '2023-11-27 15:18:50'),
(336, 4, 'Head phone 3', 'Head phone 3', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098329/se-shop/products/ll3wgnpjp8lspciapsyu.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:18:52', '2023-11-27 15:18:52');
INSERT INTO `product` (`id`, `categoryId`, `name`, `description`, `imageUrl`, `screen`, `operatingSystem`, `processor`, `ram`, `storageCapacity`, `weight`, `batteryCapacity`, `color`, `price`, `stockQuantity`, `createdAt`, `updatedAt`) VALUES
(337, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098344/se-shop/products/uwywsycgwh1natlxlrnj.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:07', '2023-11-27 15:19:07'),
(338, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098347/se-shop/products/ej2s2ex4eme3dntluxe9.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:09', '2023-11-27 15:19:09'),
(339, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098349/se-shop/products/x6ezij6pkiy2nsduwl7w.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:11', '2023-11-27 15:19:11'),
(340, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098359/se-shop/products/ysoml0rkejvu5devfouj.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:21', '2023-11-27 15:19:21'),
(341, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098360/se-shop/products/gzp6x1ghy3wtx1bdebat.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:22', '2023-11-27 15:19:22'),
(342, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098361/se-shop/products/gtjl4jetvdvkhyutvr6c.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:23', '2023-11-27 15:19:23'),
(343, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098363/se-shop/products/pl67panrydhgnpziugej.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:25', '2023-11-27 15:19:25'),
(344, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098364/se-shop/products/ydyaj2iunxaruq75ci8f.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:26', '2023-11-27 15:19:26'),
(345, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098365/se-shop/products/uahbcog0p7vuaixoupjh.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:27', '2023-11-27 15:19:27'),
(346, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098367/se-shop/products/evtudchdbmx7pkqwabfy.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:28', '2023-11-27 15:19:28'),
(347, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098368/se-shop/products/szcpsowsstg8nhknpwum.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:30', '2023-11-27 15:19:30'),
(348, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098370/se-shop/products/nqfwrqzmllx1h6oua6b2.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:32', '2023-11-27 15:19:32'),
(349, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098371/se-shop/products/mcfr79ndb3gyv8iettyc.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:33', '2023-11-27 15:19:33'),
(350, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098373/se-shop/products/ikjc2szhjp4zgad9eudf.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:35', '2023-11-27 15:19:35'),
(351, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098374/se-shop/products/agckjporp7pfhacjpn49.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:36', '2023-11-27 15:19:36'),
(352, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098376/se-shop/products/exurc55wdgrpqowuonsb.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:38', '2023-11-27 15:19:38'),
(353, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098378/se-shop/products/qrn66dnty5zraumqitzb.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:40', '2023-11-27 15:19:40'),
(354, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098380/se-shop/products/o23ba2zb6clrpy2seai2.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:42', '2023-11-27 15:19:42'),
(355, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098381/se-shop/products/jszbmzpyzbmerwtjg5bz.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:43', '2023-11-27 15:19:43'),
(356, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098382/se-shop/products/ev2a3zouzozy0ix6gyor.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:44', '2023-11-27 15:19:44'),
(357, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098384/se-shop/products/aoowseyzqznumup8ttnd.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:46', '2023-11-27 15:19:46'),
(358, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098385/se-shop/products/n0kwsozp4tthpugdukgy.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:48', '2023-11-27 15:19:48'),
(359, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098387/se-shop/products/otllj9nilnpc0nhdvie2.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:49', '2023-11-27 15:19:49'),
(360, 4, 'Head phone 4', 'Head phone 4', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098389/se-shop/products/ge8yvhpp4l9ncktgtyva.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:19:51', '2023-11-27 15:19:51'),
(361, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098406/se-shop/products/dftwzh95vdogts2u79uj.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:08', '2023-11-27 15:20:08'),
(362, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098408/se-shop/products/fuarxksprtaa3skcftkw.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:10', '2023-11-27 15:20:10'),
(363, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098410/se-shop/products/irq3ja5uhxeq9gcsemnz.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:12', '2023-11-27 15:20:12'),
(364, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098412/se-shop/products/w5wc6iziizvgwc5tt6w0.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:14', '2023-11-27 15:20:14'),
(365, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098413/se-shop/products/dq11wqj7ffqn21gnrvar.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:15', '2023-11-27 15:20:15'),
(366, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098415/se-shop/products/z9wv7mpnpct0id8tyrcx.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:17', '2023-11-27 15:20:17'),
(367, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098416/se-shop/products/zn61kgc6qrybskkf8ixw.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:18', '2023-11-27 15:20:18'),
(368, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098418/se-shop/products/pw9mftpmbzkzhxmxbfjw.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:20', '2023-11-27 15:20:20'),
(369, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098419/se-shop/products/lxcs9jsalilhoj2b332z.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:21', '2023-11-27 15:20:21'),
(370, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098421/se-shop/products/ttujfj3kqztpdfjabdig.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:23', '2023-11-27 15:20:23'),
(371, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098426/se-shop/products/gpw66qhcsitso3svdace.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:30', '2023-11-27 15:20:30'),
(372, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098429/se-shop/products/srqipakuix86hv4r3bod.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:31', '2023-11-27 15:20:31'),
(373, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098430/se-shop/products/nr5ul4nywvdn6aahijzh.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:32', '2023-11-27 15:20:32'),
(374, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098432/se-shop/products/we6w6kdalhtxgl7ng0nv.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:34', '2023-11-27 15:20:34'),
(375, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098433/se-shop/products/d4ppejrfxwlrew6vuixr.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:35', '2023-11-27 15:20:35'),
(376, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098435/se-shop/products/dscyj0i43hsq7mjxhxu1.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:37', '2023-11-27 15:20:37'),
(377, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098436/se-shop/products/ua8ecush3nwpjpmpxox8.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:38', '2023-11-27 15:20:38'),
(378, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098438/se-shop/products/hrbconamqndp33xwfouc.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:40', '2023-11-27 15:20:40'),
(379, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098440/se-shop/products/ysfvec7n9les1i2xgozu.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:42', '2023-11-27 15:20:42'),
(380, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098441/se-shop/products/oqpssoszqrdcii5oqmpl.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:43', '2023-11-27 15:20:43'),
(381, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098442/se-shop/products/ucfp5q06tsfncku8t5ie.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:44', '2023-11-27 15:20:44'),
(382, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098445/se-shop/products/wzakj6gvrmmgmrcand3e.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:47', '2023-11-27 15:20:47'),
(383, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098450/se-shop/products/yxeipiddi0wxcqkbzzvq.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:52', '2023-11-27 15:20:52'),
(384, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098455/se-shop/products/hf3kwxloanb3mkwsagme.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:20:58', '2023-11-27 15:20:58'),
(385, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098459/se-shop/products/rvfpkfcyqoxaekselifq.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:01', '2023-11-27 15:21:01'),
(386, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098461/se-shop/products/b47v8o1uzciugw1aepab.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:03', '2023-11-27 15:21:03'),
(387, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098462/se-shop/products/xgqcuwebio2aov9zbvdw.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:04', '2023-11-27 15:21:04'),
(388, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098464/se-shop/products/pqky2p8s6q1ipqw4vhoz.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:06', '2023-11-27 15:21:06'),
(389, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098465/se-shop/products/uievu1lfmy4jtey2gcil.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:08', '2023-11-27 15:21:08'),
(390, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098468/se-shop/products/xanjxigpjwm0jxisw3yi.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:09', '2023-11-27 15:21:09'),
(391, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098469/se-shop/products/pu2nccdv8k4r5exns7um.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:11', '2023-11-27 15:21:11'),
(392, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098470/se-shop/products/ulbwzjyv5bgeatzvoqqc.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:13', '2023-11-27 15:21:13'),
(393, 4, 'Head phone 5', 'Head phone 5', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098472/se-shop/products/snekwc4qkhun6gr4joqj.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:15', '2023-11-27 15:21:15'),
(394, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098489/se-shop/products/qsg9todoe6ktdpaiqitg.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:31', '2023-11-27 15:21:31'),
(395, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098492/se-shop/products/vlq2laez4nbwgv3anxc0.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:35', '2023-11-27 15:21:35'),
(396, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098494/se-shop/products/ihz0udmff8dfikmuqouc.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:36', '2023-11-27 15:21:36'),
(397, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098496/se-shop/products/sdbk6jlly5kyaq6a5jvf.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:38', '2023-11-27 15:21:38'),
(398, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098497/se-shop/products/gj3f9zpqpre6skbszmmy.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:39', '2023-11-27 15:21:39'),
(399, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098499/se-shop/products/kto9ldshni15lisffmie.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:41', '2023-11-27 15:21:41'),
(400, 4, 'Head phone 6', 'Head phone 6', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098500/se-shop/products/hakslbmax4aoqjkwqrhc.webp', '4K | IPS', 'JBL ', 'JBL ', 1, 1, 1.20, 555, 'Blue', 299, 80, '2023-11-27 15:21:43', '2023-11-27 15:21:43'),
(401, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098727/se-shop/products/tl5ozw4ksl99pupbuunc.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:25:29', '2023-11-27 15:25:29'),
(402, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098737/se-shop/products/vddcl7ffemmqmefywq8q.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:25:40', '2023-11-27 15:25:40'),
(403, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098746/se-shop/products/os4bdskwqivpamv3tz4g.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 78, '2023-11-27 15:25:48', '2023-12-01 11:09:54'),
(404, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098751/se-shop/products/mluzf5a4gutu8eeb7kmg.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:25:53', '2023-11-27 15:25:53'),
(405, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098759/se-shop/products/acjsyqgt45c7onfuxqrh.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:01', '2023-11-27 15:26:01'),
(406, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098765/se-shop/products/y8gwqchooegqhrbrtv2q.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:07', '2023-11-27 15:26:07'),
(407, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098770/se-shop/products/csex6gcyyhcnx6pytmb9.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:13', '2023-11-27 15:26:13'),
(408, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098780/se-shop/products/uwf1lgh4mejfy8vyrefg.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:22', '2023-11-27 15:26:22'),
(409, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098786/se-shop/products/sjk2hgjjcwnwh6tebgvx.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:28', '2023-11-27 15:26:28'),
(410, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098791/se-shop/products/zc0r3izrfyfv5afdbjrb.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:33', '2023-11-27 15:26:33'),
(411, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098796/se-shop/products/weuimzhfzkz9dmpgdlwe.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:38', '2023-11-27 15:26:38'),
(412, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098801/se-shop/products/zaxbrbrlvir8yntg6der.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:43', '2023-11-27 15:26:43'),
(413, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098805/se-shop/products/vcstecazdmcc09qaxfas.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:47', '2023-11-27 15:26:47'),
(414, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098811/se-shop/products/mxolbcpsho9bshcartct.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:53', '2023-11-27 15:26:53'),
(415, 5, 'Camera For Home 1', 'Camera For Home 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098816/se-shop/products/tjlfumtfyu1ahjgh1u0e.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:26:58', '2023-11-27 15:26:58'),
(416, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098834/se-shop/products/oxnfk0h6gg7byqceugwk.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:16', '2023-11-27 15:27:16'),
(417, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098838/se-shop/products/swoooxylli6xtwx3liwp.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:20', '2023-11-27 15:27:20'),
(418, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098842/se-shop/products/ytnuj27xspfnkkzf6u0r.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:25', '2023-11-27 15:27:25'),
(419, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098848/se-shop/products/opsfphgi4e4sridr59pa.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:30', '2023-11-27 15:27:30'),
(420, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098853/se-shop/products/h9wxq783i1apg6bd8kfk.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:36', '2023-11-27 15:27:36'),
(421, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098855/se-shop/products/bgw9le2ucdhk34gnfs6m.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:38', '2023-11-27 15:27:38'),
(422, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098858/se-shop/products/obf1nlrv4ijg2fyuk84t.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:40', '2023-11-27 15:27:40'),
(423, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098860/se-shop/products/btledktkigduketnku5p.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:43', '2023-11-27 15:27:43'),
(424, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098862/se-shop/products/xer8u40dhkca0kunvyqb.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:45', '2023-11-27 15:27:45'),
(425, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098864/se-shop/products/bwmbjsqtvjtmxzw6frap.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:47', '2023-11-27 15:27:47'),
(426, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098866/se-shop/products/rzgecmie0k0mr4bn5598.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:49', '2023-11-27 15:27:49'),
(427, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098868/se-shop/products/pv08wonl5eabvhbomeac.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:51', '2023-11-27 15:27:51'),
(428, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098871/se-shop/products/h3c5ndd1smrriyufzf6v.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:53', '2023-11-27 15:27:53'),
(429, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098873/se-shop/products/xpvindukmbp0gcxcv6ma.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:55', '2023-11-27 15:27:55'),
(430, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098875/se-shop/products/p92eswzehbmrzuzj4hpf.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:57', '2023-11-27 15:27:57'),
(431, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098877/se-shop/products/qbffu0ggenklaxunhchl.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:27:59', '2023-11-27 15:27:59'),
(432, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098879/se-shop/products/uw5bewki7clbewajixqi.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:02', '2023-11-27 15:28:02'),
(433, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098883/se-shop/products/q3c41o3s2i3iv1virlm5.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:04', '2023-11-27 15:28:04'),
(434, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098883/se-shop/products/fijsplwaltakceluciyb.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:06', '2023-11-27 15:28:06'),
(435, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098885/se-shop/products/rm1mtkblaqewyrnljcey.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:08', '2023-11-27 15:28:08'),
(436, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098887/se-shop/products/wgd7f9qylyocmiva7vwn.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:09', '2023-11-27 15:28:09'),
(437, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098889/se-shop/products/a47mcknfu3ntzgdqmffx.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:11', '2023-11-27 15:28:11'),
(438, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098891/se-shop/products/way8eehqjmtlq0jjfijr.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:13', '2023-11-27 15:28:13'),
(439, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098892/se-shop/products/km8bgbott1azxa8vxr4k.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:15', '2023-11-27 15:28:15'),
(440, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098894/se-shop/products/vg09cpsm1dhdolphhr0n.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:16', '2023-11-27 15:28:16'),
(441, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098896/se-shop/products/iy7ixjsio3x5ezq0jqpq.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:18', '2023-11-27 15:28:18'),
(442, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098897/se-shop/products/jgnsmvgh3kjql7lhkm1f.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:20', '2023-11-27 15:28:20'),
(443, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098899/se-shop/products/yuonzv0gnp9y4kihdbj0.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:21', '2023-11-27 15:28:21'),
(444, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098901/se-shop/products/hxmxx66yzvnmj9wz6a7w.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:23', '2023-11-27 15:28:23'),
(445, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098902/se-shop/products/dz8iluk7qhetggowxrij.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:24', '2023-11-27 15:28:24'),
(446, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098904/se-shop/products/ksjaapmvhwaafrqz7yyp.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:26', '2023-11-27 15:28:26'),
(447, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098905/se-shop/products/qro9v8kxjc27z59ta96m.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:27', '2023-11-27 15:28:27'),
(448, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098907/se-shop/products/nik2l48x4vwfyfofaph7.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:29', '2023-11-27 15:28:29'),
(449, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098908/se-shop/products/gxdo7ifo2cgsiob9ivzv.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:31', '2023-11-27 15:28:31'),
(450, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098910/se-shop/products/hsfcvw6z6zckzojckpde.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:32', '2023-11-27 15:28:32'),
(451, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098912/se-shop/products/voa7bgqzwf9h5rlx1mmv.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:34', '2023-11-27 15:28:34'),
(452, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098913/se-shop/products/vklcrdlkdvmdwc8ndvpy.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:35', '2023-11-27 15:28:35'),
(453, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098923/se-shop/products/ba2dlam8jxoctylynhql.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:46', '2023-11-27 15:28:46'),
(454, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098926/se-shop/products/pjfs4l7jszz00sj3ezff.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:48', '2023-11-27 15:28:48'),
(455, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098927/se-shop/products/pa4fqflamnzhkd9o0a4t.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:50', '2023-11-27 15:28:50'),
(456, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098929/se-shop/products/ohj7bh8dsdpedecnejbz.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:52', '2023-11-27 15:28:52'),
(457, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098935/se-shop/products/rrolklyehrlaqtjsuu8f.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:28:57', '2023-11-27 15:28:57'),
(458, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098937/se-shop/products/o7cqjk8arrjpnnftuerk.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:00', '2023-11-27 15:29:00'),
(459, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098939/se-shop/products/bhrnxu94bxthtgwvnx3a.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:01', '2023-11-27 15:29:01'),
(460, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098941/se-shop/products/pmn0yvijil70c9ain9hz.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:03', '2023-11-27 15:29:03'),
(461, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098943/se-shop/products/w5sdbx6yuo3uhzlmvstv.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:06', '2023-11-27 15:29:06'),
(462, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098945/se-shop/products/ckp6jqz1699q0zzgus1q.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:08', '2023-11-27 15:29:08'),
(463, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098955/se-shop/products/zumgahld0fkxjmmrilb1.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:18', '2023-11-27 15:29:18'),
(464, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098957/se-shop/products/zy2mpg56slnkhotsaf9a.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:20', '2023-11-27 15:29:20'),
(465, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098960/se-shop/products/wntcoqwzfajc8pha1koa.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:23', '2023-11-27 15:29:23'),
(466, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098963/se-shop/products/lwgyinwneyc2eihk64wf.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:25', '2023-11-27 15:29:25'),
(467, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098965/se-shop/products/upreiz7fytamogbaqtkp.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:28', '2023-11-27 15:29:28'),
(468, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098967/se-shop/products/fa72jwvfeeh404cpbbzl.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:30', '2023-11-27 15:29:30'),
(469, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098969/se-shop/products/blykohmhorcq0uqmgrlh.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:32', '2023-11-27 15:29:32'),
(470, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098971/se-shop/products/bpmm1plsdk5inw57r0mi.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:33', '2023-11-27 15:29:33'),
(471, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098981/se-shop/products/pjncnupjpwxaexpcg43u.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:43', '2023-11-27 15:29:43'),
(472, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098984/se-shop/products/kxclimbimbg76fxts7co.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:46', '2023-11-27 15:29:46'),
(473, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098986/se-shop/products/uqjgbpqk3xthwmmapuau.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:49', '2023-11-27 15:29:49'),
(474, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098990/se-shop/products/xbkzk0x0jo1rw12x3ixv.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:52', '2023-11-27 15:29:52'),
(475, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098992/se-shop/products/dvpnkalnbhoe9jrtrftd.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:54', '2023-11-27 15:29:54'),
(476, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098993/se-shop/products/whb6samqlovjncnkeykd.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:56', '2023-11-27 15:29:56'),
(477, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098995/se-shop/products/zjnqexhdjlvx7xs4djd4.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:57', '2023-11-27 15:29:57'),
(478, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098997/se-shop/products/qfllonlat6tze2uwt8h6.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:29:59', '2023-11-27 15:29:59'),
(479, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701098999/se-shop/products/axig0gxrk3arfv5srqhk.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:30:01', '2023-11-27 15:30:01'),
(480, 5, 'Camera For Home 2', 'Camera For Home 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099000/se-shop/products/zczu3muspzxhvhubbv3h.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 89, 80, '2023-11-27 15:30:02', '2023-11-27 15:30:02'),
(481, 6, 'MSI Monitor 1', 'MSI Monitor 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099751/se-shop/products/ai4pbo5pplsthvszgdvd.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:42:33', '2023-11-27 15:42:33'),
(482, 6, 'MSI Monitor 1', 'MSI Monitor 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099759/se-shop/products/ualjjdivpxoru39dcbny.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:42:42', '2023-11-27 15:42:42'),
(483, 6, 'MSI Monitor 1', 'MSI Monitor 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099771/se-shop/products/c99ggw3d3nyjijclaiid.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:42:53', '2023-11-27 15:42:53'),
(484, 6, 'MSI Monitor 1', 'MSI Monitor 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099776/se-shop/products/uhi1w6prjunlip7aafuz.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:42:58', '2023-11-27 15:42:58'),
(485, 6, 'MSI Monitor 1', 'MSI Monitor 1', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099781/se-shop/products/blsdfh3g35b5sbpiw272.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:43:04', '2023-11-27 15:43:04'),
(486, 6, 'MSI Monitor 2', 'MSI Monitor 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099787/se-shop/products/zu6qbyxefx8smnc3bwcg.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:43:09', '2023-11-27 15:43:09'),
(487, 6, 'MSI Monitor 2', 'MSI Monitor 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099791/se-shop/products/ahkxdkla61wsgll2qvtk.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:43:14', '2023-11-27 15:43:14'),
(488, 6, 'MSI Monitor 2', 'MSI Monitor 2', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099796/se-shop/products/lwkbet4l1vu9tu9fqmqz.webp', '4K | IPS', 'Camera', 'Camera', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 15:43:18', '2023-11-27 15:43:18'),
(489, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099849/se-shop/products/iwc3cms1rubn5a90fwnx.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:11', '2023-11-27 15:44:11'),
(490, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099854/se-shop/products/yp6btfehqubg88zaoqq3.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:17', '2023-11-27 15:44:17'),
(491, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099860/se-shop/products/oit1ursmmw2dv8ggixb1.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:22', '2023-11-27 15:44:22'),
(492, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099865/se-shop/products/uu0ul59rjk0w1c05bu3r.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:28', '2023-11-27 15:44:28'),
(493, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099871/se-shop/products/rp5fmucgugao5klsxqty.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:33', '2023-11-27 15:44:33'),
(494, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099880/se-shop/products/d9gpkfyx9cebtaahytpn.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:43', '2023-11-27 15:44:43'),
(495, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099884/se-shop/products/klwtyug0m8e1yftbqfw2.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:44:46', '2023-11-27 15:44:46'),
(496, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099899/se-shop/products/jddtimbqtjosluibqy21.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:01', '2023-11-27 15:45:01'),
(497, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099909/se-shop/products/mgupn9zuqkgvvxwjze01.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:12', '2023-11-27 15:45:12'),
(498, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099914/se-shop/products/rv6ddub01nojnizirnes.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:16', '2023-11-27 15:45:16'),
(499, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099920/se-shop/products/e6d9hw8kylzqy5i7ikwa.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:22', '2023-11-27 15:45:22'),
(500, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099926/se-shop/products/yaxuysdncj5ypjtl3snv.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:28', '2023-11-27 15:45:28'),
(501, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099931/se-shop/products/onw5syccookjzvwl9nik.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:33', '2023-11-27 15:45:33'),
(502, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099934/se-shop/products/fhsw8u6z0ldxzqm82re9.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:37', '2023-11-27 15:45:37'),
(503, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099939/se-shop/products/uyz2jivx79ejh4hz6xsj.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:42', '2023-11-27 15:45:42'),
(504, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099944/se-shop/products/e0eqycvb93owuqvmrltx.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:46', '2023-11-27 15:45:46'),
(505, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099957/se-shop/products/rwwelib5xvx656euwybz.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:45:59', '2023-11-27 15:45:59'),
(506, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099961/se-shop/products/breihelmf25842xp7uqn.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:46:03', '2023-11-27 15:46:03'),
(507, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099967/se-shop/products/rs25ae2xicprwla0hmbs.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:46:10', '2023-11-27 15:46:10'),
(508, 6, 'Asus PC Build For Gaming', 'Asus PC Build For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701099972/se-shop/products/pfilrr2i1ol0ubminv7j.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 499, 80, '2023-11-27 15:46:14', '2023-11-27 15:46:14'),
(509, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100001/se-shop/products/i7rpfk0bklbp5bhsfmuh.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:46:44', '2023-11-27 15:46:44'),
(510, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100005/se-shop/products/yu6hhu3jygzgglzaji3x.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:46:47', '2023-11-27 15:46:47'),
(511, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100010/se-shop/products/hwgzaiuj6n4shwzoqppe.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:46:52', '2023-11-27 15:46:52'),
(512, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100015/se-shop/products/s997c2mabhgpith53wlz.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:46:58', '2023-11-27 15:46:58'),
(513, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100021/se-shop/products/ffszh4bckobk4ppn430u.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:47:03', '2023-11-27 15:47:03'),
(514, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100032/se-shop/products/scan23stjfszqyl5w2n9.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:47:15', '2023-11-27 15:47:15'),
(515, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100038/se-shop/products/gv7jgod2uie6jcpn0up8.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:47:20', '2023-11-27 15:47:20'),
(516, 6, 'AsusMonitor', 'AsusMonitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100050/se-shop/products/hw858eauxiqg7aejeytx.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:47:32', '2023-11-27 15:47:32'),
(517, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100055/se-shop/products/gjoj4v7n1f3xuap1zozd.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 599, 80, '2023-11-27 15:47:37', '2023-11-27 15:47:37'),
(518, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100063/se-shop/products/iepudnyazqphse0iju6m.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 599, 80, '2023-11-27 15:47:45', '2023-11-27 15:47:45'),
(519, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100071/se-shop/products/mnywope4uhckw1lcpddh.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 599, 80, '2023-11-27 15:47:53', '2023-11-27 15:47:53'),
(520, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100083/se-shop/products/uwzdqqwn9uzwnjszaprt.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 599, 80, '2023-11-27 15:48:05', '2023-11-27 15:48:05'),
(521, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100085/se-shop/products/nj9ghukbgvle4xu4qyhe.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 15:48:08', '2023-11-27 15:48:08'),
(522, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100094/se-shop/products/qyxpsgkibbwuwc71qrfk.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 599, 80, '2023-11-27 15:48:16', '2023-11-27 15:48:16'),
(523, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100097/se-shop/products/va4y3zeft5czatagv08r.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:19', '2023-11-27 15:48:19'),
(524, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100108/se-shop/products/gvboban6h8xnrqtuvcgz.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:30', '2023-11-27 15:48:30');
INSERT INTO `product` (`id`, `categoryId`, `name`, `description`, `imageUrl`, `screen`, `operatingSystem`, `processor`, `ram`, `storageCapacity`, `weight`, `batteryCapacity`, `color`, `price`, `stockQuantity`, `createdAt`, `updatedAt`) VALUES
(525, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100111/se-shop/products/tqkqgzvmzkliyc1qpqsp.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:33', '2023-11-27 15:48:33'),
(526, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100113/se-shop/products/sxpvzooljcbikhhdcapq.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:36', '2023-11-27 15:48:36'),
(527, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100116/se-shop/products/veyhwi5twmspymcuuqng.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:38', '2023-11-27 15:48:38'),
(528, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100118/se-shop/products/vlvq17wje6tmlcgcchgg.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:40', '2023-11-27 15:48:40'),
(529, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100120/se-shop/products/qwcbqxmbl7kskxd5hoix.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:42', '2023-11-27 15:48:42'),
(530, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100122/se-shop/products/zugy5sxpzmpjy6rxocve.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:44', '2023-11-27 15:48:44'),
(531, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100124/se-shop/products/wbj89kvnqh6kqp2llu4h.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:46', '2023-11-27 15:48:46'),
(532, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100126/se-shop/products/exvgw84majzcpveaa13l.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:48', '2023-11-27 15:48:48'),
(533, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100128/se-shop/products/x4umxbj6hpli2ykgwoa8.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:50', '2023-11-27 15:48:50'),
(534, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100130/se-shop/products/uxei34mgfob1peah15tn.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:52', '2023-11-27 15:48:52'),
(535, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100132/se-shop/products/eq2tf3jitnldj8zskkpy.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:54', '2023-11-27 15:48:54'),
(536, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100135/se-shop/products/fl7l0usjksxwhta7rej4.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:57', '2023-11-27 15:48:57'),
(537, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100135/se-shop/products/yrd6swcxw4h49xetdddp.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:58', '2023-11-27 15:48:58'),
(538, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100137/se-shop/products/bennydymdul4zuzzlxkq.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:48:59', '2023-11-27 15:48:59'),
(539, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100139/se-shop/products/sxqstruqupo1qfyyxicw.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:01', '2023-11-27 15:49:01'),
(540, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100141/se-shop/products/zvdvnptjxzj4divrxv1x.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:04', '2023-11-27 15:49:04'),
(541, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100148/se-shop/products/yhptmn1fpwsfwhnfxvkw.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:10', '2023-11-27 15:49:10'),
(542, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100153/se-shop/products/tsoitgu1rfeteqyc3n7m.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:15', '2023-11-27 15:49:15'),
(543, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100155/se-shop/products/g41pem21im2cjeqbenx3.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:17', '2023-11-27 15:49:17'),
(544, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100157/se-shop/products/vj8wh3f6tmbm0o3vxvvl.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:19', '2023-11-27 15:49:19'),
(545, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100162/se-shop/products/xfg4ktqwgpcrylv05pgu.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:24', '2023-11-27 15:49:24'),
(546, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100164/se-shop/products/lwtqacwflludp7e05l5m.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:26', '2023-11-27 15:49:26'),
(547, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100166/se-shop/products/gwtu2d3wy6hhf00rp5tc.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:28', '2023-11-27 15:49:28'),
(548, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100168/se-shop/products/pxwchpb9wbs4xd2rntno.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:30', '2023-11-27 15:49:30'),
(549, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100171/se-shop/products/vb1h5stbhpr2ebsm44cs.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:33', '2023-11-27 15:49:33'),
(550, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100173/se-shop/products/yudzsbwm7s78gk2ibk5p.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:35', '2023-11-27 15:49:35'),
(551, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100175/se-shop/products/b53lx9ndgaoiyt6bvyaa.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:37', '2023-11-27 15:49:37'),
(552, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100178/se-shop/products/okbwrajdocgngnqupvcp.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:40', '2023-11-27 15:49:40'),
(553, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100183/se-shop/products/hodlnuopznt5rwkfipyp.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:45', '2023-11-27 15:49:45'),
(554, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100185/se-shop/products/fhzeprynbsrvqdtogf17.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:47', '2023-11-27 15:49:47'),
(555, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100187/se-shop/products/shnqwhikug7un4n2q63k.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:49', '2023-11-27 15:49:49'),
(556, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100189/se-shop/products/fw0ov49spqtnnvp2e4ya.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:52', '2023-11-27 15:49:52'),
(557, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100192/se-shop/products/paf88jcdcqwuhvv6marw.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:54', '2023-11-27 15:49:54'),
(558, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100195/se-shop/products/iru2kdmhotivz6sitn9i.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:57', '2023-11-27 15:49:57'),
(559, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100197/se-shop/products/dqepnduzkjk9ksgma24w.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:49:59', '2023-11-27 15:49:59'),
(560, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100199/se-shop/products/snppayirsvpojwjyajjb.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:01', '2023-11-27 15:50:01'),
(561, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100201/se-shop/products/noeh1xnfwe4aqrppc90j.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:04', '2023-11-27 15:50:04'),
(562, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100217/se-shop/products/fztagkubbnn6eu8gxz5s.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:19', '2023-11-27 15:50:19'),
(563, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100219/se-shop/products/ypjuuyrx4nozlu9snheu.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:21', '2023-11-27 15:50:21'),
(564, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100220/se-shop/products/v3xa8bop9reajpgf52zd.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:22', '2023-11-27 15:50:22'),
(565, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100222/se-shop/products/fmxmotdbhbzepqbfwr0r.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:24', '2023-11-27 15:50:24'),
(566, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100224/se-shop/products/jyobfvhro6btopgkqz0d.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:26', '2023-11-27 15:50:26'),
(567, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100225/se-shop/products/ukmocdeej9wpwnfvaviw.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:28', '2023-11-27 15:50:28'),
(568, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100227/se-shop/products/xbpun46fwynj3f2djqij.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:29', '2023-11-27 15:50:29'),
(569, 6, 'Asus Monitor', 'Asus Monitor', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100229/se-shop/products/xpnorikulsx06djgch7m.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 588, 80, '2023-11-27 15:50:31', '2023-11-27 15:50:31'),
(570, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100251/se-shop/products/gq0feryprp9hqqouk06f.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:50:53', '2023-11-27 15:50:53'),
(571, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100254/se-shop/products/vgqye8beju27thmtnmue.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:50:56', '2023-11-27 15:50:56'),
(572, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100257/se-shop/products/q45zx8r7wqhxsooa8fcg.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:50:59', '2023-11-27 15:50:59'),
(573, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100259/se-shop/products/fkmhfopxuk73xaylrmnj.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:01', '2023-11-27 15:51:01'),
(574, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100268/se-shop/products/dpi0ftktubkkhlwumuob.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:10', '2023-11-27 15:51:10'),
(575, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100269/se-shop/products/cmlmzdjli2285yzecmid.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:12', '2023-11-27 15:51:12'),
(576, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100271/se-shop/products/kmbyesgbrroz7sixw9p6.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:13', '2023-11-27 15:51:13'),
(577, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100273/se-shop/products/pzmvnnb6gygeeuye9lrd.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:15', '2023-11-27 15:51:15'),
(578, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100275/se-shop/products/fiddyd9bnub5ezicsaah.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:17', '2023-11-27 15:51:17'),
(579, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100276/se-shop/products/gkxdmdsyxe8i6lhsmnqq.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:18', '2023-11-27 15:51:18'),
(580, 6, 'Asus Monitor For Gaming', 'Asus Monitor For Gaming', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100278/se-shop/products/c5viosgqe0dnyucloquo.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 899, 'Blue', 121, 80, '2023-11-27 15:51:20', '2023-11-27 15:51:20'),
(581, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100781/se-shop/products/qobrb0ngkjo4sbfoaqb1.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 76, '2023-11-27 15:59:43', '2023-11-30 02:32:00'),
(582, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100787/se-shop/products/ialdbhmiljjujzz1iyo4.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 15:59:49', '2023-11-27 15:59:49'),
(583, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100791/se-shop/products/rjppwapy2hbhbjgcz71b.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 15:59:53', '2023-11-27 15:59:53'),
(584, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100797/se-shop/products/f49z3shnq79mgtjxo8yp.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 15:59:59', '2023-11-27 15:59:59'),
(585, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100802/se-shop/products/ok8bbg4kgbzcal1w64ls.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:04', '2023-11-27 16:00:04'),
(586, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100807/se-shop/products/utcsxs3mphmakrjswzqm.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:09', '2023-11-27 16:00:09'),
(587, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100812/se-shop/products/fgdebygauqusdbmfp1g9.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:14', '2023-11-27 16:00:14'),
(588, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100817/se-shop/products/ukx5ostmangmmjn7vs0p.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:20', '2023-11-27 16:00:20'),
(589, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100821/se-shop/products/fstv5phmv05kjnrailme.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:23', '2023-11-27 16:00:23'),
(590, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100825/se-shop/products/thhjt6zyftfdxbw4w8w2.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:28', '2023-11-27 16:00:28'),
(591, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100829/se-shop/products/ledotazzbgovw1vs5bk3.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:32', '2023-11-27 16:00:32'),
(592, 7, 'Google TV', 'Google TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100842/se-shop/products/hqowopaccllx2iuyq9di.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:44', '2023-11-27 16:00:44'),
(593, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100852/se-shop/products/yklzymhekizfvpvhbrzw.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1002, 80, '2023-11-27 16:00:54', '2023-11-27 16:00:54'),
(594, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100858/se-shop/products/krjfhwwacoa0dyycev6q.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1988, 80, '2023-11-27 16:01:01', '2023-11-27 16:01:01'),
(595, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100862/se-shop/products/rtwwiykkoyhoxszpvwsd.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1988, 80, '2023-11-27 16:01:04', '2023-11-27 16:01:04'),
(596, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100865/se-shop/products/fnoimuigrkyodgekavty.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1988, 80, '2023-11-27 16:01:08', '2023-11-27 16:01:08'),
(597, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100869/se-shop/products/the835kktcnf1ymny1mn.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1988, 80, '2023-11-27 16:01:11', '2023-11-27 16:01:11'),
(598, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100873/se-shop/products/s883apfmhanw5tw1lz6w.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1988, 80, '2023-11-27 16:01:15', '2023-11-27 16:01:15'),
(599, 7, 'SamSung TV', 'SamSung TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100878/se-shop/products/l0ihavhxxrtzi2a17grz.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1988, 80, '2023-11-27 16:01:20', '2023-11-27 16:01:20'),
(600, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100893/se-shop/products/wwtgsflrioqiw6jovf3f.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:01:35', '2023-11-27 16:01:35'),
(601, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100899/se-shop/products/qhdnixjoniurhlugazsi.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:01:41', '2023-11-27 16:01:41'),
(602, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100905/se-shop/products/ktejezrw4opijybtxbu1.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:01:54', '2023-11-27 16:01:54'),
(603, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100916/se-shop/products/scrum972ezx0cjr4p5ka.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:01:58', '2023-11-27 16:01:58'),
(604, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100921/se-shop/products/dpayiurba1h8ms6vm0ee.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:02:03', '2023-11-27 16:02:03'),
(605, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100926/se-shop/products/xrahmztdqayebumk5a6s.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:02:08', '2023-11-27 16:02:08'),
(606, 7, 'SamSung Oled TV', 'SamSung Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100931/se-shop/products/yznforiwaayydtt9qtsq.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 1249, 80, '2023-11-27 16:02:13', '2023-11-27 16:02:13'),
(607, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100949/se-shop/products/lycwesmzealwir8hxwi2.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:02:31', '2023-11-27 16:02:31'),
(608, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100955/se-shop/products/edd8dm5d9aoby6kpgswu.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:02:39', '2023-11-27 16:02:39'),
(609, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100960/se-shop/products/e7lsgmg6tfvxmyyze6by.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:02:42', '2023-11-27 16:02:42'),
(610, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100964/se-shop/products/teys8zwb5nsvxhjoeusq.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:02:46', '2023-11-27 16:02:46'),
(611, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100970/se-shop/products/gihb4f0euuscfqgcg3yg.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:02:52', '2023-11-27 16:02:52'),
(612, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100973/se-shop/products/adm8vwjc4kmgvvcxfqjr.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:02:55', '2023-11-27 16:02:55'),
(613, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100979/se-shop/products/hzjf7jymrpep6ku3hi59.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:01', '2023-11-27 16:03:01'),
(614, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100988/se-shop/products/f0lt9vmnuetwv2i2w3wq.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:10', '2023-11-27 16:03:10'),
(615, 7, 'Asus Oled TV', 'Asus Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701100993/se-shop/products/fwlbhb73c9p9qwmijp89.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:15', '2023-11-27 16:03:15'),
(616, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101013/se-shop/products/uz3noovybiheldpxpat5.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:35', '2023-11-27 16:03:35'),
(617, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101015/se-shop/products/msjtpzuy1glkdlazlhff.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:37', '2023-11-27 16:03:37'),
(618, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101020/se-shop/products/gqn52ngaxe0wfaxvrztg.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:42', '2023-11-27 16:03:42'),
(619, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101024/se-shop/products/neeo7x3mdf6cpfploqzt.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:46', '2023-11-27 16:03:46'),
(620, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101029/se-shop/products/u8qd4tm20o33ikdpohdp.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:51', '2023-11-27 16:03:51'),
(621, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101032/se-shop/products/yknctdx7i0in9idycu7y.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:03:54', '2023-11-27 16:03:54'),
(622, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101037/se-shop/products/i63jkusasdbcpajbtj18.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:00', '2023-11-27 16:04:00'),
(623, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101043/se-shop/products/n8kxtimyhou9rcvrnuap.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:05', '2023-11-27 16:04:05'),
(624, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101051/se-shop/products/zi0m2j0xlubzvckfimun.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:13', '2023-11-27 16:04:13'),
(625, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101054/se-shop/products/yh27seltliezljewug3l.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:16', '2023-11-27 16:04:16'),
(626, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101058/se-shop/products/shke2ic94moci7ldn4qt.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:20', '2023-11-27 16:04:20'),
(627, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101068/se-shop/products/bhsxmak0ytt9bqnys3a9.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:30', '2023-11-27 16:04:30'),
(628, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101079/se-shop/products/yyj5q4hltblc6vxwdra4.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:41', '2023-11-27 16:04:41'),
(629, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101081/se-shop/products/u7acifdaligymjn00z8j.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:43', '2023-11-27 16:04:43'),
(630, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101087/se-shop/products/vptnznsmdkmpxvstssks.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:49', '2023-11-27 16:04:49'),
(631, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101092/se-shop/products/wrcufwwjwipgbtq1gtug.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:04:54', '2023-11-27 16:04:54'),
(632, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101100/se-shop/products/scrm3vnm0mp0ewirfy5g.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:02', '2023-11-27 16:05:02'),
(633, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101106/se-shop/products/fu3qmqsxfhicueeupcxi.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:08', '2023-11-27 16:05:08'),
(634, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101108/se-shop/products/g8hnldlpxuq9pkvwfy52.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:10', '2023-11-27 16:05:10'),
(635, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101109/se-shop/products/f5tw5co1ejeok001gmqv.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:12', '2023-11-27 16:05:12'),
(636, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101112/se-shop/products/kmfmomqewdjqkg665rbn.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:14', '2023-11-27 16:05:14'),
(637, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101114/se-shop/products/ftgdgnrs7urqmnoakf1r.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:16', '2023-11-27 16:05:16'),
(638, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101116/se-shop/products/jcgzrs6ftzysbw5hrmw1.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:18', '2023-11-27 16:05:18'),
(639, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101122/se-shop/products/ppsva9mqtnyfiawjutwf.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:25', '2023-11-27 16:05:25'),
(640, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101124/se-shop/products/lhtc81ttsujgbhr2ckvg.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:27', '2023-11-27 16:05:27'),
(641, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101126/se-shop/products/tmlmdkanekmgr4ezrusj.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:29', '2023-11-27 16:05:29'),
(642, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101129/se-shop/products/aw5ni051lhqzajelui5o.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:31', '2023-11-27 16:05:31'),
(643, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101131/se-shop/products/uxexz7ni2yh8bybwejgo.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:34', '2023-11-27 16:05:34'),
(644, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101133/se-shop/products/njssk5ttid9ozerraurc.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:35', '2023-11-27 16:05:35'),
(645, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101135/se-shop/products/lkgdqjoeypn6bsfhie4m.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:37', '2023-11-27 16:05:37'),
(646, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101137/se-shop/products/vbabailxdtdx3reneave.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:39', '2023-11-27 16:05:39'),
(647, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101139/se-shop/products/cnihcyzmiudztgqdw08h.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:41', '2023-11-27 16:05:41'),
(648, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101140/se-shop/products/v3v4dhkea5tmr66wchhk.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:43', '2023-11-27 16:05:43'),
(649, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101142/se-shop/products/w1uhetg3vxufxtlgv2j0.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 799, 80, '2023-11-27 16:05:44', '2023-11-27 16:05:44'),
(650, 7, 'Sony Oled TV', 'Sony Oled  TV', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701101144/se-shop/products/nhbrdzpaplxa7prpjfxk.webp', '4K | IPS', 'Windows', 'AMD', 1, 1, 1.20, 555, 'Blue', 999, 80, '2023-11-27 16:05:47', '2023-11-27 16:05:47');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `description`, `createdAt`, `updatedAt`) VALUES
(1, 'Admin', 'Administrators have full rights on the system', '2023-11-26 16:12:48', '2023-11-26 16:12:48'),
(2, 'Staff', 'Sales staff', '2023-11-26 16:13:44', '2023-11-26 16:13:44'),
(3, 'Customer', 'Potential customers of the system', '2023-11-26 16:14:16', '2023-11-26 16:14:16');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `roleId` tinyint(3) UNSIGNED DEFAULT 3,
  `lastName` varchar(50) DEFAULT NULL,
  `firstName` varchar(30) DEFAULT NULL,
  `imageUrl` varchar(200) DEFAULT NULL,
  `phoneNumber` varchar(11) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(100) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(200) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `roleId`, `lastName`, `firstName`, `imageUrl`, `phoneNumber`, `email`, `address`, `username`, `password`, `createdAt`, `updatedAt`, `active`) VALUES
(1, 1, 'Kiet', 'Doan Anh', 'https://res.cloudinary.com/dtnpgltl4/image/upload/v1701524065/se-shop/avatars/fosl7a4zqb3uaha814iu.webp', '0834480241', 'user@gmail.com', 'Q. Tân Bình, TP. Hồ Chí Minh', 'adminadmin', '$2y$10$qIgrAx3YW0dYj54RBhiRXOXV7KG.SCo4RzZ/R3wu5Q/CXamY6vBSq', '2023-11-28 11:18:46', '2023-12-02 13:34:25', 1),
(2, 1, 'Dac', 'Lam', 'http://localhost/se-php/uploads/10f-9.png', '0832038769', 'admin@gmail.com', 'Ben Tre City', 'daclam', '$2y$10$lv5S.6RGSVrjpvraIL4C3.EchaG2RATyYWN5T9Dvff1cJkH/zeYmm', '2023-12-02 13:37:21', '2023-12-02 13:40:40', 1),
(21, 3, 'trannguyen', 'daclam', 'http://localhost/uploads/10f-8.png', NULL, 'daclam@gmail.com', NULL, 'daclam123', '$2y$10$FLDs7XBsGlKKaCznx1dWW.8bftXnM.O1LDvYEVNzBYiCwEq9yxoTK', '2024-03-06 03:36:06', '2024-03-06 03:36:06', 1),
(22, 3, 'halo', 'halo', 'http://localhost/se-php/uploads/10f-8.png', '0934581249', 'halo@gmail.com', 'SAI GON TOWER', 'halohalo', '$2y$10$groqtaBNN.enRmCZxIQ3febO4mh.o9bXK3XwsA6mzFKlbBM4jb6ay', '2024-03-06 03:58:40', '2024-03-06 03:58:40', 1),
(43, 3, 'Lam', 'DAc', NULL, NULL, 'trannguyendaclam@gmail.com', NULL, 'trannguyendaclam', '$2y$10$fK2.cTojsol53jg1ZAQ8SuKtrXyn90EqLUwK4Bu9Voi0xniZEB5IW', '2024-03-12 01:59:23', '2024-03-12 01:59:23', 1);

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `autoCreateCart` AFTER INSERT ON `user` FOR EACH ROW BEGIN
    INSERT INTO cart (userId) VALUES (NEW.id);
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `cartdetail`
--
ALTER TABLE `cartdetail`
  ADD PRIMARY KEY (`cartId`,`productId`),
  ADD KEY `productId` (`productId`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orderStatusId` (`orderStatusId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`orderId`,`productId`),
  ADD KEY `productId` (`productId`);

--
-- Indexes for table `orderstatus`
--
ALTER TABLE `orderstatus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_id` (`invoice_id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryId` (`categoryId`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phoneNumber` (`phoneNumber`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `roleId` (`roleId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=651;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `Cart_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`);

--
-- Constraints for table `cartdetail`
--
ALTER TABLE `cartdetail`
  ADD CONSTRAINT `CartDetail_ibfk_1` FOREIGN KEY (`cartId`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `CartDetail_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `product` (`id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `Order_ibfk_1` FOREIGN KEY (`orderStatusId`) REFERENCES `orderstatus` (`id`),
  ADD CONSTRAINT `Order_ibfk_3` FOREIGN KEY (`userId`) REFERENCES `user` (`id`);

--
-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `User_ibfk_2` FOREIGN KEY (`roleId`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
