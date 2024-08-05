<?php
use NhatHoa\Framework\Database\Connector;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(Connector $connector): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS inventory (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(50),
            color_id INT NULL,
            size VARCHAR(50) NULL,
            store_id INT,
            stock INT,
            price INT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (color_id) REFERENCES product_colors(id) ON DELETE CASCADE,
            FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
        )";
        $connector->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(Connector $connector): void
    {
        $sql = "DROP TABLE IF EXISTS inventory";
        $connector->exec($sql);
    }
};