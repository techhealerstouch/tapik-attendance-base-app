
<script src="{{ asset('assets/external-dependencies/fontawesome.js') }}" crossorigin="anonymous"></script>
<style>
@media only screen and (max-width: 1500px) {
  .pre-side{display:none!important;}
  .pre-left{width:100%!important;}
  .pre-bottom{display:block!important;}
}

@media only screen and (min-width: 1501px) {
  .pre-left{width:70%!important;}
  .pre-right{width:30%!important;}
  .pre-bottom{display:none!important;}
}
</style>
<style>.delete{position:relative; color:transparent; background-color:tomato; border-radius:5px; left:5px; padding:5px 12px; cursor: pointer;}.delete:hover{color:transparent;background-color:#f13d1d;}html,body{max-width:100%;overflow-x:hidden;}</style>
<div class="d-flex justify-content-center">
    <h6 style="font-size: 20px" class='form-label text-gray-400'>Social media icons</h6>
</div>
<section style="margin-left:-15px;margin-right:-15px;" class='text-gray-400'>
                            <div class="card-body p-0 p-md-3">

                                <div class="form-group col-lg-8">
                            
                                        @php
                                        function iconLink($icon){
                                        $iconLink = DB::table('links')
                                        ->where('user_id', Auth::id())
                                        ->where('title', $icon)
                                        ->where('button_id', 94)
                                        ->value('link');
                                          if (is_null($iconLink)){
                                               return false;
                                          } else {
                                                return $iconLink;}}
                                        function searchIcon($icon)
                                    {$iconId = DB::table('links')
                                        ->where('user_id', Auth::id())
                                        ->where('title', $icon)
                                        ->where('button_id', 94)
                                        ->value('id');
                                    if(is_null($iconId)){return false;}else{return $iconId;}}
                                        function iconclicks($icon){
                                        $iconClicks = searchIcon($icon);
                                        $iconClicks = DB::table('links')->where('id', $iconClicks)->value('click_number');
                                          if (is_null($iconClicks)){return 0;}
                                          else {return $iconClicks;}}
                            
                                          function icon($name, $label) {
                                              echo '<div style="padding-left: 10px; padding-right: 10px" class="mb-3">
                                                      <label class="form-label">'.$label.'</label>
                                                      <div class="input-group">
                                                        <span class="input-group-text"><i class="fab fa-'.$name.'"></i></span>
                                                        <input type="url" class="form-control" name="'.$name.'" value="'.iconLink($name).'" />
                                                        '.(searchIcon($name) != NULL ? '<a href="'.route("deleteLink", searchIcon($name)).'" class="btn btn-danger"><i class="bi bi-trash-fill"></i></a>' : '').'
                                                      </div>
                                                    </div>';
                                            }
                                        @endphp
                                    <style>input{border-top-right-radius: 0.25rem!important; border-bottom-right-radius: 0.25rem!important;}</style>
                            
                            
                                {!!icon('mastodon', 'Mastodon')!!}
                            
                                {!!icon('instagram', 'Instagram')!!}
                            
                                {!!icon('twitter', 'Twitter')!!}
                            
                                {!!icon('facebook', 'Facebook')!!}
                            
                                {!!icon('github', 'GitHub')!!}
                            
                                {!!icon('twitch', 'Twitch')!!}
                            
                                {!!icon('linkedin', 'LinkedIn')!!}
                            
                                {!!icon('tiktok', 'TikTok')!!}
                            
                                {!!icon('discord', 'Discord')!!}
                            
                                {!!icon('youtube', 'YouTube')!!}
                            
                                {!!icon('snapchat', 'Snapchat')!!}
                            
                                {!!icon('reddit', 'Reddit')!!}
                            
                                {!!icon('pinterest', 'Pinterest')!!}
                            
                                {{-- {!!icon('telegram', 'Telegram')!!}
                            
                                {!!icon('whatsapp', 'WhatsApp')!!} --}}
                            

                            
                            
                            </div>
                            </section>