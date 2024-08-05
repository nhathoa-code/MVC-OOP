<?php
use NhatHoa\Framework\Database\Connector;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(Connector $connector): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            store_id INT,
            employee_id INT,
            customer_id INT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            total_amount INT(20) NOT NULL,
            FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
            FOREIGN KEY (employee_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
        )";
        $connector->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(Connector $connector): void
    {
        $sql = "DROP TABLE IF EXISTS sales";
        $connector->exec($sql);
    }
};