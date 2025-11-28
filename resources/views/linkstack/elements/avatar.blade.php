<!-- Your Image Here -->

@if(file_exists(base_path(findAvatar($userinfo->id))))

        <img alt="avatar" id="avatar" class="rounded-avatar fadein" src="{{ url(findAvatar($userinfo->id)) }}" style="width: 250px; height: 250px; object-fit: fill;">

        @elseif(file_exists(base_path("assets/linkstack/images/").findFile('avatar')))

        <img alt="avatar" id="avatar" class="fadein" src="{{ url("assets/linkstack/images/")."/".findFile('avatar') }}" style="width: 250px; height: 250px; object-fit: contain;">

        @else

        <img alt="avatar" id="avatar" class="fadein" src="{{ asset('assets/linkstack/images/logo.svg') }}" style="height: 250px; width:auto;min-width:250px;object-fit: contain;">

        @endif