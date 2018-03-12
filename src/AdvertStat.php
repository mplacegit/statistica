<?php
namespace Mplacegit\Statistica;
class AdvertStat
{
	private $date;
	private $clids=[];
	private $widgets=[];
	private $pids=[];
	private $wids=[];
	private $Servers=[];
	private $ManagerProcent=[];
	public function setDate($date){
		$this->date=$date;
	}
	public function  getServerName($id_widget){
		if(!isset($this->Servers[$id_widget])){
		$this->serverSth->execute([$id_widget]);
		$d=$this->serverSth->fetch(\PDO::FETCH_ASSOC);
		$this->Servers[$id_widget]=$d;
		}
		return $this->Servers[$id_widget];
	}
	private function getClientCommission($servd,$flag,$summa,&$clicks){
		$value=0;
		if($servd["user_id"]==360){
			$servd["dop_status"]=3;
		}
		switch($flag){
			case 2:
			//2 коммисия топадверта
                if($servd["user_id"]==56){# Ильясов Вадим
					$value=$clicks*2.5;
				}else{					
			    if(isset($this->defaultProductCommissia[$servd["user_id"]][2])){
				    $value=$this->defaultProductCommissia[$servd["user_id"]][2]*$summa;
			    }else{
				    $value=$this->topadvertCommissia->value*$summa;
			    }
				
				}
			break;
			case 1:
			//1 коммисия яндекса
			    if(isset($this->defaultProductCommissia[$servd["user_id"]][1])){
				    $value=$this->defaultProductCommissia[$servd["user_id"]][1]*$summa;
			    }else{
				    $value=$this->yandexCommissia->value*$summa;
			    }
				
				
				print_r([$servd,$flag,$summa,$value]);
			break;
			default:
			return null;
			break;
			
		}
		if(!$value) return 0;
		switch($servd["dop_status"]){
			#case 1:
			#break
			case 2:
			$value*=0.65;
			break;
			case 3:
			$value*=0.5;
			break;						
		}
        $value=round($value,4);
		return $value;
		#print_r([$servd,$flag,$summa,$value]);
	}
    private function getRemoteTopAdvert(){
    $url="http://service.topadvert.ru/stat_external_pin?feed_id=13910&access_key=3d00ab379ea4b003e322fc3a5e7d4591&date_min=".$this->date."&date_max=".$this->date."";   
//var_dump($url); die();
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $result = curl_exec($ch); 
    curl_close($ch); 
    $pinz=[];
    $xml = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
    $json = json_encode($xml);
    if(!$json){
	    echo "no topadvert json\n"; return;
        }
    $array = json_decode($json,TRUE);
    if(!$array)
	    {
		echo  "no topadvert json array\n"; return;
        } 
	foreach($array["item"] as $item){
	   if(is_array($item["pin"])){
		       
			   continue;
	   }
	   $item["pin"]=trim($item["pin"]);
	   if($item["pin"]=="572ba8d726ed1_1"){
			$item["pin"]=955;
				   
		}
		if(!isset($this->pids[$item["pin"]])){
			print $item["pin"]." голыдьба\n"; continue;
		}
		#if(preg_match('/^_/')){
			
		#}
		$widId=$this->pids[$item["pin"]];
		if(!isset($this->widgets[$widId]["summaAdv"]))
	      $this->widgets[$widId]["summaAdv"]=0;
	   if(!isset($this->widgets[$widId]["clicksAdv"]))
		  $this->widgets[$widId]["clicksAdv"]=0;
	    $this->widgets[$widId]["summaAdv"]+=$item["money"];
	    $this->widgets[$widId]["clicksAdv"]++;
		
	    
	}  
		
		
		
    }
	
	public function getTopadvertClids(){
		$this->getRemoteTopAdvert();
	$pdo = \DB::connection("pgstatistic")->getPdo();	
		$sql="select 
id_server,
id_widget,
count(*) as cnt 
 from  advert_stat_clicks
where new_driv=3 and date= '".$this->date."'
group by id_server,
id_widget";
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		$this->widgets[$click["id_widget"]]["myClicksAdv"]=$click["cnt"];
	}
	
