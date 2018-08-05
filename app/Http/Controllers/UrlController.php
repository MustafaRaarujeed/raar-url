<?php

namespace App\Http\Controllers;

use App\Url;
use Carbon\Carbon;
use App\Rules\UrlValid;
use Illuminate\Http\Request;

class UrlController extends Controller
{

	/**
	 * $MONTH_TO_EXPIRE
	 * $INDEX_START
	 * $LENGTH
	 * @var integer
	 */
	protected $MONTH_TO_EXPIRE = 2;
	protected $INDEX_START = 1;
	protected $LENGTH = 7;

	/**
	 * [index description]
	 * @return [type] [description]
	 */
    public function index()
    {
    	$urls = Url::orderBy('created_at', 'ASC')->get();
    	return view('url_form', [
    		'urls' => $urls
    	]);
    }

    /**
     * [store description]
     * @param  Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
    	// Rules
    	$rules = [
    		'original' => 'required',
    	];

    	// Validate the request
    	$this->validate($request, $rules);

    	// create hash from original url
    	$url_hash = $this->createHashUrl($request['original']);

    	// check duplicate and store the record
		if($this->checkUrlDuplicate($url_hash)) {
	    	try {
	    		$url = new Url();
	    		$url->hash = $url_hash;
	    		$url->original = $request['original'];
	    		$url->expire_at = Carbon::now()->addMonth($this->MONTH_TO_EXPIRE);
	    		$url->save();
	    	} catch (Exception $e) {
	    		print_r($e);
	    	}
		}

    	return redirect()->route('url.get');
    }

    /**
     * [redirects description]
     * @param  Request $request  
     * @param  string  $hash_url 
     * @return mixed            
     */
    public function redirects(Request $request, $hash_url)
    {
    	$url = Url::where('hash', $hash_url)->first();
    	if($url) {
    		return redirect('http://'. $url->original);
    	} else {
    		return json_encode("sd");
    	}
    }


    /**
     * check if there is a hash duplicate in DB
     * if found perform createHashUrl in the @param
     * @param  string $url
     * @return mixed
     */
    protected function checkUrlDuplicate($url)
    {
    	$dbUrl = Url::where('hash', $url)->get();
    	if($dbUrl) {
    		$url_hash = $this->createHashUrl($dbUrl);
    		return $url_hash;
    	} else {
    		return true;
    	}
    }

    /**
     * create hash string from given string
     * @param  string $url
     * @return string $url_hash
     */
    protected function createHashUrl($url)
    {
    	$data = $url . uniqid(time());
    	$url_hash = hash('md5', $data);
    	$url_hash = base64_encode($url_hash);
    	$url_hash = substr($url_hash, $this->INDEX_START, $this->LENGTH);
    	return $url_hash;
    }

    /**
     * [checkUrlValid description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    protected function checkUrlValid($url)
    {
    	$ch = curl_init($url);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $data = curl_exec($ch);
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
	    curl_close($ch);
    	dd($httpcode);
    }
}
