<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Ukuran Kapor - <?php echo e($satker->name); ?></title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 6.5pt;
            margin: 0;
            padding: 5px;
        }
        .header-left {
            text-align: center;
            margin-bottom: 3px;
            font-size: 6pt;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding-bottom: 1px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 0px;
            font-size: 8pt;
        }
        .subtitle {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
            font-size: 8pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        th, td {
            border: 1px solid black;
            padding: 0.5px 1px;
            text-align: center;
            word-wrap: break-word;
            line-height: 1;
        }
        th {
            background-color: #f2f2f2;
            font-size: 6pt;
        }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border: none;
        }
        .footer-table td {
            border: none;
            text-align: center;
            width: 33%;
        }
    </style>
</head>
<body>
    <div class="header-left">
        KEPOLISIAN NEGARA REPUBLIK INDONESIA<br>
        DAERAH NUSA TENGGARA BARAT<br>
        BIRO LOGISTIK
    </div>

    <div class="title">DATA UKURAN KAPOR PERSONEL POLRI SATKER <?php echo e(strtoupper($satker->name)); ?></div>
    <div class="subtitle">UNTUK DUKUNGAN KAPOR TA. <?php echo e($fiscalYear); ?></div>

    <table>
        <thead>
            <tr>
                <th rowspan="3" style="width: 20px;">NO</th>
                <th rowspan="3">NAMA</th>
                <th rowspan="3">PANGKAT</th>
                <th rowspan="3">GOLONGAN</th>
                <th rowspan="3">NRP</th>
                <th rowspan="3">JABATAN</th>
                <th rowspan="3">BAG/FUNGSI</th>
                <th rowspan="3">JENIS KELAMIN P/W</th>
                <th colspan="9">UKURAN</th>
                <th rowspan="3">KETERANGAN</th>
            </tr>
            <tr>
                <th rowspan="2">TUTUP KEPALA</th>
                <th colspan="3">TUTUP BADAN</th>
                <th colspan="2">TUTUP KAKI</th>
                <th rowspan="2">JAKET</th>
                <th rowspan="2">SABUK</th>
                <th rowspan="2">JILBAB</th>
            </tr>
            <tr>
                <th>KEMEJA</th>
                <th>CELANA/ ROK</th>
                <th>T.SHIRT/ OLHRG</th>
                <th>DINAS</th>
                <th>OLHRG</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $personnels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $subs = $p->submissions->keyBy(function($s) {
                        return strtolower($s->kaporItem->item_name);
                    });
                    
                    $getVal = function($itemName) use ($subs) {
                        $s = $subs->get(strtolower($itemName));
                        return $s ? $s->kaporSize->size_label : '';
                    };
                ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td style="text-align: left;"><?php echo e(strtoupper($p->full_name)); ?></td>
                    <td><?php echo e($p->rank->name); ?></td>
                    <td><?php echo e($p->golongan); ?></td>
                    <td><?php echo e($p->nrp); ?></td>
                    <td style="text-align: left;"><?php echo e(strtoupper($p->jabatan)); ?></td>
                    <td><?php echo e(strtoupper($p->bagian)); ?></td>
                    <td><?php echo e($p->gender == 'L' ? 'P' : 'W'); ?></td>
                    
                    
                    <td><?php echo e($getVal('Tutup Kepala')); ?></td>
                    <td><?php echo e($getVal('Kemeja')); ?></td>
                    <td><?php echo e($getVal('Celana/Rok')); ?></td>
                    <td><?php echo e($getVal('T-Shirt/Olahraga')); ?></td>
                    <td><?php echo e($getVal('Sepatu Dinas')); ?></td>
                    <td><?php echo e($getVal('Sepatu Olahraga')); ?></td>
                    <td><?php echo e($getVal('Jaket')); ?></td>
                    <td><?php echo e($getVal('Sabuk')); ?></td>
                    <td><?php echo e($getVal('Jilbab')); ?></td>
                    
                    <td><?php echo e($p->keterangan); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td></td>
                <td></td>
                <td>
                    <?php echo e($location); ?>, <?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?><br>
                    <?php echo e(strtoupper($signatory_role)); ?><br>
                    <br><br><br><br>
                    <span style="text-decoration: underline; font-weight: bold;"><?php echo e(strtoupper($signatory_name)); ?></span><br>
                    <?php echo e(strtoupper($signatory_nrp)); ?>

                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php /**PATH D:\1 KAPOR\si-kapor\resources\views/admin/reports/personnel_satker_pdf.blade.php ENDPATH**/ ?>