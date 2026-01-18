<x-admin-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-12" x-data="{
        isOpen: false,
        isEdit: false,
        user: { id: '', name: '', email: '', role: 'student' },
        actionUrl: '',
        openModal(mode, data = null) {
            this.isOpen = true;
            this.isEdit = (mode === 'edit');
            if (this.isEdit && data) {
                this.user = { ...data };
                this.actionUrl = `/admin/users/${data.id}`;
            } else {
                this.user = { id: '', name: '', email: '', role: 'student' };
                this.actionUrl = '{{ route('admin.users.store') }}';
            }
        }
    }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tight text-slate-800">Manajemen Pengguna</h2>
                    <p class="text-sm font-medium italic text-slate-500">Data Admin, Mentor, dan Siswa.</p>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center rounded-xl border border-slate-200 bg-white px-3 py-1.5 shadow-sm">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user..." class="w-40 border-none bg-transparent text-xs focus:ring-0 md:w-60">
                        <button type="submit" class="text-slate-400 hover:text-[#20C896]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg></button>
                    </form>
                    <button @click="openModal('create')" class="rounded-xl bg-[#20C896] px-6 py-2.5 text-xs font-black uppercase tracking-widest text-white shadow-md transition hover:bg-emerald-600">Tambah User</button>
                </div>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-6 py-4">Pengguna</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Terdaftar</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @foreach ($users as $u)
                            <tr class="transition hover:bg-slate-50/50">
                                <td class="flex items-center gap-4 px-6 py-4">
                                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-100 text-xs font-black text-slate-400">
                                        {{ $u->initials }}
                                    </div>
                                    <div>
                                        <span class="block font-bold text-slate-700">{{ $u->name }}</span>
                                        <span class="text-[10px] font-medium text-slate-400">{{ $u->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $u->role == 'admin' ? 'bg-rose-100 text-rose-600' : ($u->role == 'mentor' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600') }} rounded-full px-3 py-1 text-[10px] font-black uppercase">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">{{ $u->created_at->format('d M Y') }}</td>
                                <td class="space-x-1 px-6 py-4 text-right">
                                    <button @click="openModal('edit', { id: '{{ $u->id }}', name: '{{ $u->name }}', email: '{{ $u->email }}', role: '{{ $u->role }}' })" class="rounded-xl p-2 text-amber-500 transition hover:bg-amber-50">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="delete-form inline">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete rounded-xl p-2 text-rose-500 transition hover:bg-rose-50">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $users->links() }}</div>
        </div>

        {{-- MODAL OVERLAY --}}
        <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex min-h-screen items-center justify-center p-4 text-center">
                <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md"></div>

                <div x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" class="relative w-full max-w-4xl transform overflow-hidden rounded-[3rem] bg-white p-12 text-left shadow-2xl transition-all">

                    <div class="mb-10">
                        <h3 class="text-3xl font-black uppercase tracking-tight text-slate-800" x-text="isEdit ? 'Update Pengguna' : 'Tambah Pengguna Baru'"></h3>
                        <p class="mt-2 text-sm font-medium italic text-slate-400">Konfigurasi hak akses dan informasi autentikasi pengguna platform.</p>
                    </div>

                    <form :action="actionUrl" method="POST">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                        <div class="grid grid-cols-1 gap-10 md:grid-cols-2">
                            <div class="space-y-6">
                                <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-8">
                                    <h4 class="mb-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#20C896]">Informasi Profil</h4>

                                    <div class="space-y-5">
                                        <div>
                                            <label class="mb-2 ml-1 block text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Lengkap</label>
                                            <input type="text" name="name" x-model="user.name" placeholder="Contoh: Muhammad Rizky" class="w-full rounded-2xl border-slate-200 bg-white p-4 font-bold text-slate-700 transition placeholder:text-slate-300 focus:border-[#20C896] focus:ring-0" required>
                                        </div>
                                        <div>
                                            <label class="mb-2 ml-1 block text-[10px] font-black uppercase tracking-widest text-slate-400">Alamat Email</label>
                                            <input type="email" name="email" x-model="user.email" placeholder="nama@email.com" class="w-full rounded-2xl border-slate-200 bg-white p-4 font-bold text-slate-700 transition placeholder:text-slate-300 focus:border-[#20C896] focus:ring-0" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="rounded-[2rem] border border-slate-100 bg-slate-50/50 p-8">
                                    <h4 class="mb-6 text-[10px] font-black uppercase tracking-[0.2em] text-[#20C896]">Akses & Keamanan</h4>

                                    <div class="space-y-5">
                                        <div>
                                            <label class="mb-2 ml-1 block text-[10px] font-black uppercase tracking-widest text-slate-400">Hak Akses (Role)</label>
                                            <select name="role" x-model="user.role" class="w-full appearance-none rounded-2xl border-slate-200 bg-white p-4 font-black text-slate-700 transition focus:border-[#20C896] focus:ring-0">
                                                <option value="student">STUDENT</option>
                                                <option value="mentor">MENTOR</option>
                                                <option value="admin">ADMINISTRATOR</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="mb-2 ml-1 block text-[10px] font-black uppercase tracking-widest text-slate-400">Password</label>
                                            <input type="password" name="password" placeholder="••••••••" class="w-full rounded-2xl border-slate-200 bg-white p-4 font-bold text-slate-700 transition focus:border-[#20C896] focus:ring-0" :required="!isEdit">
                                            <p x-show="isEdit" class="mt-2 text-[10px] font-bold italic leading-tight text-amber-500">* Biarkan kosong jika tidak ingin mengubah password lama.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 flex items-center justify-end gap-4 border-t border-slate-100 pt-8">
                            <button type="button" @click="isOpen = false" class="px-8 py-4 text-xs font-black uppercase tracking-widest text-slate-400 transition hover:text-slate-600">
                                Batal
                            </button>
                            <button type="submit" class="transform rounded-2xl bg-[#20C896] px-10 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-emerald-100 transition-all hover:-translate-y-1 hover:bg-emerald-600 hover:shadow-emerald-200">
                                <span x-text="isEdit ? 'Update Data Pengguna' : 'Simpan Pengguna Baru'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                Swal.fire({
                    title: 'Hapus User?',
                    text: "Data tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#20C896',
                    cancelButtonColor: '#f43f5e',
                    confirmButtonText: 'Ya, Hapus!',
                    customClass: {
                        popup: 'rounded-[2rem]'
                    }
                }).then((r) => {
                    if (r.isConfirmed) this.closest('form').submit();
                });
            });
        });
    </script>
</x-admin-layout>
