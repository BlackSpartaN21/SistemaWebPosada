const chatData = {
    inicio: {
        texto: "¡Hola! Bienvenido a la asistencia en línea de la Posada Turística Las Mandarinas. ¿En qué puedo ayudarte hoy?",
        opciones: [
            { texto: "💰 Precios de habitaciones", siguiente: "precios" },
            { texto: "📅 Quiero realizar una reserva", siguiente: "reserva" }, // <-- NUEVA OPCIÓN
            { texto: "📍 Dirección y Ubicación", siguiente: "direccion" },
            { texto: "⏰ Horario de atención", siguiente: "horario" },
            { texto: "🏔️ Lugares cercanos de interés", siguiente: "turismo" }
        ]
    },
    reserva: {
        // Aquí puedes cambiar el enlace '#' por tu link real de WhatsApp más adelante
        texto: "¡Excelente! Nos encantaría recibirte en nuestra posada. Actualmente gestionamos todas nuestras reservas de forma directa y personalizada a través de WhatsApp.<br><br>Haz clic en el siguiente enlace para comunicarte con nosotros:<br><br><a href='https://wa.me/584264731506?text=Hola%20quiero%20reservar%20una%20habitaci%C3%B3n' target='_blank' style='display: inline-block; background-color: #25D366; color: white; padding: 10px 20px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; box-shadow: 0px 2px 5px rgba(0,0,0,0.2);'>🟢 Reservar por WhatsApp</a>",
        opciones: [
            { texto: "🏠 Volver al menú", siguiente: "inicio" }
        ]
    },
    precios: {
        texto: "Contamos con excelentes tarifas adaptadas a tu estadía:\n\n• Tiempo corto: 5$\n• Habitación Matrimonial: 10$\n• Habitación Triple: 15$",
        opciones: [
            { texto: "🏠 Volver al menú", siguiente: "inicio" }
        ]
    },
    direccion: {
        texto: "Nos encontramos en una ubicación muy accesible. Nuestra dirección exacta es:\n\n📍 C. Centenario, Ejido 5111, Mérida, Venezuela. (Como referencia, nos puedes ubicar fácilmente sobre la Avenida Centenario).",
        opciones: [
            { texto: "🏠 Volver al menú", siguiente: "inicio" }
        ]
    },
    horario: {
        texto: "Nuestro equipo está listo para recibirte en las siguientes horas:\n\n⏰ Lunes a Sábado: de 7:00 AM a 10:00 PM.",
        opciones: [
            { texto: "🏠 Volver al menú", siguiente: "inicio" }
        ]
    },
    turismo: {
        texto: "Nuestra posada goza de una ubicación estratégica en Ejido. ¿Qué tipo de lugares cercanos te gustaría conocer?",
        opciones: [
            { texto: "🍴 Restaurantes y Comida", siguiente: "restaurantes" },
            { texto: "💊 Farmacias y Servicios", siguiente: "farmacias" },
            { texto: "🎡 Atracciones Turísticas", siguiente: "atracciones" },
            { texto: "⬅️ Atrás", siguiente: "inicio" }
        ]
    },
    restaurantes: {
        texto: "Opciones gastronómicas recomendadas en la misma Av. Centenario:\n\n• Arepera Centenario: a 0.2 km de la posada.\n• Mi Nenes Burgers: a 0.4 km de la posada.\n• Restaurant Villa Gourmet: a 1.1 km de la posada en la zona de La Vega.",
        opciones: [
            { texto: "⬅️ Volver a lugares", siguiente: "turismo" },
            { texto: "🏠 Menú principal", siguiente: "inicio" }
        ]
    },
    farmacias: {
        texto: "Servicios de salud cercanos para tu tranquilidad:\n\n• Farmacia del Centro de Ejido: a 0.6 km de la posada.\n• Farmacias y centros asistenciales sobre la Av. Centenario: a menos de 1 km a la redonda.",
        opciones: [
            { texto: "⬅️ Volver a lugares", siguiente: "turismo" },
            { texto: "🏠 Menú principal", siguiente: "inicio" }
        ]
    },
    atracciones: {
        texto: "Sitios icónicos que puedes visitar saliendo desde Ejido:\n\n• Parque Temático 'La Venezuela de Antier': a unos 7 km.\n• Ciudad de Mérida (Plaza Bolívar / Catedral): a unos 12 km.\n• Estación del Teleférico Mukumbarí: a 13.5 km.\n• Parque Zoológico Chorros de Milla: a unos 16 km.",
        opciones: [
            { texto: "⬅️ Volver a lugares", siguiente: "turismo" },
            { texto: "🏠 Menú principal", siguiente: "inicio" }
        ]
    }
};

const chatContainer = document.getElementById('chat-container');
const chatBox = document.getElementById('chat-box');

function toggleChat() {
    chatContainer.classList.toggle('chat-hidden');
    if (!chatContainer.classList.contains('chat-hidden') && chatBox.innerHTML === "") {
        cargarNodo("inicio");
    }
}

function cargarNodo(nodoClave) {
    const nodo = chatData[nodoClave];
    
    const botDiv = document.createElement('div');
    botDiv.className = 'msg bot-msg';
    
    // CAMBIO IMPORTANTE: Ahora usamos .innerHTML en vez de .innerText 
    // Esto permite que el chat renderice el botón con el enlace a WhatsApp correctamente.
    botDiv.innerHTML = nodo.texto.replace(/\n/g, '<br>');
    chatBox.appendChild(botDiv);
    
    nodo.opciones.forEach(opcion => {
        const boton = document.createElement('button');
        boton.className = 'chat-btn';
        boton.innerText = opcion.texto;
        
        boton.onclick = function() {
            const botonesAnteriores = chatBox.querySelectorAll('.chat-btn');
            botonesAnteriores.forEach(b => b.disabled = true);
            
            const userDiv = document.createElement('div');
            userDiv.className = 'msg user-msg';
            userDiv.innerText = opcion.texto;
            chatBox.appendChild(userDiv);
            
            setTimeout(() => {
                cargarNodo(opcion.siguiente);
            }, 350);
        };
        chatBox.appendChild(boton);
    });
    
    chatBox.scrollTop = chatBox.scrollHeight;
}