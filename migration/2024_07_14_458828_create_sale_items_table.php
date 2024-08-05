<?php
use NhatHoa\Framework\Database\Connector;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(Connector $connector): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS sale_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_id INT,
            product_id VARCHAR(50),
            quantity INT NOT NULL,
            price INT(10) NOT NULL,
            size VARCHAR(25) DEFAULT NULL,
            color_id INT(11) DEFAULT NULL,
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (color_id) REFERENCES product_colors(id) ON DELETE CASCADE
        )";
        $connector->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(Connector $connector): void
    {
        $sql = "DROP TABLE IF EXISTS sale_items";
        $connector->exec($sql);
    }
};