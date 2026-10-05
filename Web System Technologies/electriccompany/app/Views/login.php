<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<section class="hero-section login-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome Back to Puihaha Electric</h1>
                <p class="lead mb-4">
                    Sign in to access your account and stay connected with your
                    trusted electrical service team.
                </p>
                <div class="d-flex flex-column gap-3 login-benefits">
                    <div><i class="fas fa-shield-alt text-warning me-2"></i>Secure account access</div>
                    <div><i class="fas fa-clock text-warning me-2"></i>24/7 emergency support</div>
                    <div><i class="fas fa-headset text-warning me-2"></i>Help whenever you need it</div>
                </div>
            </div>

            <div class="col-lg-5 ms-lg-auto">
                <div class="card login-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="feature-icon login-icon"><i class="fas fa-user"></i></div>
                        <h2 class="fw-bold text-primary-custom mb-2">Log In</h2>
                        <p class="text-muted mb-0">Enter your account details below.</p>
                    </div>

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

                    <form action="<?= base_url('login') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-user text-primary-custom"></i>
                                </span>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="username"
                                    id="username"
                                    value="<?= esc(old('username')) ?>"
                                    placeholder="Enter your username"
                                    autocomplete="username"
                                    required
                                    autofocus
                                >
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-lock text-primary-custom"></i>
                                </span>
                                <input
                                    type="password"
                                    class="form-control"
                                    name="password"
                                    id="password"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                >
                                <button
                                    class="btn btn-outline-secondary password-toggle"
                                    type="button"
                                    id="passwordToggle"
                                    aria-label="Show password"
                                    aria-pressed="false"
                                >
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-right-to-bracket me-2"></i>Log In
                        </button>
                    </form>

                    <p class="text-center text-muted mt-4 mb-0">
                        Don't have an account?
                        <a href="<?= base_url('register') ?>" class="fw-semibold text-primary-custom">
                            Register here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .login-hero {
        min-height: 690px;
        display: flex;
        align-items: center;
    }

    .login-card {
        color: var(--dark-color);
        border-top: 5px solid var(--secondary-color);
    }

    .login-card:hover {
        transform: none;
    }

    .login-icon {
        width: 70px;
        height: 70px;
        margin-bottom: 16px;
    }

    .login-benefits {
        font-size: 1.05rem;
    }

    .login-card .input-group-text,
    .login-card .form-control,
    .password-toggle {
        border-color: #d1d5db;
    }

    .login-card .input-group:focus-within .input-group-text,
    .login-card .input-group:focus-within .form-control,
    .login-card .input-group:focus-within .password-toggle {
        border-color: var(--secondary-color);
    }

    .login-card .form-control:focus {
        box-shadow: none;
    }

    .password-toggle {
        padding: 0 1rem;
        border-radius: 0 10px 10px 0;
    }

    @media (max-width: 991.98px) {
        .login-hero {
            min-height: auto;
            padding: 70px 0;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const password = document.getElementById('password');
        const toggle = document.getElementById('passwordToggle');
        const icon = toggle.querySelector('i');

        toggle.addEventListener('click', function () {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
            toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            toggle.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        });
    });
</script>

<?= $this->endSection() ?>
