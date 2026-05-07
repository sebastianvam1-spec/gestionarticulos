const API_URL = "gestion_articulos.php";

let editandoId = null;


/* =====================================
   MOSTRAR MENSAJES
===================================== */

function mostrarMensaje(texto, color = "red") {

    const mensaje = document.getElementById("mensaje");

    mensaje.innerText = texto;

    mensaje.style.color = color;
}


/* =====================================
   CARGAR ARTÍCULOS
===================================== */

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

        console.log(error);

        mostrarMensaje(
            "Error al cargar artículos"
        );
    }
}


/* =====================================
   VALIDAR FORMULARIO
===================================== */

function validar(nombre, marca, cantidad, bodega) {

    if (
        !nombre ||
        !marca ||
        !cantidad ||
        !bodega
    ) {

        mostrarMensaje(
            "Todos los campos son obligatorios"
        );

        return false;
    }

    if (parseInt(cantidad) < 0) {

        mostrarMensaje(
            "Cantidad inválida"
        );

        return false;
    }

    return true;
}


/* =====================================
   LIMPIAR FORMULARIO
===================================== */

function limpiar() {

    document.getElementById("nombre").value = "";

    document.getElementById("marca").value = "";

    document.getElementById("cantidad").value = "";

    document.getElementById("bodega").value = "";
}


/* =====================================
   CREAR ARTÍCULO
===================================== */

async function crear() {

    const nombre =
        document.getElementById("nombre").value.trim();

    const marca =
        document.getElementById("marca").value.trim();

    const cantidad =
        document.getElementById("cantidad").value.trim();

    const bodega =
        document.getElementById("bodega").value.trim();


    if (
        !validar(
            nombre,
            marca,
            cantidad,
            bodega
        )
    ) {
        return;
    }

    try {

        const response = await fetch(API_URL, {

            method: "POST",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({

                nombre: nombre,

                marca: marca,

                cantidad: parseInt(cantidad),

                bodega: bodega
            })
        });

        const data = await response.json();

        console.log(data);

        if (data.error) {

            mostrarMensaje(data.error);

            return;
        }

        mostrarMensaje(
            "Artículo creado correctamente",
            "green"
        );

        limpiar();

        cargar();

    } catch (error) {

        console.log(error);

        mostrarMensaje(
            "Error al crear artículo"
        );
    }
}


/* =====================================
   EDITAR ARTÍCULO
===================================== */

async function editar(id) {

    try {

        const response =
            await fetch(`${API_URL}?id=${id}`);

        const a = await response.json();

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

    } catch (error) {

        console.log(error);

        mostrarMensaje(
            "Error al cargar artículo"
        );
    }
}


/* =====================================
   ACTUALIZAR
===================================== */

async function actualizar() {

    const nombre =
        document.getElementById(
            "editarNombre"
        ).value.trim();

    const marca =
        document.getElementById(
            "editarMarca"
        ).value.trim();

    const cantidad =
        document.getElementById(
            "editarCantidad"
        ).value.trim();

    const bodega =
        document.getElementById(
            "editarBodega"
        ).value.trim();


    try {

        const response = await fetch(
            `${API_URL}?id=${editandoId}`,
            {

                method: "PUT",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({

                    nombre: nombre,

                    marca: marca,

                    cantidad: parseInt(cantidad),

                    bodega: bodega
                })
            }
        );

        const data = await response.json();

        console.log(data);

        mostrarMensaje(
            "Artículo actualizado",
            "green"
        );

        cancelarEdicion();

        cargar();

    } catch (error) {

        console.log(error);

        mostrarMensaje(
            "Error al actualizar"
        );
    }
}


/* =====================================
   CANCELAR EDICIÓN
===================================== */

function cancelarEdicion() {

    editandoId = null;

    document.getElementById(
        "seccionEditar"
    ).style.display = "none";
}


/* =====================================
   ELIMINAR
===================================== */

async function eliminar(id) {

    if (
        !confirm(
            "¿Eliminar artículo?"
        )
    ) {
        return;
    }

    try {

        const response = await fetch(
            `${API_URL}?id=${id}`,
            {
                method: "DELETE"
            }
        );

        const data = await response.json();

        console.log(data);

        mostrarMensaje(
            "Artículo eliminado",
            "green"
        );

        cargar();

    } catch (error) {

        console.log(error);

        mostrarMensaje(
            "Error al eliminar"
        );
    }
}


/* =====================================
   INICIAR APP
===================================== */

cargar();