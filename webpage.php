<?php

class WebPage{

    protected $db;
    protected $login;
    protected $website_name;
    protected $list_title;
    protected $orders_email;

    function __construct($db,$website_name,$list_title,$orders_email,$login) {
        $this->db = $db;
        $this->login = $login;
        $this->website_name=$website_name;
        $this->list_title=$list_title;
        $this->orders_email = $orders_email;
    }
   

    function render()
    {
        ?>
        <html>
        <head>
        <title>
        <?php echo ($this->website_name);?></title>
          <meta charset="UTF-8">
          <link rel="stylesheet" href="style.css">
          <script src="script.js"></script>
        </head> 
        <body>
        
        <center>
        
        <!-- warstwa do wyświetlania komunikatu. Na początku jest oznaczona jako ukryta -->
        <div id="message">
        <div id="message-content">
        Test message
        </div>
        <button id="message-close-button" onClick="hideMessage();">Close</button>    
        </div>
        
        
        
        <h1><?php 
     
        echo ($this->website_name);
        ?></h1>
        <?php 
        
        echo ("Ilość zarejestrowanych użytkowników: " . $this->db->users_count());
        ?>
        <br><br><br>
        
        <?php
       
        if($this->login=="")
        {
            echo "<a href='login.php'>Logowanie</a><br>";
            echo "<a href='rejestracja.php'>Rejestracja</a><br><br>";

        }
        else
        {
            echo("Zalogowany jako: " . $this->login );        
            echo ("<br><a href='login.php?logout'>Wyloguj</a><br><br>");

        }
        
        
        if(isset($_GET['cart_add']))
        {
            if(!isset( $this->login))
            {
                echo "Musisz być zalogowany<br><br><a href='index.php'>Strona główna</a>";
                exit();
            }
            $this->cart_add();
        }
        else if(isset($_GET['cart_remove']))
        {
            if(!isset( $this->login))
            {
                echo "Musisz być zalogowany<br><br><a href='index.php'>Strona główna</a>";
                exit();
            }
            $this->cart_remove();
        }
        else if(isset($_GET['cart_view']))
        {
            if(!isset( $this->login))
            {
                echo "Musisz być zalogowany<br><br><a href='index.php'>Strona główna</a>";
                exit();
            }
            $this->cart_view();
        }
        else
        {
            $this->product_list();
        }
        
        ?>
        
        </center>
        
        
        </body>
        </html>
        <?php

    }


////////////////////////////////////////////////////////////////////////////////////

function cart_add(){
    $product_id = $_POST["product_id"];
    $product_count = $_POST["product_count"];
    $client_id = $_SESSION['client_id'];
    $result = $this->db->cart_add($client_id,$product_id,$product_count);
    echo ($result);
}

    ////////////////////////////////////////////////////////////////////////////////////

    function cart_remove(){
        $product_id = $_POST["product_id"];
        $client_id = $_SESSION['client_id'];

        $result = $this->db->cart_remove($client_id,$product_id);
        echo ($result);
    }


 ////////////////////////////////////////////////////////////////////////////////////

