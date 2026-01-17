<x-app-layout>
    <div class="mx-auto max-w-[1100px] p-5">

        <div class="mb-5 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-extrabold text-slate-800">Pembayaran Kursus</h2>
                <div class="text-gray-600">Selesaikan pembayaran untuk mengamankan tempat Anda</div>
            </div>
            <div class="text-sm text-gray-600">Metode aman • Transaksi terenkripsi</div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_360px]">
            {{-- LEFT: Detail Pesanan dan Formulir Pembeli --}}
            <div>
                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-800">Detail Pesanan</h3>
                        <div class="text-sm text-gray-600">Periksa sebelum membayar</div>
                    </div>

                    <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 p-4 shadow-sm">
                        <p class="text-sm font-semibold text-slate-800">Kelas yang Didaftar</p>
                        <h4 class="text-xl font-extrabold">{{ $course->name }}</h4>
                        <p class="mt-1 text-sm text-gray-600">
                            <span class="font-bold">{{ $class->name }}</span><br>
                            Siswa: {{ $user->name }} ({{ $user->email }})
                        </p>
                    </div>

                    <div class="mb-3 mt-5 flex items-center justify-between">
                        <h4 class="text-base font-semibold text-slate-800">Informasi Pembeli</h4>
                        <div class="text-sm text-gray-600">Pastikan data benar</div>
                    </div>


                    <input type="hidden" name="course_class_id" value="{{ $class->id }}">
                    <input type="hidden" name="final_amount" value="{{ $finalPrice }}">

                    <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1 block text-sm text-gray-600">Nama Lengkap</label>
                            <input id="name" name="name" type="text" value="{{ $user->name }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <label for="email" class="mb-1 block text-sm text-gray-600">Email</label>
                            <input id="email" name="email" type="email" value="{{ $user->email }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2.5 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                    </div>



                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <button id="pay-button" type="button" class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-3 font-extrabold text-white shadow-lg transition-transform hover:shadow-xl active:translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                <path fill-rule="evenodd" d="M18 9H2v7a2 2 0 002 2h12a2 2 0 002-2V9zM4 14a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                            Bayar Sekarang
                            <span class="ml-2 rounded-full bg-white px-2 py-0.5 text-sm text-blue-600">
                                Rp {{ number_format($finalPrice, 0, ',', '.') }}
                            </span>
                        </button>

                        <a href="{{ route('detailkursus', $course->slug) }}" class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-3 font-semibold text-gray-600 transition hover:bg-gray-50">
                            Batalkan
                        </a>
                    </div>
                </div>
            </div>

            <aside>
                <div class="space-y-4">
                    <div class="sticky top-5 rounded-xl bg-white p-5 shadow-lg">
                        <div class="mb-4 flex items-center justify-between border-b pb-3">
                            <h4 class="text-lg font-bold text-slate-800">Ringkasan Pembayaran</h4>
                            <div class="text-sm text-gray-600">Tagihan Final</div>
                        </div>

                        {{-- Item yang dibeli --}}
                        <div class="text-sm text-gray-500">Item</div>
                        <div class="mb-3 font-extrabold text-slate-900">{{ $course->name }} (Kelas {{ $class->name }})</div>

                        {{-- Perhitungan Harga --}}
                        <div class="mb-1 flex justify-between text-gray-600">
                            <div>Harga Normal</div>
                            <div>Rp {{ number_format($originalPrice, 0, ',', '.') }}</div>
                        </div>

                        <div class="mb-3 flex justify-between text-red-500">
                            <div>Diskon</div>
                            <div>-Rp {{ number_format($discountAmount, 0, ',', '.') }}</div>
                        </div>

                        {{-- Total Pembayaran --}}
                        <div class="mt-4 flex justify-between border-t border-gray-200 pt-3 text-xl font-extrabold text-slate-800">
                            <div>TOTAL</div>
                            <div>Rp {{ number_format($finalPrice, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div class="rounded-xl bg-white p-4 shadow-sm">
                        <h4 class="mb-2 font-bold text-slate-800">Keamanan Transaksi</h4>
                        <div class="text-sm text-gray-600">Pembayaran diproses melalui Payment Gateway terenkripsi. Setelah pembayaran berhasil, Anda akan otomatis terdaftar dalam kelas ini.</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.getElementById('pay-button').addEventListener('click', function(e) {
            e.preventDefault();
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('payment.success') }}";
                },
                onError: function(result) {
                    alert('Pembayaran gagal.');
                    window.history.back();
                },
                onClose: function() {
                    alert('Pembayaran dibatalkan.');
                    window.history.back();
                }
            });
        });
    </script>
</x-app-layout>
