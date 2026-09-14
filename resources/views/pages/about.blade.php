@extends('layouts.app')

@section('title', __('app.About us') . ' — ' . config('business.business.name', 'Rent Specialist'))

@section('content')
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-20 pb-16">
        <h1 class="font-display text-3xl md:text-4xl font-bold text-surface-800 tracking-tight mb-8">{{ __('app.About us') }}</h1>

        <div class="space-y-6 text-surface-700 leading-relaxed">
            <p>
                {{ config('business.business.name', 'Rent Specialist') }} adalah layanan penyewaan kendaraan harian di area operasional kami. Kami mengelola armada mobil, SUV, dan sepeda motor yang selalu diperiksa sebelum diserahkan kepada penyewa. Tujuannya sederhana: Anda mendapat kendaraan yang bersih, laik jalan, dan sesuai waktu yang disepakati.
            </p>

            <p>
                Kami memilih alur berbasis WhatsApp agar komunikasi tetap langsung. Anda bisa menanyakan ketersediaan, durasi, dan harga sekaligus tanpa perlu mengisi formulir panjang. Setelah detail disepakati, tim kami akan menyiapkan kendaraan dan Anda tinggal datang ke lokasi untuk mengambilnya.
            </p>

            <p>
                Pemesanan dibuat oleh staf kami di dalam sistem admin. Hal ini memastikan jadwal, pembayaran, dan kondisi kendaraan tercatat dengan rapi. Anda tidak perlu membuat akun atau membayar di situs ini.
            </p>

            <p>
                Jika ada pertanyaan, silakan hubungi kami via WhatsApp. Kami akan membantu memilih kendaraan yang paling cocok untuk kebutuhan Anda.
            </p>
        </div>

        <div class="mt-10">
            <x-whatsapp-cta :label="__('app.Chat on WhatsApp')" />
        </div>
    </section>
@endsection
