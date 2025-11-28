<div>
    <input wire:model="rfid_no" placeholder="" type='text' name="rfid_no" class='form-control' autofocus 
        style="border: none; outline: none; box-shadow: none; color: transparent; caret-color: transparent; background-color: transparent; -webkit-text-fill-color: transparent;" />

    <br><br>
    <div>
        @if (session()->has('error'))
            <span class="text-danger">{{ session('error') }}</span>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
        const rfidInput = document.querySelector('input[name="rfid_no"]');

        if (rfidInput) {
            // Function to ensure the input always regains focus
            const ensureFocus = () => {
                if (document.activeElement !== rfidInput) {
                    rfidInput.focus();
                }
            };

            // Continuously check and refocus every 100ms
            setInterval(ensureFocus, 100);

            // Ensure focus after Livewire updates
            document.addEventListener('livewire:update', ensureFocus);
        }
    });
</script>
