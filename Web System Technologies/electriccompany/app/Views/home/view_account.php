<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<section class="account-detail-header">
    <div class="container">
        <a href="<?= base_url('dashboard') ?>" class="text-white text-decoration-none d-inline-block mb-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
            <div>
                <span class="badge rounded-pill text-bg-warning px-3 py-2 mb-3">Account Details</span>
                <h1 class="display-5 fw-bold mb-2"><?= esc($account['customer_name']) ?></h1>
                <p class="lead mb-0">Account <?= esc($account['account_number']) ?></p>
            </div>
            <a href="<?= base_url('account/' . $account['id'] . '/edit') ?>" class="btn btn-outline-light btn-lg">
                <i class="fas fa-pen me-2"></i>Edit Account
            </a>
        </div>
    </div>
</section>

<section class="section-padding bg-light-custom">
    <div class="container">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-circle-check me-2"></i><?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <div class="card account-detail-card p-4 p-lg-5">
            <div class="row g-4">
                <?php
                $details = [
                    ['Account Number', $account['account_number'], 'fa-hashtag'],
                    ['Meter Number', $account['meter_number'] ?: 'Not provided', 'fa-gauge'],
                    ['Customer Name', $account['customer_name'], 'fa-user'],
                    ['Phone', $account['phone'] ?: 'Not provided', 'fa-phone'],
                    ['Email', $account['email'] ?: 'Not provided', 'fa-envelope'],
                    ['Connection Type', ucfirst($account['connection_type']), 'fa-plug'],
                    ['Created', date('F j, Y g:i A', strtotime($account['created_at'])), 'fa-calendar-plus'],
                    ['Last Updated', date('F j, Y g:i A', strtotime($account['updated_at'])), 'fa-clock-rotate-left'],
                ];
                ?>

                <?php foreach ($details as [$label, $value, $icon]): ?>
                    <div class="col-md-6">
                        <div class="detail-item h-100">
                            <div class="detail-icon"><i class="fas <?= esc($icon) ?>"></i></div>
                            <div>
                                <div class="small text-muted fw-semibold text-uppercase"><?= esc($label) ?></div>
                                <div class="fs-5 fw-semibold"><?= esc($value) ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="col-12">
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-location-dot"></i></div>
                        <div>
                            <div class="small text-muted fw-semibold text-uppercase">Address</div>
                            <div class="fs-5 fw-semibold"><?= esc($account['address']) ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <?php $status = strtolower((string) $account['status']); ?>
                    <div class="detail-item">
                        <div class="detail-icon"><i class="fas fa-circle-info"></i></div>
                        <div>
                            <div class="small text-muted fw-semibold text-uppercase">Status</div>
                            <span class="badge fs-6 status-<?= esc($status) ?>"><?= esc(ucfirst($status)) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between gap-3 mt-5 pt-4 border-top">
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard
                </a>
                <form action="<?= base_url('account/' . $account['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this customer account? This action cannot be undone.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger btn-lg rounded-pill px-4">
                        <i class="fas fa-trash me-2"></i>Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .account-detail-header {
        padding: 60px 0;
        color: #fff;
        background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
    }

    .account-detail-card {
        border-top: 5px solid var(--secondary-color);
    }

    .account-detail-card:hover {
        transform: none;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 12px;
        background: #f8fafc;
    }

    .detail-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        color: #fff;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    }

    .status-active { color: #065f46; background: #d1fae5; }
    .status-inactive { color: #475569; background: #e2e8f0; }
    .status-suspended { color: #92400e; background: #fef3c7; }
</style>

<?= $this->endSection() ?>
