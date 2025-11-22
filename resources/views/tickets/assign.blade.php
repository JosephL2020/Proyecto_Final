@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-md-7 col-lg-5">
    <h4 class="mb-3">Asignar Ticket {{ $ticket->code }}</h4>

    <div class="card shadow-sm border-0" style="border-radius: 0.9rem;">
      <div class="card-body">

        {{-- Resumen rápido del ticket --}}
        <div class="mb-3 p-2 rounded" style="background-color:#f9fafb;">
          <div class="small text-muted">Ticket</div>
          <div class="fw-semibold">{{ $ticket->title }}</div>
          <div class="small text-muted">
            Prioridad: <strong>{{ $ticket->priority_label }}</strong> ·
            Estado actual: <strong>{{ $ticket->status_label }}</strong>
          </div>
        </div>

        <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
          @csrf

          {{-- Avatar preview del soporte seleccionado --}}
          <div class="mb-3">
            <label class="form-label">Asignar a</label>

            <div class="d-flex align-items-center mb-2" id="assigneePreview" style="display:none;">
              <div class="me-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 36px; height:36px; background:#e5edff; color:#1d4ed8; font-weight:600;"
                     id="assigneeAvatar">
                    D
                </div>
              </div>
              <div>
                <div class="fw-semibold mb-0" id="assigneeName">Soporte</div>
                <small class="text-muted" id="assigneeRole">Rol</small>
              </div>
            </div>

            <select name="assigned_to"
                    class="form-select"
                    required
                    id="assigneeSelect">
              <option value="">Seleccione un técnico...</option>
              @foreach($its as $u)
                @php
                  $initial = strtoupper(mb_substr($u->name, 0, 1));
                @endphp
                <option value="{{ $u->id }}"
                        data-name="{{ $u->name }}"
                        data-role="{{ $u->role }}"
                        data-initial="{{ $initial }}"
                        @selected($ticket->assigned_to==$u->id)>
                  {{ $initial }} · {{ $u->name }} ({{ $u->role }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="d-flex gap-2 justify-content-end">
            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-secondary">
              Cancelar
            </a>
            <button class="btn btn-primary">
              Guardar asignación
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    const select   = document.getElementById('assigneeSelect');
    const preview  = document.getElementById('assigneePreview');
    const avatar   = document.getElementById('assigneeAvatar');
    const nameEl   = document.getElementById('assigneeName');
    const roleEl   = document.getElementById('assigneeRole');

    function updatePreview() {
      const option = select.options[select.selectedIndex];
      if (!option || !option.value) {
        preview.style.display = 'none';
        return;
      }
      avatar.textContent = option.dataset.initial || '?';
      nameEl.textContent = option.dataset.name || '';
      roleEl.textContent = option.dataset.role || '';
      preview.style.display = 'flex';
    }

    select.addEventListener('change', updatePreview);
    // Inicializar por si ya viene uno seleccionado
    updatePreview();
  })();
</script>
@endpush
@endsection
