@extends('layouts.app')

@section('content')
<div class="container">

	   <div class="row">
	    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
	     @if($summadata)
	<table cellpadding=2 cellspacing=2 width="70%">
	 <tr>
	 <td>
	 Кол. Страниц
	 </td>
 	 <th>
	 {{$summadata->pages_cnt}}
	 </th>
	 </tr>
	 <tr>
	 <td>
	 Всего загрузок
	 </td>
 	 <th>
	 {{$summadata->request_sum}}
	 </th>
	 </tr>
	 <tr>
	 <td>
	 Макс. загрузки
	 </td>
 	 <th>
	 {{$summadata->max_loaded}}
	 </th>
	 </tr>
	 <tr>
	 <td>
	 Средн. загрузки
	 </td>
 	 <th>
	 {{$summadata->avg_loaded}}
	 </th>
	 </tr>	 
         </table>	 
		 @endif	 
        </div>
		
   	    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
		
		{{ $collection->appends([])->links() }}
         <table class="table">
 	         <thead>
		        <tr>
			      <th>{!! $sorts["ltd"] !!}</th>
				   <th>Партнер</th>
				   <th>Кол. загрузок</th>
			       <th>{!! $sorts["avg_loaded"] !!}</th>
				   <th>{!! $sorts["max_loaded"] !!}</th>
		         </tr>
	        </thead>
			@foreach($collection as $col)
			<tr>
			<td><a href="{{route('mpstatistica.loaded_server',['id'=>$col->server_id])}}">{{$col->ltd}}</a></td>
			<td>{{$col->user_name}}</td>
			<td>{{$col->cnt}}</td>
			<td>{{$col->avg_loaded}}</td>
			<td>{{$col->max_loaded}}</td>
			</tr>
			@endforeach
	        <tbody>
            </tbody>
		</table>	
	    </div>

    </div>
</div>
@endsection