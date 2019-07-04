@component('mail::message')
# Hi {{ $user->name }}

Your account is successfully setup and ready to use.

## Rules

- Please don't forget to put your tasks / bug fixing you will be working on everyday.
- Update the status of tasks / issues you are working on before 6:59 PM.
- Your tasks and issues report will be automatically emailed to respective Project Manager at 7:00 PM.
- After 6:59 PM you will not be able to update the tasks.

## Login Details

@component('mail::table')
| Name              | Email              | Password              |
|-:-:---------------|-:-:----------------|-:-:-------------------|
| {{ $user->name }} | {{ $user->email }} | {{ $user->password }} |
@endcomponent

@component('mail::button', ['url' => url('/login') , 'color' => 'green'])
Click here to Goto Portal
@endcomponent

Note: After login, Please change your password.

Let me know if i can help you will something.
<br>
<br>
Thanks,<br>
Jagroop Singh
@endcomponent
