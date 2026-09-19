<div class="sidebar">

    <h2>Sistema 360</h2>

    <a
href="dashboard.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'dashboard.php'
? 'active'
: ''
?>"
>

    Dashboard

</a>

   <a
href="productos.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'productos.php'
? 'active'
: ''
?>"
>

    productos

</a>

    <a
href="clientes.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'clientes.php'
? 'active'
: ''
?>"
>

    Clientes

</a>

   <a
href="proveedores.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'proveedores.php'
? 'active'
: ''
?>"
>

    Proveedores

</a>
    <a
href="compras.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'compras.php'
? 'active'
: ''
?>"
>

    Compras

</a>
    <a
href="ventas.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'ventas.php'
? 'active'
: ''
?>"
>

    Ventas

</a>
    <a
href="contabilidad.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'contabilidad.php'
? 'active'
: ''    
?>"
>

    Contabilidad

</a>
    <a
href="logout.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'logout.php'
? 'active'
: ''
?>"
>

    Logout

</a>
<a
href="usuarios.php"

class="<?=
basename($_SERVER['PHP_SELF'])
== 'usuarios.php'
? 'active'
: ''
?>"
>

    Usuarios

</a>
</div>
<button class="dark-btn" onclick="toggleDark()" id="themeBtn">
    🌙
</button>

<script>

const btn = document.getElementById("themeBtn");

function updateIcon(){

    if(document.body.classList.contains("dark")){

        btn.innerHTML = "☀️";

    }else{

        btn.innerHTML = "🌙";

    }

}

function toggleDark(){

    document.body.classList.toggle("dark");

    localStorage.setItem(
        "darkmode",
        document.body.classList.contains("dark")
    );

    updateIcon();

}

if(localStorage.getItem("darkmode") === "true"){

    document.body.classList.add("dark");

}

updateIcon();

</script>
