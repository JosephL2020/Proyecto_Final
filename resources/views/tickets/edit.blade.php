@extends('layouts.app')

@section('content')
<style>
  /* ---------- ESTILOS ORIGINALES ---------- */
  .tickets-header-title {
      font-size: 1.2rem;
      font-weight: 600;
  }
  .tickets-subtitle {
      font-size: .85rem;
  }

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
      gap: .45rem;
  }
  .avatar-wrapper {
      position: relative;
      width: 34px;
      height: 34px;
      border-radius: 999px;
      background: linear-gradient(135deg, #3b82f6, #22c55e);
      padding: 2px;
      box-shadow: 0 0 0 1px rgba(15,23,42,0.06), 0 4px 8px rgba(15,23,42,0.18);
  }
  .avatar-inner {
      width: 100%;
      height: 100%;
      border-radius: inherit;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .8rem;
      font-weight: 700;
      background: #f9fafb;
      color: #1f2937;
  }
  .avatar-it .avatar-inner {
      background: #eff6ff;
      color: #1d4ed8;
  }
  .avatar-default .avatar-inner {
      background: #f3f4f6;
      color: #4b5563;
  }

  .table thead th {
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .06em;
      cursor: default;
      white-space: nowrap;
  }
  .table thead th.sortable {
      cursor: pointer;
  }

  .ticket-code-pill {
      display: inline-flex;
      align-items: center;
      gap: .25rem;
      padding: .1rem .45rem;
      border-radius: 999px;
      background-color: #f9fafb;
      border: 1px solid #e5e7eb;
      font-size: .75rem;
      font-weight: 500;
      color: #111827;
  }
  .ticket-title {
      font-size: .9rem;
      font-weight: 500;
  }
  .ticket-meta {
      font-size: .75rem;
  }

  .sort-icon {
      font-size: .65rem;
      margin-left: .2rem;
      opacity: .6;
  }

  .filter-label {
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #6b7280;
      font-weight: 600;
  }

  /* ============================
     AJUSTES PARA MODO OSCURO
     ============================ */

  body.dark-mode {
      color: #e5e7eb;
  }

  body.dark-mode .tickets-subtitle {
      color: #9ca3af;
  }

  /* Card principal de la tabla */
  body.dark-mode .card.border-0.shadow-sm {
      background-color: #020617 !important; /* casi negro */
      border-color: #1e293b !important;
      color: #e5e7eb;
  }

  /* Bootstrap 5 pinta celdas con este selector, así que lo sobreescribimos */
  body.dark-mode .table,
  body.dark-mode .table > :not(caption) > * > * {
      background-color: #020617 !important;   /* fondo filas */
      color: #e5e7eb !important;              /* texto claro */
      border-color: #1e293b !important;       /* líneas discretas */
  }

  /* Encabezado de la tabla */
  body.dark-mode .table thead,
  body.dark-mode .table thead tr,
  body.dark-mode .table thead th {
      background-color: #0b1120 !important;
      color: #f1f5f9 !important;
  }

  /* Hover */
  body.dark-mode .table-hover tbody tr:hover {
      background-color: #111827 !important;
  }

  /* Píldora del código */
  body.dark-mode .ticket-code-pill {
      background-color: #0b1120 !important;
      color: #e2e8f0 !important;
      border-color: #334155 !important;
  }

  /* Texto suave */
  body.dark-mode .ticket-meta,
  body.dark-mode .text-muted {
      color: #94a3b8 !important;
  }

  /* Badges de estado en modo oscuro */
  body.dark-mode .badge-status-open {
      background-color: #1d4ed8;
      color: #e5f0ff;
  }
  body.dark-mode .badge-status-assigned {
      background-color: #b45309;
      color: #fef3c7;
  }
  body.dark-mode .badge-status-in-progress {
      background-color: #c05621;
      color: #ffedd5;
  }
  body.dark-mode .badge-status-resolved {
      background-color: #15803d;
      color: #dcfce7;
  }
  body.dark-mode .badge-status-closed {
      background-color: #4b5563;
      color: #e5e7eb;
  }
  body.dark-mode .badge-status-cancelled {
      background-color: #b91c1c;
      color: #fee2e2;
  }

  /* Badges de prioridad en modo oscuro */
  body.dark-mode .badge-priority-high {
      background-color: #b91c1c;
      color: #fee2e2;
  }
  body.dark-mode .badge-priority-medium {
      background-color: #b45309;
      color: #fef3c7;
  }
  body.dark-mode .badge-priority-low {
      background-color: #374151;
      color: #e5e7eb;
  }

  /* Inputs / selects */
  body.dark-mode input.form-control,
  body.dark-mode select.form-select {
      background-color: #020617 !important;
      color: #e2e8f0 !important;
      border-color: #334155 !important;
  }

  /* Botones */
  body.dark-mode .btn-outline-secondary {
      border-color: #94a3b8 !important;
      color: #e2e8f0 !important;
  }

  body.dark-mode .btn-outline-primary {
      border-color: #3b82f6 !important;
      color: #60a5fa !important;
  }

  body.dark-mode .btn-primary {
      background-color: #3b82f6 !important;
      border-color: #2563eb !important;
  }

  /* Filtros */
  body.dark-mode .filter-label {
      color: #cbd5e1 !important;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <div class="tickets-header-title">Tickets de soporte</div>
    <div class="tickets-subtitle text-muted">
      Vista general de los tickets registrados en el sistema.
    </div>
  </div>
  <div class="d-flex gap-2">
    <button type="button" class="btn btn-outline-secondary" id="btnExportExcel">
      Exportar Excel
    </button>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
      Nuevo ticket
    </a>
  </div>
</div>

{{-- Búsqueda --}}
<form method="GET" class="mb-2">
  <div class="row g-2">
    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-text">
          <i class="bi bi-search"></i>
        </span>
        <input
          type="text"
          name="s"
          class="form-control"
          placeholder="Buscar por código, título o descripción"
          value="{{ request('s') }}"
        >
        <button class="btn btn-outline-secondary">
          Buscar
        </button>
      </div>
    </div>
  </div>
</form>

{{-- Filtros rápidos --}}
@php
  $currentStatus   = request('status');
  $currentPriority = request('priority');
@endphp

<div class="mb-3">
  <div class="d-flex flex-wrap align-items-center gap-3">

    {{-- Filtro por estado --}}
    <div class="d-flex flex-wrap align-items-center gap-2">
      <span class="filter-label">Estado</span>

      {{-- Todos --}}
      @php
        $paramsAllStatus = request()->except(['page','status']);
      @endphp
      <a href="{{ route('tickets.index', $paramsAllStatus) }}"
         class="btn btn-sm {{ $currentStatus ? 'btn-outline-secondary' : 'btn-primary' }}">
        Todos
      </a>

      {{-- Abiertos --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','status']), ['status' => 'open'])) }}"
         class="btn btn-sm {{ $currentStatus === 'open' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Abiertos
      </a>

      {{-- Asignados --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','status']), ['status' => 'assigned'])) }}"
         class="btn btn-sm {{ $currentStatus === 'assigned' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Asignados
      </a>

      {{-- En progreso --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','status']), ['status' => 'in_progress'])) }}"
         class="btn btn-sm {{ $currentStatus === 'in_progress' ? 'btn-primary' : 'btn-outline-secondary' }}">
        En progreso
      </a>

      {{-- Resueltos --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','status']), ['status' => 'resolved'])) }}"
         class="btn btn-sm {{ $currentStatus === 'resolved' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Resueltos
      </a>

      {{-- Cerrados --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','status']), ['status' => 'closed'])) }}"
         class="btn btn-sm {{ $currentStatus === 'closed' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Cerrados
      </a>

      {{-- Cancelados --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','status']), ['status' => 'cancelled'])) }}"
         class="btn btn-sm {{ $currentStatus === 'cancelled' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Cancelados
      </a>
    </div>

    {{-- Filtro por prioridad --}}
    <div class="d-flex flex-wrap align-items-center gap-2">
      <span class="filter-label">Prioridad</span>

      {{-- Todas --}}
      @php
        $paramsAllPrio = request()->except(['page','priority']);
      @endphp
      <a href="{{ route('tickets.index', $paramsAllPrio) }}"
         class="btn btn-sm {{ $currentPriority ? 'btn-outline-secondary' : 'btn-primary' }}">
        Todas
      </a>

      {{-- Alta --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','priority']), ['priority' => 'high'])) }}"
         class="btn btn-sm {{ $currentPriority === 'high' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Alta
      </a>

      {{-- Media --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','priority']), ['priority' => 'medium'])) }}"
         class="btn btn-sm {{ $currentPriority === 'medium' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Media
      </a>

      {{-- Baja --}}
      <a href="{{ route('tickets.index', array_merge(request()->except(['page','priority']), ['priority' => 'low'])) }}"
         class="btn btn-sm {{ $currentPriority === 'low' ? 'btn-primary' : 'btn-outline-secondary' }}">
        Baja
      </a>
    </div>

  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="ticketsTable">
        <thead class="table-light">
          <tr>
            <th style="width: 12%;" class="sortable" data-sort-col="0">
              Código
              <span class="sort-icon">↕</span>
            </th>
            <th style="width: 30%;" class="sortable" data-sort-col="1">
              Ticket
              <span class="sort-icon">↕</span>
            </th>
            <th style="width: 12%;" class="sortable" data-sort-col="2">
              Estado
              <span class="sort-icon">↕</span>
            </th>
            <th style="width: 12%;" class="sortable" data-sort-col="3">
              Prioridad
              <span class="sort-icon">↕</span>
            </th>
            <th style="width: 18%;" class="sortable" data-sort-col="4">
              Asignado a
              <span class="sort-icon">↕</span>
            </th>
            <th style="width: 16%;" class="sortable" data-sort-col="5">
              Fecha
              <span class="sort-icon">↕</span>
            </th>
            <th style="width: 10%;">Acciones / Asignar</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tickets as $ticket)
            @php
              $assignee = $ticket->assignee;
              $initial  = $assignee ? strtoupper(mb_substr($assignee->name, 0, 1)) : null;
              $role     = $assignee->role ?? null;
              $avatarClass = ($role === 'IT' || $role === 'Manager') ? 'avatar-it' : 'avatar-default';

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

              $prioClass = match($ticket->priority) {
                'high'   => 'badge-priority-high',
                'medium' => 'badge-priority-medium',
                default  => 'badge-priority-low',
              };

              $dateText = $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '-';
            @endphp

            <tr>
              <td>
                <span class="ticket-code-pill">
                  {{ $ticket->code }}
                </span>
              </td>
              <td>
                <div class="ticket-title">{{ $ticket->title }}</div>
                <div class="ticket-meta text-muted">
                  {{ Str::limit($ticket->description, 80) }}
                </div>
              </td>
              <td>
                <span class="badge-status {{ $statusClass }}">
                  {{ $ticket->status_label }}
                </span>
              </td>
              <td>
                <span class="badge-priority {{ $prioClass }}">
                  {{ $ticket->priority_label }}
                </span>
              </td>
              <td>
                @if($assignee)
                  <div class="avatar-chip">
                    <div class="avatar-wrapper {{ $avatarClass }}">
                      <div class="avatar-inner">
                        {{ $initial }}
                      </div>
                    </div>
                    <div>
                      <div class="fw-semibold" style="font-size: .85rem;">
                        {{ $assignee->name }}
                      </div>
                      <div class="text-muted" style="font-size: .75rem;">
                        {{ $assignee->role }}
                      </div>
                    </div>
                  </div>
                @else
                  <span class="text-muted">Sin asignar</span>
                @endif
              </td>
              <td>{{ $dateText }}</td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <div>
                    <a class="btn btn-sm btn-outline-primary w-100"
                       href="{{ route('tickets.show', $ticket) }}">
                      Ver
                    </a>
                  </div>
                  @can('assign', $ticket)
                    <form method="POST"
                          action="{{ route('tickets.assign', $ticket) }}"
                          class="d-flex gap-1">
                      @csrf
                      <select name="assigned_to"
                              class="form-select form-select-sm"
                              onchange="this.form.submit()">
                        <option value="">Sin asignar</option>
                        @foreach($its as $u)
                          <option value="{{ $u->id }}"
                            @selected($ticket->assigned_to == $u->id)>
                            {{ $u->name }} ({{ $u->role }})
                          </option>
                        @endforeach
                      </select>
                    </form>
                  @endcan
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-3">
                No hay tickets registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="mt-3">
  {{ $tickets->links() }}
</div>

@endsection

@push('scripts')
<script>
  // Ordenar columnas (frontend simple)
  (function() {
    const table = document.getElementById('ticketsTable');
    if (!table) return;

    const headers = table.querySelectorAll('thead th.sortable');
    let sortState = {};

    headers.forEach(th => {
      th.addEventListener('click', () => {
        const colIndex = parseInt(th.dataset.sortCol, 10);
        const currentDir = sortState[colIndex] === 'asc' ? 'asc' : 'desc';
        const newDir = currentDir === 'asc' ? 'desc' : 'asc';
        sortState[colIndex] = newDir;

        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
          const aText = a.children[colIndex].innerText.trim().toLowerCase();
          const bText = b.children[colIndex].innerText.trim().toLowerCase();

          if (aText < bText) return newDir === 'asc' ? -1 : 1;
          if (aText > bText) return newDir === 'asc' ? 1 : -1;
          return 0;
        });

        rows.forEach(r => tbody.appendChild(r));
      });
    });
  })();

  // Exportar CSV (Excel)
  (function() {
    const btnExport = document.getElementById('btnExportExcel');
    const table = document.getElementById('ticketsTable');

    if (!btnExport || !table) return;

    btnExport.addEventListener('click', () => {
      const rows = Array.from(table.querySelectorAll('tr'));
      const csv = [];
      const separator = ';';

      rows.forEach((row, rowIndex) => {
        const cells = Array.from(row.querySelectorAll(rowIndex === 0 ? 'th' : 'td'));
        const rowData = cells.map(cell =>
          '"' + cell.innerText.replace(/"/g, '""').trim() + '"'
        );
        csv.push(rowData.join(separator));
      });

      const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = 'tickets_export.csv';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(url);
    });
  })();
</script>
@endpush
