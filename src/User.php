<?php

    class User
    {
        private $name;
        private $password;
        private $id;

        function __construct($name, $password, $id = null)
        {
            $this->name = $name;
            $this->password = $password;
            $this->id = $id;
        }

        function getName()
        {
            return $this->name;
        }

        function getId() {
            return $this->id;
        }

        function save()
        {
            $exec = $GLOBALS['DB']->prepare("INSERT INTO users (name, password) VALUES (:name, :password)");
            $exec->execute([':name' => $this->getName(), ':password' => $this->password]);
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function updateName($new_name)
        {
            $exec = $GLOBALS['DB']->prepare("UPDATE users SET name = :name WHERE id = :id;");
            $exec->execute([':name' => $new_name, ':id' => $this->getId()]);
            $this->name = $new_name;
        }

        function verifyPassword($password)
        {
            if ($this->password == $password) {
                return true;
            }
        }

        static function find($id)
        {
            $found_user;
            $users = User::getAll();
            foreach ($users as $user) {
                if ($user->getId() == $id) {
                    $found_user = $user;
                }
            }
            return $found_user;
        }

        static function logIn($username, $password)
        {
            $users = User::getAll();
            foreach ($users as $user) {
                if ($user->getName() == $username && $user->verifyPassword($password)) {
                    $_SESSION['user'] = $user;
                }
            }
        }

        static function getAll()
        {
            $users = [];
            $returned_users = $GLOBALS['DB']->query("SELECT * FROM users;");

            foreach ($returned_users as $user) {
                $name = $user['name'];
                $password = $user['password'];
                $id = $user['id'];
                $new_user = new User($name, $password, $id);
                array_push($users, $new_user);
            }
            return $users;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM users;");
        }
    }

?>
