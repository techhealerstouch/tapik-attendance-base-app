<!-- Short Bio -->

<style>
        .description-parent * {
            margin-bottom: 1em;
            font-size: 10px !important;
        }
        .description-parent {
            padding-bottom: 30px;
            margin-top: 10px;
        }
        .description-parent p {
                
            line-height: 17px;
            overflow-wrap: break-word;
            white-space: pre-wrap; /* Ensures text wraps onto a new line */
            word-break: break-word; /* Ensures long words break onto a new line */
        }
    </style>
    
    <center>
        <div class="fadein description-parent dynamic-contrast">
            
                @if(env('ALLOW_USER_HTML') === true)
                    {!! $info->littlelink_description !!}
                @else
                    {{ $info->littlelink_description }}
                @endif

        </div>
    </center>
    