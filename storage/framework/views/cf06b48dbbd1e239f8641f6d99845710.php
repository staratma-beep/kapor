

<?php $__env->startSection('title', 'Dashboard Personil'); ?>
<?php $__env->startSection('page-title', 'Dashboard Personil'); ?>
<?php $__env->startSection('page-subtitle', 'Tahun Anggaran ' . $fiscalYear); ?>

<?php $__env->startSection('content'); ?>

<div class="card" style="border-left: 4px solid var(--accent);">
    <div class="card-body" style="display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
        <div style="width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-light)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px; font-weight:700; flex-shrink:0;">
            <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

        </div>
        <div style="flex:1; min-width:200px;">
            <div style="font-size:18px; font-weight:700;"><?php echo e($user->name); ?></div>
            <div style="font-size:13px; color:#64748B; margin-top:2px;">
                NRP/NIP: <strong><?php echo e($user->nrp_nip); ?></strong>
                <?php if($personnel): ?>
                    &nbsp;·&nbsp; <?php echo e($personnel->rank->name ?? ''); ?>

                    &nbsp;·&nbsp; <?php echo e($personnel->satker->name ?? ''); ?>

                <?php endif; ?>
            </div>
        </div>
        <div>
            <?php if($hasSubmitted): ?>
                <span class="badge badge-success" style="font-size:13px; padding:8px 16px;">
                    <i class="ri-check-double-line"></i> Sudah Input TA <?php echo e($fiscalYear); ?>

                </span>
            <?php else: ?>
                <span class="badge badge-warning" style="font-size:13px; padding:8px 16px;">
                    <i class="ri-alert-line"></i> Belum Input TA <?php echo e($fiscalYear); ?>

                </span>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card">
        <div class="stat-icon <?php echo e($hasSubmitted ? 'green' : 'yellow'); ?>">
            <i class="ri-<?php echo e($hasSubmitted ? 'check-double' : 'edit'); ?>-line"></i>
        </div>
        <div class="stat-value"><?php echo e($submissions->count()); ?></div>
        <div class="stat-label">Item Terisi</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ri-calendar-line"></i></div>
        <div class="stat-value"><?php echo e($fiscalYear); ?></div>
        <div class="stat-label">Tahun Anggaran</div>
    </div>
</div>


<?php if(!$hasSubmitted): ?>
<div class="card" style="border: 2px dashed var(--accent); background: #FFFDF5;">
    <div class="card-body" style="text-align:center; padding:40px;">
        <div style="font-size:48px; color:var(--accent); margin-bottom:16px;"><i class="ri-shirt-line"></i></div>
        <h3 style="font-size:18px; font-weight:700; margin-bottom:8px;">Belum Ada Data Kapor</h3>
        <p style="font-size:14px; color:#64748B; margin-bottom:24px;">
            Silakan input ukuran kapor Anda untuk Tahun Anggaran <?php echo e($fiscalYear); ?>.
        </p>
        <a href="<?php echo e(route('personil.kapor.index')); ?>" class="btn btn-accent" style="font-size:15px; padding:14px 32px;">
            <i class="ri-edit-line"></i> Mulai Input Ukuran Kapor
        </a>
    </div>
</div>
<?php endif; ?>


<?php if($hasSubmitted): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="ri-shirt-line" style="margin-right:8px; color:var(--accent);"></i> Data Ukuran Kapor Anda</h3>
        <span class="badge badge-info">TA <?php echo e($fiscalYear); ?></span>
    </div>
    <div class="card-body">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Item</th>
                        <th>Ukuran</th>
                        <th>Tanggal Input</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($idx + 1); ?></td>
                        <td><span class="badge badge-info"><?php echo e(str_replace('_', ' ', $sub->kaporItem->category ?? '-')); ?></span></td>
                        <td style="font-weight:600;"><?php echo e($sub->kaporItem->item_name ?? '-'); ?></td>
                        <td><span style="font-weight:700; color:var(--primary);"><?php echo e($sub->kaporSize->size_label ?? '-'); ?></span></td>
                        <td><?php echo e($sub->created_at->format('d M Y, H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\1 KAPOR\si-kapor\resources\views/dashboard/personil.blade.php ENDPATH**/ ?>