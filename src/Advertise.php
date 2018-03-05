<?php
namespace Mplacegit\Statistica;
class Advertise
{
	private $date;
	public function setDate($date){
		$this->date=$date;
		$this->Servers=[];
		$pdo =\DB::connection()->getPdo();
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
		$this->serverSth=$pdo->prepare($sql);
		
	}
	public function  getServerName($id_server){
		if(!isset($this->Servers[$id_server])){
		$d=$this->serverSth->execute([$id_server]);
		$d=$this->serverSth->fetch(\PDO::FETCH_ASSOC);
		$this->Servers[$id_server]=$d;
		}
		return $this->Servers[$id_server];
		
	}
	public function  Calculate(){
		
		print $this->date; print " всё будет ок\n";
		$pdo=\DB::connection('pgstatistic_new')->getPdo();
		
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
        avg_loaded=?
        WHERE day=? and server_id =? and hash=? 
        ";
		$sthUpdate=$pdo->prepare($sql);
		$sql="insert into widget_requests_server_loaded (
        day,
        server_id,
        hash,
        cnt,
        avg_loaded
        )
	    select ?,?,?,?,?,?
		WHERE NOT EXISTS (SELECT 1 FROM widget_requests_server_loaded WHERE day=? and server_id =? and hash=?) 
        ";
		$sthInsert=$pdo->prepare($sql);
		$sql="select 
        day,id_server,hash,url,count(*) as cnt,nosearch,round(avg(loaded),4) as vv
        ,max(loaded) as mm
        from widget_requests where day = '".$this->date."'
        group by day,id_server,hash,url,nosearch
        order by vv desc";
		$data=\DB::connection('pgstatistic_new')->select($sql);
		foreach($data as $d){
			$rept=[$d->cnt,
			$d->vv,
			$d->day,
			$d->id_server
			];
			var_dump($rept);
		}
		
	}

}
