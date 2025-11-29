@extends('layouts.app')

@section('content')
<h4 class="mb-3">Tablero Kanban de Tickets</h4>
<p class="text-muted small mb-4">
  Arrastre los tickets entre columnas para cambiar su estado.
</p>

<div class="row g-3 kanban-board">
  @foreach($columns as $statusKey => $label)
    @php
      $items = $grouped[$statusKey] ?? collect();
    @endphp
    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
      <div class="card h-100">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
          <span class="fw-semibold">{{ $label }}</span>
          <span class="badge bg-secondary-subtle text-secondary">
            {{ $items->count() }}
          </span>
        </div>
        <div class="card-body p-2 kanban-column"
             data-status="{{ $statusKey }}">
          <div class="kanban-list" style="min-height: 60px;">
            @foreach($items as $ticket)
              <div class="card mb-2 kanban-card"
                   draggable="true"
                   data-ticket-id="{{ $ticket->id }}">
                <div class="card-body p-2">
                  <div class="small fw-semibold text-truncate"
                       title="{{ $ticket->title }}">
                    {{ $ticket->code }} · {{ $ticket->title }}
                  </div>
                  <div class="small text-muted">
                    @if($ticket->assignee)
                      <span class="badge bg-light text-muted border me-1">
                        {{ $ticket->assignee->name }}
                      </span>
                    @else
                      <span class="badge bg-light text-muted border me-1">
                        Sin asignar
                      </span>
                    @endif
                    <span>
                      {{ $ticket->created_at?->format('d/m H:i') }}
                    </span>
                  </div>
                  <div class="mt-1">
                    <a href="{{ route('tickets.show', $ticket) }}"
                       class="small text-decoration-none">
                      Ver detalle
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  @endforeach
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards   = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-column');
    const token   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    cards.forEach(card => {
        card.addEventListener('dragstart', e => {
            e.dataTransfer.setData('text/plain', card.dataset.ticketId);
            card.classList.add('opacity-50');
        });
        card.addEventListener('dragend', e => {
            card.classList.remove('opacity-50');
        });
    });

    columns.forEach(col => {
        col.addEventListener('dragover', e => {
            e.preventDefault();
            col.classList.add('bg-light');
        });
        col.addEventListener('dragleave', e => {
            col.classList.remove('bg-light');
        });
        col.addEventListener('drop', e => {
            e.preventDefault();
            col.classList.remove('bg-light');

            const ticketId = e.dataTransfer.getData('text/plain');
            const card     = document.querySelector(`.kanban-card[data-ticket-id="${ticketId}"]`);
            const list     = col.querySelector('.kanban-list');
            const status   = col.dataset.status;

            if (!card || !list) return;

            // mover visualmente
            list.appendChild(card);

            // llamada AJAX para actualizar estado
            fetch(`/tickets/${ticketId}/move`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({ status })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.ok) {
                    alert(data.message || 'No se pudo actualizar el estado');
                    // TODO: opcional: recargar página para revertir
                }
            })
            .catch(() => {
                alert('Error de red al actualizar el ticket');
            });
        });
    });
});
</script>
@endpush
@endsection
