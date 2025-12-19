<!-- Step 3 - Theme Selection -->
<style>
.step-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 15px;
}

.step-header {
    text-align: center;
    margin-bottom: 30px;
}

.step-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.step-subtitle {
    font-size: 14px;
    color: #6c757d;
}

.theme-selector-wrapper {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.theme-select-label {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    display: block;
}

.form-select-theme {
    width: 100%;
    padding: 14px 40px 14px 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 14px;
    background-color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
}

.form-select-theme:focus {
    outline: none;
    border-color: #db5363;
    box-shadow: 0 0 0 4px rgba(219, 83, 99, 0.1);
}

.theme-preview-container {
    margin-top: 25px;
}

.preview-label {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
    display: block;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 40px 0;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #db5363;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.theme-preview-image {
    display: none;
    width: 100%;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.theme-preview-image:hover {
    transform: scale(1.02);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #dee2e6;
}

.empty-state-icon {
    font-size: 48px;
    margin-bottom: 15px;
}

.empty-state-text {
    color: #6c757d;
    font-size: 14px;
}

@media (max-width: 768px) {
    .step-title {
        font-size: 20px;
    }
    
    .theme-selector-wrapper {
        padding: 20px;
    }
}
</style>

<div class="step-container">
    <div class="step-header">
        <h6 class="step-title">Choose Your Theme</h6>
        <p class="step-subtitle">Personalize your profile's appearance</p>
    </div>

    <div class="theme-selector-wrapper">
        <form action="{{ route('editTheme') }}" enctype="multipart/form-data" method="post">
            @csrf
            
            <label class="theme-select-label">Select a theme</label>
            <select id="themeSelect" name="selected_theme" class="form-select-theme">
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

            <div class="theme-preview-container">
                <label class="preview-label">Theme Preview</label>
                
                <div class="empty-state" id="emptyState">
                    <div class="empty-state-icon">🎨</div>
                    <p class="empty-state-text">Select a theme to see preview</p>
                </div>
                
                <div id="loadingIndicator" class="loading-spinner">
                    <div class="spinner"></div>
                    <p style="margin-top: 15px; color: #6c757d; font-size: 14px;">Loading preview...</p>
                </div>
                
                <img id="selectedThemeImage" src="" alt="Selected Theme" class="theme-preview-image" />
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
        const emptyState = document.getElementById('emptyState');

        themeSelect.addEventListener('change', function() {
            const selectedTheme = themeSelect.value;
            selectedThemeInput.value = selectedTheme;

            // Hide empty state and image, show loading
            emptyState.style.display = 'none';
            selectedThemeImage.style.display = 'none';
            loadingIndicator.style.display = 'block';

            // Update image source
            selectedThemeImage.src = `{{url('themes/${selectedTheme}/preview.png')}}`;

            // Handle image load
            selectedThemeImage.onload = function() {
                loadingIndicator.style.display = 'none';
                selectedThemeImage.style.display = 'block';
            };

            // Handle image error
            selectedThemeImage.onerror = function() {
                loadingIndicator.style.display = 'none';
                emptyState.style.display = 'block';
                emptyState.querySelector('.empty-state-text').textContent = 'Preview not available for this theme';
            };
        });
    });
</script>