<table class="table table-hover table-bordered" style="margin-top: 10px">
	<thead>
	    <tr>
		@foreach($header as $k=>$row)
			<td>
		      @if($row['index'])<a class="table_href" href="/{{$row['url']}}#my_stat">{{$row['title']}}</a>@else {{$row['title']}} @endif
			</td>
	    @endforeach
		</tr>
    </thead>
		<tbody>
	
		    <tr style="background: #000; color: #fff;">
			<td>Всего</td>
		    <td>{{$summary["views"]}}</td>
			<td>{{$summary["clicks"]}}</td>
			<td>{{$summary["myclicks"]}}</td>
			<td>{{$summary["ctr"]}}</td>
			<td>{{$summary["cpc"]}}</td>
			<td>{{$summary["summa"]}}</td>
			<td>{{$summary["client_summa"]}}</td>
			</tr>
	
		    @foreach ($summaryStats as $summaryStat)
		    <tr>
			<td>{{$summaryStat->day}}</td>
			<td>{{$summaryStat->views}}</td>
			<td>{{$summaryStat->clicks}}</td>
			<td>{{$summaryStat->myclicks}}</td>
			<td>{{$summaryStat->ctr}}</td>
			<td>{{$summaryStat->cpc}}</td>
			<td>{{$summaryStat->summa}}</td>
			<td>{{$summaryStat->client_summa}}</td>
		   </tr>
		   @endforeach
	</tbody>		
</table>	