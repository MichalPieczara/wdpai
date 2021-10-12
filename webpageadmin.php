<?php

class WebPageAdmin extends WebPage{

    function render()
    {
        ?>
        <html>
        <body>
        <head>
        <title><?php  echo ($this->website_name);?></title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        </head> 
        <body>
        <center>
        <h1><?php 
      
        echo ($this->website_name);
        ?></h1>

        Administracja<br><br>
        <?php

        if($this->login=="admin" || $this->login=="Admin")
        {
            echo("<br><nav class='admin-nsv'>
            <a href='admin.php?user_list'>Administracja użytkownikami</a> |
            <a href='admin.php?product_list'>Administracja produktami</a> |
            <a href='admin.php?audit'>Audyt</a> |
            <a href='admin.php?orders'>Zamówienia</a>
            </nav><br><br>");

        }
        
        if($this->login!="")
        {
            echo("Zalogowany jako: " .$this->login );        
            echo ("<br><a href='login.php?logout'>Wyloguj</a><br><br>");
        }
        else
        {
            echo "<a href='login.php'>Logowanie</a><br>";
            echo "<a href='rejestracja.php'>Rejestracja</a><br><br>";
        }


        //wywołujemy odpowiednią funkcję w zależności od tego jaka zmienna została podana w requeście 

        if(isset($_GET['product_add']))
            $this->product_add();
        else if(isset($_GET['product_change']))
            $this->product_change();
        else if(isset($_GET['product_remove']))
            $this->product_remove();
        else  if(isset($_GET['product_list']))
            $this->product_list();
        else if(isset($_GET['user_change']))
            $this->user_change();
        else if(isset($_GET['user_remove']))
            $this->user_remove();
        else  if(isset($_GET['user_list']))
            $this->user_list();
        else  if(isset($_GET['user_cart']))
            $this->user_cart();
        else  if(isset($_GET['cart_remove']))
            $this->cart_remove();
        else  if(isset($_GET['audit']))
            $this->audit_log();
        else  if(isset($_GET['orders']))
            $this->orders();


        ?>

        </center>
        </body>
        </html>
        <?php

    }



    function product_list()
    {
        

        $rows = $this->db->product_list();


        ?>

        <table>
        <tr>
        <th>nazwa</th>
        <th>model</th>
        <th>opis</th>
        <th>cena</th>
        <th>akcja</th>
        </tr>

        <tr>
            <form method='POST' action='admin.php?product_add'>
                <td><input type='text' name='product_name' size='14' value=''></td>
                <td><input type='text' name='product_model' size='14' value=''></td>
                <td><input type='text' name='product_description'  value=''></td>
                <td><input type='text' name='product_price' size='8' value=''></td>
                <td><input type='submit' value='Dodaj nowy'></td>
            </form>
        </tr>
    
        <?php
        for($i = 0; $i < count($rows); ++$i) 
        {
            $row = $rows[$i];
    
            echo "<tr>
            <form method='POST' action='admin.php?product_change'>
            <td><input type='text' name='product_name' size='14' value='".$row['name']."'></td>
            <td><input type='text' name='product_model' size='14' value='".$row['model']."'></td>
            <td><input type='text' name='product_description'  value='".$row['description']."'></td>
            <td><input type='text' name='product_price' size='8' value='".$row['price']."'></td>
            <td><input type='submit' value='Zmień'><input type='submit' value='Usuń' formaction='admin.php?product_remove'></td>
            <input type='hidden' name='product_id' size='4' value='".$row['id'] ."'>
            </form>
            </tr>";
                
        }
        echo("</table>");


        
            ?>
            <a href='index.php'>Strona główna</a><br>
         
            <?php
    
    }

    ////////////////////////////////////////////////////////////////////////////////////

    function product_add(){
        

        $product_name = $_POST["product_name"];
        $product_model = $_POST["product_model"];
        $product_description = $_POST["product_description"];
        $product_price = $_POST["product_price"];
        $product_price = str_replace(",",".",$product_price);

        if($product_name == "" || $product_model == "" || $product_description == "" || $product_price== "" )
        {
            echo("Wypełnij wszystkie pola<br>");
            echo" <a href='javascript:history.back()'>Powrót</a>";
            return;

        }
        else
        {
        
            $this->db->product_add( $product_name,$product_model,$product_description,$product_price);

        
            echo("Produkt ". $product_name. " został dodany<br>");
            echo" <a href='admin.php?product_list'>Powrót</a>";
        }
    
    }


    ////////////////////////////////////////////////////////////////////////////////////

    function product_change(){
        

        $product_id = $_POST["product_id"];
        $product_name = $_POST["product_name"];
        $product_model = $_POST["product_model"];
        $product_description = $_POST["product_description"];
        $product_price = $_POST["product_price"];
        $product_price= str_replace(",",".",$product_price);

            
        $this->db->product_change( $product_name,$product_model,$product_description,$product_price,$product_id);

        echo("Produkt id=". $product_id. " został zmieniony<br>");
        echo" <a href='admin.php?product_list'>Powrót</a>";

    
    }

    ////////////////////////////////////////////////////////////////////////////////////

    function product_remove(){
        

        $product_id = $_POST["product_id"];


        $this->db->product_remove($product_id);

        ?>

        Produkt został usunięty<br>
        <a href='admin.php?product_list'>Powrót</a>
    
    
        <?php
    }


    ////////////////////////////////////////////////////////////////////////////////////

    function user_list()
    {
        

        ?>

        <table>
        <tr>
        <th>login</th>
        <th>imię</th>
        <th>nazwisko</th>
        <th>email</th>
        <th>status</th>
        <th>akcja</th>
        </tr>
    
        <?php
        $rows = $this->db->user_list();

        for($i = 0; $i < count($rows); ++$i) 
        {
            $row = $rows[$i];
    
            echo "<tr>";
        
            echo "<form method='POST' action='admin.php?user_change'>";
        
            echo "<td><input type='text' name='user_login' size='14' value='".$row['login']."'></td>";
            echo "<td><input type='text' name='user_name' size='14' value='".$row['name']."'></td>";
            echo "<td><input type='text' name='user_surname' size='30' value='".$row['surname']."'></td>";
            echo "<td><input type='text' name='user_email' size='8' value='".$row['email']."'></td>";
            echo "<td><input type='text' name='user_status' size='8' value='".$row['status']."'></td>";
            echo "<td><input type='submit' value='Zmień'><input type='submit' value='Usuń' formaction='admin.php?user_remove'><input type='submit' value='Koszyk' formaction='admin.php?user_cart'></td>";
            echo "<input type='hidden' name='user_id' size='4' value='".$row['id'] ."'>";
        
        
            echo "</form>";
            echo "</tr>";
                
        }
        echo("</table>");
    

        
            ?>
            <a href='index.php'>Strona główna</a><br>
           
            <?php
    
    }


    ////////////////////////////////////////////////////////////////////////////////////

    function user_change(){
        

        $user_id = $_POST["user_id"];
        $user_login = $_POST["user_login"];
        $user_name = $_POST["user_name"];
        $user_surname = $_POST["user_surname"];
        $user_email = $_POST["user_email"];
        $user_status = $_POST["user_status"];
    
        $this->db->user_change( $user_login,$user_name,$user_surname,$user_email,$user_status,$user_id);
    

        echo("Użytkownik id=". $user_id. " został zmieniony<br>");
        echo" <a href='admin.php?user_list'>Powrót</a>";

    
    }
    ////////////////////////////////////////////////////////////////////////////////////

    function user_cart()
    {

        

        $client_id = $_POST["user_id"];


        $li=0;

        $order = "";

        echo("<table>");
        echo "<tr>";
        echo "<th>nazwa</th>";
        echo "<th>opis</th>";
        echo "<th>cena</th>";
        echo "<th>ilość</th>";
        echo "<th>do realizacji</th>";
        echo "<th>akcja</th>";
        echo "</tr>";
        
        $pcount = 0;

        $sum = 0;

        $rows = $this->db->cart_view($client_id,true);

        for($i = 0; $i < count($rows); ++$i) 
        {
            $row = $rows[$i];
            echo "<tr>
            <td>".$row['name']."</td>
            <td>".$row['description']."</td>
            <td>".$row['price']." zł</td>
            <td>".$row['product_count']."</td>
            <td>".$row['requested']."</td>
            <td>
            <form method='POST' action='admin.php?cart_remove'>
            <input type='submit' value='Usuń'>
            <input type='hidden' name='product_id' size='4' value='".$row['product_id']."'>
            <input type='hidden' name='user_id' size='4' value='".$client_id."'></form>
            </td>
            </tr>";
            $pcount++;

            $sum += ($row['price'] * $row['product_count']);

            $order .= "id=" .$row['id'] . ", ". $row['name'] . ", " .$row['price'] . " zł, " .$row['product_count'] . "%0D%0A";
                
        }
        echo("</table>");
        $order .= "suma: " . $sum;



        if($pcount==0)
        {
            echo("<a href='admin.php?user_list'>Brak zamówień. Powrót</a>"); 
        }
        else
        {
            echo("Łączna kwota zamówienia: " . $sum . " zł<br><br>");
            echo("<a href='admin.php?user_list'>Powrót</a>");
        }

        
        
    }

    
    ////////////////////////////////////////////////////////////////////////////////////

    function cart_remove(){
        $product_id = $_POST["product_id"];
        $client_id = $_POST['user_id'];
       
        $result = $this->db->cart_remove($client_id,$product_id);
        ?>

        Produkt został usunięty<br>
        <a href='admin.php?user_list'>Powrót</a>

        <?php
        
    }


    ////////////////////////////////////////////////////////////////////////////////////

    function user_remove(){
        

        $client_id = $_POST["user_id"];


        $this->db->user_remove($client_id);

        ?>

        Użytkownik został usunięty<br>
        <a href='admin.php?user_list'>Powrót</a>
    
    
        <?php
    }

    ////////////////////////////////////////////////////////////////////////////////////

    function audit_log()
    {

        
        $rows = $this->db->audit_log();

        ?>

        <table>
        <tr>
        <th>data</th>
        <th>opis</th>
        <th>login</th>
        <th>email</th>
        </tr>
    
        <?php
        for($i = 0; $i < count($rows); ++$i) 
        {
            $row = $rows[$i];
            echo "<tr>";       
            echo "<td>".$row['date']."</td>";
            echo "<td>".$row['description']."</td>";
            echo "<td>".$row['login']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "</tr>";
                
        }
        echo("</table>");
        
            ?>
            <a href='index.php'>Strona główna</a><br>
         
            <?php

    }

    ////////////////////////////////////////////////////////////////////////////////////

    function orders()
    {

        
        $rows = $this->db->orders();

        ?>

        <table>
        <tr>
        <th>login</th>
        <th>email</th>
        <th>nazwa produktu</th>
        <th>ilość</th>
        <th>wartość</th>
        <th>do realizacji</th>
        </tr>
    
        <?php
        for($i = 0; $i < count($rows); ++$i) 
        {
            $row = $rows[$i];
            echo "<tr>";       
            echo "<td>".$row['login']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "<td>".$row['name']."</td>";
            echo "<td>".$row['product_count']."</td>";
            echo "<td>".$row['value']."</td>";
            echo "<td>".$row['requested']."</td>";
            echo "</tr>";
                
        }
        echo("</table>");
        
            ?>
            <a href='index.php'>Strona główna</a><br>
        
            <?php

    }

}

?>