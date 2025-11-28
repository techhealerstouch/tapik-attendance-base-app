<?php

namespace App\Http\Livewire;

use Livewire\Component;

class MultiStepForm extends Component
{

    public $currentStep = 1;
    public $total_steps = 3;

    public function render()
    {
        return view('livewire.multi-step-form', ['currentStep' => $this->currentStep]);
    }

    public function nextStep()
    {
        // Limit the steps to a certain number (e.g., 3)
        if ($this->currentStep < 3) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        // Ensure we don't go below step 1
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function submitForm()
    {
        // Handle form submission logic here
        // You can access $this->field1, $this->field2, etc. for form data
    }
}