	$sql="select  id_widget, id_server,count(*) as cnt
	from topadvert_requests where day= '".$this->date."' and found>0
	group by id_widget, id_server
	";
	#echo $sql; die();
	$sql22="
	select id_widget,id_server,count(*) as cnt 
from (
select  wid as id_widget, pad as id_server,hash,count(*) as cnt
        from topadvert_views where day= '".$this->date."'
        group by wid ,pad,hash
) t
 group by id_widget,id_server
	";
	$clicks=\DB::connection("pgstatistic_new")->select($sql);
	#$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
			$this->widgets[$click->id_widget]["viewsAdv"]=$click->cnt;
		}	
		
	}	
	public function getMyreklClids(){
	$pdo = \DB::connection("pgstatistic")->getPdo();	
	$sql="select wid as id_widget, pad as id_server
	,sum(price) as price
	,sum(client_price) as client_price
	,count(*) as cnt 
	from myadvert_clicks where day= '".$this->date."' and status =1
        group by wid ,pad
        ";
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		$this->widgets[$click["id_widget"]]["myClicksR"]=$click["cnt"];
		$this->widgets[$click["id_widget"]]["clicksR"]=$click["cnt"];
		$this->widgets[$click["id_widget"]]["summaR"]=$click["price"];
		$this->widgets[$click["id_widget"]]["clientSummaR"]=$click["client_price"];
	}
	$sql="select  id_widget, id_server,count(*) as cnt
	from my_requests where day= '".$this->date."' and found>0
	group by id_widget, id_server
	";
	$sql22="
	select id_widget,id_server,count(*) as cnt 
