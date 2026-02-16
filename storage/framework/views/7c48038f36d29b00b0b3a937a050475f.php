

<?php $__env->startSection('title', 'Personel'); ?>
<?php $__env->startSection('breadcrumb', 'Personel'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-row" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <h1 class="page-title">Personel</h1>
            <p class="page-subtitle">Direktori personel dan informasi kapor</p>
        </div>
        <div class="page-header-actions" style="display: flex; gap: 12px;">
            <button class="btn btn-outline" onclick="openModal('importModal')" style="display: flex; align-items: center; gap: 8px; border-radius: 10px; padding: 10px 18px; font-weight: 600; border-color: #E5E7EB; color: #374151;">
                <i class="ri-file-upload-line" style="color: #B91C1C;"></i> Impor CSV
            </button>
            <button class="btn btn-primary" onclick="openModal('addPersonnelModal')" style="display: flex; align-items: center; gap: 8px; border-radius: 10px; padding: 10px 18px; font-weight: 700;">
                <i class="ri-user-add-line"></i> Tambah Personel
            </button>
        </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-blue">
            <i class="ri-group-line"></i>
        </div>
        <div class="stat-content">
            <span class="stat-label">TOTAL RILL PERSONEL</span>
            <span class="stat-number"><?php echo e(number_format($stats['total_real'])); ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-green">
            <i class="ri-checkbox-circle-line"></i>
        </div>
        <div class="stat-content">
            <span class="stat-label">SUDAH INPUT DATA</span>
            <span class="stat-number"><?php echo e(number_format($stats['submitted'])); ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-red">
            <i class="ri-close-circle-line"></i>
        </div>
        <div class="stat-content">
            <span class="stat-label">BELUM INPUT DATA</span>
            <span class="stat-number"><?php echo e(number_format($stats['pending'])); ?></span>
        </div>
    </div>
</div>



<div class="filter-bar">
    <form method="GET" action="<?php echo e(route('admin.personnel.index')); ?>" class="filter-form" id="filterForm">
        <div class="search-input" style="flex: 2;">
            <i class="ri-search-line"></i>
            <input type="text" name="search" id="searchInput" value="<?php echo e(request('search')); ?>" placeholder="Cari berdasarkan nama, NRP/NIP, atau golongan..." oninput="debounceSearch()">
            <?php if(request('search')): ?>
                <button type="button" class="clear-search" onclick="document.getElementById('searchInput').value=''; document.getElementById('filterForm').submit();" style="background: none; border: none; color: #D1D5DB; cursor: pointer; padding: 4px; display: flex; align-items: center; margin-left: 8px;">
                    <i class="ri-close-circle-fill" style="font-size: 18px;"></i>
                </button>
            <?php endif; ?>
        </div>

        <div class="filter-group">
            <div class="custom-select-wrapper" style="flex: 1;">
                <div class="custom-select" onclick="toggleDropdown(this)">
                    <div class="select-trigger">
                        <span><?php echo e(request('rank_id') ? $ranks->firstWhere('id', request('rank_id'))->name : 'Semua Pangkat'); ?></span>
                        <i class="ri-arrow-down-s-line"></i>
                    </div>
                    <div class="custom-options">
                        <div class="options-scroll">
                            <div class="option <?php echo e(!request('rank_id') ? 'selected' : ''); ?>" onclick="selectOptionSearch(this, 'rank_id', '', 'Semua Pangkat')">Semua Pangkat</div>
                            <?php $__currentLoopData = $ranks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="option <?php echo e(request('rank_id') == $rank->id ? 'selected' : ''); ?>" onclick="selectOptionSearch(this, 'rank_id', '<?php echo e($rank->id); ?>', '<?php echo e($rank->name); ?>')"><?php echo e($rank->name); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="rank_id" value="<?php echo e(request('rank_id')); ?>">
            </div>

            <div class="custom-select-wrapper" style="flex: 1;">
                <div class="custom-select" onclick="toggleDropdown(this)">
                    <div class="select-trigger">
                        <span><?php echo e(request('satker_id') ? $satkers->firstWhere('id', request('satker_id'))->name : 'Semua Satker'); ?></span>
                        <i class="ri-arrow-down-s-line"></i>
                    </div>
                    <div class="custom-options">
                        <div class="options-scroll">
                            <div class="option <?php echo e(!request('satker_id') ? 'selected' : ''); ?>" onclick="selectOptionSearch(this, 'satker_id', '', 'SEMUA SATKER')">SEMUA SATKER</div>
                            <?php $__currentLoopData = $satkers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="option <?php echo e(request('satker_id') == $satker->id ? 'selected' : ''); ?>" onclick="selectOptionSearch(this, 'satker_id', '<?php echo e($satker->id); ?>', '<?php echo e($satker->name); ?>')"><?php echo e($satker->name); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="satker_id" value="<?php echo e(request('satker_id')); ?>">
            </div>
        </div>

        <div>
            <button type="button" class="btn-outline" onclick="window.location.href=updateQueryStringParameter(window.location.href, 'export', '1')">
                <i class="ri-download-line"></i> Ekspor
            </button>
        </div>
    </form>
</div>


<div class="table-container">
    <div class="table-responsive">
        <table class="user-table">
            <thead>
                <tr>
                    <th style="border-top-left-radius: 12px;">PERSONEL</th>
                    <th>PANGKAT / GOL</th>
                    <th>JABATAN / BAGIAN</th>
                    <th>SATUAN KERJA</th>
                    <th style="border-top-right-radius: 12px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $personnels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="avatar" style="background-color: <?php echo e(['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#EC4899'][ord($p->full_name[0]) % 7]); ?>;">
                                <?php echo e(strtoupper(substr($p->full_name, 0, 1))); ?>

                            </div>
                            <div class="details">
                                <span class="name"><?php echo e($p->full_name); ?></span>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    <span class="nrp"><?php echo e($p->nrp); ?></span>
                                    <i class="ri-file-copy-line icon-copy" title="Salin NRP" onclick="copyToClipboard('<?php echo e($p->nrp); ?>')"></i>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <span style="font-weight: 500; color: #111827; font-size: 13px;"><?php echo e($p->rank->name ?? '—'); ?></span>
                            <span style="font-size: 12px; color: #6B7280;"><?php echo e($p->golongan ?? $p->rank->category ?? '—'); ?></span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 2px;">
                            <span style="font-weight: 500; color: #111827; font-size: 13px;"><?php echo e($p->jabatan ?? '—'); ?></span>
                            <span style="font-size: 12px; color: #6B7280;"><?php echo e($p->bagian ?? '—'); ?></span>
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 13px; color: #4B5563; font-weight: 500;"><?php echo e($p->satker->name ?? '—'); ?></span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon green" onclick="openDetailModal(<?php echo e(json_encode($p)); ?>)" title="Lihat Detail & Ukuran">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn-icon blue" onclick="openEditModal(<?php echo e(json_encode($p)); ?>)" title="Edit Data">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button class="btn-icon red" title="Hapus Data" onclick="confirmDelete(<?php echo e($p->id); ?>, '<?php echo e($p->full_name); ?>')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 48px; color: #9CA3AF;">
                        <i class="ri-user-unfollow-line" style="font-size: 48px; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                        Belum ada data personel ditemukan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if($personnels->total() > 0): ?>
        <div class="table-footer">
            <div class="footer-left">
                Menampilkan <?php echo e($personnels->firstItem() ?? 0); ?> hingga <?php echo e($personnels->lastItem() ?? 0); ?> dari <?php echo e($personnels->total()); ?> data
                <div class="per-page-selector" style="margin-left: 12px;">
                    <div class="custom-select-wrapper" style="min-width: 80px;">
                        <div class="custom-select" onclick="toggleDropdown(this)">
                            <div class="select-trigger" style="height: 34px; padding: 0 10px; font-size: 13px;">
                                <span><?php echo e($perPage); ?></span>
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                            <div class="custom-options" style="background: #fff !important; bottom: calc(100% + 8px); top: auto;">
                                <div class="options-scroll">
                                    <div class="option <?php echo e($perPage == 10 ? 'selected' : ''); ?>" onclick="window.location.href=updateQueryStringParameter(window.location.href, 'per_page', '10')">10</div>
                                    <div class="option <?php echo e($perPage == 25 ? 'selected' : ''); ?>" onclick="window.location.href=updateQueryStringParameter(window.location.href, 'per_page', '25')">25</div>
                                    <div class="option <?php echo e($perPage == 50 ? 'selected' : ''); ?>" onclick="window.location.href=updateQueryStringParameter(window.location.href, 'per_page', '50')">50</div>
                                    <div class="option <?php echo e($perPage == 100 ? 'selected' : ''); ?>" onclick="window.location.href=updateQueryStringParameter(window.location.href, 'per_page', '100')">100</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="footer-right">
                <div class="pagination-controls">
                    <a href="<?php echo e($personnels->url(1)); ?>" class="page-btn <?php echo e($personnels->onFirstPage() ? 'disabled' : ''); ?>">
                        <i class="ri-double-left-line"></i>
                    </a>
                    <a href="<?php echo e($personnels->previousPageUrl()); ?>" class="page-btn <?php echo e($personnels->onFirstPage() ? 'disabled' : ''); ?>">
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                    <span class="page-info">Halaman <strong><?php echo e($personnels->currentPage()); ?></strong> dari <strong><?php echo e($personnels->lastPage()); ?></strong></span>
                    <a href="<?php echo e($personnels->nextPageUrl()); ?>" class="page-btn <?php echo e(!$personnels->hasMorePages() ? 'disabled' : ''); ?>">
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                    <a href="<?php echo e($personnels->url($personnels->lastPage())); ?>" class="page-btn <?php echo e(!$personnels->hasMorePages() ? 'disabled' : ''); ?>">
                        <i class="ri-double-right-line"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<div id="addPersonnelModal" class="modal">
    <div class="modal-content" style="max-width: 650px;">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="ri-user-add-line" style="color: #B91C1C; margin-right: 10px;"></i> Tambah Personel
            </h2>
            <button class="modal-close" onclick="closeModal('addPersonnelModal')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="<?php echo e(route('admin.personnel.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="modal_type" value="add">
            <div class="modal-body">
                <div class="form-grid">
                    <!-- Row 1 -->
                    <div class="form-group">
                        <label>JENIS PERSONIL</label>
                        <div class="selection-grid">
                            <label class="selection-card">
                                <input type="radio" name="personnel_type" value="Polri" <?php echo e(old('personnel_type', 'Polri') == 'Polri' ? 'checked' : ''); ?> onclick="filterRanks('Polri')">
                                <div class="card-content">
                                    <span class="card-title">Polri</span>
                                    <span class="card-desc">Anggota Kepolisian</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                            <label class="selection-card">
                                <input type="radio" name="personnel_type" value="PNS" <?php echo e(old('personnel_type') == 'PNS' ? 'checked' : ''); ?> onclick="filterRanks('PNS')">
                                <div class="card-content">
                                    <span class="card-title">PNS</span>
                                    <span class="card-desc">Pegawai Negeri</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>NRP / NIP</label>
                        <input type="text" name="nrp" value="<?php echo e(old('modal_type') == 'add' ? old('nrp') : ''); ?>" required placeholder="Masukkan Nomor Identitas" class="form-input <?php $__errorArgs = ['nrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e(old('modal_type') == 'add' ? 'has-error' : ''); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                        <?php if(old('modal_type') == 'add'): ?>
                            <?php $__errorArgs = ['nrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="error-message"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Row 2 -->
                    <div class="form-group">
                        <label>NAMA LENGKAP</label>
                        <input type="text" name="full_name" value="<?php echo e(old('modal_type') == 'add' ? old('full_name') : ''); ?>" required placeholder="Nama Tanpa Gelar" class="form-input" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label>PANGKAT</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="add_rank_label"><?php echo e(old('modal_type') == 'add' && old('rank_id') ? $ranks->firstWhere('id', old('rank_id'))->name : '— Pilih Pangkat —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php
                                            $pnsGrades = [
                                                'Pembina Utama' => 'IV/e',
                                                'Pembina Utama Madya' => 'IV/d',
                                                'Pembina Utama Muda' => 'IV/c',
                                                'Pembina Tingkat I' => 'IV/b',
                                                'Pembina' => 'IV/a',
                                                'Penata Tingkat I' => 'III/d',
                                                'Penata' => 'III/c',
                                                'Penata Muda Tingkat I' => 'III/b',
                                                'Penata Muda' => 'III/a',
                                                'Pengatur Tingkat I' => 'II/d',
                                                'Pengatur' => 'II/c',
                                                'Pengatur Muda Tingkat I' => 'II/b',
                                                'Pengatur Muda' => 'II/a',
                                                'Juru Tingkat I' => 'I/d',
                                                'Juru' => 'I/c',
                                                'Juru Muda Tingkat I' => 'I/b',
                                                'Juru Muda' => 'I/a',
                                            ];
                                        ?>
                                        <?php $__currentLoopData = $ranks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $fillValue = ($rank->category == 'PNS' && isset($pnsGrades[$rank->name])) ? $pnsGrades[$rank->name] : $rank->category;
                                            ?>
                                            <div class="option" 
                                                 data-value="<?php echo e($rank->id); ?>" 
                                                 data-label="<?php echo e($rank->name); ?>" 
                                                 data-category="<?php echo e($rank->category); ?>" 
                                                 data-fill="<?php echo e($fillValue); ?>"
                                                 onclick="selectRank(this)">
                                                <?php echo e($rank->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="rank_id" required id="add_rank_id" value="<?php echo e(old('modal_type') == 'add' ? old('rank_id') : ''); ?>">
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="form-group">
                        <label>GOLONGAN (POLRI/PNS)</label>
                        <input type="text" name="golongan" value="<?php echo e(old('modal_type') == 'add' ? old('golongan') : ''); ?>" placeholder="Contoh: III/A" class="form-input" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label>JABATAN</label>
                        <input type="text" name="jabatan" value="<?php echo e(old('modal_type') == 'add' ? old('jabatan') : ''); ?>" placeholder="Contoh: BANIT, KASUBNIT" class="form-input" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <!-- Row 4 -->
                    <div class="form-group">
                        <label>SATKER / SATWIL</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="add_satker_label"><?php echo e(old('modal_type') == 'add' && old('satker_id') ? $satkers->firstWhere('id', old('satker_id'))->name : '— Pilih Satker —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = $satkers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" 
                                                 data-value="<?php echo e($satker->id); ?>" 
                                                 data-label="<?php echo e($satker->name); ?>"
                                                 onclick="selectSatkerOption(this, 'add')">
                                                <?php echo e($satker->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="satker_id" required id="add_satker_id" value="<?php echo e(old('modal_type') == 'add' ? old('satker_id') : ''); ?>">
                        </div>
                    </div>
                    <div class="form-group" id="add_bagian_manual_wrapper">
                        <label>BAGIAN / FUNGSI</label>
                        <input type="text" name="bagian" id="add_bagian_manual" value="<?php echo e(old('modal_type') == 'add' ? old('bagian') : ''); ?>" placeholder="Contoh: RESKRIM, INTEL" class="form-input" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group" id="add_bagian_select_wrapper" style="display: none;">
                        <label>BAGIAN / FUNGSI</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="add_bagian_select_label"><?php echo e(old('modal_type') == 'add' && old('bagian') ? old('bagian') : '— Pilih Bagian —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = ['PIMPINAN', 'BAG OPS', 'BAG REN', 'BAG SDM', 'BAG LOG', 'SIUM', 'SIKEU', 'SIPROPAM', 'SIWAS', 'SIKUM', 'SIHUMAS', 'SIDOKKES', 'SITIK', 'SPKT', 'SAT INTELKAM', 'SAT RESKRIM', 'SAT RESNARKOBA', 'SAT SAMAPTA', 'SAT BINMAS', 'SAT LANTAS', 'SAT POLAIRUD', 'SAT TAHTI', 'SAT OBVIT', 'POLSEK']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" onclick="selectBagianDropdown(this, 'add', '<?php echo e($opt); ?>')"><?php echo e($opt); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="bagian" id="add_bagian_select" value="<?php echo e(old('modal_type') == 'add' ? old('bagian') : ''); ?>" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>KETERANGAN</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="add_keterangan_label"><?php echo e(old('modal_type') == 'add' && old('keterangan') ? old('keterangan') : '— Pilih Keterangan —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = ['STAF', 'SAMAPTA', 'LANTAS', 'PROVOS', 'RESKRIM', 'INTEL', 'PAMINAL', 'SIKUM', 'HUMAS', 'TIK']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" onclick="selectOptionManual(this, 'keterangan', '<?php echo e($opt); ?>', '<?php echo e($opt); ?>', 'add_keterangan_label')"><?php echo e($opt); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="keterangan" id="add_keterangan" value="<?php echo e(old('modal_type') == 'add' ? old('keterangan') : ''); ?>">
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="form-group">
                        <label>NO HP (WHATSAPP)</label>
                        <div class="input-with-icon">
                            <i class="ri-whatsapp-line"></i>
                            <input type="text" name="phone" value="<?php echo e(old('modal_type') == 'add' ? old('phone') : ''); ?>" placeholder="Contoh: 08123456789" class="form-input pl-10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>JENIS KELAMIN</label>
                        <div class="selection-grid">
                            <label class="selection-card">
                                <input type="radio" name="gender" value="L" <?php echo e(old('modal_type') == 'add' && old('gender') == 'L' ? 'checked' : (old('modal_type') != 'add' ? 'checked' : '')); ?> onclick="filterMeasurements('L', 'addPersonnelModal')">
                                <div class="card-content">
                                    <span class="card-title">Laki-laki</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                            <label class="selection-card">
                                <input type="radio" name="gender" value="P" <?php echo e(old('modal_type') == 'add' && old('gender') == 'P' ? 'checked' : ''); ?> onclick="filterMeasurements('P', 'addPersonnelModal')">
                                <div class="card-content">
                                    <span class="card-title">Perempuan</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                        </div>
                    </div>

                    <!-- Row 6 -->
                    <div class="form-group col-span-2-desktop">
                        <label>AGAMA</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select dropup" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="add_religion_label"><?php echo e(old('modal_type') == 'add' && old('religion') ? old('religion') : '— Pilih Agama —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = ['ISLAM', 'PROTESTAN', 'KATOLIK', 'HINDU', 'BUDHA', 'KHONGHUCU']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" onclick="selectOptionManual(this, 'religion', '<?php echo e($rel); ?>', '<?php echo e($rel); ?>', 'add_religion_label')"><?php echo e($rel); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="religion" id="add_religion" value="<?php echo e(old('modal_type') == 'add' ? old('religion') : ''); ?>">
                        </div>
                    </div>
                    
                    <!-- Measurements Section -->
                    <div class="col-span-2-desktop" style="margin-top: 10px; padding-top: 20px; border-top: 1px dashed #E5E7EB;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 16px;">DATA UKURAN (KAPOR)</h4>
                        <div class="form-grid">
                            <?php $__currentLoopData = $kaporItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-group">
                                    <label><?php echo e($item->item_name); ?> (<?php echo e($item->category); ?>)</label>
                                    <div class="custom-select-wrapper">
                                        <div class="custom-select" onclick="toggleDropdown(this)">
                                            <div class="select-trigger"><span id="add_measurement_label_<?php echo e($item->id); ?>"><?php echo e((old('modal_type') == 'add' && old('measurements.'.$item->id)) ? ($item->sizes->firstWhere('id', old('measurements.'.$item->id))->size_label ?? '— Pilih Ukuran —') : '— Pilih Ukuran —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                            <div class="custom-options">
                                                <div class="options-scroll">
                                                    <?php $__currentLoopData = $item->sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="option" 
                                                             data-gender="<?php echo e($size->gender); ?>"
                                                             onclick="selectOptionManual(this, 'measurements[<?php echo e($item->id); ?>]', '<?php echo e($size->id); ?>', '<?php echo e($size->size_label); ?>', 'add_measurement_label_<?php echo e($item->id); ?>')">
                                                            <?php echo e($size->size_label); ?>

                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="measurements[<?php echo e($item->id); ?>]" value="<?php echo e(old('modal_type') == 'add' ? old('measurements.'.$item->id) : ''); ?>">
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="gap: 12px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('addPersonnelModal')" style="border-radius: 10px; padding: 10px 24px; font-weight: 600; border-color: #E5E7EB; color: #374151;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 10px; padding: 10px 24px; font-weight: 700;">Simpan Personel</button>
            </div>
        </form>
    </div>
</div>


<div id="detailPersonnelModal" class="modal">
    <div class="modal-content" style="max-width: 650px;">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="ri-file-user-line" style="color: #10B981; margin-right: 10px;"></i> Detail Personel
            </h2>
            <button class="modal-close" onclick="closeModal('detailPersonnelModal')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 24px;">
                <div style="width: 80px; height: 80px; background: #E5E7EB; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 700; color: #6B7280; flex-shrink: 0;" id="detail_avatar">
                    <!-- Initials -->
                </div>
                <div>
                    <h3 id="detail_name" style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 4px;"></h3>
                    <div style="font-size: 14px; color: #6B7280; margin-bottom: 2px;" id="detail_nrp"></div>
                    <div style="display: flex; gap: 8px; margin-top: 8px;">
                        <span id="detail_rank" class="role-pill" style="background: #F3F4F6; color: #374151; border-color: #E5E7EB;"></span>
                        <span id="detail_type" class="role-pill"></span>
                    </div>
                </div>
            </div>

            <div class="form-grid" style="margin-bottom: 24px;">
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Satker</label>
                    <div id="detail_satker" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Jabatan</label>
                    <div id="detail_jabatan" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Bagian</label>
                    <div id="detail_bagian" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Keterangan</label>
                    <div id="detail_keterangan" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                 <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Golongan</label>
                    <div id="detail_golongan" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Agama</label>
                    <div id="detail_religion" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">Jenis Kelamin</label>
                    <div id="detail_gender" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
                <div>
                    <label style="font-weight: 800; color: #000; font-size: 14px; display: block; margin-bottom: 2px;">No HP</label>
                    <div id="detail_phone" style="font-size: 14px; font-weight: 500; color: #4B5563;"></div>
                </div>
            </div>

            <h4 style="font-size: 14px; font-weight: 700; color: #111827; border-bottom: 1px solid #E5E7EB; padding-bottom: 8px; margin-bottom: 16px;">
                Data Ukuran (Kapor)
            </h4>
            
            <div id="detail_measurements" class="form-grid" style="gap: 12px;">
                <!-- Populated by JS -->
                <div class="text-sm text-gray-500 italic col-span-2">Belum ada data ukuran.</div>
            </div>
            
        </div>
        <div class="modal-footer" style="display: flex; justify-content: flex-end; padding: 16px 24px;">
            <button type="button" class="btn btn-outline" style="border-radius: 10px; padding: 8px 20px; font-weight: 600; border-color: #E5E7EB; color: #374151; font-size: 14px;" onclick="closeModal('detailPersonnelModal')">Tutup</button>
        </div>
    </div>
</div>
<div class="modal" id="deleteModal">
    <div class="modal-content" style="max-width: 360px; border-radius: 24px; padding: 8px;">
        <div class="modal-body" style="padding: 24px; text-align: center;">
            <div style="width: 64px; height: 64px; background: #FEF2F2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="ri-error-warning-fill" style="font-size: 32px; color: #EF4444;"></i>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 8px;">Hapus Personel?</h3>
            <p style="font-size: 15px; color: #6B7280; line-height: 1.5; margin-bottom: 24px;">
                Apakah Anda yakin ingin menghapus <strong id="delete_person_name" style="color: #374151;"></strong>? Data yang telah dihapus tidak dapat dikembalikan.
            </p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button type="button" class="btn btn-outline" style="flex: 1; height: 44px; padding: 0; border-radius: 12px; font-weight: 700; border-color: #E5E7EB; color: #374151; font-size: 14px; display: flex; align-items: center; justify-content: center;" onclick="closeModal('deleteModal')">Batal</button>
                <form id="deleteForm" method="POST" style="flex: 1;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-primary" style="width: 100%; height: 44px; padding: 0; background: #EF4444; border-color: #EF4444; border-radius: 12px; font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="editPersonnelModal" class="modal">
    <div class="modal-content" style="max-width: 650px;">
        <div class="modal-header">
            <h2 class="modal-title">
                <i class="ri-user-settings-line" style="color: #B91C1C; margin-right: 10px;"></i> Edit Data Personel
            </h2>
            <button class="modal-close" onclick="closeModal('editPersonnelModal')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form id="editForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="modal_type" value="edit">
            <input type="hidden" name="id" id="edit_personnel_id" value="<?php echo e(old('modal_type') == 'edit' ? old('id') : ''); ?>">
            <div class="modal-body">
                <div class="form-grid">
                    <!-- Row 1: Personnel Type -->
                    <div class="form-group col-span-2-desktop">
                        <label>JENIS PERSONIL</label>
                        <div class="selection-grid">
                            <label class="selection-card">
                                <input type="radio" name="personnel_type" id="edit_type_polri" value="Polri" <?php echo e(old('modal_type') == 'edit' && old('personnel_type') == 'Polri' ? 'checked' : ''); ?> onclick="filterRanksEdit('Polri')">
                                <div class="card-content">
                                    <span class="card-title">Polri</span>
                                    <span class="card-desc">Anggota Kepolisian</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                            <label class="selection-card">
                                <input type="radio" name="personnel_type" id="edit_type_pns" value="PNS" <?php echo e(old('modal_type') == 'edit' && old('personnel_type') == 'PNS' ? 'checked' : ''); ?> onclick="filterRanksEdit('PNS')">
                                <div class="card-content">
                                    <span class="card-title">PNS</span>
                                    <span class="card-desc">Pegawai Negeri</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Row 2 -->
                    <div class="form-group">
                        <label>NRP / NIP</label>
                        <input type="text" name="nrp" id="edit_nrp" value="<?php echo e(old('modal_type') == 'edit' ? old('nrp') : ''); ?>" class="form-input <?php $__errorArgs = ['nrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e(old('modal_type') == 'edit' ? 'has-error' : ''); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Contoh: 12345678" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                        <?php if(old('modal_type') == 'edit'): ?>
                            <?php $__errorArgs = ['nrp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="error-message"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label>NAMA LENGKAP</label>
                        <input type="text" name="full_name" id="edit_full_name" value="<?php echo e(old('modal_type') == 'edit' ? old('full_name') : ''); ?>" class="form-input" required placeholder="Nama Lengkap tanpa gelar" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <!-- Row 3 -->
                    <div class="form-group">
                        <label>PANGKAT</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="edit_rank_label"><?php echo e(old('modal_type') == 'edit' && old('rank_id') ? $ranks->firstWhere('id', old('rank_id'))->name : '— Pilih Pangkat —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll" id="edit_rank_options">
                                        <?php
                                            $pnsGrades = [
                                                'Pembina Utama' => 'IV/e', 'Pembina Utama Madya' => 'IV/d', 'Pembina Utama Muda' => 'IV/c',
                                                'Pembina Tingkat I' => 'IV/b', 'Pembina' => 'IV/a', 'Penata Tingkat I' => 'III/d',
                                                'Penata' => 'III/c', 'Penata Muda Tingkat I' => 'III/b', 'Penata Muda' => 'III/a',
                                                'Pengatur Tingkat I' => 'II/d', 'Pengatur' => 'II/c', 'Pengatur Muda Tingkat I' => 'II/b',
                                                'Pengatur Muda' => 'II/a', 'Juru Tingkat I' => 'I/d', 'Juru' => 'I/c',
                                                'Juru Muda Tingkat I' => 'I/b', 'Juru Muda' => 'I/a',
                                            ];
                                        ?>
                                        <?php $__currentLoopData = $ranks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $fillValue = ($rank->category == 'PNS' && isset($pnsGrades[$rank->name])) ? $pnsGrades[$rank->name] : $rank->category;
                                            ?>
                                            <div class="option" 
                                                 data-value="<?php echo e($rank->id); ?>" 
                                                 data-label="<?php echo e($rank->name); ?>"
                                                 data-category="<?php echo e($rank->category); ?>"
                                                 data-fill="<?php echo e($fillValue); ?>"
                                                 onclick="selectRankEdit(this)">
                                                <?php echo e($rank->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="rank_id" id="edit_rank_id" value="<?php echo e(old('modal_type') == 'edit' ? old('rank_id') : ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                         <label>GOLONGAN (POLRI/PNS)</label>
                        <input type="text" name="golongan" id="edit_golongan" value="<?php echo e(old('modal_type') == 'edit' ? old('golongan') : ''); ?>" class="form-input" placeholder="Contoh: III/A" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <!-- Row 4 -->
                    <div class="form-group">
                        <label>JABATAN</label>
                        <input type="text" name="jabatan" id="edit_jabatan" value="<?php echo e(old('modal_type') == 'edit' ? old('jabatan') : ''); ?>" class="form-input" placeholder="Contoh: BANIT, KASUBNIT" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label>SATKER / SATWIL</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="edit_satker_label"><?php echo e(old('modal_type') == 'edit' && old('satker_id') ? $satkers->firstWhere('id', old('satker_id'))->name : '— Pilih Satker —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = $satkers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $satker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" 
                                                 data-value="<?php echo e($satker->id); ?>" 
                                                 data-label="<?php echo e($satker->name); ?>"
                                                 onclick="selectSatkerOption(this, 'edit')">
                                                <?php echo e($satker->name); ?>

                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="satker_id" id="edit_satker_id" value="<?php echo e(old('modal_type') == 'edit' ? old('satker_id') : ''); ?>" required>
                        </div>
                    </div>

                    <!-- Row 5 -->
                    <div class="form-group" id="edit_bagian_manual_wrapper">
                        <label>BAGIAN / FUNGSI</label>
                        <input type="text" name="bagian" id="edit_bagian_manual" value="<?php echo e(old('modal_type') == 'edit' ? old('bagian') : ''); ?>" class="form-input" placeholder="Contoh: RESKRIM, INTEL" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group" id="edit_bagian_select_wrapper" style="display: none;">
                        <label>BAGIAN / FUNGSI</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="edit_bagian_select_label"><?php echo e(old('modal_type') == 'edit' && old('bagian') ? old('bagian') : '— Pilih Bagian —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = ['PIMPINAN', 'BAG OPS', 'BAG REN', 'BAG SDM', 'BAG LOG', 'SIUM', 'SIKEU', 'SIPROPAM', 'SIWAS', 'SIKUM', 'SIHUMAS', 'SIDOKKES', 'SITIK', 'SPKT', 'SAT INTELKAM', 'SAT RESKRIM', 'SAT RESNARKOBA', 'SAT SAMAPTA', 'SAT BINMAS', 'SAT LANTAS', 'SAT POLAIRUD', 'SAT TAHTI', 'SAT OBVIT', 'POLSEK']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" onclick="selectBagianDropdown(this, 'edit', '<?php echo e($opt); ?>')"><?php echo e($opt); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="bagian" id="edit_bagian_select" value="<?php echo e(old('modal_type') == 'edit' ? old('bagian') : ''); ?>" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>KETERANGAN</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="edit_keterangan_label"><?php echo e(old('modal_type') == 'edit' && old('keterangan') ? old('keterangan') : '— Pilih Keterangan —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = ['STAF', 'SAMAPTA', 'LANTAS', 'PROVOS', 'RESKRIM', 'INTEL', 'PAMINAL', 'SIKUM', 'HUMAS', 'TIK']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" onclick="selectOptionManual(this, 'keterangan', '<?php echo e($opt); ?>', '<?php echo e($opt); ?>', 'edit_keterangan_label')"><?php echo e($opt); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="keterangan" id="edit_keterangan" value="<?php echo e(old('modal_type') == 'edit' ? old('keterangan') : ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>NO HP (WHATSAPP)</label>
                        <div class="input-with-icon">
                            <i class="ri-whatsapp-line"></i>
                            <input type="text" name="phone" id="edit_phone" value="<?php echo e(old('modal_type') == 'edit' ? old('phone') : ''); ?>" class="form-input pl-10" placeholder="Contoh: 08123456789" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>

                    <!-- Row 6 -->
                    <div class="form-group">
                        <label>JENIS KELAMIN</label>
                        <div class="selection-grid">
                            <label class="selection-card">
                                <input type="radio" name="gender" id="edit_gender_l" value="L" <?php echo e(old('modal_type') == 'edit' && old('gender') == 'L' ? 'checked' : ''); ?> onclick="filterMeasurements('L', 'editPersonnelModal')">
                                <div class="card-content">
                                    <span class="card-title">Laki-laki</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                            <label class="selection-card">
                                <input type="radio" name="gender" id="edit_gender_p" value="P" <?php echo e(old('modal_type') == 'edit' && old('gender') == 'P' ? 'checked' : ''); ?> onclick="filterMeasurements('P', 'editPersonnelModal')">
                                <div class="card-content">
                                    <span class="card-title">Perempuan</span>
                                </div>
                                <div class="card-check"><i class="ri-check-line"></i></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Row 7 -->
                     <div class="form-group col-span-2-desktop">
                        <label>AGAMA</label>
                        <div class="custom-select-wrapper">
                            <div class="custom-select dropup" onclick="toggleDropdown(this)">
                                <div class="select-trigger"><span id="edit_religion_label"><?php echo e(old('modal_type') == 'edit' && old('religion') ? old('religion') : '— Pilih Agama —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                <div class="custom-options">
                                    <div class="options-scroll">
                                        <?php $__currentLoopData = ['ISLAM', 'PROTESTAN', 'KATOLIK', 'HINDU', 'BUDHA', 'KHONGHUCU']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="option" onclick="selectOptionManual(this, 'religion', '<?php echo e($rel); ?>', '<?php echo e($rel); ?>', 'edit_religion_label')"><?php echo e($rel); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="religion" id="edit_religion" value="<?php echo e(old('modal_type') == 'edit' ? old('religion') : ''); ?>">
                        </div>
                    </div>
                    
                    <!-- Measurements Section -->
                    <div class="col-span-2-desktop" style="margin-top: 10px; padding-top: 20px; border-top: 1px dashed #E5E7EB;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #111827; margin-bottom: 16px;">DATA UKURAN (KAPOR)</h4>
                        <div class="form-grid">
                            <?php $__currentLoopData = $kaporItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="form-group">
                                    <label><?php echo e($item->item_name); ?> (<?php echo e($item->category); ?>)</label>
                                    <div class="custom-select-wrapper">
                                        <div class="custom-select" onclick="toggleDropdown(this)">
                                            <div class="select-trigger"><span id="edit_measurement_label_<?php echo e($item->id); ?>"><?php echo e((old('modal_type') == 'edit' && old('measurements.'.$item->id)) ? ($item->sizes->firstWhere('id', old('measurements.'.$item->id))->size_label ?? '— Pilih Ukuran —') : '— Pilih Ukuran —'); ?></span><i class="ri-arrow-down-s-line"></i></div>
                                            <div class="custom-options">
                                                <div class="options-scroll">
                                                    <?php $__currentLoopData = $item->sizes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="option" 
                                                             data-gender="<?php echo e($size->gender); ?>"
                                                             onclick="selectOptionManual(this, 'measurements[<?php echo e($item->id); ?>]', '<?php echo e($size->id); ?>', '<?php echo e($size->size_label); ?>', 'edit_measurement_label_<?php echo e($item->id); ?>')">
                                                            <?php echo e($size->size_label); ?>

                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="measurements[<?php echo e($item->id); ?>]" id="edit_measurement_<?php echo e($item->id); ?>" value="<?php echo e(old('modal_type') == 'edit' ? old('measurements.'.$item->id) : ''); ?>">
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    </div>
                </div>

            <div class="modal-footer" style="gap: 12px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('editPersonnelModal')" style="border-radius: 10px; padding: 10px 24px; font-weight: 600; border-color: #E5E7EB; color: #374151;">Batal</button>
                <button type="submit" class="btn btn-primary" style="border-radius: 10px; padding: 10px 24px; font-weight: 700;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>



<div id="toastContainer" class="toast-container"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
    /* ── Header ─────────────────────────────────────────── */
    .btn-maroon {
        background: #B91C1C !important;
        border: none !important;
        box-shadow: 0 4px 14px rgba(185, 28, 28, 0.2) !important;
        border-radius: 8px !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
        color: #fff !important;
        display: flex; align-items: center; gap: 8px; cursor: pointer;
    }
    .btn-maroon:hover { background: #991B1B !important; }

    .btn-outline {
        background: #fff;
        border: 1px solid #E5E7EB;
        color: #374151;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        transition: all 0.2s;
    }
    .btn-outline:hover { background: #F9FAFB; border-color: #D1D5DB; }

    /* ── Stats ──────────────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #fff;
        border: 1px solid #F3F4F6;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .stat-card:hover { transform: translateY(-4px); border-color: #E5E7EB; }

    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
    }
    .icon-blue { background: #EBF5FF; color: #3B82F6; }
    .icon-purple { background: #F5F3FF; color: #8B5CF6; }
    .icon-orange { background: #FFF7ED; color: #F97316; }
    .icon-green { background: #F0FDF4; color: #22C55E; }
    .icon-red { background: #FEF2F2; color: #EF4444; }
    
    .stat-content { display: flex; flex-direction: column; }
    .stat-label { font-size: 13px; color: #6B7280; font-weight: 600; text-transform: uppercase; letter-spacing: 0.025em; }
    .stat-number { font-size: 22px; font-weight: 700; color: #111827; }

    /* ── Filters ────────────────────────────────────────── */
    .filter-bar {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }
    .filter-form { display: flex; gap: 16px; align-items: center; }
    .filter-group { display: flex; gap: 12px; flex: 2; }
    
    .search-input {
        flex: 1;
        position: relative;
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 0 16px;
        height: 46px;
        transition: all 0.2s ease;
    }
    .search-input:focus-within { 
        border-color: #B91C1C; 
        box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.05); 
    }
    .search-input i.ri-search-line { 
        color: #64748B; 
        font-size: 20px;
        margin-right: 12px;
        flex-shrink: 0;
    }
    .search-input input {
        width: 100%;
        height: 100%;
        background: transparent;
        border: none;
        outline: none;
        font-size: 14px;
        color: #1E293B;
        padding: 0;
    }
    .search-input input::placeholder { color: #94A3B8; font-weight: 400; }

    /* ── Custom Select UI (Refined) ───────────────────── */
    .custom-select-wrapper { position: relative; width: 100%; }
    
    .custom-select {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
        height: 48px;
        display: flex; align-items: center;
    }
    .custom-select:hover { border-color: #D1D5DB; }
    
    /* Active State: Red Border, Shadow, Text Color */
    .custom-select.active {
        border-color: #B91C1C;
        box-shadow: 0 0 0 4px #FEF2F2;
        background: #fff;
    }

    .select-trigger {
        width: 100%;
        padding: 0 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 500;
        color: #374151;
        font-size: 14px;
    }
    .select-trigger i { 
        color: #9CA3AF; 
        font-size: 20px; 
        transition: transform 0.2s ease; 
    }
    .custom-select.active .select-trigger { color: #111827; }
    .custom-select.active .select-trigger i { 
        transform: rotate(180deg); 
        color: #B91C1C; 
    }

    /* Dropdown Menu */
    .custom-options {
        position: absolute;
        top: calc(100% + 8px);
        left: 0; right: 0;
        background: #fff;
        border: 1px solid #F3F4F6;
        border-radius: 16px;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
        z-index: 2000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
        padding: 8px; /* Padding inside the box */
        display: flex; flex-direction: column;
    }
    
    /* Dropup Variant (Open Upwards) */
    .custom-select.dropup .custom-options {
        top: auto;
        bottom: calc(100% + 8px);
        transform: translateY(10px);
        box-shadow: 0 -10px 40px -10px rgba(0,0,0,0.1);
    }
    
    .custom-select.active .custom-options { 
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    /* Scrollable Area */
    .options-scroll {
        max-height: 240px; /* Fixed height for scrolling */
        overflow-y: auto;
        padding-right: 2px; /* Slight padding for visual balance */
    }
    
    /* Custom Scrollbar Styling */
    .options-scroll::-webkit-scrollbar { width: 4px; }
    .options-scroll::-webkit-scrollbar-track { background: transparent; }
    .options-scroll::-webkit-scrollbar-thumb { 
        background-color: #E5E7EB; 
        border-radius: 10px; 
    }
    .options-scroll::-webkit-scrollbar-thumb:hover { background-color: #D1D5DB; }
    
    /* Option Item */
    .option {
        padding: 10px 12px;
        cursor: pointer;
        transition: all 0.15s;
        font-size: 14px;
        color: #4B5563;
        border-radius: 8px;
        margin-bottom: 2px;
        font-weight: 500;
        display: flex; align-items: center; justify-content: space-between;
    }
    .option:last-child { margin-bottom: 0; }
    
    .option:hover {
        background-color: #F9FAFB;
        color: #111827;
    }
    
    /* Selected Option */
    .option.selected {
        background-color: #F3F4F6; /* Grey background */
        color: #111827; /* Dark text */
        font-weight: 600;
    }
    /* Add checkmark for selected (optional but nice) */
    .option.selected::after {
        content: ""; /* Can add check icon here if needed via background-image or ::after content */
    }
    /* If you want the specific Red style from image */
    .option.selected {
        background-color: #FEF2F2;
        color: #B91C1C;
    }
        color: #4B5563;
        cursor: pointer;
        transition: all 0.1s;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 2px;
    }
    .option:last-child { margin-bottom: 0; }
    .option:hover { background: #F3F4F6; color: #111827; }
    .option.selected { 
        background: #FEF2F2; 
        color: #B91C1C; 
        font-weight: 600; 
    }

    /* ── Table ──────────────────────────────────────────── */
    .table-container {
        background: #fff;
        border: 1px solid #F3F4F6;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .user-table { width: 100%; border-collapse: collapse; }
    .user-table th {
        background: #FAFAFA;
        padding: 12px 20px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        border-bottom: 1px solid #F3F4F6;
    }
    .user-table th i { font-size: 14px; margin-left: 4px; color: #D1D5DB; }
    .user-table td { padding: 16px 20px; border-bottom: 1px solid #F3F4F6; vertical-align: middle; }
    
    .user-info { display: flex; align-items: center; gap: 12px; }
    .avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 14px;
    }
    .details { display: flex; flex-direction: column; }
    .name { font-weight: 600; color: #111827; }
    .nrp { font-size: 12px; color: #9CA3AF; }

    .role-pill {
        background: #EFF6FF;
        color: #3B82F6;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #DBEAFE;
    }

    .action-buttons { display: flex; gap: 8px; justify-content: center; }
    .btn-icon {
        width: 32px; height: 32px;
        border-radius: 6px;
        border: 1px solid #E5E7EB;
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s; font-size: 16px;
    }
    .btn-icon.blue { color: #3B82F6; }
    .btn-icon.blue:hover { background: #3B82F6; color: #fff; border-color: #3B82F6; }
    
    .btn-icon.green { color: #10B981; }
    .btn-icon.green:hover { background: #10B981; color: #fff; border-color: #10B981; }

    .btn-icon.red { color: #EF4444; }
    .btn-icon.red:hover { background: #EF4444; color: #fff; border-color: #EF4444; }

    /* ── Table Footer (Pagination) ────────────────────── */
    .table-footer {
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #F3F4F6;
        background: #fff;
    }
    .footer-left { display: flex; align-items: center; gap: 12px; color: #6B7280; font-size: 13px; }
    
    .per-page-selector { display: flex; align-items: center; margin-left: 12px; }

    .pagination-controls { display: flex; align-items: center; gap: 4px; }
    .page-btn {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid #E5E7EB;
        background: #fff;
        border-radius: 6px;
        color: #374151;
        text-decoration: none;
        transition: all 0.2s;
    }
    .page-btn:hover:not(.disabled) { background: #F9FAFB; border-color: #D1D5DB; }
    .page-btn.disabled { opacity: 0.3; cursor: not-allowed; pointer-events: none; }
    .page-info { font-size: 13px; color: #4B5563; margin: 0 12px; }
    .page-info strong { color: #111827; }

    /* ── Modal (Updated for Overflow) ────────────────── */
    .modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(17, 24, 39, 0.4); backdrop-filter: blur(4px);
        display: none; 
        z-index: 1000; 
        overflow-y: auto; /* Scroll on the wrapper */
        padding: 20px 0; /* Add padding for vertical spacing */
    }
    .modal.open { display: block; } /* Use block instead of flex for scroll */
    
    .modal-content {
        background: #fff;
        border-radius: 20px;
        width: 100%;
        max-width: 650px;
        margin: 20px auto; /* Center with margin */
        position: relative;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        animation: zoomIn 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        overflow: visible; /* Allow dropdowns to go outside */
    }
    
    .modal-header {
        padding: 20px 28px;
        border-bottom: 1px solid #F3F4F6;
        display: flex; justify-content: space-between; align-items: center;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background: #fff;
    }
    .modal-title { font-size: 20px; font-weight: 800; color: #111827; letter-spacing: -0.025em; }
    .modal-close {
        background: #F3F4F6; border: none; width: 32px; height: 32px; 
        border-radius: 50%; font-size: 20px; color: #6B7280; 
        cursor: pointer; display: flex; align-items: center; justify-content: center; 
        transition: all 0.2s;
    }
    .modal-close:hover { background: #E5E7EB; color: #111827; }
    /* ── Page Header ────────────────────────────────────── */
    .page-header { margin-bottom: 24px; }
    .page-header-row {
        display: flex; justify-content: space-between; align-items: flex-end;
    }
    .page-title { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }
    .page-subtitle { color: #6B7280; font-size: 14px; margin-top: 4px; }
    .page-header-actions { display: flex; gap: 12px; }

    /* ── Responsive Design ─────────────────────────────── */
    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 columns on tablet */
        }
    }

    @media (max-width: 768px) {
        .page-header-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        .page-header-actions {
            width: 100%;
        }
        .page-header-actions button {
            flex: 1;
            justify-content: center;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        .header-actions {
            width: 100%;
            justify-content: space-between;
        }
        .btn-maroon, .btn-outline {
            /* flex: 1; removed to avoid conflict with above specific selector */
            justify-content: center;
        }
        
        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-group {
            width: 100%;
            flex-direction: column;
        }
        .search-input {
            width: 100%;
        }
        
        .table-container {
            border-radius: 0;
            border-left: none;
            border-right: none;
        }
        
        /* Table Responsive Scroll */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .user-table {
            min-width: 800px; /* Ensure table doesn't squish */
        }
        
        /* Pagination */
        .table-footer {
            flex-direction: column;
            gap: 16px;
            align-items: center;
            text-align: center;
        }
        .footer-left {
            flex-direction: column;
            gap: 8px;
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr; /* Stack on mobile */
        }
        
        .modal-content {
            width: 95%; /* Use almost full width on mobile */
            margin: 10px auto;
            border-radius: 16px;
        }
        .form-grid {
            grid-template-columns: 1fr !important; /* Stack columns */
            gap: 16px !important;
        }
        .selection-grid {
            grid-template-columns: 1fr; /* Stack selection cards */
        }
        
        .modal-body { padding: 20px; }
        .modal-footer { flex-direction: column-reverse; gap: 10px; }
        .btn-save, .btn-cancel { width: 100%; }
        
        /* Adjust font sizes for mobile */
        .page-title { font-size: 20px; }
        .stat-number { font-size: 18px; }
        .stat-label { font-size: 11px; }
    }

    .modal-body {
        padding: 24px 28px 40px; 
        overflow: visible; /* DO NOT CLIP */
    }

    /* Modal Footer rounded corners */
    .modal-footer {
        padding: 20px 28px;
        border-top: 1px solid #F3F4F6;
        display: flex; justify-content: flex-end; gap: 14px;
        background: #F9FAFB;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-group input { width: 100%; height: 46px; padding: 0 16px; border: 1px solid #E5E7EB; border-radius: 10px; font-size: 14px; color: #1F2937; outline: none; transition: all 0.2s; background: #F9FAFB; }
    .form-group input:focus { border-color: #B91C1C; background: #fff; box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.08); }
    .form-group input::placeholder { color: #9CA3AF; }
    
    .btn-save { background: #B91C1C; color: #fff; border: 1px solid #B91C1C; padding: 0 24px; height: 46px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-save:hover { background: #991B1B; }
    
    .btn-cancel { background: #fff; color: #4B5563; border: 1px solid #E5E7EB; padding: 0 24px; height: 46px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
    .btn-cancel:hover { background: #F9FAFB; border-color: #D1D5DB; }

    /* ── Responsive Modal ──────────────────────── */
    @media (max-width: 640px) {
        .modal-content {
            width: 95%; /* Use almost full width on mobile */
            max-height: 90vh; /* slightly taller on mobile */
        }
        .form-grid {
            grid-template-columns: 1fr !important; /* Stack columns */
            gap: 16px !important;
        }
        .modal-body { padding: 20px; }
        .modal-footer { flex-direction: column-reverse; gap: 10px; }
        .btn-save, .btn-cancel { width: 100%; }
    }

    /* ── Toast ─────────────────────────────────── */
    .toast-container { position: fixed; top: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; }
    .toast { min-width: 320px; background: #fff; border-radius: 12px; padding: 16px; display: flex; align-items: center; gap: 14px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border-left: 4px solid #10B981; animation: toast-slide-in 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; }
    .toast.success { border-left-color: #10B981; }
    .toast.error { border-left-color: #EF4444; }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes zoomIn { from { transform: scale(0.95) translateY(10px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }
    @keyframes toast-slide-in { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    /* ── Elegant Form Styles ───────────────────────────── */
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-input {
        width: 100%;
        height: 44px;
        padding: 0 14px;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        font-size: 14px;
        color: #1F2937;
        transition: all 0.2s;
        background: #F9FAFB;
        outline: none;
        font-family: inherit;
    }
    .form-input:focus {
        background: #fff;
        border-color: #B91C1C;
        box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.1);
    }
    .input-with-icon { position: relative; }
    .input-with-icon i {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        font-size: 18px; color: #9CA3AF;
        pointer-events: none;
    }
    .form-input.pl-10 { padding-left: 42px; }

    /* Selection Cards (Radio/Checkbox) */
    .selection-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .selection-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        user-select: none;
    }
    .selection-card:hover {
        border-color: #D1D5DB;
        background: #F9FAFB;
    }
    .selection-card input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0; width: 0;
    }
    
    /* Checked State */
    .selection-card:has(input:checked) {
        border-color: #B91C1C;
        background: #FEF2F2;
        box-shadow: 0 0 0 1px #B91C1C inset; /* subtle border emphasis */
    }
    .selection-card:has(input:checked) .card-title {
        color: #B91C1C;
        font-weight: 700;
    }
    .selection-card:has(input:checked) .card-desc {
        color: #B91C1C;
        opacity: 0.8;
    }
    .selection-card:has(input:checked) .card-check {
        transform: scale(1);
        opacity: 1;
    }
    
    .card-content { display: flex; flex-direction: column; }
    .card-title {
        font-size: 14px;
        color: #374151;
        font-weight: 600;
    }
    .card-desc {
        font-size: 11px;
        color: #6B7280;
        font-weight: 400;
    }
    .card-check {
        width: 20px; height: 20px;
        background: #B91C1C;
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
        transform: scale(0.5);
        opacity: 0;
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    /* Form Grid Layout */
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .col-span-2-desktop {
        grid-column: span 2;
    }
    
    @media (max-width: 640px) {
        .col-span-2-desktop { grid-column: auto; }
    }
    .form-input.has-error {
        border-color: #EF4444 !important;
        background-color: #FEF2F2 !important;
    }
    .error-message {
        color: #EF4444;
        font-size: 11px;
        margin-top: 4px;
        font-weight: 500;
        text-transform: none;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    // Global click listener to close dropdowns
    document.addEventListener('click', (e) => {
        // If click is not inside a custom-select, close all
        if (!e.target.closest('.custom-select')) {
            document.querySelectorAll('.custom-options').forEach(opt => {
                opt.style.display = 'none';
            });
        }
    });

    // Handle scroll to close dropdowns preventing detachment
    document.addEventListener('scroll', (e) => {
        // Ignore scroll events coming from inside the dropdown options
        if (e.target.classList && (e.target.classList.contains('options-scroll') || e.target.closest('.options-scroll'))) {
            return;
        }

        document.querySelectorAll('.custom-options').forEach(opt => {
            if (opt.style.display === 'block') {
                // Determine if we should close: 
                // If it's a fixed position dropdown (previous logic), we might want to close on outer scroll.
                // But with our current CSS-based (absolute) dropdowns inside overflow-visible modal, 
                // we technically don't need to close on modal scroll unless we really want to.
                // However, preserving the behavior for window/modal scroll is fine, just NOT inner scroll.
                
                // Close it
                opt.style.display = 'none';
                opt.closest('.custom-select').classList.remove('active');
            }
        });
    }, true); // Capture phase

    function toggleDropdown(el) {
        // Toggle this dropdown
        const options = el.querySelector('.custom-options');
        const isOpen = options.style.display === 'block';

        // Close all others
        document.querySelectorAll('.custom-options').forEach(opt => opt.style.display = 'none');
        document.querySelectorAll('.custom-select').forEach(sel => sel.classList.remove('active'));

        if (!isOpen) {
            options.style.display = 'block';
            // We rely on CSS for positioning (top, left, width, z-index)
            // Do NOT overwrite with inline styles here
            el.classList.add('active');
        } 
        
        event.stopPropagation();
    }

    function selectSatkerOption(el, type) {
        const value = el.dataset.value;
        const label = el.dataset.label;
        
        // Update Satker
        document.getElementById(type + '_satker_id').value = value;
        document.getElementById(type + '_satker_label').innerText = label;
        
        // Handle Bagian Visibility
        updateBagianVisibility(label, type);
        
        // Visual feedback
        const wrapper = el.closest('.custom-select-wrapper');
        wrapper.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
        el.classList.add('selected');
        
        // Close dropdown
        el.closest('.custom-select').querySelector('.custom-options').style.display = 'none';
        el.closest('.custom-select').classList.remove('active');
        
        event.stopPropagation();
    }

    function updateBagianVisibility(satkerName, type, currentBagian = '') {
        const isPolres = satkerName.toUpperCase().includes('POLRES') || satkerName.toUpperCase().includes('POLRESTA');
        const manualWrapper = document.getElementById(type + '_bagian_manual_wrapper');
        const selectWrapper = document.getElementById(type + '_bagian_select_wrapper');
        const manualInput = document.getElementById(type + '_bagian_manual');
        const selectInput = document.getElementById(type + '_bagian_select');
        
        if (isPolres) {
            manualWrapper.style.display = 'none';
            selectWrapper.style.display = 'block';
            manualInput.disabled = true;
            selectInput.disabled = false;
            
            if (currentBagian) {
                selectInput.value = currentBagian;
                document.getElementById(type + '_bagian_select_label').innerText = currentBagian;
            } else {
                selectInput.value = '';
                document.getElementById(type + '_bagian_select_label').innerText = '— Pilih Bagian —';
            }
        } else {
            manualWrapper.style.display = 'block';
            selectWrapper.style.display = 'none';
            manualInput.disabled = false;
            selectInput.disabled = true;
            
            if (currentBagian) {
                manualInput.value = currentBagian;
            }
        }
    }

    function selectBagianDropdown(el, type, value) {
        const wrapper = el.closest('.custom-select-wrapper');
        const label = document.getElementById(type + '_bagian_select_label');
        const input = document.getElementById(type + '_bagian_select');
        
        label.innerText = value;
        input.value = value;
        
        // Visual feedback
        wrapper.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
        el.classList.add('selected');
        
        // Close dropdown
        el.closest('.custom-select').querySelector('.custom-options').style.display = 'none';
        el.closest('.custom-select').classList.remove('active');
        
        if (event) event.stopPropagation();
    }

    function selectOptionSearch(el, inputName, value, label) {
        const wrapper = el.closest('.custom-select-wrapper');
        const trigger = wrapper.querySelector('.select-trigger span');
        const input = wrapper.querySelector('input[type="hidden"]');
        
        trigger.innerText = label;
        input.value = value;
        
        // Submit form
        document.getElementById('filterForm').submit();
    }

    function selectOptionManual(el, inputName, value, label, triggerId, category = null) {
        const wrapper = el.closest('.custom-select-wrapper');
        const trigger = document.getElementById(triggerId);
        const input = wrapper.querySelector('input[type="hidden"]');
        
        trigger.innerText = label;
        input.value = value;

        // Auto-fill Golongan if category is provided (for Rank)
        if (category && inputName === 'rank_id') {
            const golonganInput = document.querySelector('input[name="golongan"]');
            if (golonganInput) {
                golonganInput.value = category;
                // Add a visual flash effect to indicate auto-fill
                golonganInput.style.transition = 'background-color 0.3s';
                golonganInput.style.backgroundColor = '#ecfdf5'; // Light green
                setTimeout(() => {
                    golonganInput.style.backgroundColor = '#F9FAFB'; // Back to default
                }, 500);
            }
        }
        
        // Visual feedback
        wrapper.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
        el.classList.add('selected');
        
        // Close dropdown
        el.closest('.custom-select').querySelector('.custom-options').style.display = 'none';
        el.closest('.custom-select').classList.remove('active');
        
        event.stopPropagation();
    }


    function selectRank(el) {
        const value = el.dataset.value;
        const label = el.dataset.label;
        const category = el.dataset.category;
        const fill = el.dataset.fill;
        
        // Update Hidden Input (Rank ID)
        document.getElementById('add_rank_id').value = value;
        // Update Label
        document.getElementById('add_rank_label').innerText = label;
        
        // Auto-fill Golongan (PNS & Polri)
        const golonganInput = document.querySelector('input[name="golongan"]');
        if (golonganInput) {
            // Use the data-fill attribute which contains either the PNS Grade (IV/a) or Polri Category (PATI/PAMEN/etc)
            golonganInput.value = fill; 
            
            // Visual flash effect (green background)
            golonganInput.style.transition = 'background-color 0.3s';
            golonganInput.style.backgroundColor = '#ecfdf5'; 
            setTimeout(() => { golonganInput.style.backgroundColor = '#F9FAFB'; }, 500);
        }
    
        // UI Feedback (highlight selected option)
        const wrapper = el.closest('.custom-select-wrapper');
        wrapper.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
        el.classList.add('selected');
        
        // Close Dropdown
        el.closest('.custom-select').querySelector('.custom-options').style.display = 'none';
        el.closest('.custom-select').classList.remove('active');
        
        event.stopPropagation();
    }

    function filterRanks(type) {
        // Select all rank options inside the add personnel modal
        const optionsWrapper = document.querySelector('#addPersonnelModal .custom-select-wrapper .options-scroll');
        if (!optionsWrapper) return;
        
        const ranks = optionsWrapper.querySelectorAll('.option');
        const rankLabel = document.getElementById('add_rank_label');
        const rankId = document.getElementById('add_rank_id');

        // Reset selection if changing personnel type (optional logic)
        rankLabel.innerText = '— Pilih Pangkat —';
        rankId.value = '';

        ranks.forEach(rank => {
            // Updated to use data attributes which is much robust
            const category = rank.dataset.category || '';

            if (type === 'Polri') {
                // Show only non-PNS ranks (PATI, PAMEN, PAMA, BINTARA)
                if (category !== 'PNS') {
                    rank.style.display = 'flex'; // Restore flex display
                } else {
                    rank.style.display = 'none';
                }
            } else {
                // Show only PNS ranks
                if (category === 'PNS') {
                    rank.style.display = 'flex';
                } else {
                    rank.style.display = 'none';
                }
            }
        });
    }

    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
        
        if (id === 'addPersonnelModal') {
            // Default to Polri on open
            filterRanks('Polri');
            document.querySelector('input[name="personnel_type"][value="Polri"]').checked = true;
        }
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }

    function openDetailModal(p) {
        document.getElementById('detail_name').innerText = p.full_name;
        document.getElementById('detail_nrp').innerText = (p.nrp || '—');
        document.getElementById('detail_rank').innerText = p.rank ? p.rank.name : '—';
        document.getElementById('detail_satker').innerText = p.satker ? p.satker.name : '—';
        document.getElementById('detail_jabatan').innerText = p.jabatan || '—';
        document.getElementById('detail_bagian').innerText = p.bagian || '—';
        document.getElementById('detail_keterangan').innerText = p.keterangan || '—';
        document.getElementById('detail_golongan').innerText = p.golongan || '—';
        document.getElementById('detail_religion').innerText = p.religion || '—';
        document.getElementById('detail_gender').innerText = p.gender || '—';
        document.getElementById('detail_phone').innerText = p.phone || '—';
        
        // Type Badge Style
        const typeEl = document.getElementById('detail_type');
        typeEl.innerText = p.personnel_type;
        if(p.personnel_type === 'Polri') {
             typeEl.style.background = '#EFF6FF'; typeEl.style.color = '#3B82F6'; typeEl.style.borderColor = '#DBEAFE';
        } else {
             typeEl.style.background = '#F9FAFB'; typeEl.style.color = '#374151'; typeEl.style.borderColor = '#E5E7EB';
        }

        // Avatar Initials
        let initials = '';
        if (p.full_name) {
            const names = p.full_name.split(' ');
            if (names.length > 0) initials += names[0][0];
            if (names.length > 1) initials += names[names.length - 1][0];
        }
        document.getElementById('detail_avatar').innerText = initials.toUpperCase();

        // Measurements
        const mContainer = document.getElementById('detail_measurements');
        mContainer.innerHTML = '';
        
        if (p.submissions && p.submissions.length > 0) {
            p.submissions.forEach(sub => {
                const item = sub.kapor_item ? sub.kapor_item.item_name : 'Unknown Item';
                const size = sub.kapor_size ? sub.kapor_size.size_label : '-';
                
                const div = document.createElement('div');
                div.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #F9FAFB; border-radius: 8px; border: 1px solid #F3F4F6;">
                        <span style="font-size: 14px; font-weight: 500; color: #6B7280;">${item}</span>
                        <span style="font-size: 14px; font-weight: 700; color: #111827;">${size}</span>
                    </div>
                `;
                mContainer.appendChild(div);
            });
        } else {
            mContainer.innerHTML = '<div style="grid-column: span 2; font-size: 13px; color: #9CA3AF; font-style: italic; text-align: center; padding: 12px;">Belum ada data ukuran.</div>';
        }

        openModal('detailPersonnelModal');
    }

    function selectRankEdit(el) {
        const value = el.dataset.value;
        const label = el.dataset.label;
        const category = el.dataset.category;
        const fill = el.dataset.fill;
        
        // Update Hidden Input (Rank ID)
        document.getElementById('edit_rank_id').value = value;
        // Update Label
        document.getElementById('edit_rank_label').innerText = label;
        
        // Auto-fill Golongan (PNS & Polri)
        const golonganInput = document.getElementById('edit_golongan');
        if (golonganInput) {
            golonganInput.value = fill; 
            
            // Visual flash effect
            golonganInput.style.transition = 'background-color 0.3s';
            golonganInput.style.backgroundColor = '#ecfdf5'; 
            setTimeout(() => { golonganInput.style.backgroundColor = '#F9FAFB'; }, 500);
        }
    
        // UI Feedback
        const wrapper = el.closest('.custom-select-wrapper');
        wrapper.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
        el.classList.add('selected');
        
        // Close Dropdown
        el.closest('.custom-select').querySelector('.custom-options').style.display = 'none';
        el.closest('.custom-select').classList.remove('active');
        
        event.stopPropagation();
    }

    function filterRanksEdit(type) {
        const optionsWrapper = document.getElementById('edit_rank_options');
        if (!optionsWrapper) return;
        
        const ranks = optionsWrapper.querySelectorAll('.option');
        const rankLabel = document.getElementById('edit_rank_label');
        const rankId = document.getElementById('edit_rank_id');

        // Reset selection if needed (optional logic, kept simple here to avoid clearing existing data on load)
        // typically we only clear if the user *changes* the type manually, not on initial load
        // But for simplicity, we let the user re-select if they change type.
        
        ranks.forEach(rank => {
            const category = rank.dataset.category || '';
            if (type === 'Polri') {
                if (category !== 'PNS') rank.style.display = 'flex';
                else rank.style.display = 'none';
            } else {
                if (category === 'PNS') rank.style.display = 'flex';
                else rank.style.display = 'none';
            }
        });
    }

    function filterMeasurements(gender, modalId = 'editPersonnelModal') {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        const options = modal.querySelectorAll('.option[data-gender]');
        options.forEach(opt => {
            const sizeGender = opt.getAttribute('data-gender');
            if (!sizeGender || sizeGender === gender) {
                opt.style.display = 'flex';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    function openEditModal(p) {
        const editForm = document.getElementById('editForm');
        editForm.action = '/admin/personnel/' + p.id;
        document.getElementById('edit_personnel_id').value = p.id;
        document.getElementById('edit_nrp').value = p.nrp;
        document.getElementById('edit_full_name').value = p.full_name;
        
        // Set Rank
        document.getElementById('edit_rank_id').value = p.rank_id;
        document.getElementById('edit_rank_label').innerText = p.rank ? p.rank.name : '— Pilih Pangkat —';
        
        // Set Satker
        document.getElementById('edit_satker_id').value = p.satker_id;
        const satkerName = p.satker ? p.satker.name : '';
        document.getElementById('edit_satker_label').innerText = satkerName || '— Pilih Satker —';
        
        // Initialize Bagian Visibility
        updateBagianVisibility(satkerName, 'edit', p.bagian);
        
        document.getElementById('edit_jabatan').value = p.jabatan || '';
        document.getElementById('edit_keterangan').value = p.keterangan || '';
        document.getElementById('edit_keterangan_label').innerText = p.keterangan || '— Pilih Keterangan —';
        
        document.getElementById('edit_phone').value = p.phone || '';
        document.getElementById('edit_golongan').value = p.golongan || '';
        
        // Set Religion
        document.getElementById('edit_religion').value = p.religion || '';
        document.getElementById('edit_religion_label').innerText = p.religion || '— Pilih Agama —';

        // Set Radio Buttons & Trigger Filter
        if (p.personnel_type === 'Polri') {
            document.getElementById('edit_type_polri').checked = true;
            filterRanksEdit('Polri');
        } else if (p.personnel_type === 'PNS') {
            document.getElementById('edit_type_pns').checked = true;
            filterRanksEdit('PNS');
        }

        if (p.gender === 'L') {
            document.getElementById('edit_gender_l').checked = true;
            filterMeasurements('L', 'editPersonnelModal');
        }
        else if (p.gender === 'P') {
            document.getElementById('edit_gender_p').checked = true;
            filterMeasurements('P', 'editPersonnelModal');
        }
        
        // Reset Measurements
        document.querySelectorAll('[id^="edit_measurement_label_"]').forEach(el => el.innerText = '— Pilih Ukuran —');
        document.querySelectorAll('[id^="edit_measurement_"]').forEach(el => el.value = '');

        // Populate Measurements
        if (p.submissions && p.submissions.length > 0) {
            p.submissions.forEach(sub => {
                const itemId = sub.kapor_item_id;
                const sizeId = sub.kapor_size_id;
                const sizeLabel = sub.kapor_size ? sub.kapor_size.size_label : '';

                const input = document.getElementById('edit_measurement_' + itemId);
                const label = document.getElementById('edit_measurement_label_' + itemId);
                
                if (input && label) {
                    input.value = sizeId;
                    label.innerText = sizeLabel;
                    
                    // Highlight selected option in dropdown
                    const wrapper = input.closest('.custom-select-wrapper');
                    if (wrapper) {
                        wrapper.querySelectorAll('.option').forEach(opt => {
                             opt.classList.remove('selected');
                        });
                    }
                }
            });
        }

        openModal('editPersonnelModal');
    }

    function confirmDelete(id, name) {
        document.getElementById('delete_person_name').innerText = name;
        document.getElementById('deleteForm').action = '/admin/personnel/' + id;
        openModal('deleteModal');
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('NRP berhasil disalin: ' + text);
        });
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div style="color: ${type === 'success' ? '#10B981' : '#EF4444'}; font-size: 20px;"><i class="ri-${type === 'success' ? 'checkbox-circle' : 'error-warning'}-fill"></i></div>
            <div style="font-size: 14px; color: #374151; font-weight: 500;">${message}</div>
        `;
        container.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 3000);
    }

    <?php if(session('success')): ?> showToast("<?php echo e(session('success')); ?>"); <?php endif; ?>
    <?php if(session('error')): ?> showToast("<?php echo e(session('error')); ?>", 'error'); <?php endif; ?>
    <?php if($errors->has('nrp')): ?> 
        showToast("NRP/NIP sudah terdaftar dalam sistem.", 'error'); 
    <?php elseif($errors->any()): ?> 
        showToast("Terjadi kesalahan input data.", 'error'); 
    <?php endif; ?>

    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-options').forEach(opt => opt.style.display = 'none');
    });

    let searchTimeout;
    function debounceSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }

    function selectMeasurementSize(el, itemId, sizeId, label) {
        // Update hidden input
        const wrapper = el.closest('.custom-select-wrapper');
        const input = wrapper.querySelector('input[type="hidden"]');
        input.value = sizeId;
        
        // Update Trigger Label
        const trigger = wrapper.querySelector('.select-trigger span');
        trigger.innerText = label;
        
        // Visual Feedback
        wrapper.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
        el.classList.add('selected');
        
        // Close Dropdown
        el.closest('.custom-select').querySelector('.custom-options').style.display = 'none';
        el.closest('.custom-select').classList.remove('active');
        
        event.stopPropagation();
    }

    window.onload = function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput && "<?php echo e(request('search')); ?>") {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }

        // Initialize Add Modal Filter (Default L)
        filterMeasurements('L', 'addPersonnelModal');

        <?php if($errors->any()): ?>
            <?php if(old('modal_type') == 'add'): ?>
                openModal('addPersonnelModal');
                // Re-initialize visibility if Satker was selected
                <?php if(old('satker_id')): ?>
                    const satkerNameAdd = "<?php echo e($satkers->firstWhere('id', old('satker_id'))->name ?? ''); ?>";
                    updateBagianVisibility(satkerNameAdd, 'add', "<?php echo e(old('bagian')); ?>");
                <?php endif; ?>
            <?php elseif(old('modal_type') == 'edit' && old('id')): ?>
                document.getElementById('editForm').action = '/admin/personnel/' + "<?php echo e(old('id')); ?>";
                openModal('editPersonnelModal');
                // Re-initialize visibility if Satker was selected
                <?php if(old('satker_id')): ?>
                    const satkerNameEdit = "<?php echo e($satkers->firstWhere('id', old('satker_id'))->name ?? ''); ?>";
                    updateBagianVisibility(satkerNameEdit, 'edit', "<?php echo e(old('bagian')); ?>");
                <?php endif; ?>
                // Trigger Rank filter for Edit
                <?php if(old('personnel_type')): ?>
                    filterRanksEdit("<?php echo e(old('personnel_type')); ?>");
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\1 KAPOR\si-kapor\resources\views/admin/personnel/index.blade.php ENDPATH**/ ?>