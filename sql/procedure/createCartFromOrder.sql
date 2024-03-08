-- *********************************************CHECKOUT WITH SOME PRODUCTS IN CART*********************************************
DELIMITER $$

CREATE PROCEDURE `createCartFromOrder`(
    IN p_orderId INT,
    OUT p_cartId INT,
    OUT p_errorMessage VARCHAR(255)
)
create_cart_from_order:BEGIN
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
END $$

DELIMITER ;