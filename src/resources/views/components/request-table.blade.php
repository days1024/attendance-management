<div class="show-table">
 <table class="show-table__inner">
    <tr class="show-table__row">
        <th class="show-table__header">状態</th>
        <th class="show-table__header">名前</th>
        <th class="show-table__header">対象日時</th>
        <th class="show-table__header">申請理由</th>
        <th class="show-table__header">申請日時</th>
        <th class="show-table__header">詳細</th>
    </tr>

    @foreach($requests as $request)
    <tr class="show-table__row">
        <td class="show-table__item">
            {{ $request->status_label }}
        </td>

        <td class="show-table__item">
            {{ $request->attendance->user->name }}
        </td>

        <td class="show-table__item">
            {{ $request->attendance->work_date->format('Y年n月j日') }}
        </td>

        <td class="show-table__item">
            {{ $request->reason }}
        </td>

        <td class="show-table__item">
            {{ $request->created_at->format('Y年n月j日') }}
        </td>

        <td class="show-table__item">
             <a href="{{ route($route, $request->{$idField}) }}">詳細</a>
        </td>
    </tr>
    @endforeach 
 </table>
</div>