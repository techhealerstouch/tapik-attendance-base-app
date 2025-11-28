<div class="d-flex justify-content-center">
    <h6 style="font-size: 20px" class='form-label'>Add profile theme!</h6>
</div>
<br>

<div class="container">
    <div class="row">
    <form action="{{ route('editTheme') }}" enctype="multipart/form-data" method="post" class="col-lg-12">
        @csrf
        <div class="row" style="margin-right: 0px; margin-left: 0px; padding-left: 0px; padding-right: 0px;">
            <select id="themeSelect" name="selected_theme" class="form-select">
                <option value="" disabled selected>{{__('messages.Select a theme')}}</option>
                <?php
                if ($handle = opendir('themes')) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {
                            if (file_exists(base_path('themes') . '/' . $entry . '/readme.md')) {
                                $text = file_get_contents(base_path('themes') . '/' . $entry . '/readme.md');
                                $pattern = '/Theme Name:.*/';
                                preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE);
                                if (sizeof($matches) > 0) {
                                    $themeName = substr($matches[0][0], 12);
                                    echo '<option value="' . $entry . '">' . $themeName . '</option>';
                                }
                            }
                        }
                    }
                }
                ?>
            </select>

            <!-- Placeholder for the selected theme image and loading indicator -->
            <div id="selectedThemeImageContainer" class="mt-3">
                <div id="loadingIndicator" style="display: none;"></div>
                <img id="selectedThemeImage" src="" alt="Selected Theme" style="display: none; max-width: 100%;" />
            </div>
        </div>
        <input type="hidden" id="selectedTheme" name="selectedTheme">
    </form>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const themeSelect = document.getElementById('themeSelect');
    const selectedThemeInput = document.getElementById('selectedTheme');
    const selectedThemeImage = document.getElementById('selectedThemeImage');
    const loadingIndicator = document.getElementById('loadingIndicator');

    themeSelect.addEventListener('change', function() {
        const selectedTheme = themeSelect.value;

        // Update the value of the selectedTheme input field
        selectedThemeInput.value = selectedTheme;

        // Show loading indicator
        loadingIndicator.style.display = 'block';
        selectedThemeImage.style.display = 'none';

        // Simulate image loading with a timeout (replace this with actual image loading logic)
        setTimeout(() => {
            // Update the image source based on the selected theme
            selectedThemeImage.src = `{{url('themes/${selectedTheme}/preview.png')}}`; // Adjust the path as needed

            // Hide loading indicator and show the image
            selectedThemeImage.onload = function() {
                loadingIndicator.style.display = 'none';
                selectedThemeImage.style.display = 'block';
            };

            // Handle image load error
            selectedThemeImage.onerror = function() {
                loadingIndicator.style.display = 'none';
                selectedThemeImage.style.display = 'none';
                alert('Failed to load the image.');
            };
        }, 1000); // Simulated delay, remove this timeout in actual implementation
    });
});

</script>

<style>
    #loadingIndicator {
        border: 5px solid #f3f3f3; /* Light grey */
        border-top: 5px solid #D60024; /* Blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 2s linear infinite;
        margin: 20px auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
