<?php
namespace Mplacegit\Statistica;
class Advertise
{
	private $date;
	public function setDate($date){
		$this->date=$date;
		
	
		
	}
	public function  getServerName($id_server){
		if(!isset($this->Servers[$id_server])){
		$this->serverSth->execute([$id_server]);
		$d=$this->serverSth->fetch(\PDO::FETCH_ASSOC);
		$this->Servers[$id_server]=$d;
		}
		return $this->Servers[$id_server];
		
	}

	public function  Calculate(){
		$this->Servers=[];
		$pdoss =\DB::connection()->getPdo();
		$sql="select t1.id
,t1.user_id
,t1.domain
,w.ltd
,u.name as user_name
from (partner_pads t1
inner join users u on u.id=t1.user_id
)
left join widgets w on w.pad=t1.id
and w.ltd is not null and w.ltd<>''
 where t1.id=?";
		$this->serverSth=$pdoss->prepare($sql);
		
		
		$pages_cnt=0;
        $request_sum=0;
        $max_loaded=0;
        $avg_loaded=0;
		$sql="
		select  round(avg(loaded),4) as avg_loaded 
,max(loaded) as max_loaded
from widget_requests where nosearch =0
and
day ='".$this->date."'
		";
		$da=\DB::connection('pgstatistic_new')->select($sql);
		if($da){
	$max_loaded=$da[0]->max_loaded;
	$avg_loaded=$da[0]->avg_loaded;

}
$sql="select count(*) as cnt,sum(cnt) as summa from (
select  hash,count(*) as cnt
from widget_requests where 1=1
and day ='".$this->date."'
group by hash
) h
";	

$da=\DB::connection('pgstatistic_new')->select($sql);
if($da){
	$pages_cnt=$da[0]->cnt;
	$request_sum=$da[0]->summa;

}
  		
		print $this->date; print " всё будет ок\n";
		$pdo=\DB::connection('pgstatistic_new')->getPdo();

		$sql="update widget_day_summary 

    set pages_cnt = $pages_cnt,
    request_sum = $request_sum,
    max_loaded = $max_loaded,
    avg_loaded = $avg_loaded
    WHERE day='".$this->date."' ";
	$pdo->exec($sql);
	$sql="insert into widget_day_summary (
    day,
    pages_cnt,
    request_sum,
    max_loaded,
    avg_loaded
   ) select '".$this->date."',$pages_cnt,$request_sum,$max_loaded,$avg_loaded
   WHERE NOT EXISTS (SELECT 1 FROM widget_day_summary  WHERE day='".$this->date."' )";
   $pdo->exec($sql);

				$sql="
		update widget_requests_loaded
        set cnt=?,
        avg_loaded=?,
        max_loaded=?
	    WHERE day=? and server_id =?";
		$sthUpdate=$pdo->prepare($sql);
		
		$sql="
		insert into  widget_requests_loaded(
        day,
        server_id,
        ltd,
        user_id,
        user_name,
        cnt,
        avg_loaded,
        max_loaded
        )select ?,?,?,?,?,?,?,?
	    WHERE NOT EXISTS (SELECT 1 FROM widget_requests_loaded WHERE day=? and server_id =?) ";
		$sthInsert=$pdo->prepare($sql);
		
		$sql="select 
		day,id_server,count(*) as cnt,nosearch,round(avg(loaded),4) as vv
