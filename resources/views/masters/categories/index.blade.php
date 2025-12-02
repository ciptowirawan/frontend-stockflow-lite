@extends('layouts.index')

@section('title', 'Category')

@section('content')
<div class="container">
    <h1 class="mb-2">Kategori</h1>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary mb-3 w-25" onclick="openModal()">
            + Tambah Kategori
        </button>
    </div>

    {{-- Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Updated By</th>
                <th>Updated At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $index => $kategori)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucwords(strtolower($kategori->name)) }}</td>
                <td>{{ $kategori->updated_by->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($kategori->updated_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td width="150px">
                    <button class="btn btn-warning btn-sm" data-kategori-data='@json($kategori)' onclick="openModal(this)">Edit</button>
                    <form action="{{ route('manage.categories.destroy', $kategori->id) }}" method="POST" class="d-inline delete-form" data-kategori-name="{{ $kategori->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                    </form>
                </td>
            </tr> 
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada kategori yang terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between mt-3">
        @if($pagination->prev ?? false)
            <a href="{{ $pagination->prev }}" class="btn btn-outline-primary">Previous</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Previous</button>
        @endif

        @if($pagination->next ?? false)
            <a href="{{ $pagination->next }}" class="btn btn-outline-primary">Next</a>
        @else
            <button class="btn btn-outline-secondary" disabled>Next</button>
        @endif
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="kategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="kategoriForm">
            @csrf
            <input type="hidden" name="_method" id="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" class="form-control uppercase" name="name" id="nama" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('body-scripts')
<script>
    const clearSearchBtn = document.getElementById('clear-search-btn');
    const searchInput = document.getElementById('search-input');

    if (clearSearchBtn) { 
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = ''; 
            this.closest('form').submit();
        });
    }

    function openModal(buttonElement = null) {
        const form = document.getElementById('kategoriForm');
        const methodInput = document.getElementById('_method');
        const modalElement = document.getElementById('kategoriModal');
        const modalTitle = modalElement.querySelector('.modal-title'); 
        
        form.reset();
        form.action = '/manage/categories';
        methodInput.value = 'POST';
        modalTitle.textContent = 'Tambah Kategori';

        let kategoriData = null;

        if (buttonElement && buttonElement.hasAttribute('data-kategori-data')) {
            const jsonString = buttonElement.getAttribute('data-kategori-data');
            
            try {
                kategoriData = JSON.parse(jsonString);
            } catch (e) {
                console.error("Gagal memparsing data kategori:", e);
                alert('Gagal memuat data kategori. Silakan cek konsol.');
                return;
            }
        }

        if (kategoriData) {
            form.action = `/manage/categories/${kategoriData.id}`;
            methodInput.value = 'PUT';
        
            document.getElementById('nama').value = kategoriData.name; 
            
            modalTitle.textContent = 'Edit Kategori'; 
        }
        
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {            
            const form = this.closest('form');
            const kategoriName = form.dataset.kategoriName; 
            Swal.fire({
                title:`Hapus kategori ${kategoriName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
