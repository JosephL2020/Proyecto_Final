@extends('layouts.app')

@section('content')
@php
    use Carbon\Carbon;
    use App\Models\User;

    // Técnicos que pueden recibir asignación (IT / MANAGER)
    $its = User::whereRaw("UPPER(role) IN ('IT','MANAGER')")
        ->orderBy('name')
        ->get();

    $assignee = $ticket->assignee ?? null;
    $assigneeInitial = $assignee ? strtoupper(mb_substr($assignee->name, 0, 1)) : null;

    $isOwner = auth()->check() && auth()->id() === $ticket->created_by;
    $canRate = $isOwner && in_array($ticket->status, ['resolved','closed']) && !$ticket->rating;

    // =========================
    //   Cálculo de tiempos SLA
    // =========================
    $createdAt = $ticket->created_at ? $ticket->created_at : Carbon::now();
    $now = Carbon::now();

    // Para comparación de SLA (en horas)
    $hoursOpen = $createdAt->diffInHours($now);

    // Para mostrar: días, horas, minutos
    $totalMinutes = $createdAt->diffInMinutes($now);
    $days = intdiv($totalMinutes, 60 * 24);
    $remainingMinutes = $totalMinutes % (60 * 24);
    $hours = intdiv($remainingMinutes, 60);
    $minutes = $remainingMinutes % 60;

    $elapsedParts = [];
    if ($days > 0) {
        $elapsedParts[] = $days . ' día' . ($days > 1 ? 's' : '');
    }
    if ($hours > 0) {
        $elapsedParts[] = $hours . ' hora' . ($hours > 1 ? 's' : '');
    }
    // Siempre mostramos minutos, aunque sea 0
    $elapsedParts[] = $minutes . ' minuto' . ($minutes != 1 ? 's' : '');

    $elapsedDisplay = implode(', ', $elapsedParts);

    // SLA simple: 24 horas desde la creación
    $slaLimitHours = 24;
    $slaStatus = $hoursOpen <= $slaLimitHours ? 'Dentro de SLA' : 'Fuera de SLA';
    $slaClass = $hoursOpen <= $slaLimitHours ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';

    // Clases para estado
    $status = $ticket->status ?? '';
    $statusClass = match($status) {
        'open'        => 'badge-status-open',
        'assigned'    => 'badge-status-assigned',
        'in_progress' => 'badge-status-in-progress',
        'resolved'    => 'badge-status-resolved',
        'closed'      => 'badge-status-closed',
        'cancelled'   => 'badge-status-cancelled',
        default       => 'badge-status-closed',
    };

    // Clases para prioridad
    $prioClass = match($ticket->priority) {
        'high'   => 'badge-priority-high',
        'medium' => 'badge-priority-medium',
        default  => 'badge-priority-low',
    };
@endphp

