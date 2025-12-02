# ☁️ Sueb Cloud OOP Console

Folder ini memuat contoh program PHP OOP yang tetap memakai tema layanan cloud Sueb Cloud, namun dipisahkan dari landing page utama seperti permintaan.

## Struktur
```
sueb-cloud-oop/
├── index.php             # Entry point + tampilan mini console
├── style.css             # Styling bertema cloud malam
├── src/
│   ├── Core/
│   │   └── ServiceUnit.php       # Parent class + magic method
│   └── Services/
│       ├── ComputeService.php    # Child class 1
│       └── StorageService.php    # Child class 2
└── assets/                        # Tempat aset tambahan (opsional)
```

## Pemenuhan Ketentuan
1. **Parent class**: `ServiceUnit` memakai access modifier (`protected`, `private`, `public`) sesuai alasan enkapsulasi.
2. **Two child class**: `ComputeService` dan `StorageService` berada di namespace `SuebCloud\Services` dan mewarisi `ServiceUnit` lewat keyword `use` di `index.php`.
3. **Object operator**: Di `index.php` object operator dipakai untuk memanggil method publik (`$compute->assignTask()`, `$storage->getRegion()`) serta mengakses properti publik (`$compute->serviceCode`).
4. **≥5 method total**: Kombinasi method parent + child melampaui batas (assignTask, recordIncident, resolveIncident, scaleCapacity, getStatusSnapshot, deployCluster, toggleAutoHealing, replicateBucket, updatePolicy, dll.).
5. **≥3 magic method**: `ServiceUnit` menerapkan `__construct`, `__get`, dan `__toString`.

## Menjalankan (CLI Output)
```powershell
php sueb-cloud-oop\index.php
```
Perintah tersebut akan mencetak laporan mission console langsung di terminal tanpa membutuhkan server web.
