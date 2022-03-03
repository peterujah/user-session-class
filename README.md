# user-class
Php User session class handler

## Installation

Installation is super-easy via Composer:
```md
composer require peterujah/user-session-class
```

# USAGES

Initialize DBController with configuration array

```php
use Peterujah\NanoBlock\User;
$user = new User(User::LIVE);
```

OR as a guest

```php
use Peterujah\NanoBlock\User;
$guest = new User(User::GUEST);
```

Or extend `User` class to create a custom `Admin` class handler
```php
class Admin extends  \Peterujah\NanoBlock\User{
    const GUEST = "_admin_guest_class_";
    const LIVE = "_admin_live_class_";
    public function __construct($db){
        $this->db = $db;
        $this->index = "index";
        $this->userTable = "admin_table_name";
        $this->userIdentifier = "admin_id";
        /*
            Or you can se full query here
            $this->setUserQuery("
                SELECT *
                FROM admin_table_name 
                WHERE admin_id = :check_user_key
                LIMIT 1
            ");
        */
    }

    /**
        Create additional method for admin
    */
    public function setLastLogin($ip){
        $this->conn()->prepare("
            UPDATE {$this->userTable}
            SET admin_last_login_date = NOW(),
                admin_last_login_ip = :admin_last_login_ip  
            WHERE {$this->userIdentifier} = :admin_id
            LIMIT 1
        ");
        $this->conn()->bind(":admin_last_login_ip", $ip);
        $this->conn()->bind(":admin_id", $this->id());
        $this->conn()->execute();		
        $this->conn()->free();
        return $this;
    }
}
```

Call the custom `Admin` session class
```php
$admin = new Admin(Admin::LIVE);
```
