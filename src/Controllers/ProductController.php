<?php namespace Mplacegit\Statistica\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Route;
class ProductController extends Controller{

	public function index(Request $request){
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"day";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		if (!$number){
			$number=20;
		}
		$perPage=$number;
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
			$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Наши клики",'index'=>"myclicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Сумма выплат",'index'=>"client_summa","order"=>"",'url'=>""]
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		$sqlorder=$order;
		switch($order){
		case "day":
		$sqlorder="day";
		break;
		}
		$dopSql="";
		if($manager){
			$dopSql=" and manager_id=$manager ";
		#var_dump($manager)	;
		}
		
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allAll=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $allStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allYandex=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $yandexStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allTop=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $topStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allMy=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $myStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$params=[
		'from'=>$from,
		'to'=>$to,
		'manager'=>$manager,
		'number'=>$number,
		'direct'=>$direct,
        'order'=>$order,
		'header'=>$header,
		'allStat'=>$allStat,
		'allAll'=>$allAll,
		'allYandex'=>$allYandex,
		'yandexStat'=>$yandexStat,
		'allTop'=>$allTop,
		'topStat'=>$topStat,
		'allMy'=>$allMy,
		'myStat'=>$myStat
		];
		//var_dump($funkargs);
		return view('mp-statistica::product.index',$params);
	}
	public function pads(Request $request){
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"clicks";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		if (!$number){
			$number=20;
		}
		$perPage=$number;
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
	$header=[
            ['title'=>"Домен",'index'=>"server_name","order"=>"",'url'=>""],
			['title'=>"Имя",'index'=>"client_name","order"=>"",'url'=>""],
			['title'=>"Менеджер",'index'=>"manager_name","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Наши клики",'index'=>"myclicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Сумма выплат",'index'=>"client_summa","order"=>"",'url'=>""]
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		$sqlorder=$order;
		switch($order){
		case "clicks":
		$sqlorder="clicks";
		break;
		}
		$dopSql="";
		if($manager){
			$dopSql=" and manager_id=$manager ";
		#var_dump($manager)	;
		}
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allAll=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		group by server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $allStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allYandex =\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		group by server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $yandexStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allTop =\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		group by server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $topStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allMy =\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		group by server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $myStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
			
		$params=[
		'from'=>$from,
		'to'=>$to,
		'manager'=>$manager,
		'number'=>$number,
		'direct'=>$direct,
        'order'=>$order,
		'header'=>$header,
		'allStat'=>$allStat,
		'allAll'=>$allAll,
		'allYandex'=>$allYandex,
		'yandexStat'=>$yandexStat,
	    'allTop'=>$allTop,
		'topStat'=>$topStat,
		'allMy'=>$allMy,
		'myStat'=>$myStat
		];
		//var_dump($funkargs);
		return view('mp-statistica::product.pads.index',$params);
		
	}
public function partners(Request $request){
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"clicks";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		if (!$number){
			$number=20;
		}
		$perPage=$number;
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
	$header=[
            ['title'=>"Имя",'index'=>"client_name","order"=>"",'url'=>""],
			['title'=>"Менеджер",'index'=>"manager_name","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Наши клики",'index'=>"myclicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Сумма выплат",'index'=>"client_summa","order"=>"",'url'=>""]
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		$sqlorder=$order;
		switch($order){
		case "clicks":
		$sqlorder="clicks";
		break;
		}
		$dopSql="";
		if($manager){
			$dopSql=" and manager_id=$manager ";
		#var_dump($manager)	;
		}
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allAll=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		group by
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $allStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allYandex =\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select 
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		group by 
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $yandexStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allTop =\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select 
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		group by 
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $topStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allMy =\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name,		
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		group by server_id,
		server_name,
		client_id,
		client_name,
		manager_id,
		manager_name
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $myStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
			
		$params=[
		'from'=>$from,
		'to'=>$to,
		'manager'=>$manager,
		'number'=>$number,
		'direct'=>$direct,
        'order'=>$order,
		'header'=>$header,
		'allStat'=>$allStat,
		'allAll'=>$allAll,
		'allYandex'=>$allYandex,
		'yandexStat'=>$yandexStat,
	    'allTop'=>$allTop,
		'topStat'=>$topStat,
		'allMy'=>$allMy,
		'myStat'=>$myStat
		];
		return view('mp-statistica::product.partners.index',$params);
    }		
	
public function partner($id,Request $request){
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		
		$from=$request->input('from');
		$to=$request->input('to');
		$manager=$request->input('manager');
		$number=$request->input('number');
		$direct=$request->input('direct');
        $order=$request->input('order');
        $order=$order?$order:"clicks";
        $direct=$direct?$direct:"desc";
        $newdirect=($direct=="asc")?"desc":"asc";
		if (!$number){
			$number=20;
		}
		$perPage=$number;
		if(!($from||$to)){
            $from=date('Y-m-d',time()-3600*24*30);
            $to=date('Y-m-d');
        }
	$header=[
            ['title'=>"Дата",'index'=>"day","order"=>"",'url'=>""],
			['title'=>"Показы",'index'=>"views","order"=>"",'url'=>""],
			['title'=>"Клики",'index'=>"clicks","order"=>"",'url'=>""],
			['title'=>"Наши клики",'index'=>"myclicks","order"=>"",'url'=>""],
			['title'=>"Ctr",'index'=>"ctr","order"=>"",'url'=>""],
			['title'=>"Cpc",'index'=>"cpc","order"=>"",'url'=>""],
			['title'=>"Сумма",'index'=>"summa","order"=>"",'url'=>""],
			['title'=>"Сумма выплат",'index'=>"client_summa","order"=>"",'url'=>""]
        ];
		$baseurl=$request->path();
        $path=$request->except('order');
        $baseurl.="?1=1";
        foreach($path as $k=> $obj)
        {
            $baseurl.=("&".$k."=".$obj);
        }
        foreach($header as $k=>$filter)
        {
            $header[$k]['url']=$baseurl."&order=".$filter['index']."&direct=".$newdirect;
        }
		$sqlorder=$order;
		switch($order){
		case "clicks":
		$sqlorder="clicks";
		break;
		}
		$dopSql="";
		if($id){
			$dopSql=" and client_id=$id ";
			$client=\App\User::findOrFail($id);
			
		#var_dump($client->name)	;
		}else{
			abort(404);
		}
	$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		";
		#echo nl2br($sql); die();
		$allAll=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		#die();
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $allStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allYandex=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_yandex_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $yandexStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allTop=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_topadvert_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $topStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		
		$sql="select
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		";
		$allMy=\DB::connection("pgstatistic_new")->getPdo()->query($sql)->fetch(\PDO::FETCH_ASSOC);
		$sql="select day,
		sum(views) as views,
		sum(clicks) as clicks,
		sum(summa) as summa,
		sum(client_summa) as client_summa,
		sum(myclicks) as myclicks,
		CASE WHEN (sum(clicks)>0) then round(sum(client_summa)/sum(clicks)::numeric,4) else 0 end as cpc,
	    CASE WHEN (sum(views)>0) then round(sum(clicks)/sum(views)::numeric,4)*100 else 0 end as ctr
		from widget_statistic_my_day
		where day between '$from' and  '$to'
		$dopSql
		group by day
		order by $sqlorder $direct
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $myStat = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
		$params=[
		'from'=>$from,
		'to'=>$to,
		'manager'=>$manager,
		'number'=>$number,
		'direct'=>$direct,
        'order'=>$order,
		'header'=>$header,
		'allStat'=>$allStat,
		'allAll'=>$allAll,
		'allYandex'=>$allYandex,
		'yandexStat'=>$yandexStat,
		'allTop'=>$allTop,
		'topStat'=>$topStat,
		'allMy'=>$allMy,
		'myStat'=>$myStat,
		'partner'=>$client
		];
		#var_dump($params);
		return view('mp-statistica::product.partner.index',$params);	
		
		#var_dump("адренал");
    }	
}	