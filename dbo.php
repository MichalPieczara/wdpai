
<?php


class DataBase {
    private $host = "";
    private $db_name = "";
    private $db_user = "";
    private $db_password = "";


    function __construct($host,$name,$user,$password) {
        $this->host = $host;
        $this->db_name = $name;
        $this->db_user = $user;
        $this->db_password = $password;
        
    }
   
    function connect()
    {
        $pdo = null;

        try {
            $dsn = "pgsql:host=$this->host;port=5432;dbname=$this->db_name;";
           
            $pdo = new PDO($dsn, $this->db_user, $this->db_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            if ($pdo) {
              // success
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        } 

        return $pdo;
    }


    function login($login,$pass)
    {
       
        
        $conn = $this->connect();

        $pass=md5($pass);
    
        $statement = $conn->prepare("SELECT * FROM users WHERE login=:login AND password=:password");

        $statement->execute([
            ':login' => $login,
            ':password' => $pass
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($rows) == 0 )
            return -1;               

        return $rows[0]['id']; //zwracamy id zalogowanego usera
  
    }

    function registration($login,$pass,$pass2,$name,$surname,$birth_date,$email,$address,$education,$interests)
    {
     
        $conn = $this->connect();
        if($conn)
        {
            //sprawdź czy taki user już istnieje
            $statement = $conn->prepare("SELECT * FROM users WHERE login=:login");

            $statement->execute([
                ':login' => $login
            ]);

            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($rows) > 0 )
                return false;               

            //jesli nie istnieje to dodaj nowego usera do bazy
            $pass=md5($pass);
            $interests = implode ("," , $interests);

            $statement = $conn->prepare("INSERT INTO users (login, name, surname,birth_date,password, email, address,education,interests)
                                VALUES (:login,:name,:surname,:birth_date,:password,:email,:address,:education,:interests)"); 


            $statement->execute([
                ':login' => $login,
                ':name' => $name,
                ':surname'=> $surname,
                ':birth_date'=> $birth_date,
                ':password'=> $pass,
                ':email'=> $email,
                ':address'=> $address,
                ':education'=> $education,
                ':interests'=> $interests                
            ]);

            return true;
        }
        
    }
    
    



    function product_list() {

        $conn = $this->connect();
        if($conn)
        {
      
            $result = $conn->query("SELECT * FROM products"); 
        
            $results = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
           
                $results[] = array(
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "model" => $row['model'],
                    "description" => $row['description'],
                    "price" => $row['price']
                );
        
            
                    
            }
          
            return $results;
        }
    }

    // dodajemy produkt do koszyka
    function cart_add($client_id, $product_id,$product_count ){
      
        if($product_count <= 0)
        {
            return "Niedozwolona ilość = " . $product_count . "<br>";
        }
        else
        {
          
            $conn = $this->connect();
            if($conn)
            {
              
                $statement = $conn->prepare("SELECT * FROM cart WHERE product_id=:product_id AND client_id=:client_id AND requested=0");
    
                $statement->execute([
                    ':product_id' => $product_id,
                    ':client_id' => $client_id 
                ]);
    
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                
                if(count($rows) >=1 )
                {
                  
                    $statement = $conn->prepare("UPDATE cart SET product_count=product_count+:product_count WHERE product_id=:product_id AND client_id=:client_id");
    
                    $statement->execute([
                        ':product_id' => $product_id,
                        ':client_id' => $client_id,
                        ':product_count' => $product_count      
                    ]);

                }    
                else
                {
                    
                  
                    $statement = $conn->prepare("INSERT INTO cart(client_id,product_id,product_count) VALUES(:client_id,:product_id,:product_count)");
    
                    $statement->execute([
                        ':product_id' => $product_id,
                        ':client_id' => $client_id,
                        ':product_count' => $product_count  
                        ]);
                }           
    
            }

          
    
            return "Produkt id=". $product_id. " został dodany";
        }
      
    }

    //usuwanie produktu z koszyka
    function cart_remove($client_id,$product_id ){
 
        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("DELETE FROM cart WHERE product_id=:product_id AND client_id=:client_id");

            $statement->execute([
                ':product_id' => $product_id,
                ':client_id' => $client_id 
            ]);
              

        }


    
        return "Produkt został usunięty";
    }

    //ustawiamy zamowienia usera do realizacji
    function make_order($client_id )
    {

        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("UPDATE cart SET requested=1 WHERE client_id=:client_id");

            $statement->execute([
                ':client_id' => $client_id 
            ]);
        }

        return "Zamwienie zostao złożone";
    }

    
    // funkcja zwraca zawartość koszyka dla podanego usera
    function cart_view($client_id, $show_all=false )
    {


        $conn = $this->connect();
        if($conn)
        {
            if($show_all)
                $statement = $conn->prepare("SELECT * FROM cart INNER JOIN products ON cart.product_id = products.id WHERE client_id=:client_id");
            else
                $statement = $conn->prepare("SELECT * FROM cart INNER JOIN products ON cart.product_id = products.id WHERE client_id=:client_id AND requested=0");

            $statement->execute([
              
                ':client_id' => $client_id 
            ]);

            $results = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           
                $results[] = array(
                    "id" => $row['id'],
                    "product_id" => $row['product_id'],
                    "name" => $row['name'],
                    "model" => $row['model'],
                    "description" => $row['description'],
                    "price" => $row['price'],
                    "product_count" => $row['product_count'],
                    "requested" => $row['requested']
                );
        
            
                    
            }
          
            return $results;
        }


    }

    /*
    wywolujemy tutaj widok z bazy, który zwróci nam zdarzenia z tabeli audit
    w tabeli audit bedą odkładać się zdarzenia wywołane za pomocą trigera
    
    --------------------------------------
    --- funckcja wywoływana przez trigger
    --------------------------------------

    CREATE OR REPLACE FUNCTION audit() RETURNS TRIGGER AS $$
    BEGIN
        IF (TG_OP = 'UPDATE') THEN
            IF ( NEW.product_count > 10 ) THEN
                INSERT INTO audit(date,description,client_id) 
                    VALUES(now(),'Uzupełniono ilość towaru większą niz 10',NEW.client_id);
                RETURN NEW;
            END IF;
        END IF;
        IF (TG_OP = 'INSERT') THEN
            IF ( NEW.product_count > 10 ) THEN
                INSERT INTO audit(date,description,client_id) 
                    VALUES(now(),'Dodano ilość towaru wiekszą niż 10',NEW.client_id);
                RETURN NEW;
            END IF;
        END IF;
        RETURN NULL;
    END;
    $$ LANGUAGE plpgsql;

    --------------------------------------
    --- trigger
    --------------------------------------

    CREATE TRIGGER taudit
    AFTER INSERT OR UPDATE OR DELETE ON cart
        FOR EACH ROW EXECUTE PROCEDURE audit();

    --------------------------------------
    --- widok na dane w tabeli audyt
    --------------------------------------

    CREATE VIEW audit_view AS
    SELECT audit.date,audit.description, users.login, users.email FROM audit
    LEFT JOIN users ON audit.client_id = users.id


    */


    function audit_log()
    {


        $conn = $this->connect();
        if($conn)
        {
      
            $result = $conn->query("SELECT * FROM audit_view"); 
                  
            $results = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
           
                $results[] = array(
                    "date" => $row['date'],
                    "description" => $row['description'],   
                    "login" => $row['login'],
                    "email" => $row['email']             
                );
        
            
                    
            }
          
            return $results;
        }


    }

    /*
    Widok na zamówienia

    utworzony za pomocą:
    CREATE VIEW orders_view AS
        SELECT users.login, users.email, products.name,cart.product_count, products.price * cart.product_count as value, cart.requested FROM cart
        INNER JOIN users ON cart.client_id = users.id
        INNER JOIN products ON cart.product_id = products.id
            

    */
    function orders()
    {


        $conn = $this->connect();
        if($conn)
        {
      
            $result = $conn->query("SELECT * FROM orders_view"); 
                  
            $results = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
           
                $results[] = array(
                    "login" => $row['login'],
                    "email" => $row['email'],   
                    "name" => $row['name'],
                    "product_count" => $row['product_count'],             
                    "value" => $row['value'],  
                    "requested" => $row['requested']                

                );
        
            
                    
            }
          
            return $results;
        }


    }


    /*zwraca ilość zalogowanych userów, która pobierana jest z bazy przez wywołanie zdefiniowanej w bazie funkcji SQL get_users_count
     funkcja SQL utworzona została za pomocą polecenia:
     create function get_users_count()
        returns int
        language plpgsql
        as
        $$
            declare
            users_count integer;
            begin
            select count(*) 
            into users_count
            from users;   
            return users_count;
            end;
        $$;
        */

    function users_count()
    {
        $conn = $this->connect();
        if($conn)
        {
            $result = $conn->query("SELECT get_users_count()"); 
            $row = $result->fetchAll();
            return $row[0]['get_users_count'];
        }
    }

    function product_add( $product_name,$product_model,$product_description,$product_price)
    {

        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("INSERT INTO products(name,model,description,price) VALUES(:name,:model,:description,:price)");

            $statement->execute([
                ':name' => $product_name,
                ':model' => $product_model,
                ':description' => $product_description,
                ':price' => $product_price   
            ]);

                      

        }
   
    }

    function product_change($product_name,$product_model,$product_description,$product_price,$product_id)
    {
        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("UPDATE products SET name=:name,model=:model,description=:description,price=:price WHERE id=:id");

            $statement->execute([
                ':id' => $product_id,
                ':name' => $product_name,
                ':model' => $product_model,
                ':description' => $product_description,
                ':price' => $product_price   
            ]);

                       

        }


    }

    function product_remove($product_id)
    {
        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("DELETE FROM products WHERE id=:id");

            $statement->execute([
                ':id' => $product_id
            ]);
            

        }

    }

    function user_list()
    {
        $conn = $this->connect();
        if($conn)
        {
      
            $result = $conn->query("SELECT * FROM users"); 
        
            $results = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
           
                $results[] = array(
                    "id" => $row['id'],
                    "login" => $row['login'],
                    "name" => $row['name'],
                    "surname" => $row['surname'],
                    "email" => $row['email'],
                    "status" => $row['status']
                
                
                );
        
            
                    
            }
          
            return $results;
        }


    
    }


    function user_change( $user_login,$user_name,$user_surname,$user_email,$user_status,$user_id){
      
        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("UPDATE users SET login=:login,name=:name,surname=:surname,email=:email,status=:status WHERE id=:id");

            $statement->execute([
                ':login' => $user_login,
                ':name' => $user_name,
                ':surname' => $user_surname,
                ':email' => $user_email,
                ':status' => $user_status,
                ':id' => $user_id   
            ]);

                       

        }

    
     
    }

    function user_remove($client_id ){
      
        $conn = $this->connect();
        if($conn)
        {
          
            $statement = $conn->prepare("DELETE FROM users WHERE id=:id");

            $statement->execute([
                ':id' => $client_id 
            ]);

                        

        }

    
    }



}


?>