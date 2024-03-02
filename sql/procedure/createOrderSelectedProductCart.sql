-- *********************************************CHECKOUT WITH SOME PRODUCTS IN CART*********************************************
DELIMITER $$

CREATE PROCEDURE createOrderSelectedProductCart(
    IN p_userId INT,
    IN p_productIds VARCHAR(255), -- Comma-separated string of product IDs
    IN p_shipAddress VARCHAR(200),
    IN p_phoneNumber VARCHAR(11),
    OUT p_orderId INT,
    OUT p_errorMessage VARCHAR(255)
)
create_order_selected_product_cart:BEGIN
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
    FROM `user` U JOIN `cart` C ON U.id = c.userId
    JOIN `cartdetail` CD ON C.id = CD.cartId 
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
        INSERT INTO `orderdetail` (orderId, productId, quantity, price)
        SELECT p_orderId, cd.productId, cd.quantity, p.price
        FROM `cartdetail` cd JOIN `product` p ON cd.productId = p.id
        WHERE cd.cartId = cartId AND cd.productId = productId;

        -- Delete selected product from cart
        DELETE FROM `cartdetail` WHERE `cartdetail`.cartId = cartId AND `cartdetail`.productId = productId;
    END LOOP;

    -- Commit the transaction
    COMMIT;

END $$

DELIMITER ;


-- TEST IN DB:
-- Example call to the stored procedure
CALL ProcessSelectedCheckout(1, 100, '1,3,5', '123 Main St', '555-1234', @orderId);

-- After the call, you can retrieve the orderId from the output parameter
SELECT @orderId;

-- TEST IN PHP PDO:
    -- $userId = 1;
    -- $productIds = "1,2,3"; // Replace with comma-separated product IDs
    -- $shipAddress = '123 Main St';
    -- $phoneNumber = '555-1234';

    -- $stmt->execute();

    -- // Close the cursor to allow accessing the output parameter
    -- $stmt->closeCursor();

    -- // Retrieve the orderId using a separate query
    -- $result = $pdo->query("SELECT @orderId as orderId");
    -- $row = $result->fetch(PDO::FETCH_ASSOC);
    -- $orderId = $row['orderId'];

    -- echo "Order ID: " . $orderId;
