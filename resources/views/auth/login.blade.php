<x-guest-layout>
<div class="">
    <div class="">

        <a href="{{ url('/') }}" class="block mx-auto h-[80px]">
            <img src="https://atecocables.osole.com.ar/storage/contact/3mmW5kZhEM6wSDWAgnkjYYRLSn89Wgo1hAmZSXEa.svg" class="w-full h-full object-contain" alt="Ateco Cables">
        </a>

        <h1 class="text-2xl font-bold text-[#2D4433] text-center mt-2">Iniciar sesión</h1>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="space-y-1">
                <label class="text-sm font-medium text-[#2D4433]">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       class="w-full border border-slate-300 rounded-md px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">
                @error('email')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-[#2D4433]">Contraseña</label>
                <div class="relative">
                    <input type="password"
                           name="password"
                           id="password"
                           required
                           class="w-full border border-slate-300 rounded-md px-3 py-2 pr-10 text-sm bg-white focus:ring-2 focus:ring-blue-600 focus:outline-none">

                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition cursor-pointer">
                        <svg id="eye-closed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242"/>
                        </svg>

                        <svg id="eye-open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox"
                       name="remember"
                       id="remember"
                       {{ old('remember') ? 'checked' : '' }}
                       class="w-4 h-4 border-slate-300 rounded">
                <label for="remember" class="text-sm text-slate-700">Recordarme</label>
            </div>

            <button type="submit"
                    class="w-full px-4 py-2 bg-[#2D4433] text-white rounded-md text-sm font-medium hover:bg-[#2D6433] transition cursor-pointer">
                Entrar al panel
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeClosed = document.getElementById('eye-closed');
        const eyeOpen = document.getElementById('eye-open');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeClosed.classList.add('hidden');
            eyeOpen.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeClosed.classList.remove('hidden');
            eyeOpen.classList.add('hidden');
        }
    }
</script>
@endpush
</x-guest-layout>