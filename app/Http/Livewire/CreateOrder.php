<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Department;

class CreateOrder extends Component
{
    public $departments, $cities = [], $districts = [];
    public $department_id = '', $city_id = '', $district_id = '';
    public $address, $reference;
    public $envio_type = 1;

    public function mount()
    {
        $this->departments = Department::all();
    }

    public function render()
    {
        return view('livewire.create-order');
    }
}
