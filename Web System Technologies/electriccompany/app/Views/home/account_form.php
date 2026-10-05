<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<section class="account-form-header">
    <div class="container">
        <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none d-inline-block mb-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        <h1 class="display-5 fw-bold mb-2"><?= esc($heading) ?></h1>
        <p class="lead mb-0"><?= esc($description) ?></p>
    </div>
</section>

<section class="section-padding bg-light-custom">
    <div class="container">
        <div class="card account-form-card p-4 p-lg-5 mx-auto">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <div class="fw-bold mb-2"><i class="fas fa-circle-exclamation me-2"></i>Please correct the following:</div>
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= esc($action) ?>" method="POST">
                <?= csrf_field() ?>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="account_number" class="form-label fw-semibold">Account Number *</label>
                        <input type="text" class="form-control form-control-lg" id="account_number" name="account_number" maxlength="50" value="<?= old('account_number', $account['account_number'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label for="meter_number" class="form-label fw-semibold">Meter Number</label>
                        <input type="text" class="form-control form-control-lg" id="meter_number" name="meter_number" maxlength="50" value="<?= old('meter_number', $account['meter_number'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label for="customer_name" class="form-label fw-semibold">Customer Name *</label>
                        <input type="text" class="form-control form-control-lg" id="customer_name" name="customer_name" maxlength="150" value="<?= old('customer_name', $account['customer_name'] ?? '') ?>" required>
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label fw-semibold">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?= old('address', $account['address'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" maxlength="100" value="<?= old('email', $account['email'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="tel" class="form-control form-control-lg" id="phone" name="phone" maxlength="20" value="<?= old('phone', $account['phone'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="connection_type" class="form-label fw-semibold">Connection Type *</label>
                        <?php $selectedType = old('connection_type', $account['connection_type'] ?? 'residential'); ?>
                        <select class="form-select form-select-lg" id="connection_type" name="connection_type" required>
                            <option value="residential" <?= $selectedType === 'residential' ? 'selected' : '' ?>>Residential</option>
                            <option value="commercial" <?= $selectedType === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                            <option value="industrial" <?= $selectedType === 'industrial' ? 'selected' : '' ?>>Industrial</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Account Status *</label>
                        <?php $selectedStatus = old('status', $account['status'] ?? 'active'); ?>
                        <select class="form-select form-select-lg" id="status" name="status" required>
                            <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $selectedStatus === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= $selectedStatus === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-end gap-3 mt-5">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-floppy-disk me-2"></i><?= esc($submitLabel) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    .account-form-header {
        padding: 60px 0;
        color: #fff;
        background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
    }

    .account-form-card {
        max-width: 950px;
        border-top: 5px solid var(--secondary-color);
    }

    .account-form-card:hover {
        transform: none;
    }
</style>

<?= $this->endSection() ?>
