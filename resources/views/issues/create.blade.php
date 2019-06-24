<div class="">
    {{ Session::get('message') }}
</div>

<div class="container">

    {!! Form::open(['route' => 'issues.store']) !!}

    @form_maker_table("issues")

    {!! Form::submit('Save') !!}

    {!! Form::close() !!}

</div>