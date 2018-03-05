<?php namespace Mplacegit\Statistica\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Route;
class LoadedController extends Controller{

	public function index(Request $request){
		$funkargs=$this->config["wparams"]=Route::current()->parameters;
		$funk = Route::currentRouteName();
		$dire=$request->input("order");
		$sort=$request->input("sort");	
		if(!$dire)
	    $dire="desc";	
        if(!$sort)
	    $sort="avg_loaded";	
	    if($dire=="asc"){
		$ride="desc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes">';
	    }else{
		$ride="asc";
		$glip='<span class="glyphicon glyphicon-sort-by-attributes-alt">';
	    }
		$funkargs["sort"]="max_loaded";
	    $sorts["max_loaded"]='<a href="'.route($funk,$funkargs).'" title ="Максимальное время загрузки страницы">Макс.загрузка </a>';		
		$funkargs["sort"]="avg_loaded";
	    $sorts["avg_loaded"]='<a href="'.route($funk,$funkargs).'" title ="Среднее время загрузки страницы">Ср.загрузка </a>';		
		switch($sort){
		case "max_loaded":
		    $funkargs["sort"]="max_loaded";
			$funkargs["order"]=$ride;
	        $sorts["max_loaded"]='<a href="'.route($funk,$funkargs).'" title ="Максимальное время загрузки страницы">Макс.загрузка '.$glip.'</a>';		
			$order="max_loaded"; 
		break;
		case "avg_loaded":
		    $funkargs["sort"]="avg_loaded";
			$funkargs["order"]=$ride;
	        $sorts["avg_loaded"]='<a href="'.route($funk,$funkargs).'" title ="Среднее время загрузки страницы">Ср.загрузка '.$glip.'</a>';		
			$order="avg_loaded"; 
		break;
		}
		$sql="select * from widget_requests_loaded where day =NOW()::date
		order by $order $dire
		";
		$vza=\DB::connection("pgstatistic_new")->select($sql);
		$perPage=10;
		$found=count($vza);
        $page = $request->input('page', 1); // Get the current page or default to 1, this is what you miss!
        $offset = ($page * $perPage) - $perPage;
        $da = new LengthAwarePaginator(array_slice($vza, $offset, $perPage, true), $found, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

		return view('mp-statistica::index',['collection'=>$da,'sorts'=>$sorts]);
	}
	public function server($id,Request $request){
		var_dump($id);
	}	
}