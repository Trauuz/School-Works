<section class="member-panel">
<div class="member-table-wrap">
<table class="member-table">
<thead>
<tr>
<th>Reference</th>
<th>Type</th>
<th>Status</th>
<th>Fee (PHP)</th>
<th>Payment (PHP)</th>
</tr>
</thead>
<tbody>@forelse($transactions as $transaction)<tr>
<td>
<a href="{{ route('transactions.show',$transaction) }}">{{ $transaction->reference }}</a>
</td>
<td>{{ ucfirst($transaction->type) }}</td>
<td>{{ $transaction->display_status }}</td>
<td>{{ number_format($transaction->fee/100,2) }}</td>
<td>@if($transaction->payment)<a href="{{ route('payments.show',$transaction->payment) }}">{{ number_format($transaction->payment->amount/100,2) }} · Receipt</a>
@else
 Unpaid
@endif
</td>
</tr>
@empty
<tr>
<td colspan="5">No transactions yet.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</section>
