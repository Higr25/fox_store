CREATE TABLE `product` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `price` FLOAT(6,2) NOT NULL,
    `stock` INT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `product_price_change` (
    `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `product_id` INT NOT NULL,
    `old_price` FLOAT(6,2) NOT NULL,
    `new_price` FLOAT(6,2) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE product_price_change ADD CONSTRAINT fk_product_price_change_product_id FOREIGN KEY (product_id) REFERENCES product (id);