<?php
namespace Mplacegit\Statistica;
class Advertise
{
	private $date;
	public function setDate($date){
		$this->date=$date;
		$this->Servers=[];
		$pdo =\DB::connection()->getPdo();
		$sql="select * form partner_pads where id =?";
		$this->serverSth=$pdo->prepare($sql);
		
	}
	public function  getServerName($id_server){
		if(!isset($this->Servers[$id_server])){
		var_dump($id_server);
		$d=$this->serverSth->execute([$id_server])->fetch();
		var_dump($d);
		$this->Servers[$id_server]=1;
		}
	    return [];
		
	}
	public function  Calculate(){
		
		print $this->date; print " всё будет ок\n";
		$sql="select day,id_server,nosearch,avg(loaded) as aa 
,max(loaded) as ab
from widget_requests where day = '".$this->date."'
group by day,id_server,nosearch
order by aa desc";
		$data=\DB::connection('pgstatistic_new')->select($sql);
		foreach($data as $d){
			$server=$this->getServerName($d->id_server);
			#var_dump($d->id_server);
		}
		
	}

}
