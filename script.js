

function showMessage(text)
{
    msg = document.getElementById("message");
    msg.style.display = "flex";

    msg = document.getElementById("message-content");
    msg.innerHTML = text;

}

function hideMessage()
{
    msg = document.getElementById("message");
    msg.style.display = "none";

}

function make_order()
{
    fetch("index.php?make_order").then(response => response.text()).then(data => {
        showMessage(data);
        cart_view();
    }
    ).catch((error) => {
        showMessage('make order error ', error);
      });
}





function cart_view()
{

    fetch("index.php?cart_view_fetch").then(response => response.text()).then(data => {
        cart_div = document.getElementById("cart_view");
        cart_div.innerHTML = data;
    }
    ).catch((error) => {
        showMessage('cart view error ', error);
      });


}


function cart_add(product_id)
{
    product_count = document.getElementById("product_"+ product_id + "_count").value;


    fetch("index.php?cart_add_fetch",
    {
     method:'POST',
     headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
     body:"product_id="+product_id+"&product_count="+product_count}
    ).then(response => response.text()).then(data => {
        showMessage(data);
    }
    ).catch((error) => {
        showMessage('cart add error ', error);
      });

}


function cart_remove(product_id)
{

    fetch("index.php?cart_remove_fetch",
    {
     method:'POST',
     headers: {'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'},
     body:"product_id="+product_id}
    ).then(response => response.text()).then(data => {
        showMessage(data);
        cart_view();
    }
    ).catch((error) => {
        showMessage('car remove error ', error);
      });


}