// Inicializar Select2 en los selects de cliente
$(document).ready(function () {
  $('.cliente-select').each(function () {
    const modalId = $(this).closest('.modal').attr('id');
    $(this).select2({
      width: 'resolve',
      dropdownParent: $('#' + modalId)
    });
  });

  // Mostrar/ocultar fechas manuales
  $('.toggle-fechas').each(function () {
    const habitacionId = $(this).attr('id').replace('checkFechas', '');
    const grupoFechas = $('#grupoFechas' + habitacionId);

    $(this).on('change', function () {
if ($(this).is(':checked')) {
  grupoFechas.slideDown();
} else {
  grupoFechas.slideUp();
  grupoFechas.find('input').val('');
}

    });
  });

  // Calcular días de estadía
  const gruposFechas = document.querySelectorAll('.grupo-fechas');
  gruposFechas.forEach(grupo => {
    const id = grupo.id.replace('grupoFechas', '');
    const llegadaInput = document.getElementById('fechaLlegada' + id);
    const salidaInput = document.getElementById('fechaSalida' + id);
    const contenedorDias = document.getElementById('diasEstadia' + id);
    const numDias = contenedorDias.querySelector('.num-dias');

    function calcularDias() {
      const llegada = new Date(llegadaInput.value);
      const salida = new Date(salidaInput.value);

      if (!isNaN(llegada) && !isNaN(salida) && salida > llegada) {
        const diffMs = salida - llegada;
        const diffDias = diffMs / (1000 * 60 * 60 * 24);
        numDias.textContent = Math.floor(diffDias);
        contenedorDias.classList.remove('d-none');
      } else {
        contenedorDias.classList.add('d-none');
      }
    }

    llegadaInput.addEventListener('change', calcularDias);
    salidaInput.addEventListener('change', calcularDias);
  });
});

// Mostrar mensajes SweetAlert si se realiza o falla una reserva
document.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const reservaStatus = urlParams.get('reserva');

  if (reservaStatus === 'success') {
    Swal.fire({
      icon: 'success',
      title: 'Reserva realizada',
      text: 'La reserva se ha guardado correctamente.',
      confirmButtonColor: '#3085d6',
    });
  } else if (reservaStatus === 'error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Hubo un problema al guardar la reserva.',
      confirmButtonColor: '#d33',
    });
  }

  if (reservaStatus) {
    const newUrl = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, newUrl);
  }
});

// Vaciar habitación (botón)
$(document).on('click', '.btn-vaciar', function () {
  const idHabitacion = $(this).data('id');

  Swal.fire({
    title: '¿Vaciar habitación?',
    text: "Esta acción liberará la habitación.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, vaciar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '../controllers/vaciar_habitacion.php',
        type: 'POST',
        data: { id_habitacion: idHabitacion },
        success: function () {
          Swal.fire({
            icon: 'success',
            title: 'Habitación liberada',
            text: 'La habitación ahora está disponible.',
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            location.reload();
          });
        },
        error: function () {
          Swal.fire(
            'Error',
            'No se pudo vaciar la habitación.',
            'error'
          );
        }
      });
    }
  });
});
// Validar fechas al enviar formulario
$('#formReserva<?php echo $id; ?>').on('submit', function (e) {
  const checkFechas = $('#checkFechas<?php echo $id; ?>').is(':checked');
  const fechaLlegada = $('#fechaLlegada<?php echo $id; ?>').val();
  const fechaSalida = $('#fechaSalida<?php echo $id; ?>').val();

  if (checkFechas) {
    if (!fechaLlegada || !fechaSalida) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Fechas requeridas',
        text: 'Debes ingresar ambas fechas de llegada y salida.'
      });
      return;
    }

    if (new Date(fechaSalida) <= new Date(fechaLlegada)) {
      e.preventDefault();
      Swal.fire({
        icon: 'error',
        title: 'Fechas inválidas',
        text: 'La fecha de salida debe ser posterior a la de llegada.'
      });
      return;
    }
  }
});
