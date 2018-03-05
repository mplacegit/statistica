<?php namespace Mplacegit\Statistica\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class LoadedController extends Controller{

	public function index(){
		return view('mp-statistica::index');
	}
}