<style>
  .badge-status {
      font-size: .75rem;
      padding: .2rem .55rem;
      border-radius: 999px;
      font-weight: 500;
  }
  .badge-status-open {
      background-color: #dbeafe;
      color: #1d4ed8;
  }
  .badge-status-assigned {
      background-color: #fef3c7;
      color: #b45309;
  }
  .badge-status-in-progress {
      background-color: #ffedd5;
      color: #c05621;
  }
  .badge-status-resolved {
      background-color: #dcfce7;
      color: #15803d;
  }
  .badge-status-closed {
      background-color: #e5e7eb;
      color: #374151;
  }
  .badge-status-cancelled {
      background-color: #fee2e2;
      color: #b91c1c;
  }

  .badge-priority {
      font-size: .75rem;
      padding: .2rem .55rem;
      border-radius: 999px;
      font-weight: 500;
  }
  .badge-priority-high {
      background-color: #fee2e2;
      color: #b91c1c;
  }
  .badge-priority-medium {
      background-color: #fef3c7;
      color: #b45309;
  }
  .badge-priority-low {
      background-color: #e5e7eb;
      color: #374151;
  }

  .avatar-chip {
      display: inline-flex;
      align-items: center;
      gap: .55rem;
  }
  .avatar-wrapper {
      position: relative;
      width: 40px;
      height: 40px;
      border-radius: 999px;
      background: linear-gradient(135deg, #3b82f6, #22c55e);
      padding: 2px;
      box-shadow: 0 0 0 1px rgba(15,23,42,0.08), 0 6px 12px rgba(15,23,42,0.25);
  }
  .avatar-inner {
      width: 100%;
      height: 100%;
      border-radius: inherit;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      font-weight: 700;
      background: #f9fafb;
      color: #1f2937;
  }

  .timeline {
      position: relative;
      padding-left: 1.5rem;
  }
  .timeline::before {
      content: "";
      position: absolute;
      left: 8px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #e5e7eb;
  }
  .timeline-item {
      position: relative;
      margin-bottom: 1rem;
  }
  .timeline-dot {
      position: absolute;
      left: -2px;
      top: 0.3rem;
      width: 10px;
      height: 10px;
      border-radius: 999px;
      background: #3b82f6;
      box-shadow: 0 0 0 4px rgba(59,130,246,0.2);
  }
  .timeline-title {
      font-size: .86rem;
      font-weight: 600;
  }
  .timeline-time {
      font-size: .75rem;
      color: #6b7280;
  }
  .timeline-body {
      font-size: .8rem;
      color: #4b5563;
  }

  .note-card {
      border-radius: .75rem;
      border: 1px solid #e5e7eb;
  }
  .note-header {
      font-size: .8rem;
      font-weight: 600;
      color: #4b5563;
  }
  .note-meta {
      font-size: .72rem;
      color: #9ca3af;
  }

  /* Estrellas de rating */
  .rating-stars {
      display: inline-flex;
      flex-direction: row-reverse;
      gap: .15rem;
  }
  .rating-stars input {
      display: none;
  }
  .rating-stars label {
      cursor: pointer;
      font-size: 1.25rem;
      line-height: 1;
      color: #e5e7eb;
      transition: transform .1s ease, color .1s ease;
  }
  .rating-stars label:hover {
      transform: scale(1.1);
  }
  .rating-stars input:checked ~ label,
  .rating-stars label:hover,
  .rating-stars label:hover ~ label {
      color: #facc15;
  }

  .rating-stars.readonly label {
      cursor: default;
      color: #e5e7eb;
  }
  .rating-stars.readonly .star-filled {
      color: #facc15;
  }
</style>

<div class="d-flex justify-content-between align-items-start mb-3">
  <div>
    <h4 class="mb-1">
      {{ $ticket->title }}
    </h4>
    <div class="text-muted small">
      Ticket {{ $ticket->code }} · Creado el
      {{ $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '-' }}
    </div>
  </div>
  <div class="text-end">
    <div class="mb-1">
      <span class="badge-status {{ $statusClass }}">
        {{ $ticket->status_label }}
      </span>
      <span class="badge-priority {{ $prioClass }}">
        {{ $ticket->priority_label }}
      </span>
    </div>
    <div class="small">
      <span class="badge {{ $slaClass }} rounded-pill px-3">
        SLA: {{ $slaStatus }}
      </span>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  {{-- COLUMNA PRINCIPAL --}}
  <div class="col-lg-8">
    {{-- Detalles principales --}}
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="mb-2">Descripción del ticket</h6>
        <p class="mb-3" style="white-space: pre-line;">
          {{ $ticket->description }}
        </p>

        <div class="row small text-muted">
          <div class="col-md-4 mb-1">
            <span class="fw-semibold">Categoría:</span>
            {{ optional($ticket->category)->name ?? 'No especificada' }}
          </div>
          <div class="col-md-4 mb-1">
            <span class="fw-semibold">Creado:</span>
            {{ $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '-' }}
          </div>
          <div class="col-md-4 mb-1">
            <span class="fw-semibold">Última actualización:</span>
            {{ $ticket->updated_at ? $ticket->updated_at->format('Y-m-d H:i') : '-' }}
          </div>
        </div>
      </div>
    </div>

    {{-- Notas y comentarios --}}
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0">Notas y comentarios</h6>
          <span class="badge bg-light text-muted border">
            Historial de comunicación
          </span>
        </div>

        {{-- Lista de comentarios --}}
        <div class="mb-3">
          @forelse($ticket->comments ?? [] as $comment)
            <div class="note-card mb-2 p-2">
              <div class="d-flex justify-content-between">
                <div class="note-header">
                  {{ $comment->user->name ?? 'Usuario' }}
                </div>
                <div class="note-meta">
                  {{ $comment->created_at?->format('Y-m-d H:i') }}
                </div>
              </div>
              <div class="mt-1" style="font-size: .8rem; white-space: pre-line;">
                {{ $comment->body }}
              </div>
            </div>
          @empty
            <div class="text-muted small">
              Aún no hay comentarios registrados en este ticket.
            </div>
          @endforelse
        </div>

        {{-- Nuevo comentario --}}
        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
          @csrf
          <div class="mb-2">
            <label class="form-label small fw-semibold">
              Agregar nota / comentario
            </label>
            <textarea
              name="body"
              rows="3"
              class="form-control @error('body') is-invalid @enderror"
              placeholder="Escriba aquí la actualización, nota interna o comentario para el seguimiento del caso..."
              required
            >{{ old('body') }}</textarea>
            @error('body')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-end">
            <button class="btn btn-primary btn-sm">
              Guardar comentario
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Archivos adjuntos (bloque nuevo) --}}
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0">Archivos adjuntos</h6>
          <small class="text-muted">
            {{ $ticket->attachments->count() }} archivo(s) registrado(s)
          </small>
        </div>

        {{-- Lista de adjuntos --}}
        @forelse($ticket->attachments as $att)
          <div class="d-flex justify-content-between align-items-center border rounded-3 px-2 py-1 mb-2 small">
            <div>
              <div class="fw-semibold">
                <a href="{{ route('attachments.download', $att) }}">
                  {{ $att->original_name }}
                </a>
              </div>
              <div class="text-muted">
                {{ $att->size_label }}
                · {{ $att->mime_type }}
                · Subido por {{ $att->uploader->name ?? 'N/D' }}
                · {{ $att->created_at?->format('Y-m-d H:i') }}
              </div>
            </div>
            <div>
              <a href="{{ route('attachments.download', $att) }}" class="btn btn-outline-secondary btn-sm">
                Descargar
              </a>
            </div>
          </div>
        @empty
          <div class="text-muted small mb-2">
            Aún no se han agregado archivos a este ticket.
          </div>
        @endforelse

        {{-- Formulario para subir nuevos adjuntos --}}
        <form action="{{ route('tickets.attachments.store', $ticket) }}"
              method="POST"
              enctype="multipart/form-data"
              class="mt-3">
          @csrf
          <div class="mb-2">
            <label class="form-label small fw-semibold">Agregar archivos</label>
            <input type="file"
                   name="files[]"
                   multiple
                   class="form-control form-control-sm @error('files.*') is-invalid @enderror">
            @error('files.*')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">
              Puedes adjuntar imágenes, PDF o documentos (máx. 5 MB por archivo).
            </small>
          </div>
          <div class="d-flex justify-content-end">
            <button class="btn btn-outline-primary btn-sm">
              Subir adjuntos
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>

  {{-- COLUMNA LATERAL --}}
  <div class="col-lg-4">

    {{-- DIAGNÓSTICO IA LOCAL --}}
    <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg,#0ea5e9,#6366f1);">
      <div class="card-body text-white">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div>
            <div class="small text-uppercase opacity-75">Asistente de soporte</div>
            <h6 class="mb-0">Diagnóstico sugerido</h6>
          </div>
          <span class="badge bg-light text-dark">IA local</span>
        </div>

        <div class="mt-2 mb-3" style="font-size: .82rem; white-space: pre-line;">
          @foreach($recs as $step)
              <div>{{ $step }}</div>
          @endforeach
        </div>

        <form method="POST" action="{{ route('tickets.ai', $ticket) }}" class="d-flex justify-content-between.align-items-center gap-2">
          @csrf
          <small class="opacity-75">
            Puedes regenerar el diagnóstico si cambió la información del ticket.
          </small>
          <button type="submit" class="btn btn-sm btn-outline-light">
            Regenerar diagnóstico
          </button>
        </form>
      </div>
    </div>

    {{-- CALIFICACIÓN DEL SERVICIO --}}
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0">Satisfacción del usuario</h6>
          @if($ticket->rating)
            <span class="badge bg-success-subtle text-success">
              Calificado
            </span>
          @elseif($canRate)
            <span class="badge bg-warning-subtle text-warning">
              Pendiente de calificar
            </span>
          @else
            <span class="badge bg-light text-muted">
              Solo lectura
            </span>
          @endif
        </div>

        @if($ticket->rating)
          {{-- Vista solo lectura si ya está calificado --}}
          <div class="mb-2">
            <div class="rating-stars readonly">
              @for($i = 5; $i >= 1; $i--)
                <label class="{{ $i <= $ticket->rating ? 'star-filled' : '' }}">★</label>
              @endfor
            </div>
            <div class="small text-muted mt-1">
              Calificación: {{ $ticket->rating }} / 5
              @if($ticket->rated_at)
                · el {{ \Carbon\Carbon::parse($ticket->rated_at)->format('Y-m-d H:i') }}
              @endif
            </div>
          </div>
          @if($ticket->rating_comment)
            <div class="small text-muted">
              <span class="fw-semibold">Comentario del usuario:</span><br>
              {{ $ticket->rating_comment }}
            </div>
          @endif
        @elseif($canRate)
          {{-- Formulario de calificación --}}
          <form method="POST" action="{{ route('tickets.rate', $ticket) }}">
            @csrf

            <div class="mb-2">
              <label class="form-label small fw-semibold">Calificar atención</label>
              <div class="rating-stars">
                @for($i = 5; $i >= 1; $i--)
                  <input type="radio" name="rating" id="rating-{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                  <label for="rating-{{ $i }}">★</label>
                @endfor
              </div>
              @error('rating')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-2">
              <label class="form-label small">Comentario (opcional)</label>
              <textarea
                name="rating_comment"
                rows="2"
                class="form-control form-control-sm @error('rating_comment') is-invalid @enderror"
                placeholder="Ejemplo: El técnico fue puntual y resolvió el problema claramente."
              >{{ old('rating_comment') }}</textarea>
              @error('rating_comment')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-flex justify-content-end">
              <button class="btn btn-sm btn-primary">
                Enviar calificación
              </button>
            </div>
          </form>
        @else
          <div class="small text-muted">
            La calificación solo puede ser registrada por el usuario que creó el ticket
            cuando el caso está resuelto o cerrado.
          </div>
        @endif
      </div>
    </div>

    {{-- Responsable del ticket --}}
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="mb-2">Responsable del ticket</h6>

        @if($assignee)
          <div class="avatar-chip mb-2">
            <div class="avatar-wrapper">
              <div class="avatar-inner">
                {{ $assigneeInitial }}
              </div>
            </div>
            <div>
              <div class="fw-semibold">
                {{ $assignee->name }}
              </div>
              <div class="text-muted small">
                {{ $assignee->role }}
              </div>
            </div>
          </div>
        @else
          <p class="text-muted small mb-2">
            Este ticket aún no tiene un responsable asignado.
          </p>
        @endif

        @can('assign', $ticket)
          <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label small">Cambiar responsable</label>
              <select name="assigned_to" class="form-select form-select-sm">
                <option value="">Sin asignar</option>
                @foreach($its as $u)
                  <option value="{{ $u->id }}" @selected($ticket->assigned_to == $u->id)>
                    {{ $u->name }} ({{ $u->role }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="d-flex justify-content-end">
              <button class="btn btn-outline-primary btn-sm">
                Actualizar asignación
              </button>
            </div>
          </form>
        @endcan
      </div>
    </div>

    {{-- SLA y tiempos --}}
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="mb-2">SLA y tiempos</h6>
        <div class="small mb-1">
          Tiempo transcurrido desde la creación:
        </div>
        <div class="fw-semibold mb-2">
          {{ $elapsedDisplay }}
        </div>
        <div class="small mb-1">
          Límite de SLA configurado:
        </div>
        <div class="fw-semibold mb-2">
          {{ $slaLimitHours }} horas ({{ intdiv($slaLimitHours,24) }} día{{ $slaLimitHours >= 48 ? 's' : '' }})
        </div>
        <div>
          <span class="badge {{ $slaClass }} rounded-pill px-3">
            {{ $slaStatus }}
          </span>
        </div>
      </div>
    </div>

    {{-- Línea de tiempo --}}
    <div class="card mb-3">
      <div class="card-body">
        <h6 class="mb-2">Línea de tiempo del ticket</h6>

        <div class="timeline">
          {{-- Creación --}}
          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-title">Ticket creado</div>
            <div class="timeline-time">
              {{ $ticket->created_at?->format('Y-m-d H:i') }}
            </div>
            <div class="timeline-body">
              El ticket fue registrado en el sistema.
            </div>
          </div>

          {{-- Asignación --}}
          @if($assignee)
            <div class="timeline-item">
              <div class="timeline-dot" style="background:#22c55e; box-shadow:0 0 0 4px rgba(34,197,94,.2);"></div>
              <div class="timeline-title">Asignado a soporte</div>
              <div class="timeline-time">
                {{ $ticket->updated_at?->format('Y-m-d H:i') }}
              </div>
              <div class="timeline-body">
                El ticket fue asignado a {{ $assignee->name }} ({{ $assignee->role }}).
              </div>
            </div>
          @endif

          {{-- Cierre/resolución --}}
          @if(in_array($ticket->status, ['resolved','closed','cancelled']))
            <div class="timeline-item">
              <div class="timeline-dot" style="background:#6b7280; box-shadow:0 0 0 4px rgba(107,114,128,.2);"></div>
              <div class="timeline-title">
                Ticket {{ $ticket->status_label }}
              </div>
              <div class="timeline-time">
                {{ $ticket->updated_at?->format('Y-m-d H:i') }}
              </div>
              <div class="timeline-body">
                El ticket fue marcado como {{ strtolower($ticket->status_label) }}.
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Botones inferiores --}}
    <div class="d-flex gap-2">
      <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm">
        Volver al listado
      </a>
      @can('update', $ticket)
        <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-outline-primary btn-sm">
          Editar ticket
        </a>
      @endcan
    </div>

  </div>
</div>
@endsection
