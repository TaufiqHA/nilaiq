<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raport Peserta Didik</title>
    <style>
        /* CSS Reset & Page Setup */
        @page {
            size: A4;
            margin: 12mm 15mm 12mm 15mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .no-print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 9999;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .btn-print {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-print:hover {
            background-color: #1d4ed8;
        }

        .btn-back {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-back:hover {
            color: white;
        }

        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }

        @media screen {
            body {
                background-color: #e2e8f0;
                padding-top: 60px;
                padding-bottom: 40px;
            }
            body.in-iframe {
                padding-top: 15px !important;
                background-color: #f1f5f9;
            }
            body.in-iframe .no-print-bar {
                display: none !important;
            }
            .page {
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
                padding: 15mm;
                min-height: 297mm;
            }
        }

        @media print {
            .no-print-bar {
                display: none !important;
            }
            body {
                background: none;
                padding: 0;
            }
            .page {
                box-shadow: none;
                padding: 0;
                margin: 0;
                min-height: auto;
            }
            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }

        /* Typography & Layout Elements */
        h1, h2, h3, h4, p {
            margin: 0;
            padding: 0;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10.5pt;
        }

        .header-table td {
            vertical-align: top;
            padding: 1.5px 0;
        }

        .section-title-main {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 4px;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 4px;
        }

        /* Table Styles matching PDF */
        table.raport-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
        }

        table.raport-table th, 
        table.raport-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        table.raport-table th {
            background-color: #BDD7EE !important;
            font-weight: bold;
            text-align: center;
        }

        .bg-header-blue {
            background-color: #BDD7EE !important;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .italic { font-style: italic; }

        /* Custom Box Components */
        .box-catatan {
            border: 1px solid #000;
            padding: 8px 12px;
            min-height: 50px;
            font-size: 10pt;
            margin-bottom: 12px;
        }

        .status-box-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .box-status {
            border: 1px solid #000;
            padding: 8px 12px;
            width: 45%;
            font-size: 10pt;
            text-align: center;
        }

        .signature-container {
            width: 100%;
            margin-top: 15px;
            font-size: 10pt;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
            padding: 0;
        }
    </style>
</head>
<body>

    <!-- Floating Top Bar for Screen Print Action -->
    <div class="no-print-bar">
        <a href="{{ route('wali-kelas.raport') }}" class="btn-back">&larr; Kembali ke Daftar Raport</a>
        <div>
            <button onclick="window.print()" class="btn-print">Cetak / Download PDF</button>
        </div>
    </div>

    @foreach($reports as $reportIndex => $rep)
        @php
            $student = $rep['student'];
            $classWaliKelas = $rep['classWaliKelas'];
            $settingsWaliKelas = $rep['settingsWaliKelas'];
            $academicYear = $settingsWaliKelas?->academicYear;
            $sikap = $student->sikap;
            $absensi = $student->absensi;
            $ekskul = $student->ekskul;
            $prestasi = $student->prestasi;
            $catatanWali = $student->catatanWaliKelas;
            $nilaiKeyed = $rep['nilaiKeyed'];
        @endphp

        <!-- ================= HALAMAN 1 ================= -->
        <div class="page page-break">
            <!-- Header Metadata Identitas -->
            <table class="header-table">
                <tr>
                    <td style="width: 22%; white-space: nowrap;">Nama Sekolah</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 36%;" class="font-bold">{{ strtoupper($settingsWaliKelas?->school_name ?? 'SMP NEGERI 1 TOMPOBULU') }}</td>
                    <td style="width: 17%; white-space: nowrap;">Kelas</td>
                    <td style="width: 2%;">:</td>
                    <td style="width: 21%;">{{ $classWaliKelas?->name ?? 'IX A' }}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap;">Alamat Sekolah</td>
                    <td>:</td>
                    <td>{{ $settingsWaliKelas?->school_address ?? 'Jl. Pendidikan No. 140' }}</td>
                    <td style="white-space: nowrap;">Semester</td>
                    <td>:</td>
                    <td>{{ $academicYear?->semester ?? 'Genap' }}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap;">Nama Peserta Didik</td>
                    <td>:</td>
                    <td class="font-bold">{{ strtoupper($student->name) }}</td>
                    <td style="white-space: nowrap;">Tahun Pelajaran</td>
                    <td>:</td>
                    <td>{{ $academicYear?->academic_year ?? '2025/2026' }}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap;">NIS / NISN</td>
                    <td>:</td>
                    <td>{{ $student->nis ?? '-' }} / {{ $student->nisn ?? '-' }}</td>
                    <td colspan="3"></td>
                </tr>
            </table>

            <div class="section-title-main">CAPAIAN</div>

            <!-- SECTION A. SIKAP -->
            <div class="section-title">A. SIKAP</div>
            <table class="raport-table">
                <thead>
                    <tr>
                        <th style="width: 32%;">Dimensi</th>
                        <th style="width: 68%;">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">Beriman, bertakwa kepada Tuhan Yang Maha Esa, dan berakhlak mulia</td>
                        <td>{{ $sikap?->beriman_bertakwa_dan_berakhlak_mulia ?? 'Menunjukkan sikap baik dalam mensyukuri dan menghargai anugerah Tuhan Yang Maha Esa serta berdoa sebelum dan sesudah melakukan kegiatan' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">Mandiri</td>
                        <td>{{ $sikap?->mandiri ?? 'Dapat melaksanakan kegiatan belajar di kelas dan menyelesaikan tugas dalam waktu yang disepakati' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">Bergotong royong</td>
                        <td>{{ $sikap?->bergotong_royong ?? 'Menerima dan melaksanakan tugas serta peran yang diberikan kelompok dalam sebuah kegiatan bersama' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">Kreatif</td>
                        <td>{{ $sikap?->kreatif ?? 'Mampu mengidentifikasi gagasan kreatif untuk menghadapi situasi dan permasalahan yang terjadi' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">Bernalar kritis</td>
                        <td>{{ $sikap?->bernalar_kritis ?? 'Mampu menyampaikan apa yang dipikirkan secara terperinci' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">Berkebinekaan global</td>
                        <td>{{ $sikap?->berkebinekaan_global ?? 'Dapat mengidentifikasi dan mendeskripsikan ide-ide tentang dirinya dan beberapa macam kelompok di lingkungan sekitarnya' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- SECTION B. PENGETAHUAN DAN KETERAMPILAN (KELOMPOK A) -->
            <div class="section-title">B. PENGETAHUAN DAN KETERAMPILAN</div>
            <table class="raport-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 27%;">Mata Pelajaran</th>
                        <th style="width: 12%;">Nilai Akhir</th>
                        <th style="width: 56%;">Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="italic font-bold bg-header-blue" style="padding-left: 8px; background-color: #D9E1F2 !important;">KELOMPOK A</td>
                    </tr>
                    @forelse($rep['kelompokA'] as $idx => $mapel)
                        @php
                            $nilaiObj = $nilaiKeyed->get($mapel->id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ $mapel->mapel }}</td>
                            <td class="text-center font-bold">{{ $nilaiObj?->nilai ?? '-' }}</td>
                            <td>{{ $nilaiObj?->capaian ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center italic">Belum ada data mata pelajaran Kelompok A</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ================= HALAMAN 2 ================= -->
        <div class="page page-break">
            <!-- SECTION B. PENGETAHUAN DAN KETERAMPILAN (KELOMPOK B) -->
            <table class="raport-table" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 27%;">Mata Pelajaran</th>
                        <th style="width: 12%;">Nilai Akhir</th>
                        <th style="width: 56%;">Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="italic font-bold bg-header-blue" style="padding-left: 8px; background-color: #D9E1F2 !important;">KELOMPOK B</td>
                    </tr>
                    @forelse($rep['kelompokB'] as $idx => $mapel)
                        @php
                            $nilaiObj = $nilaiKeyed->get($mapel->id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $idx + 1 }}</td>
                            <td>{{ $mapel->mapel }}</td>
                            <td class="text-center font-bold">{{ $nilaiObj?->nilai ?? '-' }}</td>
                            <td>{{ $nilaiObj?->capaian ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center">1</td>
                            <td>Seni Budaya</td>
                            <td class="text-center font-bold">-</td>
                            <td>-</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- SECTION C. EKSTRAKURIKULER -->
            <div class="section-title">C. EKSTRAKURIKULER</div>
            <table class="raport-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 37%;">Kegiatan Ekstrakurikuler</th>
                        <th style="width: 58%;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $ekskul?->ekskul1 ?? '' }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>{{ $ekskul?->ekskul2 ?? '' }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>{{ $ekskul?->ekskul3 ?? '' }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <!-- SECTION D. PRESTASI -->
            <div class="section-title">D. PRESTASI</div>
            <table class="raport-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 37%;">Prestasi</th>
                        <th style="width: 58%;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $prestasi?->prestasi1 ?? '' }}</td>
                        <td>{{ $prestasi?->catatan_prestasi1 ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>{{ $prestasi?->prestasi2 ?? '' }}</td>
                        <td>{{ $prestasi?->catatan_prestasi2 ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>{{ $prestasi?->prestasi3 ?? '' }}</td>
                        <td>{{ $prestasi?->catatan_prestasi3 ?? '' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- SECTION E. ABSENSI & PERINGKAT/JUMLAH NILAI SIDE BY SIDE -->
            <div class="section-title">E. ABSENSI</div>
            <div style="display: flex; justify-content: space-between; align-items: stretch; gap: 20px; margin-bottom: 10px;">
                <!-- Tabel Absensi -->
                <div style="width: 48%;">
                    <table class="raport-table" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th style="width: 15%;">No</th>
                                <th style="width: 55%;">Absensi</th>
                                <th style="width: 30%;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center">1</td>
                                <td>Sakit</td>
                                <td class="text-center">{{ $absensi?->sakit ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="text-center">2</td>
                                <td>Izin</td>
                                <td class="text-center">{{ $absensi?->izin ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td class="text-center">3</td>
                                <td>Tanpa Keterangan</td>
                                <td class="text-center">{{ $absensi?->alpa ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Box Peringkat & Jumlah Nilai -->
                <div style="width: 48%; display: flex; flex-direction: column; justify-content: center;">
                    <table class="raport-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 50%; font-size: 12pt;" class="font-bold">Peringkat</td>
                            <td style="width: 50%; font-size: 14pt;" class="text-center font-bold">{{ $rep['peringkat'] }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 11pt;" class="font-bold">Jumlah Nilai</td>
                            <td style="font-size: 13pt;" class="text-center font-bold">{{ $rep['jumlahNilai'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- SECTION F. CATATAN WALI KELAS -->
            <div class="section-title">F. CATATAN WALI KELAS</div>
            <div class="box-catatan">
                {{ $catatanWali?->catatan ?? 'Lebih giat dan rajin belajar lagi di rumah!' }}
            </div>

            <!-- STATUS BOX (KELULUSAN / KENAIKAN) -->
            <div class="status-box-container">
                <div class="box-status">
                    Telah menyelesaikan seluruh rangkaian pembelajaran dan dinyatakan 
                    <span class="font-bold" style="font-size: 11pt; margin-left: 5px;">LULUS</span>
                </div>
            </div>

            <!-- BLOK TANDA TANGAN -->
            @php
                $tanggalRapor = $settingsWaliKelas?->tanggal_penerimaan_rapor 
                    ? \Carbon\Carbon::parse($settingsWaliKelas->tanggal_penerimaan_rapor)->isoFormat('DD MMMM YYYY')
                    : '02 Juni 2026';
            @endphp
            <div class="signature-container">
                <table class="signature-table">
                    <tr>
                        <td style="width: 50%;"></td>
                        <td style="width: 50%; text-align: center;">
                            Malakaji, {{ $tanggalRapor }}<br>
                            Wali Kelas {{ $classWaliKelas?->name ?? 'IX A' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 60px;">Orangtua / Wali Peserta Didik</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>
                            (........................................)
                        </td>
                        <td style="text-align: center;">
                            <span class="font-bold">{{ strtoupper($settingsWaliKelas?->teacher_name ?? 'ABDULLAH, A. Md.') }}</span><br>
                            NIP. {{ $settingsWaliKelas?->teacher_nip ?? '19721225 200605 1 001' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center; padding-top: 20px;">
                            Mengetahui,<br>
                            Kepala Sekolah
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="height: 60px;"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <span class="font-bold">{{ strtoupper($settingsWaliKelas?->principal_name ?? 'SULKIFLI, S. Pd., M. Pd.') }}</span><br>
                            NIP. {{ $settingsWaliKelas?->npsn ?? '19781219 201001 1 016' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach

    <script>
        if (window.self !== window.top) {
            document.body.classList.add('in-iframe');
        }
    </script>
</body>
</html>
