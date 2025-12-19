<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Desa | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 flex items-center justify-center p-4">

<div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">

    <!-- LEFT BRAND -->
    <div class="hidden md:flex flex-col justify-center p-10 bg-gradient-to-br from-indigo-600 to-blue-700 text-white">
        <h1 class="text-3xl font-bold mb-4">E-Desa</h1>
        <p class="text-lg leading-relaxed">
            Sistem Pelayanan Surat Online Desa.<br>
            Mudah, Cepat, dan Transparan.
        </p>

        <ul class="mt-8 space-y-3 text-sm">
            <li>✔ Pengajuan Surat Online</li>
            <li>✔ Approval Bertingkat</li>
            <li>✔ Notifikasi WhatsApp & Email</li>
        </ul>
    </div>

    <!-- RIGHT FORM -->
    <div class="p-8 sm:p-10">
        <div class="flex justify-center mb-6">
            <button id="btnLogin"
                class="px-4 py-2 font-semibold border-b-2 border-blue-600 text-blue-600">
                Login
            </button>
            <button id="btnRegister"
                class="px-4 py-2 font-semibold text-gray-500">
                Register
            </button>
        </div>

        <!-- LOGIN FORM -->
        <div id="loginForm">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="text-sm font-medium">Password</label>
                    <input type="password" name="password" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <button
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition">
                    Login
                </button>
            </form>
        </div>

        <!-- REGISTER FORM -->
        <div id="registerForm" class="hidden">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium">Nama Lengkap</label>
                    <input type="text" name="name" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="text-sm font-medium">Email</label>
                    <input type="email" name="email" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="text-sm font-medium">Password</label>
                    <input type="password" name="password" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="text-sm font-medium">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>

                <button
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold transition">
                    Register
                </button>
            </form>
        </div>

    </div>
</div>

<!-- TOGGLE SCRIPT -->
<script>
const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const btnLogin = document.getElementById('btnLogin');
const btnRegister = document.getElementById('btnRegister');

btnLogin.onclick = () => {
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');

    btnLogin.classList.add('border-blue-600','text-blue-600');
    btnRegister.classList.remove('border-green-600','text-green-600');
    btnRegister.classList.add('text-gray-500');
};

btnRegister.onclick = () => {
    registerForm.classList.remove('hidden');
    loginForm.classList.add('hidden');

    btnRegister.classList.add('border-green-600','text-green-600');
    btnLogin.classList.remove('border-blue-600','text-blue-600');
    btnLogin.classList.add('text-gray-500');
};
</script>

</body>
</html>