from (
select  wid as id_widget, pad as id_server,hash,count(*) as cnt
        from myadvert_views where day= '".$this->date."'
        group by wid ,pad,hash
) t
 group by id_widget,id_server
	";
	$clicks=\DB::connection("pgstatistic_new")->select($sql);
		foreach($clicks as $click){
			$this->widgets[$click->id_widget]["viewsR"]=$click->cnt;
		}	

		
		
	}
	public function getYandexClids(){
		$pdo = \DB::connection("pgstatistic")->getPdo();
		$pdo_new = \DB::connection("pgstatistic_new")->getPdo();
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_clicks where date= '".$this->date."'
		and driver=2
        group by id_widget,id_server,driver,clid 
        ";
		#var_dump($sql);
		$clicks = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);		
		foreach($clicks as $click){
		if($click["clid"]){ 
		#print $click["id_server"];
		#print "\n";
		$this->widgets[$click["id_widget"]]["myClicksYa"]=$click["cnt"];
		$this->clids[$click["clid"]][$click["id_widget"]]=$click["cnt"];
		}
		}
		$sql="select id_widget,id_server,driver,clid,count(*) as cnt from advert_stat_pages where day= '".$this->date."'
		and driver=2
        group by id_widget,id_server,driver,clid 
        ";
		$sql="select  id_widget, id_server,count(*) as cnt
	    from yandex_requests where day= '".$this->date."' and found>0
	    group by id_widget, id_server
	";
		$clicks= $pdo_new->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($clicks as $click){
		#if($click["clid"]){ 
		$this->widgets[$click["id_widget"]]["showYa"]=$click["cnt"];
			#}
		}
      
		$sql="select * from advert_stat_yandexclicks where day= '".$this->date."'";
		$states= $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		foreach($states as $stat){
			if(isset($this->clids[$stat["clid"]])){
			$summa =array_sum($this->clids[$stat["clid"]]);
			if($summa){
             
			    foreach($this->clids[$stat["clid"]] as $key => $val){
					$x=$val*100/$summa;
					$finalSumma=$stat["summa"]/100*$x;
					$finaClicks=round($stat["clicks"]/100*$x);
					
					if(!isset($this->wids[$key])){
					var_dump(["нечем порадовать",$key]);
					continue;
					}
					$this->widgets[$key]["summaYa"]=$finalSumma;
   				    $this->widgets[$key]["clicksYa"]=$finaClicks;
					print $key." >><< >><< ".$stat["clid"]." $finaClicks ".$finalSumma." \n";
                   }
			    }
			  }
		}	
	}
	public function Calculate(){
		//комиссия по топадверту
		$this->topadvertCommissia=\DB::table('сommission_groups')->where('commissiongroupid', 'p-000002')->first();
		$this->yandexCommissia=\DB::table('сommission_groups')->where('commissiongroupid', 'p-000001')->first();
		$sql="select * from product_default_on_users";
		$defTmp =\DB::connection()->select($sql);
		foreach($defTmp as $t)
			$this->defaultProductCommissia[$t->user_id][$t->driver]=$t->commission;
		
		
		$pdoss =\DB::connection()->getPdo();
		
		
		$sql="select t1.id
,t1.user_id
,t1.domain
,w.ltd
,u.name as user_name
,up.manager
,up.dop_status
,um.name as manager_name
from (partner_pads t1
inner join (users u
left join (user_profiles up
inner join users um on um.id=up.manager
) on up.user_id=u.id
) on u.id=t1.user_id
)
inner join widgets w on w.pad=t1.id
and w.ltd is not null and w.ltd<>''
 where w.id=?";
		$this->serverSth=$pdoss->prepare($sql);

$sql="select mu.user_id,
cg.*
 from manager_commissions mu
left join 
          сommission_groups
 cg on cg.commissiongroupid=mu.commissiongroupid
 ";
$commisionTmp=\DB::connection()->select($sql);
foreach($commisionTmp as $o){
$ManagerProcent[$o->user_id]	=floatval($o->value);
}
#



		
		$pds=\App\WidgetEditor::All();
		foreach($pds as $p){
			$this->pids[$p->id]=$p->wid_id;
			$this->wids[$p->wid_id]=$p->id;

		}
	$this->getYandexClids();
	$this->getMyreklClids();
	$this->getTopadvertClids();
    $pgpdonew = \DB::connection("pgstatistic_new")->getPdo();
	$sql="select 
day,id_server,id_widget
,count(*) as cnt
 from advert_requests
where day='".$this->date."' and found >0
group by day,id_server,id_widget
";
	$sql11="
	select id_widget,id_server,count(*) as cnt 
from (
select 
day,id_server,id_widget,hash
,count(*) as cnt
 from advert_requests
where day='".$this->date."' and found >0
group by day,id_server,id_widget,hash
) t
 group by id_widget,id_server
	";
#echo $sql; die();
$sou=\DB::connection("pgstatistic_new")->select($sql);
foreach($sou as $click){
	$this->widgets[$click->id_widget]["YunShow"]=$click->cnt;
	
}

    $sql="
    update  widget_statistic_day
    set views=?,
    clicks=?,
    summa=?,
	client_summa=?,
	manager_summa=?,
    myclicks=?,
	server_name=?,
	client_id=?,
	client_name=?,
	manager_id=?,
	manager_name=?
    WHERE day=? and widget_id=? and server_id=?
  ";
  $this->updateIntSumNew= $pgpdonew->prepare($sql);	
    $sql="
    insert into widget_statistic_day(
    day,
    widget_id,
    server_id,
    views,
    clicks,
    summa,
	client_summa,
	manager_summa,
    myclicks,
	server_name,
	client_id,
	client_name,
	manager_id,
	manager_name
  )
  select ?,?,?,?,?,?,?,?,?,?,?,?,?,?
  WHERE NOT EXISTS (SELECT 1 FROM widget_statistic_day WHERE day=?  and widget_id=? and server_id=?)
  ";
  $this->insertIntSumNew= $pgpdonew->prepare($sql);
  
    $sql="
    update widget_statistic_yandex_day
    set views=?,
    clicks=?,
    summa=?,
	client_summa=?,
	manager_summa=?,
	myclicks=?,
	server_name=?,
	client_id=?,
	client_name=?,
	manager_id=?,
	manager_name=?
    WHERE day=? and widget_id=? and server_id=?
  ";
  $this->updateYandexIntSumNew= $pgpdonew->prepare($sql); 
  
    $sql="
    insert into widget_statistic_yandex_day(
    day,
    widget_id,
    server_id,
    views,
    clicks,
    summa,
	client_summa,
	manager_summa,
    myclicks,
	server_name,
	client_id,
	client_name,
	manager_id,
	manager_name
    )
  select ?,?,?,?,?,?,?,?,?,?,?,?,?,?
  WHERE NOT EXISTS (SELECT 1 FROM widget_statistic_yandex_day WHERE day=?  and widget_id=? and server_id=?)
  ";
  $this->insertYandexIntSumNew= $pgpdonew->prepare($sql);
      $sql="
    update widget_statistic_my_day
    set views=?,
    clicks=?,
    summa=?,
	client_summa=?,
	manager_summa=?,
	myclicks=?,
	server_name=?,
	client_id=?,
	client_name=?,
	manager_id=?,
	manager_name=?
    WHERE day=? and widget_id=? and server_id=?
  ";
  $this->updateMyIntSumNew= $pgpdonew->prepare($sql); 
  
    $sql="
    insert into widget_statistic_my_day(
    day,
    widget_id,
    server_id,
    views,
    clicks,
    summa,
	client_summa,
	manager_summa,
    myclicks,
	server_name,
	client_id,
	client_name,
	manager_id,
	manager_name
    )
  select ?,?,?,?,?,?,?,?,?,?,?,?,?,?
  WHERE NOT EXISTS (SELECT 1 FROM widget_statistic_my_day WHERE day=?  and widget_id=? and server_id=?)
  ";
  $this->insertMyIntSumNew= $pgpdonew->prepare($sql);
  
   
      $sql="
    update widget_statistic_topadvert_day
    set views=?,
    clicks=?,
    summa=?,
	client_summa=?,
	manager_summa=?,
	myclicks=?,
	server_name=?,
	client_id=?,
	client_name=?,
	manager_id=?,
	manager_name=?
    WHERE day=? and widget_id=? and server_id=?
  ";
  $this->updateTopIntSumNew= $pgpdonew->prepare($sql); 
  
    $sql="
    insert into widget_statistic_topadvert_day(
    day,
    widget_id,
    server_id,
    views,
    clicks,
    summa,
	client_summa,
	manager_summa,
    myclicks,
	server_name,
	client_id,
	client_name,
	manager_id,
	manager_name
    )
  select ?,?,?,?,?,?,?,?,?,?,?,?,?,?
  WHERE NOT EXISTS (SELECT 1 FROM widget_statistic_topadvert_day WHERE day=?  and widget_id=? and server_id=?)
  ";
  $this->insertTopIntSumNew= $pgpdonew->prepare($sql);
  
  
   foreach($this->widgets as $z=>$k){
	 $wid=$pid=$z;
	 
	 $yviews=0;
	 $yclicks=0;
	 $myyclicks=0;
	 $ysumma=0;
	 $yclient_summa=0;
	 $ymanager_summa=0;
	 
	 $mviews=0;
	 $mclicks=0;
	 $mymclicks=0;
	 $msumma=0;
	 $mclient_summa=0;
	 $mmanager_summa=0;
	 
	 $tviews=0;
	 $tclicks=0;
	 $mytclicks=0;
	 $tsumma=0;
	 $tclient_summa=0;
	 $tmanager_summa=0;
	 
	 
	 $views=0;
	 $clicks=0;
	 $myclicks=0;
	 $summa=0;
	 $client_summa=0;
	 $manager_summa=0;
	 if(isset($k["YunShow"])){ #общие показы
		 $views=$k["YunShow"];
	 }else{
		 $views=1;
		 #print "вотговно ..$wid..\n" ;
	 }
	 if(isset($k["showYa"])){ #яндекс показы
	 $yviews=$k["showYa"];
	 #$views+=$yviews;
	 }
	 if(isset($k["clicksYa"])){ #яндекс клики
	 $yclicks=$k["clicksYa"];
	 $clicks+=$yclicks;
	 }
    if(isset($k["summaYa"])){ #яндекс сумма
		$ysumma=$k["summaYa"];
		$summa+=$ysumma;
	}
	if(isset($k["myClicksYa"])){ #наши яндекс клики
		$myyclicks=$k["myClicksYa"];
		$myclicks+=$myyclicks;
	}
	 
	 
	 
	 
	 if(isset($k["viewsR"])){ #прямые показы
	  $mviews=$k["viewsR"];
	 }
	 if(isset($k["clicksR"])){ #прямые клики = наши прямые клики
	 $mymclicks=$mclicks=$k["clicksR"];
	 $clicks+=$mclicks;
	 $myclicks+=$mymclicks;
	 if(isset($k["summaR"])){#прямые суммы
	    $msumma=$k["summaR"];
		$summa+=$msumma; 
		if(isset($k["clientSummaR"])){ #прямые суммы клиента
		$mclient_summa=$k["clientSummaR"];
		$client_summa+=$mclient_summa;
		}
	 }
	 }
	 if(isset($k["viewsAdv"])){ #топадверт показы
	  $tviews=$k["viewsAdv"];
	 }
	 if(isset($k["clicksAdv"])){ #топаверт клики
	 $tclicks=$k["clicksAdv"];
	 $clicks+=$tclicks;
	 }
	 
	 if(isset($k["summaAdv"])){ #топаверт сумма
	 $tsumma=$k["summaAdv"];
	 $summa+=$tsumma;
	 }
	 if(isset($k["myClicksAdv"])){ #наши топаверт клики
	 $mytclicks=$k["myClicksAdv"];
	 $myclicks+=$mytclicks;
	 }
	 
	
   
		$servd=$this->getServerName($wid);
		if($servd){
			if($tsumma){
			$tclient_summa=$this->getClientCommission($servd,2,$tsumma,$tclicks);
			$client_summa+=$tclient_summa;
			}
			if($ysumma){
			$yclient_summa=$this->getClientCommission($servd,1,$ysumma,$yclicks);
			$client_summa+=$yclient_summa;
			}
			
			
			if($servd["manager"]){
				if(isset($ManagerProcent[$servd["manager"]])){
					if($mclient_summa){#прямые суммы менеджера
	                 $mmanager_summa=round($mclient_summa*$ManagerProcent[$servd["manager"]],4);		
                     $manager_summa+=$mmanager_summa;					 
					}
					if($tclient_summa){#топадверт суммы менеджера
	                 $tmanager_summa=round($tclient_summa*$ManagerProcent[$servd["manager"]],4);
                     $manager_summa+=$tmanager_summa;					 
					}
	 			    if($yclient_summa){#топадверт суммы менеджера
	                 $ymanager_summa=round($yclient_summa*$ManagerProcent[$servd["manager"]],4);
                     $manager_summa+=$ymanager_summa;							 
					}
			    
				}
			}
			#print $manager_summa."\n";
		}else{
			var_dump(["1---1",$wid]); die();
		}
		/*Общая таблица*/
	$ret1=[
	$views,
	$clicks,
	$summa,
	$client_summa,
	$manager_summa,
	$myclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	
	$this->updateIntSumNew->execute($ret1);
	
	$count = $this->updateIntSumNew->rowCount();
	
	if(!$count){ 
	$ret1=[
	$this->date,
	$wid,
	$servd["id"],
	$views,
	$clicks,
	$summa,
	$client_summa,
	$manager_summa,
	$myclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->insertIntSumNew->execute($ret1);
	}
	if(1==1 || $yviews){
	/*Яндекс таблица*/
	$ret1=[
	$yviews,
	$yclicks,
	$ysumma,
	$yclient_summa,
	$ymanager_summa,
	$myyclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->updateYandexIntSumNew->execute($ret1);
	$count = $this->updateYandexIntSumNew->rowCount();
	if(!$count){ 
	$ret1=[
	$this->date,
	$wid,
	$servd["id"],
	$yviews,
	$yclicks,
	$ysumma,
	$yclient_summa,
	$ymanager_summa,
	$myyclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->insertYandexIntSumNew->execute($ret1);
	}
	}
	if(1==1 || $mviews){
		/*Прямая таблица*/
	$ret1=[
	$mviews,
	$mclicks,
	$msumma,
	$mclient_summa,
	$mmanager_summa,
	$mymclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->updateMyIntSumNew->execute($ret1);
	$count = $this->updateMyIntSumNew->rowCount();
	if(!$count){ 
	$ret1=[
	$this->date,
	$wid,
	$servd["id"],
	$mviews,
	$mclicks,
	$msumma,
	$mclient_summa,
	$mmanager_summa,
	$mymclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->insertMyIntSumNew->execute($ret1);
	}
	}
	
		if(1==1 || $tviews){
		/*Топадверт таблица*/
	$ret1=[
	$tviews,
	$tclicks,
	$tsumma,
	$tclient_summa,
	$tmanager_summa,
	$mytclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->updateTopIntSumNew->execute($ret1);
	$count = $this->updateTopIntSumNew->rowCount();
	if(!$count){ 
	$ret1=[
	$this->date,
	$wid,
	$servd["id"],
	$tviews,
	$tclicks,
	$tsumma,
	$tclient_summa,
	$tmanager_summa,
	$mytclicks,
	$servd["ltd"],
	$servd["user_id"],
	$servd["user_name"],
	$servd["manager"],
	$servd["manager_name"],
	$this->date,
	$wid,
	$servd["id"]
	];
	$this->insertTopIntSumNew->execute($ret1);
	}
	}
	
    } 
	$myhour=preg_replace('/^0/','',date("H"));
		if($myhour==11){
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete from topadvert_requests where day <'$myday';
		delete from topadvert_requests where day <'$myday';
		delete from my_requests where day <'$myday';
		delete from yandex_requests where day <'$myday';
		delete from advert_requests where day <'$myday';
		";
		
		
		$pgpdonew->exec($sql);
		$myday=date("Y-m-d",time());
		$sql="delete from widget_statistic_day where day <'$myday';
		delete from widget_statistic_my_day where day <'$myday';
		delete from  widget_statistic_topadvert_day where day <'$myday';
		delete from  widget_statistic_yandex_day where day <'$myday';
		
		";
		
		echo $sql;
		}
	}
}