    function make_order(){
        $client_id = $_SESSION['client_id'];
        $this->db->make_order($client_id);
        echo("Zamówienie zostało złożone");
    }

////////////////////////////////////////////////////////////////////////////////////

function cart_view()
{
    if($this->login=="")
        return;

    $client_id = $_SESSION['client_id'];
    $rows = $this->db->cart_view($client_id);
    
    $li=0;

    $order = "";
    echo "<div id='cart_view'>
    <table><tr>
    <th>nazwa</th>
    <th>model</th>
    <th>opis</th>
    <th>cena</th>
    <th>ilość</th>
    <th>akcja</th>
    </tr>";
    
    $pcount = 0;

    $sum = 0;

    for($i = 0; $i < count($rows); ++$i) 
    {
        $row = $rows[$i];
        echo "<tr>
        <td>".$row['name']."</td>
        <td><img src='".$row['model']."'></td>
        <td>".$row['description']."</td>
        <td>".$row['price']." zł</td>
        <td>".$row['product_count']."</td>
        <td>
        <button onClick='cart_remove(".$row['id'].");'>Usuń</button>
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
        echo("<br><a href='index.php'>Brak zamówień. Przejdź do strony głownej</a>"); 
    }
    else
    {
      
        echo("Łączna kwota zamówienia: " . $sum . " zł<br><br>");
       // echo("<a href='mailto:" . $this->orders_email . "?subject=Zamówienie dla ".$client_id ."&body=".$order."'>Złóż zamówienie</a><br>");
       echo("
       <a href='#' onClick='make_order();'>Złóż zamówienie</button><br>
       <a href='index.php'>Kontynuuj zakupy</a>
       ");
       
    }

    echo "</div>";
  
      
    
}



    ////////////////////////////////////////////////////////////////////////////////////

    function product_list()
    {

        
        
        $li=0;
        echo("<b>" . $this->list_title . "</b><br><br>");
        echo("<table>");
        echo "<tr>";
        echo "<th>nazwa</th>";
        echo "<th>model</th>";
        echo "<th>opis</th>";
        echo "<th>cena</th>";
        echo "<th>akcja</th>";
        echo "</tr>";
        $rows = $this->db->product_list();
        
    for($i = 0; $i < count($rows); ++$i) 
    {
            $row = $rows[$i];
            echo "<tr>";
            $li++;
            echo "<td>".$row['name']."</td>";
            echo "<td><img src='".$row['model']."'></td>";
            echo "<td>".$row['description']."</td>";
            echo "<td>".$row['price']." zł</td>";
            echo "<td>";
            echo "
            <button onClick='cart_add(".$row['id'].");'>Dodaj</button>

            <input type='text' id='product_".$row['id']."_count' size='4' value='0'>&nbspsztuk
            ";
            echo "</td>";
            echo "</tr>";
        
                
        }
        echo("</table>");


        
        ?>
        <br>
        <?php
        if($this->login!="")
        {
            echo ("<a href='index.php?cart_view'> Sprawdź zamówienie </a>");
        }
    }

    function render_login()
    {
        ?>
        <html>
        <head>
        <title><?php echo ($this->website_name);?></title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        </head>

        <center>
        <h1><?php 
      
        echo ($this->website_name);
        ?></h1>
        <br><br>

        <?php
        if(isset($_GET["logout"]) )
        {
            session_destroy();
            ?>
                
                Wylogowano<br><br>
                <a href='login.php'>Zaloguj ponownie</a><br>
                <a href='index.php'>Strona główna</a><br>
                
            <?php
            exit();
        }

        if (isset($_SESSION['login'])) 
        {
            $this->login = $_SESSION['login'];

            ?>

            Jesteś zalogowany jako <?php echo($this->login) ?><br><br>
            <a href='login.php?logout'>Wyloguj</a><br>
            <a href='.'>Przejdź dalej</a>


            <?php

            exit();
        }



        if(isset($_POST["login"]) && isset($_POST["pass"]))
        {
            $this->login = $_POST['login'];
            $this->pass = $_POST['pass'];
            $user_id = $this->db->login($this->login,$this->pass);



            if($user_id==-1)
            {
            ?>
                Zła nazwa użytkonika lub hasło<br><br>
                <a href='javascript:history.back()'>Spróbuj ponownie</a>
            
            <?php
            exit();
            }
            else if (!isset($_SESSION['login'])) 
            {
            
                $_SESSION['login'] = $this->login;
                $_SESSION['client_id'] = $user_id;
            
                echo ("Użytkownik ". $this->login. " został zalogowany<br><br>");
                echo ("<a href='login.php?logout'>Wyloguj</a><br>");
            

                echo ("<a href='index.php'>Strona główna</a>");

                exit();
            }
        }


        ?>



        <form method='post'>
        login&nbsp;&nbsp;<input type='text' name='login' size='15'><br>
        <br>
        haslo&nbsp;<input type='password' name='pass' size='15'><br>
        <br>
        <input class="button" type='submit' value='zaloguj' >

        </form>


        <br>
        <a href='rejestracja.php'>Rejestracja</a><br>
        <a href='index.php'>Strona główna</a><br>

        </center>
        </body>
        </html>

    <?php
    }


    function render_registration()
    {
        ?>
        <html>
        <body>
        <head>
        <title><?php global $website_name; echo ($website_name);?></title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style.css">
        </head> 
        <body>
        <center>
        <h1><?php global $website_name; echo ($website_name);?></h1>
        Rejestracja<br><br>
        <?php


        if (isset($_GET['send']))
        {

            $login=$_POST['login'];
            $name=$_POST['name'];
            $surname=$_POST['surname'];
            $birth_date=$_POST['birth_date'];
            $email=$_POST['email'];
            $address=$_POST['address'];
            
            $education="";
            if(isset($_POST['education']))
            $education=$_POST['education'];
            
            $interests = "";
            if(isset($_POST['interests']))
            $interests=$_POST['interests'];

            $pass=$_POST['pass'];
            $pass2=$_POST['pass2'];

            

            $this->registration($login,$pass,$pass2,$name,$surname,$birth_date,$email,$address,$education,$interests);

        }
        else
        {
        ?>



            <form method="POST" action="rejestracja.php?send" >

            <table class="regtable" >
            <tr>
                <td><p align="right">login&nbsp;&nbsp;</td>
                <td><input type="text" name="login" size="20" ></td>
                </tr>
                <tr>
                <td><p align="right">imię&nbsp;&nbsp;</td>
                <td><input type="text" name="name" size="20" ></td>
                </tr>
                <tr>
                <td><p align="right">nazwisko&nbsp;&nbsp;</td>
                <td><input type="text" name="surname" size="20" ></td>
                </tr>
                <tr>
                <td><p align="right">data urodzenia&nbsp;&nbsp;</td>
                <td><input type="date" name="birth_date" size="20" ></td>
                </tr>
                <tr>
                <td><p align="right">hasło&nbsp;&nbsp;</td>
                <td><input type="text" name="pass" size="20"></td>
                </tr>
                <tr>
                <td><p align="right">powtórz hasło&nbsp;&nbsp;</td>
                <td><input type="text" name="pass2" size="20" ></td>
                </tr>
                <tr>
                <td><p align="right">e-mail&nbsp;&nbsp;</td>
                <td><input type="text" name="email" size="20" ></td>
                </tr>
                <tr>
                <td><p align="right">adres&nbsp;&nbsp;</td>
                <td><input type="text" name="address" size="20" ></td>
                </tr>
            
                <td><p align="right">wykształcenie&nbsp;&nbsp;</td>
                <td>
        
                <select name="education" >
                    <option value="1">podstawowe</option>
                    <option value="2">średnie</option>
                    <option value="3">wyższe</option>

                </select>
                
                </td>
                </tr>
                <tr>
                <td width="50%"><p align="right">zainteresowania&nbsp;&nbsp;</td>
                <td width="50%">

                <select name="interests[]"   style="border: 1 solid #C0C0C0" multiple>
                <option value="1">geografia</option>
                <option value="2">sport</option>
                <option value="3">muzyka</option>
                <option value="4">film</option>
                <option value="5">programowanie</option>          
                </select>
                </td>
                </tr>
                <tr>
                <td width="50%">            <p align="right">      </td>
                <td width="50%"><input type="submit" value="rejestracja" > </td>
                </tr>
            </table>
            </form>

            <?php
        }
        ?>

        <a href='index.php'>Strona główna</a><br><br>
            
        </center>
        </body>
        </html>
        <?php
    }


//////////////////////////////////////////////////////////////////////////////////////////
    // wersyfikacja emaila
    function vemail($d) {
                if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i",$d)){

                        return 1;}
                else { 
                        echo "<font color='#FF0000'>";
                        printf("błąd w emailu<br>");
                        echo "</font>";
                        return 0;
                        }
        }


//////////////////////////////////////////////////////////////////////////////////////////

