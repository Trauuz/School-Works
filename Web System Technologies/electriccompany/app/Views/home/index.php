<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<section class="dashboard-header">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-2">Customer Account Dashboard</h1>
                <p class="lead mb-0">
                    Welcome back, <?= esc($username) ?>. Manage and review customer accounts in one place.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding bg-light-custom dashboard-content">
    <div class="container">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-circle-exclamation me-2"></i>
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-circle-check me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100 p-4 stat-total">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Total Accounts</p>
                            <h2 class="display-6 fw-bold mb-0"><?= esc($total_accounts) ?></h2>
                        </div>
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100 p-4 stat-active">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Active Accounts</p>
                            <h2 class="display-6 fw-bold mb-0"><?= esc($active_accounts) ?></h2>
                        </div>
                        <div class="stat-icon"><i class="fas fa-circle-check"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100 p-4 stat-inactive">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Inactive Accounts</p>
                            <h2 class="display-6 fw-bold mb-0"><?= esc($inactive_accounts) ?></h2>
                        </div>
                        <div class="stat-icon"><i class="fas fa-circle-pause"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card h-100 p-4 stat-suspended">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-1">Suspended Accounts</p>
                            <h2 class="display-6 fw-bold mb-0"><?= esc($suspended_accounts) ?></h2>
                        </div>
                        <div class="stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card dashboard-panel p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                <div>
                    <h2 class="h4 fw-bold text-primary-custom mb-1">Find Customer Accounts</h2>
                    <p class="text-muted mb-0">Search by customer details or filter account records.</p>
                </div>
                <?php if ($search_keyword || $filter_status || $filter_type): ?>
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fas fa-xmark me-1"></i>Clear filters
                    </a>
                <?php endif; ?>
            </div>

            <form method="GET" action="<?= base_url('dashboard') ?>">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <label for="search" class="form-label fw-semibold">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-magnifying-glass"></i></span>
                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                placeholder="Name, account, email, or phone"
                                value="<?= esc($search_keyword ?? '') ?>"
                            >
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All statuses</option>
                            <option value="active" <?= ($filter_status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($filter_status ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            <option value="suspended" <?= ($filter_status ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-5">
                        <label for="type" class="form-label fw-semibold">Connection type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">All types</option>
                            <option value="residential" <?= ($filter_type ?? '') === 'residential' ? 'selected' : '' ?>>Residential</option>
                            <option value="commercial" <?= ($filter_type ?? '') === 'commercial' ? 'selected' : '' ?>>Commercial</option>
                            <option value="industrial" <?= ($filter_type ?? '') === 'industrial' ? 'selected' : '' ?>>Industrial</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card dashboard-panel overflow-hidden">
            <div class="p-4 border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h2 class="h4 fw-bold text-primary-custom mb-1">Customer Accounts</h2>
                        <p class="text-muted mb-0">Review account details and current service status.</p>
                    </div>
                    <a href="<?= base_url('account/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add Account
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table dashboard-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Account Number</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Connection</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($accounts)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-folder-open fa-2x text-secondary mb-3 d-block"></i>
                                    <span class="text-muted">No accounts found.</span>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($accounts as $account): ?>
                                <tr>
                                    <td class="fw-bold text-primary-custom"><?= esc($account['account_number']) ?></td>
                                    <td><?= esc($account['customer_name']) ?></td>
                                    <td><?= esc($account['email']) ?></td>
                                    <td><?= esc($account['phone']) ?></td>
                                    <td>
                                        <span class="badge connection-badge">
                                            <?= esc(ucfirst($account['connection_type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php $status = strtolower((string) $account['status']); ?>
                                        <span class="badge status-badge status-<?= esc($status) ?>">
                                            <?= esc(ucfirst($status)) ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="<?= base_url('account/' . $account['id']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="View account">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('account/' . $account['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3" title="Edit account">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="<?= base_url('account/' . $account['id'] . '/delete') ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer account? This action cannot be undone.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Delete account">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pager && $pager->getPageCount() > 1): ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-4 border-top">
                    <span class="text-muted">
                        Page <?= esc($current_page) ?> of <?= esc($pager->getPageCount()) ?>
                    </span>
                    <nav aria-label="Customer account pages">
                        <?= $pager->links() ?>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .dashboard-header {
        position: relative;
        overflow: hidden;
        padding: 70px 0;
        color: #fff;
        background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: .3;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    }

    .dashboard-header .container {
        position: relative;
        z-index: 1;
    }

    .dashboard-content {
        min-height: 600px;
    }

    .stat-card {
        color: #fff;
        border-radius: 15px;
    }

    .stat-card p {
        opacity: .85;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, .18);
        font-size: 1.5rem;
    }

    .stat-total { background: linear-gradient(135deg, #1e40af, #1f2937); }
    .stat-active { background: linear-gradient(135deg, #047857, #10b981); }
    .stat-inactive { background: linear-gradient(135deg, #475569, #64748b); }
    .stat-suspended { background: linear-gradient(135deg, #b45309, #f59e0b); }

    .dashboard-panel {
        border-radius: 15px;
    }

    .dashboard-panel:hover {
        transform: none;
    }

    .dashboard-table thead th {
        padding: 1rem;
        color: #fff;
        border: 0;
        white-space: nowrap;
        background: var(--dark-color);
    }

    .dashboard-table tbody td {
        padding: 1rem;
        border-color: #e5e7eb;
    }

    .dashboard-table tbody tr:hover {
        background: #f8fafc;
    }

    .connection-badge {
        color: var(--primary-color);
        background: #dbeafe;
    }

    .status-badge {
        padding: .45rem .7rem;
    }

    .status-active { color: #065f46; background: #d1fae5; }
    .status-inactive { color: #475569; background: #e2e8f0; }
    .status-suspended { color: #92400e; background: #fef3c7; }

    .dashboard-panel .pagination {
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        padding: .25rem;
        list-style: none;
    }

    .dashboard-panel .pagination li a {
        min-width: 2.5rem;
        height: 2.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: .45rem .8rem;
        color: var(--primary-color);
        text-decoration: none;
        border: 1px solid #dbe2ea;
        border-radius: .65rem;
        background: #fff;
        transition: all .2s ease;
    }

    .dashboard-panel .pagination li a:hover,
    .dashboard-panel .pagination li.active a {
        color: #fff;
        border-color: var(--primary-color);
        background: var(--primary-color);
    }

    @media (max-width: 767.98px) {
        .dashboard-header {
            padding: 55px 0;
            text-align: center;
        }
    }
</style>

<?= $this->endSection() ?>
