const API_URL =
"https://claseapi.alwaysdata.net/cliente1/gestion_articulos.php";

let editandoId = null;

function mostrarMensaje(texto, tipo = "red") {
    const mensaje = document.getElementById("mensaje");
    mensaje.innerText = texto;
    mensaje.style.color = tipo;
}

async function cargar() {

    try {

        const response = await fetch(API_URL);
        const data = await response.json();

        const tabla = document.getElementById("tabla");

        tabla.innerHTML = "";

        data.forEach(a => {

            tabla.innerHTML += `
                <tr>
                    <td>${a.id}</td>
                    <td>${a.nombre}</td>
                    <td>${a.marca}</td>
                    <td>${a.cantidad}</td>
                    <td>${a.bodega}</td>

                    <td>
                        <button onclick="editar(${a.id})">
                            Editar
                        </button>

                        <button onclick="eliminar(${a.id})">
                            Eliminar
                        </button>
                    </td>
                </tr>
            `;
        });

    } catch (error) {

        mostrarMensaje("Error cargando datos");
    }
}


async function crear() {

    const nombre = document.getElementById("nombre").value;
    const marca = document.getElementById("marca").value;
    const cantidad = document.getElementById("cantidad").value;
    const bodega = document.getElementById("bodega").value;

    if (!nombre || !marca || !cantidad || !bodega) {

        mostrarMensaje("Todos los campos son obligatorios");

        return;
    }

    await fetch(API_URL, {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            nombre,
            marca,
            cantidad,
            bodega
        })
    });

    mostrarMensaje("Artículo creado", "green");

    limpiar();

    cargar();
}


async function editar(id) {

    const response = await fetch(`${API_URL}?id=${id}`);

    const a = await response.json();

    document.getElementById("editarNombre").value = a.nombre;
    document.getElementById("editarMarca").value = a.marca;
    document.getElementById("editarCantidad").value = a.cantidad;
    document.getElementById("editarBodega").value = a.bodega;

    editandoId = id;

    document.getElementById("seccionEditar").style.display = "block";
}


async function actualizar() {

    const nombre = document.getElementById("editarNombre").value;
    const marca = document.getElementById("editarMarca").value;
    const cantidad = document.getElementById("editarCantidad").value;
    const bodega = document.getElementById("editarBodega").value;

    await fetch(`${API_URL}?id=${editandoId}`, {

        method: "PUT",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            nombre,
            marca,
            cantidad,
            bodega
        })
    });

    mostrarMensaje("Artículo actualizado", "green");

    cancelarEdicion();

    cargar();
}


async function eliminar(id) {

    if (!confirm("¿Eliminar artículo?")) return;

    await fetch(`${API_URL}?id=${id}`, {

        method: "DELETE"
    });

    mostrarMensaje("Artículo eliminado", "green");

    cargar();
}


function cancelarEdicion() {

    editandoId = null;

    document.getElementById("seccionEditar").style.display = "none";
}


function limpiar() {

    document.getElementById("nombre").value = "";
    document.getElementById("marca").value = "";
    document.getElementById("cantidad").value = "";
    document.getElementById("bodega").value = "";
}


cargar();