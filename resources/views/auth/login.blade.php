<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EVENTUNWIM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-unwim.png') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1e3a8a',
                        secondary: '#f59e0b',
                    }
                }
            }
        }
    </script>
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .animated-bg {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body class="animated-bg min-h-screen flex items-center justify-center p-4">

    <div class="glass-card rounded-2xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row shadow-2xl">

        <!-- Left Side: Branding -->
        <div
            class="w-full md:w-1/2 p-10 flex flex-col justify-center text-white bg-primary/80 backdrop-blur-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <i class="fa-solid fa-calendar-check text-[200px] absolute -bottom-10 -left-10 transform rotate-12"></i>
            </div>

            <div class="relative z-10">
                <img src="{{ asset('images/logo-unwim.png') }}" alt="Logo UNWIM" class="mx-auto mb-4" style="height:90px;width:auto">
                <h1 class="text-4xl font-bold mb-2">EVENT<span class="text-secondary">UNWIM</span></h1>
                <p class="text-lg opacity-90 mb-8">Platform Manajemen Event & Sertifikasi Digital Universitas Winaya
                    Mukti</p>

                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <span>Manajemen Event Terpusat</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <span>Sertifikat Digital Otomatis</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fa-solid fa-qrcode"></i>
                        </div>
                        <span>Absensi QR Code</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-10 bg-white flex flex-col justify-center">

            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Selamat Datang</h2>
                <p class="text-gray-500">Silakan masuk untuk mengelola event</p>
            </div>

            <!-- SSO Buttons -->
            <div class="space-y-3 mb-6">
                <a href="{{ route('auth.redirect', 'google') }}"
                    class="w-full flex items-center justify-center gap-3 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all group">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5"
                        alt="Google">
                    <span class="font-medium text-gray-700 group-hover:text-black">Masuk dengan Google</span>
                </a>

                <a href="{{ route('auth.redirect', 'unwim') }}"
                    class="w-full flex items-center justify-center gap-3 py-3 bg-primary/5 border border-primary/20 rounded-xl hover:bg-primary/10 transition-all group">
                    <i class="fa-solid fa-building-columns text-primary"></i>
                    <span class="font-medium text-primary">Login SSO UNWIM</span>
                </a>
            </div>

            <div class="relative flex py-2 items-center mb-4">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink mx-4 text-gray-400 text-xs uppercase">Atau login manual</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <form action="{{ url('login') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email"
                            class="pl-10 w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('email') border-red-500 @enderror"
                            placeholder="email@unwim.ac.id" value="{{ old('email') }}" required>
                    </div>
                    @error('email')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password"
                            class="pl-10 w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all @error('password') border-red-500 @enderror"
                            placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded text-primary focus:ring-primary border-gray-300">
                        <span class="text-gray-600">Ingat Saya</span>
                    </label>
                    <a href="{{ url('password/reset') }}" class="text-primary hover:underline font-medium">Lupa
                        Password?</a>
                </div>

                <button type="submit"
                    class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-blue-900 transition-all shadow-lg shadow-blue-900/30 active:scale-[0.98]">
                    Masuk
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Universitas Winaya Mukti. All rights reserved.
            </p>
        </div>
    </div>

</body>

</html>