-- *********************************************CHECKOUT WITHOUT CART, DIRECTLY FROM PRODUCT INSTEAD*********************************************
DELIMITER $$

CREATE PROCEDURE createOrderDirectlyProduct(
    IN p_userId INT,
    IN p_productId INT,
    IN p_quantity INT,
    IN p_shipAddress VARCHAR(200),
    IN p_phoneNumber VARCHAR(11),
    OUT p_orderId INT,
    OUT p_errorMessage VARCHAR(255)
)
create_order_directly_product:BEGIN
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

END $$

DELIMITER ;
