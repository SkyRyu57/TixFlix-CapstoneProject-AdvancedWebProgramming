@extends('organizer.layouts.master')

@section('title', 'Event Saya')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold">Event Saya</h2>
            <p class="text-muted">Kelola event Anda</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <button id="gridViewBtn" class="btn btn-outline-secondary active-view" data-view="grid">
                <i class="fas fa-th-large"></i>
            </button>
            <button id="listViewBtn" class="btn btn-outline-secondary" data-view="list">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <!-- Filter, Search, Sort Bar -->
    <div class="card table-custom mb-4 p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="Cari event..." id="searchInput">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select bg-dark text-white border-secondary" id="statusSelect">
                    <option value="all">Semua Status</option>
                    <option value="published">Published</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="sort" class="form-select bg-dark text-white border-secondary" id="sortSelect">
                    <option value="latest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="start_soon">Mulai Terdekat</option>
                    <option value="start_later">Mulai Terjauh</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" id="printSelectedBtn" class="btn btn-success w-100">
                    <i class="fas fa-print me-1"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>

    <!-- Container untuk event (akan di-update via AJAX) -->
    <div id="eventsContainer">
        @include('organizer.events.partials.events_list', ['events' => $events])
    </div>
</div>

<form id="printForm" action="{{ route('organizer.events.print') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="event_ids" id="printEventIds">
</form>
@endsection

@push('scripts')
<script>
    let currentView = localStorage.getItem('eventViewMode') || 'grid';
    let currentPage = 1;

    function fetchEvents() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusSelect').value;
        const sort = document.getElementById('sortSelect').value;
        const url = `{{ route('organizer.events.index') }}?search=${encodeURIComponent(search)}&status=${status}&sort=${sort}&page=${currentPage}`;
        
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('eventsContainer').innerHTML = html;
            applyView();
            attachCheckboxEvents();
        });
    }

    function applyView() {
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        if (!gridView || !listView) return;
        if (currentView === 'grid') {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            gridBtn.classList.add('active-view');
            listBtn.classList.remove('active-view');
        } else {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            listBtn.classList.add('active-view');
            gridBtn.classList.remove('active-view');
        }
    }

    function attachCheckboxEvents() {
        // Event delegation untuk checkbox di konten yang baru dimuat
        document.querySelectorAll('.event-checkbox, .event-checkbox-list').forEach(cb => {
            cb.removeEventListener('change', syncCheckboxes);
            cb.addEventListener('change', syncCheckboxes);
        });
        // Select All di list view
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll) {
            selectAll.removeEventListener('change', selectAllHandler);
            selectAll.addEventListener('change', selectAllHandler);
        }
    }

    function syncCheckboxes(e) {
        const id = e.target.dataset.id;
        const isChecked = e.target.checked;
        if (e.target.classList.contains('event-checkbox')) {
            const listCb = document.querySelector(`.event-checkbox-list[data-id="${id}"]`);
            if (listCb) listCb.checked = isChecked;
        } else {
            const gridCb = document.querySelector(`.event-checkbox[data-id="${id}"]`);
            if (gridCb) gridCb.checked = isChecked;
        }
        updateSelectAllState();
    }

    function selectAllHandler(e) {
        const isChecked = e.target.checked;
        document.querySelectorAll('.event-checkbox-list').forEach(cb => cb.checked = isChecked);
        document.querySelectorAll('.event-checkbox').forEach(cb => cb.checked = isChecked);
    }

    function updateSelectAllState() {
        const selectAll = document.getElementById('selectAllCheckbox');
        if (!selectAll) return;
        const allListCbs = document.querySelectorAll('.event-checkbox-list');
        const checkedListCbs = document.querySelectorAll('.event-checkbox-list:checked');
        if (allListCbs.length === 0) return;
        selectAll.checked = allListCbs.length === checkedListCbs.length;
        selectAll.indeterminate = checkedListCbs.length > 0 && checkedListCbs.length < allListCbs.length;
    }

    // Event listeners dengan debounce untuk search
    let debounceTimer;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            currentPage = 1;
            fetchEvents();
        }, 300);
    });
    document.getElementById('statusSelect').addEventListener('change', function() {
        currentPage = 1;
        fetchEvents();
    });
    document.getElementById('sortSelect').addEventListener('change', function() {
        currentPage = 1;
        fetchEvents();
    });

    // Pagination via event delegation
    document.addEventListener('click', function(e) {
        if (e.target.matches('.pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            currentPage = url.searchParams.get('page') || 1;
            fetchEvents();
        }
    });

    // Toggle view
    document.getElementById('gridViewBtn').addEventListener('click', function() {
        currentView = 'grid';
        localStorage.setItem('eventViewMode', currentView);
        applyView();
    });
    document.getElementById('listViewBtn').addEventListener('click', function() {
        currentView = 'list';
        localStorage.setItem('eventViewMode', currentView);
        applyView();
    });

    // Print selected
    document.getElementById('printSelectedBtn').addEventListener('click', function() {
        const selected = [];
        document.querySelectorAll('.event-checkbox:checked, .event-checkbox-list:checked').forEach(cb => {
            const id = cb.dataset.id;
            if (id && !selected.includes(id)) selected.push(id);
        });
        if (selected.length === 0) {
            alert('Pilih minimal satu event.');
            return;
        }
        document.getElementById('printEventIds').value = JSON.stringify(selected);
        document.getElementById('printForm').submit();
    });

    // Inisialisasi awal
    fetchEvents();
</script>
<style>
    .active-view {
        background-color: #f97316;
        border-color: #f97316;
        color: white;
    }
    .active-view:hover {
        background-color: #ea580c;
        color: white;
    }
    .event-checkbox, .event-checkbox-list {
        cursor: pointer;
    }
</style>
@endpush