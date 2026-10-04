<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit;
}

include("config/conexion.php");



/* PROVEEDORES */

$proveedores = mysqli_query(
    $conn,
    "SELECT * FROM proveedores"
);
/* PRODUCTOS */

$productos = mysqli_query(
    $conn,
    "SELECT id, nombre, categoria, precio, stock
     FROM productos
     ORDER BY nombre ASC"
);

/* HISTORIAL */

$compras = mysqli_query(
    $conn,
    "SELECT compras.*,

    proveedores.nombre AS proveedor,
    productos.nombre AS producto

    FROM compras

    INNER JOIN proveedores
    ON compras.proveedor_id = proveedores.id

    INNER JOIN productos
    ON compras.producto_id = productos.id

    ORDER BY compras.id DESC"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Compras</title>

<link rel="stylesheet" href="css/styles.css">

</head>

<body>

<?php include("includes/sidebar.php"); ?>

<div class="main">

    <h1>Compras a Proveedores</h1>

    <div class="form-box">

        <form method="POST">

            <!-- PROVEEDOR -->

            <select
                name="proveedor"
                class="form-control"
                required
            >

                <option value="">
                    Seleccionar proveedor
                </option>

                <?php while($p = mysqli_fetch_assoc($proveedores)){ ?>

                    <option value="<?= $p['id'] ?>">
                        <?= $p['nombre'] ?>
                    </option>

                <?php } ?>

            </select>

            <!-- PRODUCTO -->

            <select
    name="producto_id"
    id="producto"
    class="form-control"
    onchange="cargarProducto()"
    required
>

    <option value="">
        Seleccionar producto
    </option>

    <?php while($prod = mysqli_fetch_assoc($productos)){ ?>

        <option
            value="<?= $prod['id'] ?>"
            data-precio="<?= $prod['precio'] ?>"
            data-categoria="<?= htmlspecialchars($prod['categoria']) ?>"
            data-stock="<?= $prod['stock'] ?>"
        >
            <?= htmlspecialchars($prod['nombre']) ?>
        </option>

    <?php } ?>

</select>

<input
    type="text"
    id="categoria"
    class="form-control"
    placeholder="Categoría"
    readonly
>

<input
    type="number"
    step="0.01"
    id="precio_venta"
    class="form-control"
    placeholder="Precio de venta"
    readonly
>

<input
    type="text"
    id="stock_actual"
    class="form-control"
    placeholder="Stock actual"
    readonly
>

            

            <!-- CANTIDAD -->

            <input
                type="number"
                name="cantidad"
                class="form-control"
                placeholder="Cantidad"
                required
            >

            <!-- MONEDA -->

            <select
                id="moneda"
                class="form-control"
            >

                <option value="USD">USD - Dólar estadounidense</option>
                <option value="EUR">EUR - Euro</option>
                <option value="GBP">GBP - Libra esterlina</option>
                <option value="JPY">JPY - Yen japonés</option>

            </select>

            <!-- PRECIO EN MONEDA EXTRANJERA -->

            <input
                type="number"
                step="0.01"
                id="monto_moneda"
                class="form-control"
                placeholder="Costo unitario del proveedor"
            >

            <!-- BOTON CONVERSIÓN -->

           <button
    type="button"
    class="btn-login"
    onclick="convertirMoneda()"
>
    Consultar tasa y convertir
</button>

            <!-- RESULTADO -->

            <div
                id="resultadoConversion"
                style="
                    margin-top:15px;
                    padding:15px;
                    border-radius:8px;
                    background:#f3f3f3;
                    display:none;
                "
            >

                <strong>Conversión de divisas</strong>

                <p id="textoTasa"></p>

                <p id="textoConversion"></p>

            </div>

            <!-- COSTO UNITARIO EN DOP -->

            <input
                type="number"
                step="0.01"
                name="costo_unitario"
                id="costo_unitario"
                class="form-control"
                placeholder="Costo unitario en DOP"
                required
            >

        


            <!-- REGISTRAR -->

          <button
    type="button"
    class="btn-login"
    onclick="agregarAlCarrito()"
>
    + Agregar al carrito
</button>

        </form>

    </div>
<h2>Carrito de Compra</h2>

<div class="form-box">

    <table class="tabla">

        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Costo Unitario</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody id="carritoBody">

            <tr>
                <td colspan="5">
                    No hay productos agregados.
                </td>
            </tr>

        </tbody>

    </table>

    <h3 style="margin-top:20px;">
        Total de la compra:
        <span id="totalCarrito">RD$0.00</span>
    </h3>

   <button
    type="button"
    class="btn-login"
    id="btnRegistrarCompra"
    onclick="registrarCompra()"
    disabled
>
    Registrar compra completa
</button>

</div>

<br>

<!-- BUSCADOR -->
    <br>

    <!-- BUSCADOR -->

    <input
        type="text"
        id="buscador"
        class="form-control"
        placeholder="Buscar compra por ID, proveedor o producto..."
    >

    <!-- HISTORIAL -->

    <table class="tabla" id="tablaCompras">

        <tr>

            <th>ID</th>
            <th>Proveedor</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Costo Unitario</th>
            <th>Total</th>
            <th>Fecha</th>

        </tr>

        <?php while($row = mysqli_fetch_assoc($compras)){ ?>

        <tr>

            <td><?= $row['id'] ?></td>

            <td><?= $row['proveedor'] ?></td>

            <td><?= $row['producto'] ?></td>

            <td><?= $row['cantidad'] ?></td>

<td>
    RD$<?= number_format($row['costo_unitario'] ?? 0, 2) ?>
</td>
            <td>RD$<?= number_format($row['costo'], 2) ?></td>

            <td><?= $row['fecha'] ?></td>

        </tr>

        <?php } ?>

    </table>

</div>


<script>

function convertirMoneda() {

    const moneda = document.getElementById("moneda").value;
    const monto = parseFloat(
        document.getElementById("monto_moneda").value
    );

    const resultado = document.getElementById("resultadoConversion");
    const textoTasa = document.getElementById("textoTasa");
    const textoConversion = document.getElementById("textoConversion");
    const costoDop = document.getElementById("costo_unitario");

    // Validar monto
    if (isNaN(monto) || monto <= 0) {

        alert("Introduce un monto válido.");

        return;
    }

    // Mostrar cuadro
    resultado.style.display = "block";

    textoTasa.innerHTML = "Consultando tasa de cambio...";
    textoConversion.innerHTML = "";

    // Consultar nuestra API
    fetch("api_cambio.php")

        .then(function(response) {

            if (!response.ok) {
                throw new Error("Error HTTP: " + response.status);
            }

            return response.json();
        })

        .then(function(datos) {

            console.log("Respuesta:", datos);

            if (datos.success !== true) {
                throw new Error(
                    datos.message || "Error en la API."
                );
            }

            const tasas = datos.rates;

            // Tasa USD → DOP
            const tasaDOP = parseFloat(tasas.DOP);

            if (isNaN(tasaDOP)) {
                throw new Error(
                    "No se encontró la tasa DOP."
                );
            }

            let tasaMonedaDOP;

            // Si es USD
            if (moneda === "USD") {

                tasaMonedaDOP = tasaDOP;

            } else {

                // Tasa USD → moneda seleccionada
                const tasaMoneda =
                    parseFloat(tasas[moneda]);

                if (isNaN(tasaMoneda)) {

                    throw new Error(
                        "No se encontró la tasa para " + moneda
                    );
                }

                // Convertir moneda → DOP
                tasaMonedaDOP =
                    tasaDOP / tasaMoneda;
            }

            // Calcular equivalente
            const equivalente =
                monto * tasaMonedaDOP;

            // Mostrar tasa
            textoTasa.innerHTML =
                "1 " +
                moneda +
                " = " +
                tasaMonedaDOP.toFixed(2) +
                " DOP";

            // Mostrar conversión
            textoConversion.innerHTML =
                monto.toFixed(2) +
                " " +
                moneda +
                " = RD$" +
                equivalente.toFixed(2);

            // Colocar automáticamente el valor
            // en el costo unitario
            costoDop.value =
                equivalente.toFixed(2);

        })

        .catch(function(error) {

            console.error("Error:", error);

            textoTasa.innerHTML =
                "<strong>Error:</strong> " +
                error.message;

            textoConversion.innerHTML = "";

        });
}

</script>

<script>

function cargarProducto() {

    const select = document.getElementById("producto");

    const opcion =
        select.options[select.selectedIndex];

    const categoria =
        document.getElementById("categoria");

    const precio =
        document.getElementById("precio_venta");

    const stock =
        document.getElementById("stock_actual");


    if (select.value === "") {

        categoria.value = "";
        precio.value = "";
        stock.value = "";

        return;
    }


    categoria.value =
        opcion.getAttribute("data-categoria");

    precio.value =
        opcion.getAttribute("data-precio");

    stock.value =
        opcion.getAttribute("data-stock");
}
let carrito = [];

function agregarAlCarrito() {

    const productoSelect = document.getElementById("producto");
    const cantidadInput = document.querySelector('input[name="cantidad"]');
    const costoInput = document.getElementById("costo_unitario");

    const productoId = productoSelect.value;
    const cantidad = parseInt(cantidadInput.value);
    const costoUnitario = parseFloat(costoInput.value);

    if (productoId === "") {
        alert("Selecciona un producto.");
        return;
    }

    if (isNaN(cantidad) || cantidad <= 0) {
        alert("Introduce una cantidad válida.");
        return;
    }

    if (isNaN(costoUnitario) || costoUnitario <= 0) {
        alert("Primero realiza la conversión.");
        return;
    }

    const opcion =
        productoSelect.options[productoSelect.selectedIndex];

    const nombreProducto =
        opcion.textContent.trim();

    const existente = carrito.find(
        item => item.producto_id === productoId
    );

    if (existente) {

        existente.cantidad += cantidad;

        existente.subtotal =
            existente.cantidad *
            existente.costo_unitario;

    } else {

        carrito.push({
            producto_id: productoId,
            producto: nombreProducto,
            cantidad: cantidad,
            costo_unitario: costoUnitario,
            subtotal: cantidad * costoUnitario
        });

    }

    mostrarCarrito();

    cantidadInput.value = "";
    costoInput.value = "";
}


function mostrarCarrito() {

    const cuerpo =
        document.getElementById("carritoBody");

    const totalTexto =
        document.getElementById("totalCarrito");

    const btnRegistrar =
        document.getElementById("btnRegistrarCompra");

    cuerpo.innerHTML = "";

    if (carrito.length === 0) {

        cuerpo.innerHTML = `
            <tr>
                <td colspan="5">
                    No hay productos agregados.
                </td>
            </tr>
        `;

        totalTexto.textContent = "RD$0.00";
        btnRegistrar.disabled = true;

        return;
    }

    let totalCompra = 0;

    carrito.forEach(function(item, index) {

        totalCompra += item.subtotal;

        const fila =
            document.createElement("tr");

        fila.innerHTML = `
            <td>${item.producto}</td>

            <td>${item.cantidad}</td>

            <td>
                RD$${item.costo_unitario.toFixed(2)}
            </td>

            <td>
                RD$${item.subtotal.toFixed(2)}
            </td>

            <td>
                <button
                    type="button"
                    onclick="eliminarDelCarrito(${index})"
                    style="
                        background:#dc3545;
                        color:white;
                        border:none;
                        padding:7px 12px;
                        border-radius:5px;
                        cursor:pointer;
                    "
                >
                    Eliminar
                </button>
            </td>
        `;

        cuerpo.appendChild(fila);

    });

    totalTexto.textContent =
        "RD$" + totalCompra.toFixed(2);

    btnRegistrar.disabled = false;
}


function eliminarDelCarrito(index) {

    carrito.splice(index, 1);

    mostrarCarrito();
}
function registrarCompra() {

    const proveedorSelect =
        document.querySelector('select[name="proveedor"]');

    const proveedorId = proveedorSelect.value;

    // Validar proveedor
    if (proveedorId === "") {
        alert("Selecciona un proveedor.");
        return;
    }

    // Validar carrito
    if (carrito.length === 0) {
        alert("El carrito está vacío.");
        return;
    }

    // Confirmar antes de registrar
    const confirmar = confirm(
        "¿Deseas registrar esta compra completa?"
    );

    if (!confirmar) {
        return;
    }

    // Desactivar botón para evitar doble clic
    const boton =
        document.getElementById("btnRegistrarCompra");

    boton.disabled = true;
    boton.textContent = "Registrando...";

    // Enviar carrito al servidor
    fetch("registrar_compra.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            proveedor_id: proveedorId,
            productos: carrito
        })

    })
    .then(function(response) {

        return response.json();

    })
    .then(function(resultado) {

        if (resultado.success === true) {

            alert(resultado.message);

            // Vaciar carrito
            carrito = [];

            mostrarCarrito();

            // Recargar para actualizar:
            // historial, stock y datos de productos
            window.location.reload();

        } else {

            throw new Error(
                resultado.message ||
                "No se pudo registrar la compra."
            );

        }

    })
    .catch(function(error) {

        console.error(
            "Error registrando compra:",
            error
        );

        alert(
            "Error al registrar la compra: " +
            error.message
        );

        // Volver a habilitar el botón
        boton.disabled = false;
        boton.textContent =
            "Registrar compra completa";

    });
}

const buscador = document.getElementById("buscador");

buscador.addEventListener("input", function() {

    const texto =
        this.value.toLowerCase().trim();

    const filas =
        document.querySelectorAll(
            "#tablaCompras tr:not(:first-child)"
        );

    filas.forEach(function(fila) {

        const id =
            fila.cells[0].textContent.toLowerCase();

        const proveedor =
            fila.cells[1].textContent.toLowerCase();

        const producto =
            fila.cells[2].textContent.toLowerCase();

        if (
            id.includes(texto) ||
            proveedor.includes(texto) ||
            producto.includes(texto)
        ) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }

    });

});
</script>
</body>
</html>