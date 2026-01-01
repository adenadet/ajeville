<?php

namespace App\Http\Controllers;

use App\Http\Traits\Inventory\StoreTrait;
use App\Models\Lms\Lesson;
use App\Models\Policy\Policy;
use Illuminate\Http\Request;

class ModulesController extends Controller
{
    use StoreTrait;
    public function approvals()
    {
        $params = [
            'page_title' => 'Approvals',
            'icon' => 'fas fa-file-signature', 
        ];
        return view('app')->with($params);
    
    }

    public function archives()
    {
        $params = [
            'page_title' => 'Archives',
            'icon' => 'fas fa-archive', 
        ];
        return view('app')->with($params);
    
    }

    public function chats()
    {
        $params = [
            'icon' => 'fas fa-comments',
            'page_title' => 'Chats',
        ];
        return view('app')->with($params);
    }

    public function consultation()
    {
        $params = [
            'icon' => 'fas fa-user-md',
            'page_title' => 'Consultation',
        ];
        return view('app')->with($params);
    }

    public function contacts()
    {
        $params = [
            'page_title' => 'Contacts',
        ];
        return view('app')->with($params);
    }

    public function coop()
    {
        $params = [
            'icon' => 'fas fa-wallet',
            'page_title' => 'Cooperative',
        ];
        return view('app')->with($params);
    }

    public function coop_admin()
    {
        $params = [
            'icon' => 'fas fa-user-cog',
            'page_title' => 'Cooperative Admin',
        ];
        return view('app')->with($params);
    }

    public function customer_relations()
    {
        $params = [
            'page_title' => 'Customer Relations',
            'icon' => 'fa fa-users', 
        ];
        return view('app')->with($params);
    
    }

    public function dashboard()
    {
        $params = [
            'icon' => 'fas fa-tachometer-alt',
            'page_title' => 'Dashboard',
        ];
        return view('app')->with($params);
    }

    public function departments()
    {
        $params = ['page_title' => 'Departments',];
        return view('app')->with($params);
    }

    public function equipments()
    {
        $params = [
            'icon' => 'fas fa-laptop-house',
            'page_title' => 'Equipments',
        ];
        return view('app')->with($params);
    }

    public function escrow_admin()
    {
        $params = [
            'icon' => 'fas fa-money-check',
            'page_title' => 'Escrow Admin',
        ];
        return view('app')->with($params);
    }

    public function escrows()
    {
        $params = [
            'icon' => 'fas fa-money-check',
            'page_title' => 'Escrows',
        ];
        return view('app')->with($params);
    }

    public function facility()
    {
        $params = [
            'page_title' => 'Facility Management',
            'icon' => 'fas fa-building', 
        ];
        return view('app')->with($params);
    
    }

    public function finance()
    {
        $params = [
            'icon' => 'fas fa-money-bill-wave',
            'page_title' => 'Finance',
        ];
        return view('app')->with($params);
    }

    public function front_office()
    {
        $params = [
            'icon' => 'fas fa-laptop-house',
            'page_title' => 'Front Office',
        ];
        return view('app')->with($params);
    }

    public function hrms()
    {
        $params = [
            'icon' => 'fas fa-user-tie',    
            'page_title' => 'Human Resources Management',
        ];
        return view('app')->with($params);
    }

    public function hrms_admin()
    {
        $params = [
            'icon' => 'fas fa-user-cog',
            'page_title' => 'Human Resources Admin',
        ];
        return view('app')->with($params);
    }

    public function insurance()
    {
        $params = [
            'page_title' => 'Managed Care',
            'icon' => 'fas fa-file-alt',
        ];
        return view('app')->with($params);
    }

    public function internet()
    {
        $params = [
            'page_title' => 'Internet',
        ];
        return view('app')->with($params);
    }


    public function inventory()
    {
        $params = [
            'page_title' => 'Inventory',
            'icon' => 'fas fa-warehouse',
            'user_stores' => $this->inventory_store_user_get('my_stores', null, true, false, null)
        ];
        return view('app')->with($params);
    }

    public function laboratory()
    {
        $params = [
            'page_title' => 'Laboratory',
            'icon' => 'fas fa-flask',
            'user_stores' => $this->inventory_store_user_get('my_stores', null, true, false, null)
        ];
        return view('app')->with($params);
    }

    public function learn_admin()
    {
        $params = [
            'page_title' => 'Learn Admin',
            'icon' => 'fas fa-user-cog', 
        ];
        return view('app')->with($params);
    
    }

    public function learn_student()
    {
        $params = [
            'page_title' => 'Learn Student',
            'icon' => 'fas fa-chalkboard', 
        ];
        return view('app')->with($params);
    
    }

    public function learn_tutor()
    {
        $params = [
            'page_title' => 'Learn Tutor',
            'icon' => 'fas fa-chalkboard-teacher', 
        ];
        return view('app')->with($params);
    
    }

    public function loans()
    {
        $params = [
            'page_title' => 'Loans',
            'icon' => 'fas fa-user-circle',
        ];
        return view('app')->with($params);
    }

    public function loans_staff()
    {
        $params = [
            'page_title' => 'Loans',
            'icon' => 'fas fa-house-user',
        ];
        return view('app')->with($params);
    }

    public function manage_care()
    {
        $params = [
            'page_title' => 'Loans',
            'icon' => 'fas fa-house-user',
        ];
        return view('app')->with($params);
    }

    public function notices()
    {
        $params = ['page_title' => 'Notice Board',];
        return view('app')->with($params);
    }

    public function nursing()
    {
        $params = ['page_title' => 'Nursing Care', 'icon' => 'fas fa-user-nurse',];
        return view('app')->with($params);
    }

    public function operations()
    {
        $params = [
            'page_title' => 'Operations',
            'icon' => 'fas fa-cogs',
        ];
        return view('app')->with($params);
    }

    public function policies()
    {
        $params = [
            'page_title' => 'Policies',
        ];
        return view('app')->with($params);
    }

    public function policy_reader($id)
    {

        $params = [
            'page_title' => 'Policy | Reader',
            'policy' => Policy::where('id', '=', $id)->first(),
        ];
        return view('app')->with($params);
    }

    public function procurement()
    {
        $params = [
            'page_title' => 'Procurement',
            'icon' => 'fas fa-shopping-cart', 
        ];
        return view('app')->with($params);
    }

    public function profile()
    {
        $params = [
            'page_title' => 'Profile',
        ];
        return view('app')->with($params);
    }

    public function radiology()
    {
        $params = [
            'page_title' => 'Radiology',
            'icon' => 'fas fa-x-ray',
            'user_stores' => $this->inventory_store_user_get('my_stores', null, true, false, null)
        ];
        return view('app')->with($params);
    }

    public function sales_orders()
    {
        $params = [
            'page_title' => 'Sales Orders',
            'icon' => 'fas fa-cash-register',
            'user_stores' => $this->inventory_store_user_get('my_stores', null, true, false, null)
        ];
        return view('app')->with($params);
    }

    public function settings()
    {
        $params = [
            'page_title' => 'Settings',
        ];
        return view('app')->with($params);
    }

    public function staff_month()
    {
        $params = [
            'page_title' => 'Staff of the Month',
        ];
        return view('app')->with($params);
    }

    public function ticketing()
    {
        $params = [
            'page_title' => 'Tickets',
        ];
        return view('app')->with($params);
    }

    public function users()
    {
        $params = [
            'page_title' => 'Users',
        ];
        return view('app')->with($params);
    }
}
