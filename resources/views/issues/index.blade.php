<div class="container">

    <div class="">
        {{ Session::get('message') }}
    </div>

    <div class="row">
        <div class="pull-right">
            {!! Form::open(['route' => 'issues.search']) !!}
            <input class="form-control form-inline pull-right" name="search" placeholder="Search">
            {!! Form::close() !!}
        </div>
        <h1 class="pull-left">Issues</h1>
        <a class="btn btn-primary pull-right" style="margin-top: 25px" href="{!! route('issues.create') !!}">Add New</a>
    </div>

    <div class="row">
        @if($issues->isEmpty())
            <div class="well text-center">No issues found.</div>
        @else
            <table class="table">
                <thead>
                    <th>Name</th>
                    <th width="50px">Action</th>
                </thead>
                <tbody>
                @foreach($issues as $issue)
                    <tr>
                        <td>
                            <a href="{!! route('issues.edit', [$issue->id]) !!}">{{ $issue->name }}</a>
                        </td>
                        <td>
                            <a href="{!! route('issues.edit', [$issue->id]) !!}"><i class="fa fa-pencil"></i> Edit</a>
                            <form method="post" action="{!! route('issues.destroy', [$issue->id]) !!}">
                                {!! csrf_field() !!}
                                {!! method_field('DELETE') !!}
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this issue?')"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="row">
                {!! $issues; !!}
            </div>
        @endif
    </div>
</div>