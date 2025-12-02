@extends('layouts.index')

@section('title', 'User')

@section('content')
<div class="container">
    <h1 class="mb-2">User</h1>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-primary mb-3 w-25" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openModal()">
            + Tambah User
        </button>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ ucwords(strtolower($user->name)) }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ \Carbon\Carbon::parse($user->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td>{{ \Carbon\Carbon::parse($user->updated_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i:s') }}</td>
                <td>
                    <button class="btn btn-warning btn-sm"
                        onclick='openModal(@json([
                            "id" => $user->id,
                            "name" => $user->name,
                            "email" => $user->email
                        ]))'>
                        Edit
                    </button>
                    <form action="{{ route('manage.users.destroy', $user->id) }}" method="POST" class="d-inline delete-form" data-user-name="{{ $user->name }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                    </form>
                </td>        
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada user yang terdaftar.</td>
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
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="userForm">
            @csrf
            <input type="hidden" name="_method" id="_method" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control lowercase" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" class="form-control lowercase" name="email" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password"
                                   class="form-control"
                                   placeholder="Enter new password (minimum 8 characters)">
                            <div class="input-group-append">
                                <span class="input-group-text toggle-password h-100" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <div id="password-hint" style="display: none;">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
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
        clearSearchBtn.addEventListener('click', function () {
            searchInput.value = '';
            this.closest('form').submit();
        });
    }

    function openModal(data = null) {
        const form = document.getElementById('userForm');
        const methodInput = document.getElementById('_method');
        const modalElement = document.getElementById('userModal');
        const modalTitle = modalElement.querySelector('.modal-title');
        const passwordHint = document.getElementById('password-hint');

        form.reset();
        methodInput.value = 'POST';
        form.action = '{{ route("manage.users.store") }}';
        modalTitle.textContent = 'Tambah User';
        passwordHint.style.display = 'none';

        if (data) {
            form.action = `/manage/users/${data.id}`;
            methodInput.value = 'PUT';
            document.getElementById('name').value = data.name;
            document.getElementById('email').value = data.email;
            modalTitle.textContent = 'Edit User';
            passwordHint.style.display = 'block';
        }

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }

    // Prevent sending empty password on update
    document.getElementById('userForm').addEventListener('submit', function (e) {
        const methodInput = document.getElementById('_method');
        const passwordInput = document.getElementById('password');
        if (methodInput.value === 'PUT' && !passwordInput.value) {
            passwordInput.removeAttribute('name'); // exclude password from request
        }
    });

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const form = this.closest('form');
            const userName = form.dataset.userName;
            Swal.fire({
                title: `Hapus user ${userName}?`,
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
    <script src="{{ asset('js/password/form.js') }}"></script>
@endpush
