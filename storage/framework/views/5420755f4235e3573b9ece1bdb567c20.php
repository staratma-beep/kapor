

<?php $__env->startSection('title', 'Dashboard Superadmin'); ?>
<?php $__env->startSection('breadcrumb', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1>Dashboard</h1>
            <p>Selamat datang kembali. Berikut ringkasan data SI-KAPOR TA <?php echo e($stats['fiscal_year']); ?>.</p>
        </div>
        <div class="page-header-actions">
            
            <div style="display:flex;align-items:center;gap:8px;margin-right:8px;background:#fff;padding:4px 10px;border-radius:var(--radius-sm);border:1px solid var(--slate-200);">
                <i class="ri-calendar-line" style="color:var(--brand);"></i>
                <select onchange="window.location.href='?year='+this.value" style="border:none;outline:none;font-size:13px;font-weight:600;color:var(--slate-700);cursor:pointer;background:transparent;">
                    <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($year); ?>" <?php echo e($fiscalYear == $year ? 'selected' : ''); ?>>
                            TA <?php echo e($year); ?> <?php echo e($year == $defaultYear ? '(Aktif)' : ''); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <?php if($fiscalYear != $defaultYear): ?>
                <span class="badge badge-warning" style="margin-right:12px;padding:8px 12px;"><i class="ri-history-line"></i> Mode Arsip</span>
            <?php endif; ?>

            <?php if($stats['is_locked']): ?>
                <span class="btn btn-outline" style="cursor:default;"><span class="status-dot red"></span> Sistem Terkunci</span>
            <?php else: ?>
                <span class="btn btn-outline" style="cursor:default;"><span class="status-dot green"></span> Sistem Aktif</span>
            <?php endif; ?>
            <a href="<?php echo e(route('superadmin.settings.index')); ?>" class="btn btn-primary"><i class="ri-settings-3-line"></i> Pengaturan</a>
        </div>
    </div>
</div>


<div class="stats-row" style="grid-template-columns: repeat(5, 1fr);">
    
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Total POLRI</span>
            <div class="stat-icon-sm" style="background:var(--success-bg);color:var(--success);"><i class="ri-team-line"></i></div>
        </div>
        <div class="stat-value"><?php echo e(number_format($stats['total_polri'])); ?></div>
        <div class="stat-footer">Personil Aktif</div>
    </div>

    
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Total PNS/P3K</span>
            <div class="stat-icon-sm" style="background:var(--warning-bg);color:var(--warning);"><i class="ri-user-star-line"></i></div>
        </div>
        <div class="stat-value"><?php echo e(number_format($stats['total_pns'])); ?></div>
        <div class="stat-footer">Personil Aktif</div>
    </div>

    
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Total Personil</span>
            <div class="stat-icon-sm" style="background:var(--brand-bg);color:var(--brand);"><i class="ri-group-line"></i></div>
        </div>
        <div class="stat-value"><?php echo e(number_format($stats['total_personnel'])); ?></div>
        <div class="stat-footer">Polri + PNS</div>
    </div>

    
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Sudah Input</span>
            <div class="stat-icon-sm" style="background:var(--info-bg);color:var(--info);"><i class="ri-check-double-line"></i></div>
        </div>
        <div class="stat-value" style="color:var(--info);"><?php echo e(number_format($stats['personnel_submitted'])); ?></div>
        <div class="stat-footer"><span class="up"><i class="ri-arrow-up-s-fill"></i> <?php echo e($stats['fill_rate']); ?>%</span> progres</div>
    </div>

    
    <div class="stat-card">
        <div class="stat-top">
            <span class="stat-label">Belum Input</span>
            <div class="stat-icon-sm" style="background:#fef2f2;color:var(--danger);"><i class="ri-time-line"></i></div>
        </div>
        <div class="stat-value" style="color:var(--danger);"><?php echo e(number_format($stats['personnel_pending'])); ?></div>
        <div class="stat-footer">Menunggu pengisian</div>
    </div>
</div>


<div class="grid-3-1">
    
    <div class="card">
        <div class="card-head">
            <h3><i class="ri-building-2-line" style="margin-right:6px;color:var(--brand);"></i> Progres per Satker</h3>
            <div class="card-actions">
                <span class="badge badge-info">TA <?php echo e($stats['fiscal_year']); ?></span>
            </div>
        </div>
        <div class="card-body flush">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;text-align:center;">No</th>
                            <th>Satker</th>
                            <th style="width:80px;text-align:center;">POLRI</th>
                            <th style="width:80px;text-align:center;">PNS</th>
                            <th style="width:80px;text-align:center;">Jml</th>
                            <th style="width:80px;text-align:center;">Input</th>
                            <th style="width:140px;text-align:center;">Progres</th>
                            <th style="width:70px;text-align:center;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $satkerStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $total = $s->total_personnel;
                            $done = $s->submitted_count;
                            $pct = $total > 0 ? round(($done / $total) * 100) : 0;
                            $barCls = $pct >= 80 ? 'green' : ($pct >= 50 ? 'yellow' : 'red');
                            $badgeCls = $pct >= 80 ? 'badge-success' : ($pct >= 50 ? 'badge-warning' : 'badge-danger');
                        ?>
                        <tr>
                            <td style="text-align:center;color:var(--slate-400);font-size:12px;"><?php echo e($index + 1); ?></td>
                            <td style="font-weight:600;font-size:12px;"><?php echo e($s->name); ?></td>
                            <td style="text-align:center;color:var(--slate-500);font-size:12px;"><?php echo e(number_format($s->polri_count)); ?></td>
                            <td style="text-align:center;color:var(--slate-500);font-size:12px;"><?php echo e(number_format($s->pns_count)); ?></td>
                            <td style="text-align:center;font-weight:700;font-size:12px;"><?php echo e(number_format($total)); ?></td>
                            <td style="text-align:center;font-size:12px;"><?php echo e(number_format($done)); ?></td>
                            <td>
                                <div class="progress" style="height:8px;">
                                    <div class="progress-bar <?php echo e($barCls); ?>" style="width:<?php echo e($pct); ?>%;"></div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge <?php echo e($badgeCls); ?>" style="font-size:10px;padding:2px 6px;"><?php echo e($pct); ?>%</span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" style="text-align:center;color:var(--slate-400);padding:32px;">Belum ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if($satkerStats->isNotEmpty()): ?>
                    <?php
                        $grandPolri = $satkerStats->sum('polri_count');
                        $grandPns = $satkerStats->sum('pns_count');
                        $grandTotal = $satkerStats->sum('total_personnel');
                        $grandDone = $satkerStats->sum('submitted_count');
                        $grandPct = $grandTotal > 0 ? round(($grandDone / $grandTotal) * 100) : 0;
                        $grandBarCls = $grandPct >= 80 ? 'green' : ($grandPct >= 50 ? 'yellow' : 'red');
                        $grandBadgeCls = $grandPct >= 80 ? 'badge-success' : ($grandPct >= 50 ? 'badge-warning' : 'badge-danger');
                    ?>
                    <tfoot>
                        <tr style="background:var(--slate-50);border-top:2px solid var(--slate-200);">
                            <td style="text-align:center;"></td>
                            <td style="font-weight:700;">TOTAL</td>
                            <td style="text-align:center;font-weight:700;color:var(--slate-500);"><?php echo e(number_format($grandPolri)); ?></td>
                            <td style="text-align:center;font-weight:700;color:var(--slate-500);"><?php echo e(number_format($grandPns)); ?></td>
                            <td style="text-align:center;font-weight:700;"><?php echo e(number_format($grandTotal)); ?></td>
                            <td style="text-align:center;font-weight:700;"><?php echo e(number_format($grandDone)); ?></td>
                            <td>
                                <div class="progress" style="height:10px;">
                                    <div class="progress-bar <?php echo e($grandBarCls); ?>" style="width:<?php echo e($grandPct); ?>%;"></div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge <?php echo e($grandBadgeCls); ?>"><?php echo e($grandPct); ?>%</span>
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    
    <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="card">
            <div class="card-head">
                <h3><i class="ri-history-line" style="margin-right:6px;color:var(--brand);"></i> User Terbaru</h3>
            </div>
            <div class="card-body flush">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div style="display:flex;flex-direction:column;">
                                        <span style="font-weight:600;font-size:12px;"><?php echo e($ru->name); ?></span>
                                        <span style="font-size:10px;color:var(--slate-400);"><?php echo e($ru->nrp_nip); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info" style="font-size:10px;"><?php echo e($ru->roles->first()->name ?? '-'); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\1 KAPOR\si-kapor\resources\views/dashboard/superadmin.blade.php ENDPATH**/ ?>