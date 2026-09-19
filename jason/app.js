// =======================
// CLASE CLIENTE
// =======================

class Cliente {
    constructor(id, nombre, telefono) {
        this.id = id;
        this.nombre = nombre;
        this.telefono = telefono;
    }
}

// =======================
// CLASE PROVEEDOR
// =======================

class Proveedor {
    constructor(id, nombre, empresa) {
        this.id = id;
        this.nombre = nombre;
        this.empresa = empresa;
    }
}

// =======================
// CLASE PRODUCTO
// =======================

class Producto {
    constructor(id, nombre, cantidad) {
        this.id = id;
        this.nombre = nombre;
        this.cantidad = cantidad;
    }
}

// =======================
// ARRAYS
// =======================

const clientes = [];
const proveedores = [];
const productos = [];

// =======================
// FUNCIONES CLIENTES
// =======================

function agregarCliente() {

    const nombre = document.getElementById("clienteNombre").value;
    const telefono = document.getElementById("clienteTelefono").value;

    const cliente = new Cliente(
        clientes.length + 1,
        nombre,
        telefono
    );

    clientes.push(cliente);

    mostrarClientes();

    document.getElementById("clienteNombre").value = "";
    document.getElementById("clienteTelefono").value = "";
}

function mostrarClientes() {

    const tabla = document.getElementById("tablaClientes");

    tabla.innerHTML = "";

    clientes.forEach(cliente => {

        tabla.innerHTML += `
            <tr>
                <td>${cliente.id}</td>
                <td>${cliente.nombre}</td>
                <td>${cliente.telefono}</td>
            </tr>
        `;
    });
}

// =======================
// FUNCIONES PROVEEDORES
// =======================

function agregarProveedor() {

    const nombre = document.getElementById("proveedorNombre").value;
    const empresa = document.getElementById("proveedorEmpresa").value;

    const proveedor = new Proveedor(
        proveedores.length + 1,
        nombre,
        empresa
    );

    proveedores.push(proveedor);

    mostrarProveedores();

    document.getElementById("proveedorNombre").value = "";
    document.getElementById("proveedorEmpresa").value = "";
}

function mostrarProveedores() {

    const tabla = document.getElementById("tablaProveedores");

    tabla.innerHTML = "";

    proveedores.forEach(proveedor => {

        tabla.innerHTML += `
            <tr>
                <td>${proveedor.id}</td>
                <td>${proveedor.nombre}</td>
                <td>${proveedor.empresa}</td>
            </tr>
        `;
    });
}

// =======================
// FUNCIONES PRODUCTOS
// =======================

function agregarProducto() {

    const nombre = document.getElementById("productoNombre").value;
    const cantidad = document.getElementById("productoCantidad").value;

    const producto = new Producto(
        productos.length + 1,
        nombre,
        cantidad
    );

    productos.push(producto);

    mostrarProductos();

    document.getElementById("productoNombre").value = "";
    document.getElementById("productoCantidad").value = "";
}

function mostrarProductos() {

    const tabla = document.getElementById("tablaProductos");

    tabla.innerHTML = "";

    productos.forEach(producto => {

        tabla.innerHTML += `
            <tr>
                <td>${producto.id}</td>
                <td>${producto.nombre}</td>
                <td>${producto.cantidad}</td>
            </tr>
        `;
    });
}