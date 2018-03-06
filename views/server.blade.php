@extends('layouts.app')

@section('content')
<div class="container">

	   <div class="row">
	    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
	     <div class="panel-left-padding">
		 <div>
		 <a href="{{route('mpstatistica.loaded')}}">Все площадки</a>
		 </div>
        </div>
		</div>
   	    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
		
		{{ $collection->appends([])->links() }}
         <table class="table">
 	         <thead>
		        <tr>
			       <th style="width:21%">Запрос</th>
				   <th>{!! $sorts["last_loaded"] !!}</th>
				   <th>Кол. загрузок</th>
			       <th>{!! $sorts["avg_loaded"] !!}</th>
				   <th>{!! $sorts["max_loaded"] !!}</th>
				   <th>Дата макс.</th>
		         </tr>
	        </thead>
			@foreach($collection as $col)
			<tr>
			<td><a href="{{$col->url}}" target="_blank">{{$col->request}}</a></td>
			<td>{{$col->last_loaded}}</td>
			<td>{{$col->cnt}}</td>
			<td>{{$col->avg_loaded}}</td>
			<td>{{$col->max_loaded}}</td>
			<td>{{$col->datetime_max_last}}</td>
			</tr>
			@endforeach
	        <tbody>
            </tbody>
		</table>	
	    </div>

    </div>
</div>
@endsection