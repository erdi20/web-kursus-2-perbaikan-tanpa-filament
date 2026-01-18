<x-admin-layout>
    {{-- Trix untuk Rich Editor --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <div class="min-h-screen bg-[#F8FAFC] py-10" x-data="{ tab: 'identity' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-10">
                <h2 class="text-3xl font-black uppercase tracking-tight text-slate-800">Konfigurasi Identitas Situs</h2>
                <p class="text-sm font-medium text-slate-500">Kelola informasi publik, logo, dan kontak resmi platform Anda.</p>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Tabs Header --}}
                <div class="mb-6 flex w-max space-x-2 rounded-2xl bg-slate-200/50 p-1.5">
                    <button type="button" @click="tab = 'identity'" :class="tab === 'identity' ? 'bg-white shadow-sm text-[#20C896]' : 'text-slate-500 hover:text-slate-700'" class="rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-widest transition">Identitas</button>
                    <button type="button" @click="tab = 'hero'" :class="tab === 'hero' ? 'bg-white shadow-sm text-[#20C896]' : 'text-slate-500 hover:text-slate-700'" class="rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-widest transition">Hero Section</button>
                    <button type="button" @click="tab = 'contact'" :class="tab === 'contact' ? 'bg-white shadow-sm text-[#20C896]' : 'text-slate-500 hover:text-slate-700'" class="rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-widest transition">Kontak & Sosmed</button>
                    <button type="button" @click="tab = 'legal'" :class="tab === 'legal' ? 'bg-white shadow-sm text-[#20C896]' : 'text-slate-500 hover:text-slate-700'" class="rounded-xl px-6 py-2.5 text-xs font-black uppercase tracking-widest transition">Hukum</button>
                </div>

                <div class="grid grid-cols-1 gap-8">

                    {{-- Tab: Identitas --}}
                    <div x-show="tab === 'identity'" class="space-y-6">
                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm lg:col-span-2">
                                <div class="space-y-6">
                                    <div>
                                        <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Situs</label>
                                        <input type="text" name="site_name" value="{{ old('site_name', $setting->site_name) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Deskripsi Situs</label>
                                        <textarea name="site_description" rows="4" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">{{ old('site_description', $setting->site_description) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Copyright Text</label>
                                        <input type="text" name="copyright_text" value="{{ old('copyright_text', $setting->copyright_text) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                                <label class="mb-4 block text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Logo Website</label>
                                <div class="mb-4 flex justify-center">
                                    <img src="{{ $setting->logo ? asset('storage/' . $setting->logo) : 'https://placehold.co/200x200?text=Logo' }}" class="h-32 w-32 rounded-2xl border object-contain p-2">
                                </div>
                                <input type="file" name="logo" class="w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-black file:text-[#20C896]">
                                <div class="mt-8 border-t pt-6">
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Komisi Mentor (%)</label>
                                    <input type="number" name="mentor_commission_percent" value="{{ old('mentor_commission_percent', $setting->mentor_commission_percent) }}" class="w-full rounded-xl border-slate-200 p-4 text-center font-black text-[#20C896]">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Hero Section --}}
                    <div x-show="tab === 'hero'" class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                            <div>
                                <label class="mb-4 block text-[10px] font-black uppercase tracking-widest text-slate-400">Gambar Hero</label>
                                <img src="{{ $setting->hero_image ? asset('storage/' . $setting->hero_image) : 'https://placehold.co/600x400?text=Hero' }}" class="mb-4 aspect-video w-full rounded-2xl border object-cover">
                                <input type="file" name="hero_image" class="w-full text-xs text-slate-500 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-black file:text-[#20C896]">
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Hero Title</label>
                                    <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 font-bold focus:border-[#20C896]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Hero Subtitle</label>
                                    <textarea name="hero_subtitle" rows="5" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Kontak --}}
                    <div x-show="tab === 'contact'" class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm lg:col-span-2">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Email Resmi</label>
                                    <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Nomor Telepon</label>
                                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Alamat Fisik</label>
                                <textarea name="address" rows="2" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">{{ old('address', $setting->address) }}</textarea>
                            </div>
                            <div>
                                <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Google Maps (Iframe URL)</label>
                                <input type="text" name="gmaps_embed_url" value="{{ old('gmaps_embed_url', $setting->gmaps_embed_url) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]">
                            </div>
                        </div>
                        <div class="space-y-4 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                            <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-slate-400">Media Sosial</h3>
                            <input type="text" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}" placeholder="Facebook URL" class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-3 text-sm focus:border-[#20C896]">
                            <input type="text" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}" placeholder="Instagram URL" class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-3 text-sm focus:border-[#20C896]">
                            <input type="text" name="twitter_url" value="{{ old('twitter_url', $setting->twitter_url) }}" placeholder="X / Twitter URL" class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-3 text-sm focus:border-[#20C896]">
                            <input type="text" name="linkedin_url" value="{{ old('linkedin_url', $setting->linkedin_url) }}" placeholder="Linkedin URL" class="w-full rounded-xl border-slate-200 bg-slate-50/50 p-3 text-sm focus:border-[#20C896]">
                        </div>
                    </div>

                    {{-- Tab: Legal --}}
                    <div x-show="tab === 'legal'" class="space-y-6">
                        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                            <label class="mb-4 block text-xs font-black uppercase tracking-widest text-[#20C896]">Kebijakan Privasi</label>
                            <input id="privacy" type="hidden" name="privacy_policy" value="{{ old('privacy_policy', $setting->privacy_policy) }}">
                            <trix-editor input="privacy" class="prose max-w-none rounded-2xl border-slate-200"></trix-editor>
                        </div>
                        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                            <label class="mb-4 block text-xs font-black uppercase tracking-widest text-[#20C896]">Syarat & Ketentuan</label>
                            <input id="terms" type="hidden" name="terms_conditions" value="{{ old('terms_conditions', $setting->terms_conditions) }}">
                            <trix-editor input="terms" class="prose max-w-none rounded-2xl border-slate-200"></trix-editor>
                        </div>
                    </div>

                </div>

                <div class="mt-10 flex justify-end">
                    <button type="submit" class="rounded-2xl bg-[#20C896] px-12 py-4 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-emerald-200 transition hover:bg-emerald-600 active:scale-95">
                        Simpan Semua Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        trix-toolbar .trix-button-group--file-tools {
            display: none !important;
        }

        trix-editor {
            min-height: 200px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            padding: 1rem !important;
            background: #f8fafc80 !important;
        }
    </style>
</x-admin-layout>
