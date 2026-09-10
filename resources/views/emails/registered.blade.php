<x-mail::message>
# Registrasi Berhasil

Berikut nomor registrasi satpen anda.

<x-mail::button :url="''">
    <p style="font-size:30px;margin-bottom:0;">
        {{ $registerNumber }}
    </p>
</x-mail::button>

Untuk dapat masuk pada portal, anda harus login menggunakan nomor registrasi tersebut. Simpan nomor registrasi diatas.
<br>
<br>
Terimakasih,<br>
{{ config('app.name') }}
</x-mail::message>
