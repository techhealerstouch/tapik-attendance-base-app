<!-- Step 5 - Add Links -->
<?php use App\Models\Button; ?>

<style>
.step-container {
    max-width: 100%;
    margin: 0 auto;
    padding: 0 15px;
}

.step-header {
    text-align: center;
    margin-bottom: 25px;
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

.add-link-btn {
    width: 100%;
    max-width: 300px;
    margin: 0 auto 20px;
    display: block;
    padding: 14px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.add-link-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.link-form-container {
    display: none;
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    border: 2px solid #e9ecef;
}

.link-form-container.active {
    display: block;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
}

.form-select-link {
    width: 100%;
    padding: 12px 40px 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 15px;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
}

.form-select-link:focus {
    outline: none;
    border-color: #db5363;
    box-shadow: 0 0 0 3px rgba(219, 83, 99, 0.1);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-save {
    flex: 1;
    padding: 12px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #218838;
}

.btn-cancel {
    flex: 1;
    padding: 12px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cancel:hover {
    background: #c82333;
}

.links-section-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    text-align: center;
}

.link-card {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.link-card:hover {
    border-color: #db5363;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.link-card-content {
    display: flex;
    align-items: center;
    padding: 12px;
    gap: 10px;
}

.drag-handle {
    flex-shrink: 0;
    cursor: grab;
    color: #adb5bd;
    padding: 8px;
}

.drag-handle:active {
    cursor: grabbing;
}

.drag-handle svg {
    width: 20px;
    height: 20px;
    transform: rotate(90deg);
}

.link-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.link-icon img {
    max-width: 20px;
    max-height: 20px;
}

.link-details {
    flex: 1;
    min-width: 0;
    overflow: hidden;
}

.link-title {
    font-weight: 600;
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.link-url {
    font-size: 12px;
    color: #6c757d;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-decoration: none;
}

.link-url:hover {
    color: #db5363;
}

.link-stats {
    font-size: 12px;
    color: #6c757d;
    margin-top: 4px;
}

.link-actions {
    flex-shrink: 0;
    display: flex;
    gap: 5px;
}

.link-action-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: #6c757d;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.link-action-btn:hover {
    background: #dc3545;
    color: white;
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

@media (max-width: 767px) {
    .step-title {
        font-size: 20px;
    }
    
    .link-card-content {
        flex-wrap: wrap;
    }
    
    .link-details {
        flex-basis: 100%;
        order: 3;
        margin-top: 8px;
    }
    
    .link-actions {
        margin-left: auto;
    }
    
    .drag-handle {
        padding: 5px;
    }
    
    .link-icon {
        width: 36px;
        height: 36px;
    }
    
    .link-action-btn {
        width: 32px;
        height: 32px;
    }
}

@media (min-width: 768px) {
    .step-container {
        max-width: 800px;
    }
}
</style>

<div class="step-container">
    <div class="step-header">
        <h6 class="step-title">Add Your Links</h6>
        <p class="step-subtitle">Create buttons and links for your profile</p>
    </div>

    <button type="button" class="add-link-btn" id="addLinkButton">
        ➕ Add New Link
    </button>

    <div class="link-form-container" id="addLinkSection">
        <form id="my-form" method="post">
            @csrf
            <input type='hidden' name='linkid' value="{{ $LinkID }}" />
            
            <div class="form-section-title">Select Link Type</div>
            <select id="linkTypeSelect" class="form-select-link" name="linktype_id">
                <option value="" disabled selected>Choose a link type</option>
                @php
                $custom_order = [1, 2, 8, 6, 7, 3, 4, 5];
                $sorted = $LinkTypes->sortBy(function ($item) use ($custom_order) {
                    return array_search($item['id'], $custom_order);
                });
                @endphp
                @foreach ($sorted as $lt)
                @php
                $title = __('messages.block.title.'.$lt['typename']);
                @endphp
                <option value="{{$lt['id']}}" data-typename="{{$title}}">{{$title}}</option>
                @endforeach
            </select>

            <div id='link_params'></div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" id="cancelLinkBtn">Cancel</button>
                <button type="submit" class="btn-save">Save Link</button>
            </div>
        </form>
    </div>

    <h6 class="links-section-title">Your Links</h6>

    <div id="links-container">
        @if($links->total() == 0)
        <div class="empty-state">
            <div class="empty-state-icon">🔗</div>
            <p class="empty-state-text">No links added yet. Click "Add New Link" to get started!</p>
        </div>
        @else
        @foreach($links as $link)
        @php 
        $button = Button::find($link->button_id); 
        if(isset($button->name)){
            $buttonName = $button->name;
        } else {
            $buttonName = 0;
        }
        @endphp
        @if($button->name !== 'icon')
        <div class='link-card' data-id="{{$link->id}}">
            <div class="link-card-content">
                <div class="drag-handle">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M1 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V4zM1 9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V9z" />
                    </svg>
                </div>

                <div class="link-icon">
                    @if($button->name == "custom_website")
                    <img alt="icon" src="@if(file_exists(base_path("assets/favicon/icons/").localIcon($link->id))){{url('assets/favicon/icons/'.localIcon($link->id))}}@else{{getFavIcon($link->id)}}@endif" onerror="this.src='{{asset('assets/linkstack/icons/website.svg')}}'">
                    @elseif($button->name == "space")
                    <i class='bi bi-distribute-vertical'></i>
                    @elseif($button->name == "heading")
                    <i class='bi bi-card-heading'></i>
                    @elseif($button->name == "text")
                    <i class='bi bi-fonts'></i>
                    @else
                    <img alt="icon" src="{{ asset('assets/linkstack/icons/'.$buttonName) }}.svg">
                    @endif
                </div>

                <div class="link-details">
                    <div class="link-title">{{strip_tags($link->title,'')}}</div>
                    @if(!empty($link->link) and $button->name != "vcard")
                    <a href="{{ $link->link}}" target="_blank" class="link-url">{{Str::limit($link->link, 40)}}</a>
                    @elseif($button->name == "vcard")
                    <a href="{{ url('vcard/'.$link->id) }}" target="_blank" class="link-url">Download vCard</a>
                    @endif
                    @if(!empty($link->link))
                    <div class="link-stats">📊 {{ $link->click_number }} clicks</div>
                    @endif
                </div>

                <div class="link-actions">
                    <button class="link-action-btn delete-link" data-link-id="{{ $link->id }}" data-title="{{ addslashes($link->title) }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M20.708 6.23975H3.75" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif
        @endforeach
        @endif
    </div>
</div>

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/Sortable.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Extract the code from the URL
    const url = new URL(window.location.href);
    const code = url.pathname.split('/').pop();
    
    // Set the form action dynamically (keeping original functionality)
    document.getElementById('my-form').action = `/setup-profile/${code}/save-link`;

    // Toggle add link form
    document.getElementById('addLinkButton').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any default action
        const section = document.getElementById('addLinkSection');
        section.classList.toggle('active');
    });

    document.getElementById('cancelLinkBtn').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent any default action
        document.getElementById('addLinkSection').classList.remove('active');
    });

    // Link type selection
    $('#linkTypeSelect').change(function() {
        var typeId = $(this).val();
        var baseURL = "{{ url('') }}";
        $("#link_params").html('<div style="text-align:center;padding:20px;"><div style="border:3px solid #f3f3f3;border-top:3px solid #db5363;border-radius:50%;width:40px;height:40px;animation:spin 1s linear infinite;margin:0 auto;"></div></div>').load(baseURL + `/linkparamform_part/${typeId}/{{ $LinkID }}`);
    });

    // Form submission (keeping original functionality)
    document.getElementById('my-form').addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append('currentStep', 4);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Delete link
    $(document).on('click', '.delete-link', function(event) {
        event.preventDefault();
        var linkId = $(this).data('link-id');
        var title = $(this).data('title');
        
        if (confirm("Delete '" + title + "'?")) {
            $.ajax({
                type: 'GET',
                url: '{{ url("/removeLink") }}/' + linkId,
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
        }
    });

    // Make links sortable
    var linksContainer = document.getElementById('links-container');
    if (linksContainer && linksContainer.children.length > 0) {
        new Sortable(linksContainer, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost'
        });
    }
});
</script>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.sortable-ghost {
    opacity: 0.5;
    background: #f8f9fa;
}
</style>