,max(loaded) as mm
from widget_requests where day = '".$this->date."'
group by day,id_server,nosearch
order by vv desc";
		$data=\DB::connection('pgstatistic_new')->select($sql);
		foreach($data as $d){
			$server=$this->getServerName($d->id_server);
			if(!$server){
				print $d->id_server." not found\n";
			    continue;	
			}
			$rept=[
			$d->cnt,
			$d->vv,
			$d->mm,
			$d->day,
			$d->id_server,
			];
			$sthUpdate->execute($rept);
			$count = $sthUpdate->rowCount();
			if($count) continue;
			$rept=[
			$d->day,
			$d->id_server,
			$server["ltd"],
			$server["user_id"],
			$server["user_name"],
			$d->cnt,
			$d->vv,
			$d->mm,
			$d->day,
			$d->id_server,
			];
			$sthInsert->execute($rept);
			//var_dump($count);
		}
		$sql="update widget_requests_server_loaded 
        set cnt=?,
        avg_loaded=?,
		max_loaded=?,
		url=?,
		request=?,
		last_loaded=?,
		datetime_max_last=?
		WHERE day=? and server_id =? and hash=? 
        ";
		$sthUpdate=$pdo->prepare($sql);
		$sql="insert into widget_requests_server_loaded (
        day,
        server_id,
        hash,
        cnt,
        avg_loaded,
		max_loaded,
		url,
		request,
		last_loaded,
		datetime_max_last
        )
	    select ?,?,?,?,?,?,?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM widget_requests_server_loaded WHERE day=? and server_id =? and hash=?) 
        ";
		$sthInsert=$pdo->prepare($sql);
		
		$sql="select id_server,day,hash,loaded,request,max(datetime) as dt
		 from widget_requests where day = '".$this->date."'
		 group by id_server,day,hash,loaded,request
		";
		$rdata=\DB::connection('pgstatistic_new')->select($sql);
		$pererequest=[];
		foreach($rdata as $d){
			if($d->request)
			$pererequest[$d->day][$d->id_server][$d->hash][$d->loaded]=[$d->request,$d->dt];
			
		}
		
		
		$sql="select 
        day,id_server,hash,url,count(*) as cnt,nosearch,round(avg(loaded),4) as vv
        ,max(loaded) as mm
		,max(datetime) as dt
        from widget_requests where day = '".$this->date."'
        group by day,id_server,hash,url,nosearch
        order by vv desc";
		$data=\DB::connection('pgstatistic_new')->select($sql);
		foreach($data as $d){
			$req="";
			$mq=null;
			if(isset($pererequest[$d->day][$d->id_server][$d->hash][$d->mm])){
				$req=$pererequest[$d->day][$d->id_server][$d->hash][$d->mm][0];
				$mq=$pererequest[$d->day][$d->id_server][$d->hash][$d->mm][1];
				#var_dump($mq);
				
			}else{
				#print "вотфигня ".$d->url."\n";
			}
			$rept=[$d->cnt,
			$d->vv,
			$d->mm,
			$d->url,
			$req,
			$d->dt,
			$mq,
			$d->day,
			$d->id_server,
			$d->hash
			];
			$sthUpdate->execute($rept);
			$count = $sthUpdate->rowCount();
			
			if($count) continue;
			$rept=[
			$d->day,
			$d->id_server,
			$d->hash,
			$d->cnt,
			$d->vv,
			$d->mm,
			$d->url,
			$req,
			$d->dt,
			$mq,
			$d->day,
			$d->id_server,
			$d->hash
			];
			$sthInsert->execute($rept);
			//var_dump($d->dt);
		}
				$myhour=preg_replace('/^0/','',date("H"));
		if($myhour==6){
		$myday=date("Y-m-d",time()-(3600*48));
		$sql="delete from widget_requests where day <'$myday';
		";
		$pdo->exec($sql);
		
		print "delete widget_requests untill $myday !!!!\n";
		}
		$sql="insert into widget_search_requests_tmp (
        id_server,
        hash,
        request,
        str_index
        )
        select id_server,hash,request,md5(request) 
        from widget_requests
        where request is not null and request <>''
        group by id_server,hash,request;
		insert into 
widget_search_requests (
    id_server,
    hash,
    request,
    str_index
)
select 
t.id_server,
    t.hash,
    t.request,
    t.str_index
from(
select id_server,
    hash,
    request,
    str_index from widget_search_requests_tmp
	group by id_server,
    hash,
    request,
    str_index
) t
left join widget_search_requests z
on z.id_server = t.id_server
and z.hash  = t.hash
and z.str_index=t.str_index
where z.id_server is null ;
truncate table widget_search_requests_tmp
		";
		$pdo->exec($sql);
	}
	

}
