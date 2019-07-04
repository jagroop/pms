<div class="">
    {{ Session::get('message') }}
</div>

<div class="container">

    {!! Form::model($issue, ['route' => ['issues.update', $issue->id], 'method' => 'patch']) !!}

    @form_maker_object($issue, FormMaker::getTableColumns('issues'))

    {!! Form::submit('Update') !!}

    {!! Form::close() !!}
</div>
