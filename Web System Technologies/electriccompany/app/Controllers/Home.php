<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\CustomerAccountModel;

class Home extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerAccountModel();
    }

    /**
     * Display the home page.
     */
    public function home(): string
    {
        $data = [
            'title' => 'PowerFlow Electric - Reliable Energy Solutions',
            'page'  => 'home',
        ];

        return view('home', $data);
    }

    /**
     * Display dashboard with customer accounts list (paginated).
     */
    public function index()
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        // Get search keyword if exists
        $keyword = $this->request->getGet('search');
        $status  = $this->request->getGet('status');
        $type    = $this->request->getGet('type');

        // Items per page
        $perPage = 10;

        // Get paginated data based on filters
        if ($keyword) {
            $accounts = $this->customerModel->searchAccounts($keyword, $perPage);
        } elseif ($status) {
            $accounts = $this->customerModel->getAccountsByStatus($status, $perPage);
        } elseif ($type) {
            $accounts = $this->customerModel->getAccountsByType($type, $perPage);
        } else {
            $accounts = $this->customerModel->getAccountsPaginated($perPage);
        }

        // Get statistics
        $data = [
            'title'              => 'Dashboard - Puihaha Electric',
            'page'               => 'dashboard',
            'username'           => session()->get('username'),
            'accounts'         => $accounts,
            'pager'            => $this->customerModel->pager,
            'total_accounts'   => $this->customerModel->getTotalAccounts(),
            'active_accounts'  => $this->customerModel->getCountByStatus('active'),
            'inactive_accounts' => $this->customerModel->getCountByStatus('inactive'),
            'suspended_accounts' => $this->customerModel->getCountByStatus('suspended'),
            'current_page'     => $this->request->getGet('page') ?? 1,
            'search_keyword'   => $keyword,
            'filter_status'    => $status,
            'filter_type'      => $type,
        ];

        return view('home/index', $data);
    }

    /**
     * View single account details.
     */
    public function viewAccount($id)
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        $account = $this->customerModel->find($id);

        if (!$account) {
            return redirect()
                ->to('/dashboard')
                ->with('error', 'Account not found');
        }

        $data = [
            'title'   => 'Account Details - Puihaha Electric',
            'page'    => 'dashboard',
            'account' => $account,
        ];

        return view('home/view_account', $data);
    }

    public function createAccount()
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        return view('home/account_form', [
            'title'       => 'Create Account - Puihaha Electric',
            'page'        => 'dashboard',
            'heading'     => 'Create Customer Account',
            'description' => 'Add a new customer and electricity connection.',
            'action'      => base_url('account'),
            'submitLabel' => 'Create Account',
            'account'     => [],
            'errors'      => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function storeAccount()
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        $rules = $this->accountRules();
        $rules['account_number'] .= '|is_unique[customer_accounts.account_number]';

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (!$this->customerModel->insert($this->accountData())) {
            return redirect()->back()->withInput()->with('errors', $this->customerModel->errors());
        }

        return redirect()->to('/dashboard')->with('success', 'Customer account created successfully.');
    }

    public function editAccount($id)
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        $account = $this->customerModel->find($id);

        if (!$account) {
            return redirect()->to('/dashboard')->with('error', 'Account not found.');
        }

        return view('home/account_form', [
            'title'       => 'Edit Account - Puihaha Electric',
            'page'        => 'dashboard',
            'heading'     => 'Edit Customer Account',
            'description' => 'Update customer and connection information.',
            'action'      => base_url('account/' . $id),
            'submitLabel' => 'Save Changes',
            'account'     => $account,
            'errors'      => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function updateAccount($id)
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        if (!$this->customerModel->find($id)) {
            return redirect()->to('/dashboard')->with('error', 'Account not found.');
        }

        $rules = $this->accountRules();
        $rules['account_number'] .= '|is_unique[customer_accounts.account_number,id,' . (int) $id . ']';

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (!$this->customerModel->update($id, $this->accountData())) {
            return redirect()->back()->withInput()->with('errors', $this->customerModel->errors());
        }

        return redirect()->to('/account/' . $id)->with('success', 'Customer account updated successfully.');
    }

    public function deleteAccount($id)
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        if (!$this->customerModel->find($id)) {
            return redirect()->to('/dashboard')->with('error', 'Account not found.');
        }

        $this->customerModel->delete($id);

        return redirect()->to('/dashboard')->with('success', 'Customer account deleted successfully.');
    }

    private function accountRules(): array
    {
        return [
            'account_number'  => 'required|max_length[50]',
            'customer_name'   => 'required|max_length[150]',
            'address'         => 'required',
            'phone'           => 'permit_empty|max_length[20]',
            'email'           => 'permit_empty|valid_email|max_length[100]',
            'meter_number'    => 'permit_empty|max_length[50]',
            'connection_type' => 'required|in_list[residential,commercial,industrial]',
            'status'          => 'required|in_list[active,inactive,suspended]',
        ];
    }

    private function accountData(): array
    {
        return [
            'account_number'  => trim((string) $this->request->getPost('account_number')),
            'customer_name'   => trim((string) $this->request->getPost('customer_name')),
            'address'         => trim((string) $this->request->getPost('address')),
            'phone'           => trim((string) $this->request->getPost('phone')),
            'email'           => trim((string) $this->request->getPost('email')),
            'meter_number'    => trim((string) $this->request->getPost('meter_number')),
            'connection_type' => $this->request->getPost('connection_type'),
            'status'          => $this->request->getPost('status'),
        ];
    }

    /**
     * Display login page and process authentication.
     */
    public function login()
    {
        if (session()->get('isLogged') === true) {
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'username' => 'required|max_length[100]',
                'password' => 'required|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Enter both your username and password.');
            }

            $credentials = $this->request->getPost([
                'username',
                'password',
            ]);

            $user = (new User())->findByUsername($credentials['username']);

            // New accounts should store passwords with password_hash().
            // The second condition keeps existing classroom databases
            // with plaintext passwords working.
            $validPassword = $user !== null
                && (
                    password_verify(
                        $credentials['password'],
                        $user['password']
                    )
                    || hash_equals(
                        (string) $user['password'],
                        (string) $credentials['password']
                    )
                );

            if (!$validPassword) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Invalid username or password.');
            }

            session()->regenerate();

            session()->set([
                'isLogged' => true,
                'user_id'  => $user['id'],
                'username' => $user['username'],
            ]);

            return redirect()->to('/dashboard');
        }

        return view('login', [
            'title' => 'Login - Puihaha Electric',
            'page'  => 'login',
        ]);
    }

    /**
     * Display the dashboard.
     */
    public function dashboard()
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        return view('dashboard', [
            'username' => session()->get('username'),
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        if (session()->get('isLogged') !== true) {
            return redirect()->to('/login');
        }

        session()->destroy();

        return redirect()
            ->to('/login')
            ->with('success', 'You have been logged out.');
    }
}
