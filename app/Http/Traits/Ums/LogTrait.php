<?php
namespace App\Http\Traits\Ums;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\FileManagerTrait;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;

use App\Models\EMR\Patient;
use App\Models\HRMS\Employee;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;


trait LogTrait{
    use FileManagerTrait;
}