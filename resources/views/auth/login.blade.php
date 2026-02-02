<x-guest-layout>
    <div class="flex items-center justify-center select-none mb-[32px]">
        <div class="hidden-when-collapsed mx-3 mt-4 font-black text-[#003CA2]">
            <h1 class="text-4xl">Quebras</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Usuário -->
        <div>
            <x-input-label for="username" value="Usuário" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')"
                required autofocus />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Senha -->
        <div class="mt-4">
            <x-input-label for="password" value="Senha" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button type="submit">
                Entrar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
