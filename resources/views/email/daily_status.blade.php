<p>Hello Sir, Good Evening ... here is the list of tasks team members worked on today.</p>
<br>
<h3>Tasks</h3>
<br>
<table style="{{ $css['table'] }}">
  <thead>
    <tr>
      <th style="{{ $css['th'] }}" width="10%">Project</th>
      <th style="{{ $css['th'] }}" width="10%">Developer</th>
      <th style="{{ $css['th'] }}" width="20%">Task</th>
      <th style="{{ $css['th'] }}" width="40%">Description</th>
      <th style="{{ $css['th'] }}" width="5%">Hours</th>
      <th style="{{ $css['th'] }}" width="10%">Status</th>
      <th style="{{ $css['th'] }}" width="5%">Progress</th>
    </tr>
  </thead>
    <tbody>
      @foreach($tasks as $task)
      <tr style="{{ ($task['task_status'] == 'done') ? $css['green_color'] : '' }}">
        <td style="{{ $css['td'] }}">{{ $task['project_name'] }}</td>
        <td style="{{ $css['td'] }}">{{ $task['assigned_to'] }}</td>
        <td style="{{ $css['td'] }}">{{ $task['task_name'] }}</td>
        <td style="{{ $css['td'] }}">{!! App\Helpers\Tracker::format($task['task_desc']) !!}</td>
        <td style="{{ $css['td'] }}">{{ $task['work_hours'] }}</td>
        <td style="{{ $css['td'] }}">{{ $task['task_status_formated'] }}</td>
        <td style="{{ $css['td'] }}">{{ $task['completion_precentage'] }}%</td>
      </tr>
      @endforeach
    </tbody>
</table>
<br>
<hr>
<h3>Projects Working and Billing Hours</h3>
<br>
<table style="{{ $css['table'] }}">
  <thead>
    <tr>
      <th style="{{ $css['th'] }}" width="10%">Project</th>
      <th style="{{ $css['th'] }}" width="10%">Status</th>
      <th style="{{ $css['th'] }}" width="20%">Total Billing Hours</th>
      <th style="{{ $css['th'] }}" width="20%">Today Billing Hours</th>
      <th style="{{ $css['th'] }}" width="20%">Total Work Hours</th>
      <th style="{{ $css['th'] }}" width="20%">Today Work Hours</th>
    </tr>
  </thead>
    <tbody>
      @foreach($stats as $stat)
      <tr style="{{ ($stat['work_hours_today'] <= 0) ? $css['red_color'] : '' }}">
        <td style="{{ $css['td'] }}">{{ $stat['name'] }}</td>
        <td style="{{ $css['td'] }}">{{ $stat['status'] }}</td>
        <td style="{{ $css['td'] }}">{{ $stat['billing_hours'] }}</td>
        <td style="{{ $css['td'] }}">{{ $stat['billing_hours_today'] }}</td>
        <td style="{{ $css['td'] }}">{{ $stat['work_hours'] }}</td>
        <td style="{{ $css['td'] }}">{{ $stat['work_hours_today'] }}</td>
      </tr>
      @endforeach
    </tbody>
</table>
<br>
Thanks,<br>
Jagroop Singh
