<?php
namespace App\Models;
use CodeIgniter\Model;
class CustomerAccountModel extends Model
{
protected $table = 'customer_accounts';
protected $primaryKey = 'id';
protected $allowedFields = [
'account_number',
'customer_name',
'address',
'phone',
'email',
'meter_number',
'connection_type',
'status'
];
protected $useTimestamps = true;
protected $createdField = 'created_at';
protected $updatedField = 'updated_at';
protected $validationRules = [
'account_number'  => 'required|max_length[50]',
'customer_name'   => 'required|max_length[150]',
'address'         => 'required',
'phone'           => 'permit_empty|max_length[20]',
'email'           => 'permit_empty|valid_email|max_length[100]',
'meter_number'    => 'permit_empty|max_length[50]',
'connection_type' => 'required|in_list[residential,commercial,industrial]',
'status'          => 'required|in_list[active,inactive,suspended]',
];
public function getAccountsPaginated($perPage = 10)
{
return $this->orderBy('created_at', 'DESC')->paginate($perPage);
}
public function searchAccounts($keyword, $perPage = 10)
{
return $this->like('account_number', $keyword)
->orLike('customer_name', $keyword)
->orLike('email', $keyword)
->orLike('phone', $keyword)
->orderBy('created_at', 'DESC')
->paginate($perPage);
}
public function getAccountsByStatus($status, $perPage = 10)
{
return $this->where('status', $status)
->orderBy('created_at', 'DESC')
->paginate($perPage);
}
public function getAccountsByType($type, $perPage = 10)
{
return $this->where('connection_type', $type)
->orderBy('created_at', 'DESC')
->paginate($perPage);
}
public function getTotalAccounts()
{
return $this->countAllResults();
}
public function getCountByStatus($status)
{
return $this->where('status', $status)->countAllResults();
}
}
