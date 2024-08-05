<?php
use NhatHoa\Framework\Database\Connector;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(Connector $connector): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS stores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            address VARCHAR(255),
            coordinates VARCHAR(255),
            province_id INT, 
            district_id INT,
            FOREIGN KEY (province_id) REFERENCES provinces(id) ON DELETE CASCADE,
            FOREIGN KEY (district_id) REFERENCES province_districts(id) ON DELETE CASCADE
        )";
        $connector->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(Connector $connector): void
    {
        $sql = "DROP TABLE IF EXISTS stores";
        $connector->exec($sql);
    }
};