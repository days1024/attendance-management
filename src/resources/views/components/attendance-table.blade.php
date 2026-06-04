

<div class="table-wrap">
<table class="table-common">

<tr class="table-common__row">
    @foreach($headers as $header)
        <th class="table-common__header">
            {{ $header }}
        </th>
    @endforeach
</tr>

{{ $slot }}

</table>
</div>




