INSERT INTO `product` (`id`, `name`, `price`, `stock`) VALUES
	(1, 'Jablko', 10.3, 3),
	(2, 'Hřib', 15.5, 5),
	(3, 'Ořech', 3.7, 7),
	(4, 'Vlašský ořech', 6.3, 10),
	(5, 'Mrkev', 8.9, 13),
	(6, 'Malina', 2.3, 15),
	(7, 'Sklenice žluťoučkého medíku', 42, 1);

INSERT INTO `product_price_change` (`product_id`, `old_price`, `new_price`, `created_at`) VALUES
	(1, 5.2, 7.3, '2025-01-01 12:00:00'),
	(1, 7.3, 10.3, '2025-01-02 13:00:00'),
	(2, 9.6, 12.7, '2025-01-03 14:00:00'),
	(2, 12.7, 15.5, '2025-01-04 15:00:00');
