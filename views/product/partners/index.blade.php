@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
		@include('statistic.video.top_menu')
	</div>
	<div class="row">
		<div class="col-xs-12">
		<div class="row">
				<div class="col-xs-12">
					<form class="form-inline" role="form" method="get">
						<div class="row">
							<div class="input-group col-xs-2 form-group">
								<span class="input-group-addon">С:</span>
								<input type="text" class="form-control" value="{{$from}}" name="from">
							</div>
							<div class="input-group col-xs-2 form-group">
								<span class="input-group-addon">По:</span>
								<input type="text" class="form-control" value="{{$to}}" name="to">
							</div>
							<div class="input-group col-xs-2 form-group">
								<div class="col-xs-12">
									<select name='manager' class="form-control">
										<option value="0">Все</option>
										@if (Auth::user()->hasRole('admin'))
											@foreach (\App\Role::whereIn('id', [3,4,5])->get() as $role)
												@foreach ($role->users as $user)
													<option @if ($manager==$user->id) selected @endif value="{{$user->id}}">{{$user->name}}</option>
												@endforeach
											@endforeach
										@else
											<option @if ($manager==\Auth::user()->id) selected @endif value="{{\Auth::user()->id}}">{{\Auth::user()->name}}</option>
										@endif
									</select>
								</div>
							</div>
							<div class="input-group col-xs-2 form-group">
								<select name="number" class="form-control">
									<option @if ($number==5) selected @endif value="5">5</option>
									<option @if ($number==10) selected @endif value="10">10</option>
									<option @if ($number==15) selected @endif value="15">15</option>
									<option @if ($number==20) selected @endif value="20">20</option>
									<option @if ($number==30) selected @endif value="30">30</option>
									<option @if ($number==50) selected @endif value="50">50</option>
									<option @if ($number==100) selected @endif value="100">100</option>
								</select>
							</div>
							<div class="col-xs-2 input-group form-group">
								<button type="submit" class="btn btn-primary">Применить</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<h4 class="text-center">Cтатистика по партнерам по товарным виджетам в период с {{date('d-m-Y',strtotime($from))}} по {{date('d-m-Y',strtotime($to))}}</h4>
			<div class="col-xs-12">
			{!! $allStat->appends(["from"=>$from, "order"=>$order, "direct"=>$direct, 'number'=>$number, "to"=>$to, "manager"=>$manager])->render() !!}<br>
				<div>
					<ul class="nav nav-tabs nav-justified cust-tabs">
						<li class="heading text-left active"><a href="#all_stat" data-toggle="tab">Всего</a></li>
						<li class="heading text-left"><a href="#yandex_stat" data-toggle="tab">Яндекс</a></li>
						<li class="heading text-left"><a href="#ta_stat" data-toggle="tab">Адверт</a></li>
						<li class="heading text-left"><a href="#my_stat" data-toggle="tab">Прямые</a></li>
					</ul>
			    <div class="tab-content">
					<div class="tab-pane active" id="all_stat" style="margin-top: 10px;">
					@include('mp-statistica::product.partners.all',['header'=>$header,'summaryStats'=>$allStat,'summary'=>$allAll])
					</div>	
					<div class="tab-pane" id="yandex_stat" style="margin-top: 10px;">
					@include('mp-statistica::product.partners.yandex',['header'=>$header,'summaryStats'=>$yandexStat,'summary'=>$allYandex])
					</div>		
					<div class="tab-pane" id="ta_stat" style="margin-top: 10px;">
					@include('mp-statistica::product.partners.topadvert',['header'=>$header,'summaryStats'=>$topStat,'summary'=>$allTop])
					</div>	
					<div class="tab-pane" id="my_stat" style="margin-top: 10px;">
					@include('mp-statistica::product.partners.my',['header'=>$header,'summaryStats'=>$myStat,'summary'=>$allMy])
					</div>						
			    </div>		
			
			
			    </div>
			
			</div>



			</div>	
    </div>	
</div>	
@endsection	
@push('cabinet_home')
	<link href="{{ asset('css/daterange/daterangepicker.css') }}" rel="stylesheet">
	<style>
		.table{
			text-align: center;
		}
		.table > thead > tr > th, .table > thead > tr > td, .table > tbody > tr > th, .table > tbody > tr > td, .table > tfoot > tr > th, .table > tfoot > tr > td{
			vertical-align: middle;
			border: 1px solid #ababab;
		}
		.body_sum{
			font-weight: bolder;
		}
		.celi_pok{
			display: inline-block!important;
			width: 10px;
			height: 10px;
		}
		.rur{
		font-style: normal;
		}
		.right_pok{
		display: inline-block;
		width: 200px;
		}
		.table_href{
		color: inherit;
		}
		.cust-tabs .active a{
			font-weight: 600;
			color: #3b4371!important;
		}
		.cust-tabs li a{
			color: #3b4371;
			letter-spacing: 1.1px;
		}
	</style>
@endpush
@push('cabinet_home_js')
	<script src="{{ asset('js/daterange/moment.js') }}"></script>
	<script src="{{ asset('js/daterange/daterangepicker.js') }}"></script>
	<script>
		$(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
	<script>	
$(function() {
	if(window.location.hash) {
         $('a[href="' + window.location.hash + '"]').tab('show');
    }
    $('input[name="from"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
	$('input[name="to"]').daterangepicker({
	singleDatePicker: true,
        showDropdowns: true,
		"locale": {
        "format": "YYYY-MM-DD",
        "separator": " - ",
        "applyLabel": "Применить",
        "cancelLabel": "Отмена",
        "fromLabel": "От",
        "toLabel": "До",
        "customRangeLabel": "Свой",
        "daysOfWeek": [
            "Вс",
            "Пн",
            "Вт",
            "Ср",
            "Чт",
            "Пт",
            "Сб"
        ],
        "monthNames": [
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        ],
        "firstDay": 1
    }
	});
});	
</script>
@endpush
	