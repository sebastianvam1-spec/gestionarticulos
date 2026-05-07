const API_URL = "gestion_articulos.php";

let editandoId = null;


/* =========================
   MENSAJES
========================= */

function mostrarMensaje(texto, color = "red") {

    const mensaje =
        document.getElementById("mensaje");

    mensaje.innerText = texto;

    mensaje.style.color = color;
}


/* =========================
   CARGAR DATOS
========================= */

async function cargar() {

    try {

        const response =
            await fetch(API_URL);

        const data =
            await response.json();

        const tabla =
            document.getElementById("tabla");

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

    } catch(error) {

        console.log(error);

        mostrarMensaje(
            "Error al cargar artículos"
        );
    }
}


/* =========================
   CREAR
========================= */

async function crear() {

    const nombre =
        document.getElementById("nombre").value;

    const marca =
        document.getElementById("marca").value;

    const cantidad =
        document.getElementById("cantidad").value;

    const bodega =
        document.getElementById("bodega").value;


    try {

        const response =
            await fetch(API_URL, {

                method: "POST",

                headers: {
                    "Content-Type":
                        "application/json"
                },

                body: JSON.stringify({

                    nombre: nombre,

                    marca: marca,

                    cantidad: parseInt(cantidad),

                    bodega: bodega
                })
            });

        const data =
            await response.json();

        console.log(data);

        mostrarMensaje(
            "Artículo creado",
            "green"
        );

        limpiar();

        cargar();

    } catch(error) {

        console.log(error);

        mostrarMensaje(
            "Error al crear"
        );
    }
}


/* =========================
   LIMPIAR
========================= */

function limpiar() {

    document.getElementById("nombre").value = "";

    document.getElementById("marca").value = "";

    document.getElementById("cantidad").value = "";

    document.getElementById("bodega").value = "";
}


/* =========================
   EDITAR
========================= */

async function editar(id) {

    const response =
        await fetch(`${API_URL}?id=${id}`);

    const a =
        await response.json();

    document.getElementById(
        "editarNombre"
    ).value = a.nombre;

    document.getElementById(
        "editarMarca"
    ).value = a.marca;

    document.getElementById(
        "editarCantidad"
    ).value = a.cantidad;

    document.getElementById(
        "editarBodega"
    ).value = a.bodega;

    editandoId = id;

    document.getElementById(
        "seccionEditar"
    ).style.display = "block";
}


/* =========================
   ACTUALIZAR
========================= */

async function actualizar() {

    const nombre =
        document.getElementById(
            "editarNombre"
        ).value;

    const marca =
        document.getElementById(
            "editarMarca"
        ).value;

    const cantidad =
        document.getElementById(
            "editarCantidad"
        ).value;

    const bodega =
        document.getElementById(
            "editarBodega"
        ).value;


    await fetch(
        `${API_URL}?id=${editandoId}`,
        {

            method: "PUT",

            headers: {
                "Content-Type":
                    "application/json"
            },

            body: JSON.stringify({

                nombre: nombre,

                marca: marca,

                cantidad: parseInt(cantidad),

                bodega: bodega
            })
        }
    );

    mostrarMensaje(
        "Artículo actualizado",
        "green"
    );

    cancelarEdicion();

    cargar();
}


/* =========================
   CANCELAR
========================= */

function cancelarEdicion() {

    editandoId = null;

    document.getElementById(
        "seccionEditar"
    ).style.display = "none";
}


/* =========================
   ELIMINAR
========================= */

async function eliminar(id) {

    await fetch(
        `${API_URL}?id=${id}`,
        {
            method: "DELETE"
        }
    );

    mostrarMensaje(
        "Artículo eliminado",
        "green"
    );

    cargar();
}


/* =========================
   INICIO
========================= */

cargar();