    // weryfikacja hasła
    function vpass($a,$b) {
            if($a==$b){
                    if(preg_match("/^\w{4,12}$/i",$b))
                            {
                                    return 1;
                            }
                    else {
                            echo "<font color='#FF0000'>";
                            printf("Błąd w haśle. Hasło musi mieć długość od 4 do 12 znaków i składać się z liter i cyfr<br>");
                            echo "</font>";
                            return 0;
                            }
            }
            else { 
                    echo "<font color='#FF0000'>";
                    printf("błąd w haśle wpisanym ponownie<br>");
                    echo "</font>";
                    return 0;
            }
    }

//////////////////////////////////////////////////////////////////////////////////////////

    function registration($login,$pass,$pass2,$name,$surname,$birth_date,$email,$address,$education,$interests)
    {


    global $db;

    if ($login=="")
    {
        echo "Brak loginu<br><a href='javascript:history.back()'>spróbuj ponownie</a>";
        exit();
    }

    if ($name=="")
    {
        echo "Brak imienia<br><a href='javascript:history.back()'>spróbuj ponownie</a>";
        exit();
    }
    
    if ($surname=="")
    {
        echo "Brak nazwiska<br><a href='javascript:history.back()'>spróbuj ponownie</a>";
        exit();
    }

    
    if ($birth_date=="")
    {
        echo "Brak daty urodzenia<br><a href='javascript:history.back()'>spróbuj ponownie</a>";
        exit();
    }
    else{

        $diff = date_diff( new DateTime(), new DateTime($birth_date));
        if($diff->y<18)
        {
        echo "Musisz mieć co najmniej 18 lat<br><a href='javascript:history.back()'>spróbuj ponownie</a>";
        exit();
    
        }

    }

    if( gettype($interests) != "array")
    {
        echo "Nie wybrano zainteresowań<br><a href='javascript:history.back()'>spróbuj ponownie</a>";
        exit();
    
    }

        $res=0;

        $res+=$this->vemail($email);
        $res+=$this->vpass($pass,$pass2);
        
        echo "<tr><td align='center'>";
            
        if ($res!=2)
        {
            echo" <a href='javascript:history.back()'>spróbuj ponownie</a>";
        }
        else
        {
            
    
        $result = $this->db->registration($login,$pass,$pass2,$name,$surname,$birth_date,$email,$address,$education,$interests);
        
    
        if(!$result)
        {
            echo ("Bład rejestracji lub użytkownik $login istnieje<br><br>");
            echo" <a href='javascript:history.back()'>spróbuj ponownie</a>";
            exit();
        }
        
        
        echo ("<center>");
        echo ("Dziękujemy. Użytkownik $login został zerejestrowany<br><br>");
        echo ("Imię: " . $name . "<br>");
        echo ("Nazwisko: " . $surname . "<br>");
        echo ("Data urodzenia: " . $birth_date . "<br>");
        echo ("Email: " . $email . "<br>");
        echo ("Adres: " . $address . "<br>");
        echo ("Wykształcenie: " . $education . "<br>");
        echo ("Zainteresowania: " . implode(",", $interests) . "<br><br><br>");
        echo ("<a href='login.php'>Logowanie</a>");
        echo ("</center>");
        }

    }



}


    
    ?>