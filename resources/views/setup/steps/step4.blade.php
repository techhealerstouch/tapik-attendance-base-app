<!-- Step 4 - Social Media Icons -->
<script src="{{ asset('assets/external-dependencies/fontawesome.js') }}" crossorigin="anonymous"></script>

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

.social-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.social-input-group {
    background: white;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    overflow: hidden;
    transition: all 0.3s ease;
}

.social-input-group:focus-within {
    border-color: #db5363;
    box-shadow: 0 0 0 4px rgba(219, 83, 99, 0.1);
}

.social-input-wrapper {
    display: flex;
    align-items: center;
}

.social-icon {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-right: 2px solid #e9ecef;
    font-size: 20px;
}

.social-icon i {
    color: #495057;
}

.social-input {
    flex: 1;
    border: none;
    padding: 14px 15px;
    font-size: 14px;
    outline: none;
}

.social-input::placeholder {
    color: #adb5bd;
}

.delete-btn {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    border: none;
    background: transparent;
    color: #dc3545;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.delete-btn:hover {
    background: #dc3545;
    color: white;
}

.delete-btn i {
    font-size: 18px;
}

.info-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.info-banner h6 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
}

.info-banner p {
    font-size: 13px;
    margin: 0;
    opacity: 0.95;
}

@media (min-width: 768px) {
    .social-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 767px) {
    .step-title {
        font-size: 20px;
    }
    
    .social-icon {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
    
    .delete-btn {
        width: 45px;
        height: 45px;
    }
    
    .social-input {
        padding: 12px;
        font-size: 13px;
    }
}
</style>

<div class="step-container">
    <div class="step-header">
        <h6 class="step-title">Social Media Links</h6>
        <p class="step-subtitle">Connect your social profiles</p>
    </div>

    <div class="info-banner">
        <h6>🔗 Link Your Profiles</h6>
        <p>Add links to your social media profiles to make it easy for people to connect with you across platforms.</p>
    </div>

    @php
    function iconLink($icon){
        $iconLink = DB::table('links')
            ->where('user_id', Auth::id())
            ->where('title', $icon)
            ->where('button_id', 94)
            ->value('link');
        if (is_null($iconLink)){
            return '';
        } else {
            return $iconLink;
        }
    }
    
    function searchIcon($icon) {
        $iconId = DB::table('links')
            ->where('user_id', Auth::id())
            ->where('title', $icon)
            ->where('button_id', 94)
            ->value('id');
        if(is_null($iconId)){
            return false;
        } else {
            return $iconId;
        }
    }
    
    function socialIcon($name, $label, $iconClass) {
        $hasExisting = searchIcon($name) != NULL;
        echo '<div class="social-input-group">
                <div class="social-input-wrapper">
                    <div class="social-icon">
                        <i class="fab fa-'.$iconClass.'"></i>
                    </div>
                    <input 
                        type="url" 
                        class="social-input" 
                        name="'.$name.'" 
                        value="'.iconLink($name).'" 
                        placeholder="'.$label.' URL"
                    />
                    '.($hasExisting ? '<a href="'.route("deleteLink", searchIcon($name)).'" class="delete-btn"><i class="bi bi-trash-fill"></i></a>' : '').'
                </div>
            </div>';
    }
    @endphp

    <div class="social-grid">
        {!!socialIcon('instagram', 'Instagram', 'instagram')!!}
        {!!socialIcon('twitter', 'Twitter', 'twitter')!!}
        {!!socialIcon('facebook', 'Facebook', 'facebook')!!}
        {!!socialIcon('linkedin', 'LinkedIn', 'linkedin')!!}
        {!!socialIcon('youtube', 'YouTube', 'youtube')!!}
        {!!socialIcon('tiktok', 'TikTok', 'tiktok')!!}
        {!!socialIcon('github', 'GitHub', 'github')!!}
        {!!socialIcon('twitch', 'Twitch', 'twitch')!!}
        {!!socialIcon('discord', 'Discord', 'discord')!!}
        {!!socialIcon('snapchat', 'Snapchat', 'snapchat')!!}
        {!!socialIcon('reddit', 'Reddit', 'reddit')!!}
        {!!socialIcon('pinterest', 'Pinterest', 'pinterest')!!}
        {!!socialIcon('mastodon', 'Mastodon', 'mastodon')!!}
    </div>
</div>