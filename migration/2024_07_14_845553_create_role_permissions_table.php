<?php
use NhatHoa\Framework\Database\Connector;

return new class
{
    /**
     * Run the migrations.
     */
    public function up(Connector $connector): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS role_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_id INT,
            permission_id INT,
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
            FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
        )";
        $connector->exec($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(Connector $connector): void
    {
        $sql = "DROP TABLE IF EXISTS role_permissions";
        $connector->exec($sql);
    }
};